<?php
// Define o cabeçalho da resposta como JSON com o charset UTF-8 correto
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); // Permite que a API seja chamada de qualquer origem

// Inclui os arquivos necessários para conectar ao banco
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Gado.php';

// Prepara a resposta padrão
$response = [
    'success' => false,
    'message' => 'Nenhum animal ativo encontrado.',
    'cow_count' => 0,
    'data' => [] // O retorno principal será um array de animais
];

try {
    // A consulta SQL é feita diretamente aqui para simplicidade e performance,
    // já que não precisamos de toda a lógica do modelo Gado.
    $query = "SELECT brinco, leite_descarte, cor_bastao FROM gado WHERE ativo = 1 ORDER BY brinco ASC";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    
    $animais = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($animais) {
        // Se animais foram encontrados, preenche a resposta com sucesso.
        $response['success'] = true;
        $response['message'] = count($animais) . ' animais ativos encontrados.';
        $response['cow_count'] = count($animais);
        $response['data'] = $animais;
    }

} catch (PDOException $e) {
    // Em caso de erro no banco de dados, prepara uma resposta de erro.
    $response['message'] = 'Erro de banco de dados: ' . $e->getMessage();
    http_response_code(500); // Define o código de status HTTP para erro de servidor
}

// Imprime a resposta final em formato JSON, garantindo a codificação correta dos caracteres.
// Usamos JSON_UNESCAPED_UNICODE para que o "Não" saia com o acento correto.
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>