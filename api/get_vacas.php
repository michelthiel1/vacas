<?php
// Define o cabeçalho da resposta como JSON com o charset UTF-8 correto
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *'); 

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Gado.php';

$response = [
    'success' => false,
    'message' => 'Brinco não fornecido.',
    'data' => null
];

$brinco = $_GET['brinco'] ?? null;

if ($brinco) {
    try {
        $gado = new Gado($pdo);

        $query = "SELECT leite_descarte, cor_bastao FROM gado WHERE brinco = :brinco AND ativo = 1 LIMIT 1";
        $stmt = $pdo->prepare($query);
        
        $stmt->execute([':brinco' => $brinco]);
        $animal = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($animal) {
            $response['success'] = true;
            $response['message'] = 'Animal encontrado com sucesso.';
            $response['data'] = [
                'brinco' => $brinco,
                // Retorna o valor diretamente do banco de dados ("Sim" ou "Não")
                'leite_descarte' => $animal['leite_descarte'],
                'cor_bastao' => $animal['cor_bastao'] ?: 'Nenhuma'
            ];
        } else {
            $response['message'] = 'Nenhum animal ativo encontrado com o brinco fornecido.';
        }

    } catch (PDOException $e) {
        $response['message'] = 'Erro de banco de dados: ' . $e->getMessage();
        http_response_code(500);
    }
} else {
    http_response_code(400);
}

// Imprime a resposta final, garantindo a codificação correta dos caracteres especiais
echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>