<?php
// Script para importar partos a partir de um arquivo CSV (SINTAXE CORRIGIDA).

require_once __DIR__ . '/config/database.php';

echo "<pre>"; // Formata a saída

// --- 1. CONFIGURAÇÃO (Sintaxe corrigida de 'const' para 'define') ---
define('CSV_COL_BRINCO_VACA', 0);
define('CSV_COL_DATA_PARTO', 2);
define('CSV_COL_SEXO_CRIA', 3);
define('CSV_COL_TOURO', 1);
define('MIN_EXPECTED_COLUMNS', 4);

$csvFilePath = __DIR__ . '/partos.csv';


// --- 2. VERIFICAÇÃO DO ARQUIVO ---
if (!file_exists($csvFilePath)) {
    die("ERRO: Arquivo 'partos.csv' não encontrado.");
}

// --- 3. PRÉ-CARREGAMENTO DE DADOS (OTIMIZAÇÃO) ---
$gadoMap = [];
$tourosMap = [];

try {
    $stmtGado = $pdo->query("SELECT id, brinco FROM gado WHERE ativo = 1");
    while ($row = $stmtGado->fetch(PDO::FETCH_ASSOC)) {
        $gadoMap[trim($row['brinco'])] = $row['id'];
    }
    echo "Mapeamento de Gado concluído: " . count($gadoMap) . " animais carregados.\n";

    $stmtTouros = $pdo->query("SELECT id, nome FROM touros WHERE ativo = 1");
    while ($row = $stmtTouros->fetch(PDO::FETCH_ASSOC)) {
        $tourosMap[trim($row['nome'])] = $row['id'];
    }
    echo "Mapeamento de Touros concluído: " . count($tourosMap) . " touros carregados.\n\n";

} catch (PDOException $e) {
    die("ERRO ao carregar dados para mapeamento: " . $e->getMessage());
}

// --- 4. LEITURA DO CSV E IMPORTAÇÃO ---
$partosInseridos = 0;
$partosIgnorados = 0;
$erros = 0;
$linhaAtual = 1;

if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ";"); // Pula o cabeçalho

    $stmtSelectParto = $pdo->prepare("SELECT id FROM partos WHERE id_vaca = :id_vaca AND data_parto = :data_parto");
    $stmtInsertParto = $pdo->prepare(
        "INSERT INTO partos (id_vaca, id_touro, sexo_cria, data_parto, observacoes, ativo) 
         VALUES (:id_vaca, :id_touro, :sexo_cria, :data_parto, :observacoes, :ativo)"
    );

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $linhaAtual++;
        
        if (count($data) < MIN_EXPECTED_COLUMNS) {
            echo "[Linha {$linhaAtual}] AVISO: Linha malformada ou com menos de 4 colunas. Pulando.\n";
            $erros++;
            continue;
        }

        $brincoVaca = trim($data[CSV_COL_BRINCO_VACA]);
        $nomeTouro = trim($data[CSV_COL_TOURO]);
        $dataPartoStr = trim($data[CSV_COL_DATA_PARTO]);

        if (empty($brincoVaca) || empty($dataPartoStr)) {
            echo "[Linha {$linhaAtual}] ERRO: Brinco da vaca ou data do parto estão vazios. Pulando.\n";
            $erros++;
            continue;
        }

        $idVaca = $gadoMap[$brincoVaca] ?? null;
        $idTouro = $tourosMap[$nomeTouro] ?? null;

        if (!$idVaca) {
            echo "[Linha {$linhaAtual}] ERRO: Vaca com brinco '{$brincoVaca}' não encontrada no banco. Pulando.\n";
            $erros++;
            continue;
        }

        $dateObj = DateTime::createFromFormat('d/m/Y', $dataPartoStr);
        if (!$dateObj) {
            echo "[Linha {$linhaAtual}] ERRO: Formato de data inválido ('{$dataPartoStr}'). Use DD/MM/YYYY. Pulando.\n";
            $erros++;
            continue;
        }
        $dataPartoFormatada = $dateObj->format('Y-m-d');
        
        $stmtSelectParto->execute([':id_vaca' => $idVaca, ':data_parto' => $dataPartoFormatada]);
        if ($stmtSelectParto->rowCount() > 0) {
            echo "[Linha {$linhaAtual}] AVISO: Parto para a vaca '{$brincoVaca}' na data '{$dataPartoStr}' já existe. Ignorando.\n";
            $partosIgnorados++;
            continue;
        }

        $obsOriginal = isset($data[4]) ? trim($data[4]) : '';
        $observacoesFinais = "Importado via CSV." . (!empty($obsOriginal) ? " Obs original: " . $obsOriginal : "");

        try {
            $stmtInsertParto->execute([
                ':id_vaca' => $idVaca,
                ':id_touro' => $idTouro,
                ':sexo_cria' => trim($data[CSV_COL_SEXO_CRIA]),
                ':data_parto' => $dataPartoFormatada,
                ':observacoes' => $observacoesFinais,
                ':ativo' => 1
            ]);
            
            echo "[Linha {$linhaAtual}] OK: Parto da vaca '{$brincoVaca}' inserido com sucesso.\n";
            $partosInseridos++;

        } catch (Exception $e) {
            echo "[Linha {$linhaAtual}] ERRO FATAL ao processar parto para '{$brincoVaca}': " . $e->getMessage() . ".\n";
            $erros++;
        }
    }
    fclose($handle);

} else {
    die("ERRO: Não foi possível abrir o arquivo CSV 'partos.csv'.");
}

// --- 5. RELATÓRIO FINAL ---
echo "\n--- IMPORTAÇÃO DE PARTOS CONCLUÍDA ---\n";
echo "Total de partos novos inseridos: " . $partosInseridos . "\n";
echo "Partos ignorados (já existiam): " . $partosIgnorados . "\n";
echo "Linhas com erros (puladas): " . $erros . "\n";
echo "</pre>";

?>