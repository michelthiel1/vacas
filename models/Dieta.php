<?php

require_once __DIR__ . '/Estoque.php'; // Inclui o modelo Estoque

class Dieta {
    private $conn;
    private $table_name = "dieta";
    private $estoqueModel; // Para acessar os dados de estoque

    public $Id;
    public $Lote;
    public $Vacas;
    public $Silagem;
    public $Milho; 
    public $Soja; 
    public $Casca; 
    public $Polpa; 
    public $Caroco; 
    public $Feno;
    public $Mineral; 
    public $Equalizer; 
    public $Notox; 
    public $Ureia;
    public $Ice;
    public $Ativo;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->estoqueModel = new Estoque($db);
    }


 /**
     * Obtém os nomes de todos os lotes de dietas ativas.
     * @return array Uma lista com os nomes dos lotes.
     */
    public function getLotesAtivos() {
        $query = "SELECT DISTINCT Lote FROM " . $this->table_name . " WHERE Ativo = 1 ORDER BY Lote ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        // Retorna um array simples com os nomes dos lotes, ex: ['Campo', 'Lactantes', 'Pré-Parto']
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

  // ### INÍCIO DA NOVA FUNÇÃO ###
    /**
     * Obtém os dados de uma dieta específica com base no nome do lote.
     * @param string $lote O nome do lote a ser buscado (ex: 'Pré-Parto').
     * @return array|false Retorna um array associativo com os dados da dieta ou false se não for encontrada.
     */
    public function getDietaPorLote($lote) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE Lote = :lote AND Ativo = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':lote', $lote);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // ### FIM DA NOVA FUNÇÃO ###



// models/Dieta.php

    /**
     * Atualiza o número de vacas para um lote específico.
     * @param string $lote O nome do lote a ser atualizado.
     * @param int $novoNumeroVacas O novo número de vacas no lote.
     * @return bool Retorna true se a atualização for bem-sucedida, false caso contrário.
     */
    public function updateVacasPorLote($lote, $novoNumeroVacas) {
        
        // **INÍCIO DA CORREÇÃO**
        // A query foi ajustada para usar a tabela 'dieta' e as colunas 'Vacas' e 'Lote'.
        $query = "UPDATE " . $this->table_name . " SET Vacas = :vacas WHERE Lote = :lote AND ativo = 1";
        // **FIM DA CORREÇÃO**

        // Preparar a query
        $stmt = $this->conn->prepare($query);

        // Limpar os dados
        $lote_clean = htmlspecialchars(strip_tags($lote));
        $vacas_clean = htmlspecialchars(strip_tags($novoNumeroVacas));

        // Vincular os parâmetros
        $stmt->bindParam(':vacas', $vacas_clean, PDO::PARAM_INT);
        $stmt->bindParam(':lote', $lote_clean, PDO::PARAM_STR);

        // Executar a query
        if ($stmt->execute()) { // Esta é a linha 56 que estava dando erro
            return true;
        }

        // Se houver um erro, exiba-o para depuração
        // Em um ambiente de produção, você deve registrar isso em um log.
        printf("Erro: %s.\n", $stmt->error);

        return false;
    }
    /**
     * Obtém dados da dieta com base no lote.
     * O número de vacas do frontend é usado para cálculo do consumo total.
     *
     * @param string $lote O lote selecionado ('Todos', 'Campo', 'Lactantes').
     * @param int $numVacas O número de vacas selecionado no frontend (20-40).
     * @param int $vacas_lactantes_para_5dias O número de vacas de Lactantes (Id=1) para cálculo de 5 dias.
     * @return array Array de itens da dieta processados.
     */

