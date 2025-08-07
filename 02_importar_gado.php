<?php
// Fase 2 do Plano de Importação: Mapear e popular a tabela 'gado' a partir do CSV.

require_once __DIR__ . '/config/database.php';

echo "<pre>"; // Formata a saída no navegador

// --- 1. CONFIGURAÇÃO ---
$csvFilePath = __DIR__ . '/relatorio_fazenda_animais_63.csv';
// Mapeamento de colunas para seus índices (começando em 0)
const COL_BRINCO = 0;

const COL_NASCIMENTO = 4;

const COL_RACA = 5;
const COL_GRUPO = 6;
const COL_STATUS = 7;

const COL_PAI = 1;



// --- 2. VERIFICAÇÃO DO ARQUIVO ---
if (!file_exists($csvFilePath)) {
    die("ERRO: O arquivo CSV não foi encontrado no caminho: " . $csvFilePath);
}

// --- 3. PRÉ-CARREGAMENTO DOS TOUROS PARA MAPEAMENTO ---
// Isso otimiza o script, evitando uma consulta ao banco para cada linha do CSV.
$tourosMap = [];
try {
    $stmt = $pdo->query("SELECT id, nome FROM touros");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Cria um mapa com "Nome do Touro" => "ID do Touro"
        $tourosMap[trim($row['nome'])] = $row['id'];
    }
    echo "Pré-carregamento concluído: " . count($tourosMap) . " touros encontrados no banco.\n\n";
} catch (PDOException $e) {
    die("ERRO ao pré-carregar touros: " . $e->getMessage());
}


// --- 4. LEITURA DO CSV E INSERÇÃO DO GADO ---
$animaisInseridos = 0;
$animaisJaExistiam = 0;
$errosEncontrados = 0;
$linhaAtual = 1;

if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Pula o cabeçalho
    fgetcsv($handle, 1000, ";");

    // Prepara as consultas SQL para reutilização
    $stmtSelectGado = $pdo->prepare("SELECT id FROM gado WHERE brinco = :brinco");
    $stmtInsertGado = $pdo->prepare(
        "INSERT INTO gado 
        (brinco, nascimento,  raca, grupo, status,  id_pai, id_mae, ativo, observacoes) 
        VALUES 
        (:brinco,  :nascimento,  :raca, :grupo, :status, :id_pai, :id_mae, :ativo, :observacoes)"
    );

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $linhaAtual++;
        $brinco = trim($data[COL_BRINCO]);

        if (empty($brinco)) {
            echo "AVISO [Linha {$linhaAtual}]: Brinco vazio, pulando linha.\n";
            continue;
        }

        // Verifica se o animal já existe
        $stmtSelectGado->execute([':brinco' => $brinco]);
        if ($stmtSelectGado->rowCount() > 0) {
            echo "Animal com brinco '{$brinco}' já existe. Ignorando...\n";
            $animaisJaExistiam++;
            continue;
        }

        // --- Início do DE/PARA (Mapeamento e Transformação) ---

        // Data de nascimento
        $dataNascimentoStr = trim($data[COL_NASCIMENTO]);
        $dataNascimentoFormatada = null;
        if (!empty($dataNascimentoStr)) {
            // Tenta converter do formato DD/MM/YYYY para YYYY-MM-DD
            $dateObj = DateTime::createFromFormat('d/m/Y', $dataNascimentoStr);
            if ($dateObj) {
                $dataNascimentoFormatada = $dateObj->format('Y-m-d');
            } else {
                 echo "ERRO [Linha {$linhaAtual}]: Formato de data inválido para o brinco '{$brinco}': '{$dataNascimentoStr}'. Pulando animal.\n";
                 $errosEncontrados++;
                 continue;
            }
        } else {
            echo "ERRO [Linha {$linhaAtual}]: Data de nascimento não fornecida para o brinco '{$brinco}'. Pulando animal.\n";
            $errosEncontrados++;
            continue;
        }

      

        // ID do Pai (busca no mapa pré-carregado)
        $nomePai = trim($data[COL_PAI]);
        $idPai = isset($tourosMap[$nomePai]) ? $tourosMap[$nomePai] : null;

        // Monta o array de dados para inserção
        $animalData = [
            ':brinco'       => $brinco,
            
            ':nascimento'   => $dataNascimentoFormatada,
            
            ':raca'         => trim($data[COL_RACA]),
            ':grupo'        => trim($data[COL_GRUPO]),
            ':status'       => trim($data[COL_STATUS]),
           
            ':id_pai'       => $idPai,
            ':id_mae'       => null, // Deixando NULO conforme o plano
            ':ativo'        => 1,
            ':observacoes'  => 'Importado via CSV'
        ];

        // Executa a inserção
        try {
            $stmtInsertGado->execute($animalData);
            echo "NOVO: Animal com brinco '{$brinco}' inserido com sucesso!\n";
            $animaisInseridos++;
        } catch (PDOException $e) {
            echo "ERRO [Linha {$linhaAtual}]: Falha ao inserir brinco '{$brinco}'. Erro do DB: " . $e->getMessage() . "\n";
            $errosEncontrados++;
        }
    }
    fclose($handle);

} else {
    die("ERRO: Não foi possível abrir o arquivo CSV para processar os animais.");
}

// --- 6. RELATÓRIO FINAL ---
echo "\n--- PROCESSO DE IMPORTAÇÃO DE GADO CONCLUÍDO ---\n";
echo "Total de animais novos inseridos: " . $animaisInseridos . "\n";
echo "Total de animais que já existiam (ignorados): " . $animaisJaExistiam . "\n";
echo "Total de linhas com erros (puladas): " . $errosEncontrados . "\n";
echo "\nPRÓXIMO PASSO: Executar o script para atualizar o campo 'id_mae'.\n";
echo "</pre>";
?>