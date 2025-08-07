<?php
// Script de preparação: Insere touros faltantes a partir do CSV de partos.

require_once __DIR__ . '/config/database.php';

echo "<pre>"; // Formata a saída para melhor leitura

// --- 1. CONFIGURAÇÃO ---
$csvFilePath = __DIR__ . '/partos.csv';
$touroColumnIndex = 1; // A coluna "touro" é a quarta, então seu índice é 3.

// --- 2. VERIFICAÇÃO DO ARQUIVO ---
if (!file_exists($csvFilePath)) {
    die("ERRO: O arquivo 'partos.csv' não foi encontrado.");
}

// --- 3. LEITURA DO CSV E COLETA DOS NOMES ---
$nomesDeTouros = [];
$primeiraLinha = true; // Para pular o cabeçalho

if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        if ($primeiraLinha) {
            $primeiraLinha = false;
            continue;
        }

        if (isset($data[$touroColumnIndex]) && !empty(trim($data[$touroColumnIndex]))) {
            $nomesDeTouros[] = trim($data[$touroColumnIndex]);
        }
    }
    fclose($handle);
} else {
    die("ERRO: Não foi possível abrir o arquivo CSV.");
}

// --- 4. FILTRAGEM DE NOMES ÚNICOS ---
$tourosUnicos = array_unique($nomesDeTouros);
if (empty($tourosUnicos)) {
    echo "Nenhum touro encontrado no arquivo CSV para processar.\n";
    exit;
}
echo "Encontrados " . count($tourosUnicos) . " touros únicos no arquivo 'partos.csv'.\nIniciando verificação no banco de dados...\n\n";

// --- 5. VERIFICAÇÃO E INSERÇÃO NO BANCO DE DADOS ---
$tourosInseridos = 0;
$tourosJaExistiam = 0;

try {
    $stmtSelect = $pdo->prepare("SELECT id FROM touros WHERE nome = :nome");
    $stmtInsert = $pdo->prepare(
        "INSERT INTO touros (nome, observacoes, ativo) VALUES (:nome, :observacoes, :ativo)"
    );

    foreach ($tourosUnicos as $nomeTouro) {
        $stmtSelect->execute([':nome' => $nomeTouro]);
        
        if ($stmtSelect->rowCount() > 0) {
            echo "Touro '{$nomeTouro}' já existe no banco. Ignorando...\n";
            $tourosJaExistiam++;
        } else {
            $stmtInsert->execute([
                ':nome' => $nomeTouro,
                ':observacoes' => 'Importado via CSV de Partos',
                ':ativo' => 1
            ]);
            echo "NOVO: Touro '{$nomeTouro}' inserido com sucesso!\n";
            $tourosInseridos++;
        }
    }

} catch (PDOException $e) {
    die("ERRO de banco de dados: " . $e->getMessage());
}

// --- 6. RELATÓRIO FINAL ---
echo "\n--- PROCESSO DE PREPARAÇÃO DE TOUROS CONCLUÍDO ---\n";
echo "Total de touros novos inseridos: " . $tourosInseridos . "\n";
echo "Total de touros que já existiam: " . $tourosJaExistiam . "\n";
echo "</pre>";

?>