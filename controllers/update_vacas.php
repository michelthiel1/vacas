<?php
// controllers/update_vacas.php

// Configurações de erro para depuração
error_reporting(E_ALL);
ini_set('display_errors', 0); // Desativado para não interferir na resposta JSON
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_error_log_update_vacas.log');

// Limpa o log antigo para uma análise limpa a cada execução
if (file_exists(__DIR__ . '/php_error_log_update_vacas.log')) {
    unlink(__DIR__ . '/php_error_log_update_vacas.log');
}

// Inicia o buffer de saída
ob_start();

// Inclui a classe de configuração do banco de dados
require_once __DIR__ . '/../config/database.php';

// Define o cabeçalho da resposta como JSON com UTF-8 para evitar problemas de acentuação
header('Content-Type: application/json; charset=utf-8');

$response = ['success' => false, 'message' => 'Ponto de partida: O script foi iniciado mas não processou a requisição.'];

// Verifica se a requisição é do tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lote_post = $_POST['lote'] ?? '';
    $vacas_post = $_POST['vacas'] ?? '';

    // Validação dos dados
    if (empty($lote_post) || !is_numeric($vacas_post) || (int)$vacas_post < 0) {
        $response = ['success' => false, 'message' => 'Dados inválidos. Lote e número de vacas são obrigatórios.'];
    } else {
        $vacas_int = (int)$vacas_post;

        try {
            // Instancia a classe do banco de dados e obtém a conexão
            $database = new Database();
            $conn = $database->getConnection();

            // Adiciona um log para confirmar que a conexão foi bem sucedida
            if ($conn) {
                error_log("Conexão com o banco de dados estabelecida com sucesso.");
            } else {
                error_log("Falha ao obter objeto de conexão do banco de dados.");
                throw new Exception("Não foi possível conectar ao banco de dados.");
            }

            // Query SQL com os nomes corretos das colunas: 'Vacas' e 'Lote'
            $query = "UPDATE dieta SET Vacas = :vacas WHERE Lote = :lote AND ativo = 1";
            
            $stmt = $conn->prepare($query);

            $stmt->bindParam(':vacas', $vacas_int, PDO::PARAM_INT);
            $stmt->bindParam(':lote', $lote_post, PDO::PARAM_STR);
            
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $response = ['success' => true, 'message' => "Lote '{$lote_post}' atualizado para {$vacas_int} vacas."];
            } else {
                $response = ['success' => true, 'message' => "Nenhuma alteração necessária para o lote '{$lote_post}'. O valor pode já estar correto ou o lote pode estar inativo."];
            }

        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Erro de banco de dados. Verifique o log do servidor.'];
            error_log("update_vacas.php: ERRO PDO: " . $e->getMessage());
        } catch (Exception $e) {
            $response = ['success' => false, 'message' => 'Erro inesperado no servidor. Verifique o log.'];
            error_log("update_vacas.php: ERRO GERAL: " . $e->getMessage());
        }
    }
} else {
    $response = ['success' => false, 'message' => 'Método de requisição inválido. Use POST.'];
}

// Limpa o buffer e envia a resposta JSON final
ob_end_clean();
echo json_encode($response);
exit();
?>