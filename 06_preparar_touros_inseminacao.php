<?php
// Script de preparação: Insere touros faltantes a partir do CSV de Inseminações.

require_once __DIR__ . '/config/database.php';

echo "<pre>"; // Formata a saída

// --- 1. CONFIGURAÇÃO ---
define('CSV_FILE_PATH', __DIR__ . '/_relatorioInseminacoesAnimais2025-06-13-13-59.csv');
// Analisando o padrão de nome do arquivo, a coluna do touro provavelmente é a de índice 3 ou 4.
// Vamos assumir o índice 3, correspondente a "Touro". Se der errado, ajustamos.
define('TOURO_COLUMN_INDEX', 2); 

// --- 2. VERIFICAÇÃO DO ARQUIVO ---
if (!file_exists(CSV_FILE_PATH)) {
    die("ERRO: O arquivo CSV de inseminações não foi encontrado em: " . CSV_FILE_PATH);
}

// --- 3. LEITURA DO CSV E COLETA DOS NOMES ---
$nomesDeTouros = [];
$primeiraLinha = true; // Para pular o cabeçalho

if (($handle = fopen(CSV_FILE_PATH, "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        if ($primeiraLinha) {
            $primeiraLinha = false;
            continue;
        }

        if (isset($data[TOURO_COLUMN_INDEX]) && !empty(trim($data[TOURO_COLUMN_INDEX]))) {
            $nomesDeTouros[] = trim($data[TOURO_COLUMN_INDEX]);
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
echo "Encontrados " . count($tourosUnicos) . " touros únicos no arquivo de inseminações.\nIniciando verificação no banco de dados...\n\n";

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
                ':observacoes' => 'Importado via CSV de Inseminações',
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