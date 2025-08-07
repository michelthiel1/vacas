<?php

class Estoque {
    private $conn;
    private $table_name = "estoque";
public $id_categoria_financeira; // Nova propriedade
    public $Id;
    public $Produto;
    public $Quantidade;
    public $Consumo_dia;
    public $Valor;
    public $ativo;
	    // NOVAS PROPRIEDADES PARA CONVERSÃO DE UNIDADE
    public $unidade_compra;
    public $unidade_consumo;
    public $fator_conversao;
	    public $valor_compra; // VALOR DE COMPRA (ex: por saco)


    public function __construct($db) {
        $this->conn = $db;
    }

/**
     * Adiciona ou remove uma quantidade do estoque de um produto específico.
     * @param int $id_produto O ID do produto a ser ajustado.
     * @param float $quantidade_ajuste A quantidade a ser adicionada (positiva) ou removida (negativa).
     * @return bool True se a atualização for bem-sucedida, false caso contrário.
     */
    public function ajustarQuantidade($id_produto, $quantidade_ajuste) {
        $query = "UPDATE " . $this->table_name . " 
                  SET Quantidade = Quantidade + :quantidade_ajuste 
                  WHERE Id = :id_produto";
        
        $stmt = $this->conn->prepare($query);

        // Limpa os dados para segurança
        $id_produto = htmlspecialchars(strip_tags($id_produto));
        $quantidade_ajuste = str_replace(',', '.', htmlspecialchars(strip_tags($quantidade_ajuste)));

        // Vincula os parâmetros
        $stmt->bindParam(':quantidade_ajuste', $quantidade_ajuste);
        $stmt->bindParam(':id_produto', $id_produto);

        // Executa a query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
	
	/**
     * Atualiza os valores de compra e consumo de um produto no estoque.
     * @param int $id_produto O ID do produto a ser atualizado.
     * @param float $novo_valor_compra O novo valor da unidade de compra (ex: valor do saco).
     * @return bool True se a atualização for bem-sucedida, false caso contrário.
     */
    public function atualizarValores($id_produto, $novo_valor_compra) {
        // Primeiro, busca o fator de conversão do produto
        $stmt_fetch = $this->conn->prepare("SELECT fator_conversao FROM " . $this->table_name . " WHERE Id = :id_produto");
        $stmt_fetch->bindParam(':id_produto', $id_produto);
        $stmt_fetch->execute();
        $produto = $stmt_fetch->fetch(PDO::FETCH_ASSOC);

        if (!$produto) {
            return false; // Produto não encontrado
        }

        // Calcula o novo valor de consumo
        $fator = (float)$produto['fator_conversao'];
        $valor_compra = (float)str_replace(',', '.', $novo_valor_compra);
        $novo_valor_consumo = ($fator > 0) ? $valor_compra / $fator : 0;

        // Agora, atualiza o banco de dados
        $query = "UPDATE " . $this->table_name . " 
                  SET valor_compra = :valor_compra, Valor = :valor_consumo 
                  WHERE Id = :id_produto";
        
        $stmt = $this->conn->prepare($query);

        // Vincula os parâmetros
        $stmt->bindParam(':valor_compra', $valor_compra);
        $stmt->bindParam(':valor_consumo', $novo_valor_consumo);
        $stmt->bindParam(':id_produto', $id_produto);

        // Executa a query
        return $stmt->execute();
    }
 private function calcularValorConsumo() {
        $fator = (float)str_replace(',', '.', $this->fator_conversao);
        $valor_compra = (float)str_replace(',', '.', $this->valor_compra);
        // O campo 'Valor' (consumo) é o resultado da divisão
        $this->Valor = ($fator > 0) ? $valor_compra / $fator : 0;
    }

   

    // ### NOVOS MÉTODOS CRUD ###

// ### INÍCIO DA MODIFICAÇÃO ###
/**
     * Lê todos os produtos com suas categorias e quantidades.
     * @param string $search Termo de pesquisa opcional.
     * @return array
     */
    public function readAllWithCategory($search = '') {
        // ### INÍCIO DA CORREÇÃO ###
        // A consulta agora inclui e.Quantidade para garantir que o estoque seja retornado.
        $query = "SELECT e.Id, e.Produto, e.Quantidade, fc.nome as categoria_nome 
                  FROM " . $this->table_name . " e
                  LEFT JOIN financeiro_categorias fc ON e.id_categoria_financeira = fc.id";
        
        if ($search) {
            $query .= " WHERE e.Produto LIKE :search OR fc.nome LIKE :search";
        }
        
        $query .= " ORDER BY e.Produto ASC";
        
        $stmt = $this->conn->prepare($query);
        if ($search) {
            $searchTerm = "%{$search}%";
            $stmt->bindParam(':search', $searchTerm);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ### FIM DA CORREÇÃO ###
    // ### FIM DA MODIFICAÇÃO ###
    
   public function create() {
        $this->calcularValorConsumo(); // Calcula o valor de consumo antes de salvar

        $query = "INSERT INTO " . $this->table_name . " SET 
                    Produto=:produto, 
                    unidade_compra=:unidade_compra,
                    unidade_consumo=:unidade_consumo,
                    fator_conversao=:fator_conversao,
                    valor_compra=:valor_compra,
                    Valor=:valor, 
                    Quantidade=:quantidade, 
                    id_categoria_financeira=:id_categoria, 
                    ativo=:ativo";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind
        $this->Produto = htmlspecialchars(strip_tags($this->Produto));
        $this->unidade_compra = htmlspecialchars(strip_tags($this->unidade_compra));
        $this->unidade_consumo = htmlspecialchars(strip_tags($this->unidade_consumo));
        $this->fator_conversao = str_replace(',', '.', $this->fator_conversao);
        $this->valor_compra = str_replace(',', '.', $this->valor_compra);
        $this->Quantidade = htmlspecialchars(strip_tags($this->Quantidade));
        $this->id_categoria_financeira = !empty($this->id_categoria_financeira) ? $this->id_categoria_financeira : null;
        $this->ativo = !empty($this->ativo) ? $this->ativo : 1;

        $stmt->bindParam(':produto', $this->Produto);
        $stmt->bindParam(':unidade_compra', $this->unidade_compra);
        $stmt->bindParam(':unidade_consumo', $this->unidade_consumo);
        $stmt->bindParam(':fator_conversao', $this->fator_conversao);
        $stmt->bindParam(':valor_compra', $this->valor_compra);
        $stmt->bindParam(':valor', $this->Valor); // Salva o valor de consumo calculado
        $stmt->bindParam(':quantidade', $this->Quantidade);
        $stmt->bindParam(':id_categoria', $this->id_categoria_financeira);
        $stmt->bindParam(':ativo', $this->ativo);
        
        return $stmt->execute();
    }

   public function update($id) {
        $this->calcularValorConsumo(); // Calcula o valor de consumo antes de salvar

        $query = "UPDATE " . $this->table_name . " SET 
                    Produto = :produto, 
                    unidade_compra = :unidade_compra,
                    unidade_consumo = :unidade_consumo,
                    fator_conversao = :fator_conversao,
                    valor_compra = :valor_compra,
                    Valor = :valor,
                    id_categoria_financeira = :id_categoria 
                  WHERE Id = :id";
        
        $stmt = $this->conn->prepare($query);

        // Sanitize and bind
        $this->Produto = htmlspecialchars(strip_tags($this->Produto));
        $this->unidade_compra = htmlspecialchars(strip_tags($this->unidade_compra));
        $this->unidade_consumo = htmlspecialchars(strip_tags($this->unidade_consumo));
        $this->fator_conversao = str_replace(',', '.', $this->fator_conversao);
        $this->valor_compra = str_replace(',', '.', $this->valor_compra);
        $this->id_categoria_financeira = !empty($this->id_categoria_financeira) ? $this->id_categoria_financeira : null;
        $id = htmlspecialchars(strip_tags($id));
        
        $stmt->bindParam(':produto', $this->Produto);
        $stmt->bindParam(':unidade_compra', $this->unidade_compra);
        $stmt->bindParam(':unidade_consumo', $this->unidade_consumo);
        $stmt->bindParam(':fator_conversao', $this->fator_conversao);
        $stmt->bindParam(':valor_compra', $this->valor_compra);
        $stmt->bindParam(':valor', $this->Valor);
        $stmt->bindParam(':id_categoria', $this->id_categoria_financeira);
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE Id = :id";
        $stmt = $this->conn->prepare($query);
        $id = htmlspecialchars(strip_tags($id));
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // ### MÉTODOS ANTIGOS (MANTIDOS) ###
 public function readOneProduct($id) {
        $query = "SELECT Id, Produto, id_categoria_financeira, unidade_compra, unidade_consumo, fator_conversao, valor_compra, Valor 
                  FROM " . $this->table_name . " WHERE Id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ### NOVO MÉTODO PARA ATUALIZAR UM PRODUTO ###
    public function updateCategoriaProduto($id_produto, $id_categoria) {
        $query = "UPDATE " . $this->table_name . " SET id_categoria_financeira = :id_categoria WHERE Id = :id_produto";
        $stmt = $this->conn->prepare($query);

        $id_categoria = empty($id_categoria) ? null : $id_categoria;

        $stmt->bindParam(':id_categoria', $id_categoria);
        $stmt->bindParam(':id_produto', $id_produto);

        return $stmt->execute();
    }
// ### INÍCIO DA NOVA FUNÇÃO ###
    /**
     * Atualiza a quantidade e o valor de um item no estoque após uma compra.
     * @param int $id_produto O ID do produto na tabela estoque.
     * @param float $quantidade_adicional A quantidade a ser somada ao estoque.
     * @param float $novo_valor O novo preço do produto.
     * @return bool True se a atualização for bem-sucedida.
     */
// ### INÍCIO DA NOVA FUNÇÃO ###
    /**
     * Atualiza a quantidade e o valor de um item no estoque após uma compra.
     * @param int $id_produto O ID do produto na tabela estoque.
     * @param float $quantidade_adicional A quantidade a ser somada ao estoque.
     * @param float $novo_valor O novo preço do produto, que irá substituir o antigo.
     * @return bool True se a atualização for bem-sucedida.
     */
    public function entradaEstoque($id_produto, $quantidade_adicional, $novo_valor_compra) {
        $stmt_factor = $this->conn->prepare("SELECT fator_conversao FROM " . $this->table_name . " WHERE Id = :id");
        $stmt_factor->bindParam(':id', $id_produto);
        $stmt_factor->execute();
        $produto_info = $stmt_factor->fetch(PDO::FETCH_ASSOC);

        if (!$produto_info) { return false; }

        $fator_conversao = (float)$produto_info['fator_conversao'];
        $novo_valor_consumo = ($fator_conversao > 0) ? (float)$novo_valor_compra / $fator_conversao : 0;

        $query = "UPDATE " . $this->table_name . " 
                  SET Quantidade = Quantidade + :quantidade, valor_compra = :valor_compra, Valor = :valor_consumo
                  WHERE Id = :id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':quantidade', $quantidade_adicional);
        $stmt->bindParam(':valor_compra', $novo_valor_compra);
        $stmt->bindParam(':valor_consumo', $novo_valor_consumo);
        $stmt->bindParam(':id', $id_produto);

        return $stmt->execute();
    }
    
    /**
     * Lê todos os produtos ativos para preencher seletores.
     * @return array
     */
/**
     * Lê todos os produtos ativos para preencher seletores.
     * @return array
     */
/**
     * Lê todos os produtos ativos para preencher seletores.
     * @return array
     */
    public function readAllProducts() {
        $query = "SELECT Id, Produto FROM " . $this->table_name . " WHERE ativo = 1 ORDER BY Produto ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ### FIM DA NOVA FUNÇÃO ###
    // ### FIM DA NOVA FUNÇÃO ###
    /**
     * Obtém a quantidade em estoque de um produto pelo seu nome.
     * Busca o nome do produto EXATAMENTE como ele é recebido.
     *
     * @param string $nomeProduto O nome do produto (ex: 'Silagem', 'Milho').
     * @return float A quantidade em estoque do produto, ou 0 se não encontrado.
     */
    public function getQuantidadeEmEstoque($nomeProduto) {
        error_log("DEBUG EstoqueModel: >> INICIANDO getQuantidadeEmEstoque para Produto: '{$nomeProduto}' <<");

        $query = "SELECT Quantidade FROM " . $this->table_name . " WHERE Produto = :nomeProduto AND ativo = 1 LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("ERRO EstoqueModel: Falha ao preparar a query. Erro: " . print_r($this->conn->errorInfo(), true));
            return 0.0;
        }

        $stmt->bindParam(':nomeProduto', $nomeProduto);
        
        if (!$stmt->execute()) {
            error_log("ERRO EstoqueModel: Falha ao executar a query para produto '{$nomeProduto}'. Erro: " . print_r($stmt->errorInfo(), true));
            return 0.0;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            error_log("DEBUG EstoqueModel: Estoque ENCONTRADO para produto EXATO: '{$nomeProduto}' -> Quantidade: " . $row['Quantidade']);
            return (float)$row['Quantidade'];
        }
        
        error_log("DEBUG EstoqueModel: Estoque NÃO ENCONTRADO para produto '{$nomeProduto}'. Retornando 0.");
        return 0.0;
    }
	
	// ### INÍCIO DA NOVA FUNÇÃO ###
    /**
     * Obtém informações completas de um produto (Quantidade e Valor).
     * @param string $nomeProduto O nome do produto.
     * @return array Retorna um array com Quantidade e Valor.
     */
    public function getEstoqueInfoPorProduto($nomeProduto) {
        $query = "SELECT Quantidade, Valor FROM " . $this->table_name . " WHERE Produto = :nomeProduto AND ativo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nomeProduto', $nomeProduto);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? $row : ['Quantidade' => 0, 'Valor' => 0];
    }
    // ### FIM DA NOVA FUNÇÃO ###
	
	// ### INÍCIO DA NOVA FUNÇÃO ###
    /**
     * Atualiza o estoque de múltiplos produtos.
     * @param array $estoqueData Dados do formulário no formato [id_produto => novo_valor].
     * @return bool True se bem-sucedido.
     */
    public function updateEstoqueBatch($estoqueData) {
        $query = "UPDATE " . $this->table_name . " SET Quantidade = :quantidade WHERE Id = :id";
        
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare($query);

            foreach ($estoqueData as $id => $quantidade) {
                // Apenas atualiza se um valor foi de fato enviado para aquele produto
                if ($quantidade !== '' && is_numeric($quantidade)) {
                    $stmt->execute([
                        ':quantidade' => str_replace(',', '.', $quantidade),
                        ':id'         => $id
                    ]);
                }
            }
            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Erro em EstoqueModel->updateEstoqueBatch: " . $e->getMessage());
            return false;
        }
    }
    // ### FIM DA NOVA FUNÇÃO ###
}
