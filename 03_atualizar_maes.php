<?php
// Fase 3 do Plano de Importação: Conectar os filhotes às suas mães.

require_once __DIR__ . '/config/database.php';

echo "<pre>"; // Formata a saída no navegador

// --- 1. CONFIGURAÇÃO ---
$csvFilePath = __DIR__ . '/relatorio_fazenda_animais_63.csv';
const COL_BRINCO_FILHO = 0;
const COL_BRINCO_MAE = 3;

// --- 2. VERIFICAÇÃO DO ARQUIVO ---
if (!file_exists($csvFilePath)) {
    die("ERRO: O arquivo CSV não foi encontrado no caminho: " . $csvFilePath);
}

// --- 3. PRÉ-CARREGAMENTO DE TODO O GADO (ID E BRINCO) ---
// Isso cria um mapa para consultas rápidas sem acessar o banco a cada linha.
$gadoMap = [];
try {
    $stmt = $pdo->query("SELECT id, brinco FROM gado");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Mapa: 'brinco' => id
        if (!empty(trim($row['brinco']))) {
            $gadoMap[trim($row['brinco'])] = $row['id'];
        }
    }
    echo "Pré-carregamento concluído: " . count($gadoMap) . " animais encontrados no banco de dados.\n\n";
} catch (PDOException $e) {
    die("ERRO ao pré-carregar a tabela gado: " . $e->getMessage());
}

// --- 4. LEITURA DO CSV PARA CRIAR O MAPA DE FILHO -> MÃE ---
$relacionamentos = [];
$linhaAtual = 1;
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    fgetcsv($handle, 1000, ";"); // Pula o cabeçalho

    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        $linhaAtual++;
        if (isset($data[COL_BRINCO_FILHO]) && isset($data[COL_BRINCO_MAE])) {
            $brincoFilho = trim($data[COL_BRINCO_FILHO]);
            $brincoMae = trim($data[COL_BRINCO_MAE]);

            // Adiciona ao mapa apenas se ambos os brincos existirem na linha
            if (!empty($brincoFilho) && !empty($brincoMae)) {
                $relacionamentos[$brincoFilho] = $brincoMae;
            }
        }
    }
    fclose($handle);
    echo "Mapeamento de relacionamentos a partir do CSV concluído. " . count($relacionamentos) . " relações encontradas.\n\n";
} else {
    die("ERRO: Não foi possível abrir o arquivo CSV.");
}

// --- 5. ATUALIZAÇÃO DO CAMPO id_mae NO BANCO DE DADOS ---
$updatesSucesso = 0;
$errosMaeNaoEncontrada = 0;
$errosFilhoNaoEncontrado = 0;

try {
    // Prepara a consulta UPDATE para ser reutilizada
    $stmtUpdate = $pdo->prepare("UPDATE gado SET id_mae = :id_mae WHERE id = :id_filho");

    // Itera sobre o mapa de relacionamentos
    foreach ($relacionamentos as $brincoFilho => $brincoMae) {
        // Busca os IDs no mapa pré-carregado
        $idFilho = $gadoMap[$brincoFilho] ?? null;
        $idMae = $gadoMap[$brincoMae] ?? null;

        if (!$idFilho) {
            echo "AVISO: Filho com brinco '{$brincoFilho}' não foi encontrado no banco. Pulando...\n";
            $errosFilhoNaoEncontrado++;
            continue;
        }

        if (!$idMae) {
            echo "AVISO: Mãe com brinco '{$brincoMae}' (do filho '{$brincoFilho}') não foi encontrada no banco. Pulando...\n";
            $errosMaeNaoEncontrada++;
            continue;
        }

        // Executa o update
        $stmtUpdate->execute([
            ':id_mae' => $idMae,
            ':id_filho' => $idFilho
        ]);

        if ($stmtUpdate->rowCount() > 0) {
            echo "OK: Filho '{$brincoFilho}' (ID: {$idFilho}) atualizado com a mãe '{$brincoMae}' (ID: {$idMae}).\n";
            $updatesSucesso++;
        } else {
            echo "INFO: Filho '{$brincoFilho}' já possuía a mãe correta ou não precisou de atualização.\n";
        }
    }

} catch (PDOException $e) {
    die("ERRO de banco de dados durante a atualização: " . $e->getMessage());
}

// --- 6. RELATÓRIO FINAL ---
echo "\n--- PROCESSO DE ATUALIZAÇÃO DE MÃES CONCLUÍDO ---\n";
echo "Total de animais atualizados com sucesso: " . $updatesSucesso . "\n";
echo "Filhos não encontrados no banco (pulados): " . $errosFilhoNaoEncontrado . "\n";
echo "Mães não encontradas no banco (puladas): " . $errosMaeNaoEncontrada . "\n";
echo "\nImportação finalizada!\n";
echo "</pre>";

?>