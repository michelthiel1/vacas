<?php
// Fase 1 do Plano de Importação: Mapear e popular a tabela 'touros' a partir do CSV.

// Inclui a configuração do banco de dados para obter a conexão $pdo.
require_once __DIR__ . '/config/database.php';

echo "<pre>"; // Usar <pre> para formatar a saída no navegador e facilitar a leitura.

// --- 1. CONFIGURAÇÃO ---
$csvFilePath = __DIR__ . '/relatorio_fazenda_animais_63.csv'; // Caminho para o seu arquivo CSV.
$paiColumnIndex = 1; // Índice da coluna "pai" no CSV (começando em 0).

// --- 2. VERIFICAÇÃO DO ARQUIVO ---
if (!file_exists($csvFilePath)) {
    die("ERRO: O arquivo CSV não foi encontrado no caminho: " . $csvFilePath);
}

// --- 3. LEITURA DO CSV E COLETA DOS NOMES DOS PAIS ---
$todosOsPais = [];
$primeiraLinha = true; // Flag para ignorar o cabeçalho

// Abre o arquivo para leitura
if (($handle = fopen($csvFilePath, "r")) !== FALSE) {
    // Percorre cada linha do CSV
    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
        // Pula o cabeçalho (primeira linha)
        if ($primeiraLinha) {
            $primeiraLinha = false;
            continue;
        }

        // Pega o nome do pai, se existir e não estiver vazio
        if (isset($data[$paiColumnIndex]) && !empty(trim($data[$paiColumnIndex]))) {
            $todosOsPais[] = trim($data[$paiColumnIndex]);
        }
    }
    fclose($handle);
} else {
    die("ERRO: Não foi possível abrir o arquivo CSV.");
}

// --- 4. FILTRAGEM DE NOMES ÚNICOS ---
$tourosUnicos = array_unique($todosOsPais);
echo "Encontrados " . count($tourosUnicos) . " touros únicos no arquivo CSV.\n\n";

// --- 5. VERIFICAÇÃO E INSERÇÃO NO BANCO DE DADOS ---
$tourosInseridos = 0;
$tourosJaExistiam = 0;

try {
    // Prepara as consultas SQL para reutilização dentro do loop
    $stmtSelect = $pdo->prepare("SELECT id FROM touros WHERE nome = :nome");
    $stmtInsert = $pdo->prepare(
        "INSERT INTO touros (nome, observacoes, ativo) VALUES (:nome, :observacoes, :ativo)"
    );

    foreach ($tourosUnicos as $nomeTouro) {
        // Verifica se o touro já existe
        $stmtSelect->execute([':nome' => $nomeTouro]);
        
        if ($stmtSelect->rowCount() > 0) {
            // Se encontrou, apenas informa e incrementa o contador
            echo "Touro '{$nomeTouro}' já existe no banco de dados. Ignorando...\n";
            $tourosJaExistiam++;
        } else {
            // Se não encontrou, insere o novo touro
            $stmtInsert->execute([
                ':nome' => $nomeTouro,
                ':observacoes' => 'Importado via CSV',
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
echo "\n--- PROCESSO CONCLUÍDO ---\n";
echo "Total de touros novos inseridos: " . $tourosInseridos . "\n";
echo "Total de touros que já existiam: " . $tourosJaExistiam . "\n";
echo "</pre>";

?>