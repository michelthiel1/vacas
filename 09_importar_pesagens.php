<?php
// 09_importar_pesagens.php
// Script para importar pesagens de um arquivo CSV para o banco de dados. (VERSÃO CORRIGIDA)

// ATENÇÃO: Faça um backup completo do seu banco de dados antes de executar.

header('Content-Type: text/plain; charset=utf-8');
echo "<pre>"; // Formata a saída para melhor leitura no navegador

// 1. INICIALIZAÇÃO E CARREGAMENTO DOS MODELOS
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Gado.php';
require_once __DIR__ . '/models/Pesagem.php';

$pesagemModel = new Pesagem($pdo);

$csvFilePath = __DIR__ . '/pesagens.csv';

if (!file_exists($csvFilePath)) {
    die("ERRO FATAL: Arquivo 'pesagens.csv' não encontrado no diretório raiz do projeto.");
}

echo "--- INICIANDO IMPORTAÇÃO DE PESAGENS (V2 - CORRIGIDA) ---\n\n";

$sucesso = 0;
$erros = 0;
$linhaAtual = 0;

// Abre o arquivo CSV para leitura
$handle = fopen($csvFilePath, "r");

if ($handle === FALSE) {
    die("ERRO: Não foi possível abrir o arquivo CSV.");
}

// Pula o cabeçalho do CSV
fgetcsv($handle, 1000, ";");

// 2. LER O ARQUIVO CSV E PROCESSAR CADA LINHA
while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
    $linhaAtual++;

    // Mapeamento das colunas do CSV
    $brinco = trim($data[0]);
    $data_pesagem_csv = trim($data[1]);
    $peso_csv = trim($data[2]);

    echo "Processando linha {$linhaAtual}: Brinco [{$brinco}], Data [{$data_pesagem_csv}], Peso [{$peso_csv}]\n";

    // 3. LÓGICA DO DE/PARA E VALIDAÇÃO
    if (empty($brinco) || empty($data_pesagem_csv) || empty($peso_csv)) {
        echo "  -> ERRO: Dados incompletos na linha. Pulando.\n\n";
        $erros++;
        continue;
    }

    // DE/PARA: Encontrar o ID do gado a partir do brinco
    $stmtGado = $pdo->prepare("SELECT id FROM gado WHERE brinco = :brinco AND ativo = 1");
    $stmtGado->execute([':brinco' => $brinco]);
    $gado = $stmtGado->fetch(PDO::FETCH_ASSOC);

    if (!$gado) {
        echo "  -> ERRO: Nenhum animal ativo encontrado com o brinco '{$brinco}'. Pulando.\n\n";
        $erros++;
        continue;
    }
    $id_gado = $gado['id'];

    // Conversão da data para o formato do banco de dados (YYYY-MM-DD)
    try {
        $data_pesagem_obj = DateTime::createFromFormat('d/m/Y', $data_pesagem_csv);
        if (!$data_pesagem_obj) {
             throw new Exception("Formato de data inválido.");
        }
        $data_pesagem_db = $data_pesagem_obj->format('Y-m-d');
    } catch (Exception $e) {
        echo "  -> ERRO: A data '{$data_pesagem_csv}' não está no formato DD/MM/YYYY. " . $e->getMessage() . " Pulando.\n\n";
        $erros++;
        continue;
    }
    
    // Tratamento do peso (substitui vírgula por ponto)
    $peso_db = str_replace(',', '.', $peso_csv);
    if (!is_numeric($peso_db)) {
        echo "  -> ERRO: O valor do peso '{$peso_csv}' não é um número válido. Pulando.\n\n";
        $erros++;
        continue;
    }


    // 4. INSERÇÃO NO BANCO DE DADOS
    try {
        // ### CORREÇÃO APLICADA AQUI ###
        // A propriedade correta no modelo é 'id_gado', e não 'id_vaca'.
        $pesagemModel->id_gado = $id_gado;
        
        $pesagemModel->data_pesagem = $data_pesagem_db;
        $pesagemModel->peso = $peso_db;
        $pesagemModel->observacoes = 'Importado via CSV'; // Adicionando observação padrão

        if ($pesagemModel->create()) {
            echo "  -> SUCESSO: Pesagem de {$peso_db} kg para a vaca com brinco {$brinco} importada com sucesso.\n\n";
            $sucesso++;
        } else {
            throw new Exception("O método create() do modelo retornou falso.");
        }
    } catch (Exception $e) {
        echo "  -> ERRO: Falha ao inserir no banco de dados para o brinco {$brinco}. Detalhes: " . $e->getMessage() . "\n\n";
        $erros++;
    }
}

fclose($handle);

echo "--- RELATÓRIO FINAL ---\n";
echo "Pesagens importadas com sucesso: {$sucesso}\n";
echo "Linhas com erro ou ignoradas: {$erros}\n";
echo "--- FIM DO SCRIPT ---\n";

echo "</pre>";
?>