public function getDietaPorLoteEVaca($lote, $numVacas, $vacas_lactantes_para_5dias) {
        error_log("DEBUG DietaModel: >> INICIANDO getDietaPorLoteEVaca para Lote: '{$lote}', Vacas (para cálculo): '{$numVacas}', Vacas Lactantes para 5 Dias (PARÂMETRO): '{$vacas_lactantes_para_5dias}' <<");

        $query = "SELECT * FROM " . $this->table_name . " WHERE Ativo = 1";

        $filters = [];
        $queryParams = [];

        if ($lote !== 'Todos') {
            $filters[] = "Lote = :lote";
            $queryParams[':lote'] = $lote;
        }
        
        if (!empty($filters)) {
            $query .= " AND " . implode(" AND ", $filters);
        }
        $query .= " ORDER BY Vacas DESC, Id DESC LIMIT 1"; 
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("ERRO DietaModel: Falha ao PREPARAR a query principal. Erro: " . print_r($this->conn->errorInfo(), true));
            return [];
        }

        foreach ($queryParams as $param => $value) {
            $stmt->bindParam($param, $value);
        }
        
        if (!$stmt->execute()) {
            error_log("ERRO DietaModel: Falha ao EXECUTAR a query principal. Erro: " . print_r($stmt->errorInfo(), true));
            return [];
        }

        $dietaRaw = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dietaRaw) {
            return [];
        }

        $processedDieta = [];
        $vacas_para_calculo_consumo_total_frontend = (is_numeric($numVacas) && (int)$numVacas > 0) ? (int)$numVacas : 1; 
        if ($vacas_para_calculo_consumo_total_frontend === 0) $vacas_para_calculo_consumo_total_frontend = 1; 

        $ingredientes_map = [
            'Silagem' => ['coluna_dieta_bd' => 'Silagem', 'nome_produto_estoque_bd' => 'Silagem', 'nome_exibicao' => 'Silagem'],
            'Milho' => ['coluna_dieta_bd' => 'Milho', 'nome_produto_estoque_bd' => 'Milho', 'nome_exibicao' => 'Milho'], 
            'Soja' => ['coluna_dieta_bd' => 'Soja', 'nome_produto_estoque_bd' => 'Soja', 'nome_exibicao' => 'Soja'], 
            'Casca' => ['coluna_dieta_bd' => 'Casca', 'nome_produto_estoque_bd' => 'Casca', 'nome_exibicao' => 'Casca'],   
            'Polpa' => ['coluna_dieta_bd' => 'Polpa', 'nome_produto_estoque_bd' => 'Polpa', 'nome_exibicao' => 'Polpa'], 
            'Caroco' => ['coluna_dieta_bd' => 'Caroco', 'nome_produto_estoque_bd' => 'Caroco', 'nome_exibicao' => 'Caroço'], 
            'Feno' => ['coluna_dieta_bd' => 'Feno', 'nome_produto_estoque_bd' => 'Feno', 'nome_exibicao' => 'Feno'],
            'Mineral' => ['coluna_dieta_bd' => 'Mineral', 'nome_produto_estoque_bd' => 'Mineral', 'nome_exibicao' => 'Mineral'], 
            'Equalizer' => ['coluna_dieta_bd' => 'Equalizer', 'nome_produto_estoque_bd' => 'Equalizer', 'nome_exibicao' => 'Equalizer'], 
            'Notox' => ['coluna_dieta_bd' => 'Notox', 'nome_produto_estoque_bd' => 'Notox', 'nome_exibicao' => 'Notox'], 
            'Ureia' => ['coluna_dieta_bd' => 'Ureia', 'nome_produto_estoque_bd' => 'Ureia', 'nome_exibicao' => 'Ureia'],
            'Ice' => ['coluna_dieta_bd' => 'Ice', 'nome_produto_estoque_bd' => 'Ice', 'nome_exibicao' => 'Ice']
        ];
        
        foreach ($ingredientes_map as $key => $map_info) {
            $nome_produto_estoque_bd = $map_info['nome_produto_estoque_bd'];
            
            // ### ALTERAÇÃO AQUI ###
            // Usa a nova função para buscar tanto quantidade quanto valor
            $estoqueInfo = $this->estoqueModel->getEstoqueInfoPorProduto($nome_produto_estoque_bd);

            $consumo_por_vaca_original = (float)($dietaRaw[$map_info['coluna_dieta_bd']] ?? 0);
            
            $processedDieta[] = [
                'ingrediente_nome' => $map_info['nome_exibicao'],
                'consumo_por_vaca_kg' => $consumo_por_vaca_original,
                'consumo_total_kg' => $consumo_por_vaca_original * $vacas_para_calculo_consumo_total_frontend,
                'estoque_kg' => (float)($estoqueInfo['Quantidade'] ?? 0),
                'valor_kg' => (float)str_replace(',', '.', ($estoqueInfo['Valor'] ?? 0)), // Pega o valor e o trata
                'consumo_zero_por_vaca' => ($consumo_por_vaca_original <= 0),
            ];
        }
        
        return $processedDieta;
    }
  /**
     * Atualiza o campo 'Vacas' para um registro de dieta específico baseado no Lote.
     * Se houver múltiplos registros para o mesmo lote, atualiza o mais relevante.
     *
     * @param string $lote O lote da dieta a ser atualizada.
     * @param int $novoNumeroVacas O novo número de vacas a ser salvo.
     * @return bool True em caso de sucesso, false caso contrário.
     */
 

	/**
     * Atualiza o campo 'Vacas' para um registro de dieta específico baseado no Lote.
     * Se houver múltiplos registros para o mesmo lote, atualiza o primeiro encontrado (pelo Id DESC).
     *
     * @param string $lote O lote da dieta a ser atualizada.
     * @param int $novoNumeroVacas O novo número de vacas a ser salvo.
     * @return bool True em caso de sucesso, false caso contrário.
     */
 

    /**
     * Calcula o consumo total de cada produto (Silagem, Milho, etc.)
     * de TODAS as dietas ativas por um número específico de dias (X dias, fixado em 5).
     *
     * @return array Um array associativo onde a chave é o nome do produto (ex: 'Silagem')
     * e o valor é o consumo total acumulado para aquele produto em 5 dias.
     */
    public function getConsumo10DiasTotalPorIngrediente() { // NOME DA FUNÇÃO EXATA SOLICITADA
        $dias = 5; // Número de dias fixo conforme solicitado

        $consumoAcumuladoPorProduto = [];

        // Mapeamento das colunas da dieta para os nomes dos produtos que queremos totalizar.
        // Os nomes devem ser os nomes das COLUNAS DA TABELA DIETA.
        $colunas_ingredientes_dieta = [
            'Silagem', 'Milho', 'Soja', 'Casca', 'Polpa', 'Caroco', 'Feno',
            'Mineral', 'Equalizer', 'Notox', 'Ureia', 'Ice'
        ];

        // Constrói a lista de colunas para selecionar
        $select_columns = implode(', ', $colunas_ingredientes_dieta);

        $query = "SELECT Id, Lote, Vacas, {$select_columns} FROM " . $this->table_name . " WHERE Ativo = 1";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("ERRO DietaModel: Falha ao PREPARAR a query getConsumoTotalDeTodasDietasPorProdutoParaXDias. Erro: " . print_r($this->conn->errorInfo(), true));
            return [];
        }

        if (!$stmt->execute()) {
            error_log("ERRO DietaModel: Falha ao EXECUTAR a query getConsumoTotalDeTodasDietasPorProdutoParaXDias. Erro: " . print_r($stmt->errorInfo(), true));
            return [];
        }

        $todasDietas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("DEBUG DietaModel: getConsumoTotalDeTodasDietasPorProdutoParaXDias - Todas as Dietas Brutas: " . print_r($todasDietas, true));

        // Inicializa os totais com zero
        foreach ($colunas_ingredientes_dieta as $coluna) {
            $consumoAcumuladoPorProduto[$coluna] = 0.0;
        }

        foreach ($todasDietas as $dieta) {
            $vacasDaDieta = (int)$dieta['Vacas'];
            $loteDieta = $dieta['Lote'];
            $dietaId = (int)$dieta['Id']; // Pega o ID da dieta

            foreach ($colunas_ingredientes_dieta as $coluna) {
                $consumoPorVaca = (float)($dieta[$coluna] ?? 0);

                // Aplicar a regra de divisão por 2 para Lactantes (se for dieta Id=1)
                // REMOVIDO: Regra de divisão por 2 aqui
                // if ($loteDieta === 'Lactantes' && $dietaId === 1) { // Só divide por 2 se for Id=1 (Lactantes)
                //     $consumoPorVaca /= 2;
                //     error_log("DEBUG DietaModel: getConsumoTotalDeTodasDietasPorProdutoParaXDias - Lote '{$loteDieta}' (Id={$dietaId}), '{$coluna}' por vaca DIVIDIDO por 2. Novo valor: {$consumoPorVaca}");
                // }

                // Acumula o consumo total diário para este produto e esta dieta
                $consumoDiarioDesteProdutoNestaDieta = $consumoPorVaca * $vacasDaDieta;
                $consumoAcumuladoPorProduto[$coluna] += $consumoDiarioDesteProdutoNestaDieta;
            }
        }

        // Multiplica o consumo diário acumulado pelo número de dias
        foreach ($consumoAcumuladoPorProduto as $produto => $consumoDiarioTotal) {
            $consumoAcumuladoPorProduto[$produto] = $consumoDiarioTotal * $dias; // Multiplica por 5 dias
        }
        
        error_log("DEBUG DietaModel: getConsumoTotalDeTodasDietasPorProdutoParaXDias - Consumo Total Acumulado para {$dias} dias: " . print_r($consumoAcumuladoPorProduto, true));
        return $consumoAcumuladoPorProduto;
    }
	// ### INÍCIO DA NOVA FUNÇÃO ###
    /**
     * Atualiza múltiplos registros de dieta de uma vez.
     * @param array $dietasData Dados do formulário no formato [id_dieta => [campo => valor]].
     * @return bool True se todas as atualizações forem bem-sucedidas.
     */
    public function updateBatch($dietasData) {
        $query = "UPDATE " . $this->table_name . " SET 
                    Vacas = :Vacas, Silagem = :Silagem, Milho = :Milho, Soja = :Soja, 
                    Casca = :Casca, Polpa = :Polpa, Caroco = :Caroco, Feno = :Feno, 
                    Mineral = :Mineral, Equalizer = :Equalizer, Notox = :Notox, 
                    Ureia = :Ureia, Ice = :Ice 
                  WHERE Id = :Id";
        
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare($query);

            foreach ($dietasData as $id => $data) {
                $stmt->execute([
                    ':Vacas'     => $data['Vacas'],
                    ':Silagem'   => $data['Silagem'],
                    ':Milho'     => $data['Milho'],
                    ':Soja'      => $data['Soja'],
                    ':Casca'     => $data['Casca'],
                    ':Polpa'     => $data['Polpa'],
                    ':Caroco'    => $data['Caroco'],
                    ':Feno'      => $data['Feno'],
                    ':Mineral'   => $data['Mineral'],
                    ':Equalizer' => $data['Equalizer'],
                    ':Notox'     => $data['Notox'],
                    ':Ureia'     => $data['Ureia'],
                    ':Ice'       => $data['Ice'],
                    ':Id'        => $id
                ]);
            }
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Erro em DietaModel->updateBatch: " . $e->getMessage());
            return false;
        }
    }
    // ### FIM DA NOVA FUNÇÃO ###
	// ### INÍCIO DA NOVA FUNÇÃO ###
    /**
     * Retorna todas as dietas ativas.
     * @return array
     */
    public function readAllActive() {
        // Esta query busca todas as linhas da tabela dieta onde o campo Ativo é 1.
        $query = "SELECT * FROM " . $this->table_name . " WHERE Ativo = 1 ORDER BY Id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        // fetchAll sempre retorna um array, mesmo que vazio, o que evita o erro.
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ### FIM DA NOVA FUNÇÃO ###
}