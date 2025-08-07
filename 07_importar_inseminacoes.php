<?php
// Script final para importar registros de inseminação a partir de um arquivo CSV.
// Conforme solicitado, este script NÃO ALTERA a tabela 'gado'.

require_once __DIR__ . '/config/database.php';

echo "<pre>"; // Formata a saída

// --- 1. CONFIGURAÇÃO ---
define('CSV_FILE_PATH', __DIR__ . '/_relatorioInseminacoesAnimais2025-06-13-13-59.csv');
// Mapeamento de colunas para seus índices
define('COL_DATA_INSEMINACAO', 1);
define('COL_BRINCO_VACA', 0);
define('COL_TIPO', 4);
define('COL_TOURO', 2);
define('COL_INSEMINADOR', 3);
define('MIN_EXPECTED_COLUMNS', 5);

// --- 2. VERIFICAÇÃO DO ARQUIVO ---
if (!file_exists(CSV_FILE_PATH)) {
    die("ERRO: O arquivo CSV de inseminações não foi encontrado.");
}

// --- 3. PRÉ-CARREGAMENTO DE DADOS (OTIMIZAÇÃO) ---
$gadoMap = [];
$tourosMap = [];
$inseminadoresMap = [];

try {
    // Mapeia brinco da vaca para seu ID
    $stmtGado = $pdo->query("SELECT id, brinco FROM gado WHERE ativo = 1");
    while ($row = $stmtGado->fetch(PDO::FETCH_ASSOC)) {
        $gadoMap[trim($row['brinco'])] = $row['id'];
    }
    echo "Mapeamento de Gado concluído: " . count($gadoMap) . " animais carregados.\n";

    // Mapeia nome do touro para seu ID
    $stmtTouros = $pdo->query("SELECT id, nome FROM touros WHERE ativo = 1");
    while ($row = $stmtTouros->fetch(PDO::FETCH_ASSOC)) {
        $tourosMap[trim($row['nome'])] = $row['id'];
    }
    echo "Mapeamento de Touros concluído: " . count($tourosMap) . " touros carregados.\n";
    
    // Mapeia nome do inseminador para seu ID
    $stmtInseminadores = $pdo->query("SELECT id, nome FROM inseminadores WHERE ativo = 1");
    while ($row = $stmtInseminadores->fetch(PDO::FETCH_ASSOC)) {
        $inseminadoresMap[trim($row['nome'])] = $row['id'];
    }
    echo "Mapeamento de Inseminadores concluído: " . count($inseminadoresMap) . " inseminadores carregados.\n\n";

} catch (PDOException $e) {
    die("ERRO ao carregar dados para mapeamento: " . $e->getMessage());
}

// --- 4. LEITURA DO CSV E IMPORTAÇÃO ---
$inseminacoesInseridas = 0;
$inseminacoesIgnoradas = 0;
$erros = 0;
$linhaAtual = 1;

if (($handle = fopen(CSV_FILE_PATH, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ";"); // Pula o cabeçalho

    $stmtSelectInseminacao = $pdo->prepare("SELECT id FROM inseminacoes WHERE id_vaca = :id_vaca AND data_inseminacao = :data_inseminacao");
    $stmtInsertInseminacao = $pdo->prepare(
        "INSERT INTO inseminacoes (id_vaca, id_touro, id_inseminador, tipo, data_inseminacao, status_inseminacao, ativo, observacoes) 
         VALUES (:id_vaca, :id_touro, :id_inseminador, :tipo, :data_inseminacao, :status_inseminacao, 1, 'Importado via CSV')"
    );

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $linhaAtual++;

        if (count($data) < MIN_EXPECTED_COLUMNS) {
            echo "[Linha {$linhaAtual}] AVISO: Linha malformada ou com colunas faltando. Pulando.\n";
            $erros++;
            continue;
        }

        $brincoVaca = trim($data[COL_BRINCO_VACA]);
        $dataStr = trim($data[COL_DATA_INSEMINACAO]);
        $nomeTouro = trim($data[COL_TOURO]);
        $nomeInseminador = trim($data[COL_INSEMINADOR]);
        $tipoInseminacao = trim($data[COL_TIPO]);

        if (empty($brincoVaca) || empty($dataStr)) {
            echo "[Linha {$linhaAtual}] ERRO: Brinco ou data estão vazios. Pulando.\n";
            $erros++;
            continue;
        }

        $idVaca = $gadoMap[$brincoVaca] ?? null;
        if (!$idVaca) {
            echo "[Linha {$linhaAtual}] ERRO: Vaca com brinco '{$brincoVaca}' não encontrada. Pulando.\n";
            $erros++;
            continue;
        }

        $idTouro = $tourosMap[$nomeTouro] ?? null;
        $idInseminador = $inseminadoresMap[$nomeInseminador] ?? null;
        
        $dateObj = DateTime::createFromFormat('d/m/Y', $dataStr);
        if (!$dateObj) {
            echo "[Linha {$linhaAtual}] ERRO: Formato de data inválido ('{$dataStr}'). Pulando.\n";
            $erros++;
            continue;
        }
        $dataFormatada = $dateObj->format('Y-m-d');
        
        $stmtSelectInseminacao->execute([':id_vaca' => $idVaca, ':data_inseminacao' => $dataFormatada]);
        if ($stmtSelectInseminacao->rowCount() > 0) {
            echo "[Linha {$linhaAtual}] AVISO: Inseminação para '{$brincoVaca}' na data '{$dataStr}' já existe. Ignorando.\n";
            $inseminacoesIgnoradas++;
            continue;
        }

        try {
            $stmtInsertInseminacao->execute([
                ':id_vaca' => $idVaca,
                ':id_touro' => $idTouro,
                ':id_inseminador' => $idInseminador,
                ':tipo' => $tipoInseminacao,
                ':data_inseminacao' => $dataFormatada,
                ':status_inseminacao' => 'Aguardando Diagnostico'
            ]);
            
            echo "[Linha {$linhaAtual}] OK: Inseminação da vaca '{$brincoVaca}' inserida com sucesso.\n";
            $inseminacoesInseridas++;

        } catch (Exception $e) {
            echo "[Linha {$linhaAtual}] ERRO FATAL ao processar inseminação para '{$brincoVaca}': " . $e->getMessage() . ".\n";
            $erros++;
        }
    }
    fclose($handle);
}

// --- 5. RELATÓRIO FINAL ---
echo "\n--- IMPORTAÇÃO DE INSEMINAÇÕES CONCLUÍDA ---\n";
echo "Registros inseridos: " . $inseminacoesInseridas . "\n";
echo "Registros ignorados (duplicados): " . $inseminacoesIgnoradas . "\n";
echo "Linhas com erros (puladas): " . $erros . "\n";
echo "</pre>";

?>