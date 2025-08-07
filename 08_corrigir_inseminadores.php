<?php
// Script para ATUALIZAR os registros de inseminação com o ID correto do inseminador.

require_once __DIR__ . '/config/database.php';

echo "<pre>"; // Formata a saída

// --- 1. CONFIGURAÇÃO ---
define('CSV_FILE_PATH', __DIR__ . '/_relatorioInseminacoesAnimais2025-06-13-13-59.csv');
// Mapeamento de colunas para seus índices
define('COL_DATA_INSEMINACAO', 1);
define('COL_BRINCO_VACA', 0);
define('COL_INSEMINADOR', 3);
define('MIN_EXPECTED_COLUMNS', 5);

// --- 2. VERIFICAÇÃO DO ARQUIVO ---
if (!file_exists(CSV_FILE_PATH)) {
    die("ERRO: O arquivo CSV de inseminações não foi encontrado.");
}

// --- 3. PRÉ-CARREGAMENTO DE DADOS (OTIMIZAÇÃO) ---
$gadoMap = [];
$inseminadoresMap = [];

try {
    // Mapeia brinco da vaca para seu ID
    $stmtGado = $pdo->query("SELECT id, brinco FROM gado WHERE ativo = 1");
    while ($row = $stmtGado->fetch(PDO::FETCH_ASSOC)) {
        $gadoMap[trim($row['brinco'])] = $row['id'];
    }
    echo "Mapeamento de Gado concluído: " . count($gadoMap) . " animais carregados.\n";
    
    // Mapeia nome do inseminador para seu ID
    $stmtInseminadores = $pdo->query("SELECT id, nome FROM inseminadores WHERE ativo = 1");
    while ($row = $stmtInseminadores->fetch(PDO::FETCH_ASSOC)) {
        $inseminadoresMap[trim($row['nome'])] = $row['id'];
    }
    echo "Mapeamento de Inseminadores concluído: " . count($inseminadoresMap) . " inseminadores carregados.\n\n";

} catch (PDOException $e) {
    die("ERRO ao carregar dados para mapeamento: " . $e->getMessage());
}

// --- 4. LEITURA DO CSV E ATUALIZAÇÃO ---
$updatesRealizados = 0;
$registrosSemMatch = 0;
$erros = 0;
$linhaAtual = 1;

if (($handle = fopen(CSV_FILE_PATH, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ";"); // Pula o cabeçalho

    // Prepara a consulta UPDATE
    $stmtUpdate = $pdo->prepare(
        "UPDATE inseminacoes SET id_inseminador = :id_inseminador 
         WHERE id_vaca = :id_vaca AND data_inseminacao = :data_inseminacao"
    );

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $linhaAtual++;

        if (count($data) < MIN_EXPECTED_COLUMNS) {
            echo "[Linha {$linhaAtual}] AVISO: Linha malformada. Pulando.\n";
            $erros++;
            continue;
        }

        $brincoVaca = trim($data[COL_BRINCO_VACA]);
        $dataStr = trim($data[COL_DATA_INSEMINACAO]);
        $nomeInseminador = trim($data[COL_INSEMINADOR]);
        
        if (empty($brincoVaca) || empty($dataStr) || empty($nomeInseminador)) {
            echo "[Linha {$linhaAtual}] AVISO: Dados essenciais (brinco, data ou inseminador) faltando. Pulando.\n";
            continue;
        }

        // Busca IDs nos mapas pré-carregados
        $idVaca = $gadoMap[$brincoVaca] ?? null;
        $idInseminador = $inseminadoresMap[$nomeInseminador] ?? null;

        if (!$idVaca) {
            echo "[Linha {$linhaAtual}] AVISO: Vaca '{$brincoVaca}' não encontrada no banco. Pulando.\n";
            $registrosSemMatch++;
            continue;
        }

        if (!$idInseminador) {
            echo "[Linha {$linhaAtual}] AVISO: Inseminador '{$nomeInseminador}' não encontrado no banco. Pulando.\n";
            $registrosSemMatch++;
            continue;
        }
        
        // Formata a data para a busca no banco
        $dateObj = DateTime::createFromFormat('d/m/Y', $dataStr);
        if (!$dateObj) {
            echo "[Linha {$linhaAtual}] AVISO: Formato de data inválido ('{$dataStr}'). Pulando.\n";
            $erros++;
            continue;
        }
        $dataFormatada = $dateObj->format('Y-m-d');
        
        try {
            $stmtUpdate->execute([
                ':id_inseminador' => $idInseminador,
                ':id_vaca' => $idVaca,
                ':data_inseminacao' => $dataFormatada
            ]);
            
            // rowCount() > 0 significa que a linha foi efetivamente alterada
            if ($stmtUpdate->rowCount() > 0) {
                echo "[Linha {$linhaAtual}] OK: Registro da vaca '{$brincoVaca}' atualizado com o inseminador '{$nomeInseminador}'.\n";
                $updatesRealizados++;
            }

        } catch (Exception $e) {
            echo "[Linha {$linhaAtual}] ERRO FATAL ao atualizar registro para '{$brincoVaca}': " . $e->getMessage() . ".\n";
            $erros++;
        }
    }
    fclose($handle);
}

// --- 5. RELATÓRIO FINAL ---
echo "\n--- ATUALIZAÇÃO DE INSEMINADORES CONCLUÍDA ---\n";
echo "Registros atualizados com sucesso: " . $updatesRealizados . "\n";
echo "Registros não encontrados (vaca ou inseminador): " . $registrosSemMatch . "\n";
echo "Linhas com erros (formato, etc.): " . $erros . "\n";
echo "</pre>";

?>