<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Parto.php';
require_once __DIR__ . '/../models/Gado.php';
require_once __DIR__ . '/../models/Touro.php'; 
require_once __DIR__ . '/../models/Inseminacao.php'; 

$parto = new Parto($pdo);
$gado = new Gado($pdo);
$touro = new Touro($pdo);
$inseminacao = new Inseminacao($pdo); 

$action = $_POST['action'] ?? $_GET['action'] ?? null;

error_log("PartoController: AÃ§Ã£o recebida: " . ($action ?? 'NULO - Nenhuma aÃ§Ã£o recebida'));

 
 
 switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $parto->id_vaca = $_POST['id_vaca'];
            $parto->id_touro = $_POST['id_touro'];
            $parto->data_parto = $_POST['data_parto'];
            $parto->sexo_cria = $_POST['sexo_cria'];
            $parto->observacoes = $_POST['observacoes'];
            $parto->ativo = 1;

            if ($parto->create()) {
                // AQUI ESTÃ A MUDANÃ‡A:
                // Chamamos a nova funÃ§Ã£o para atualizar o status para 'Vazia' e o grupo para 'Lactante'
                $gado->updateStatusAndGroup($parto->id_vaca, 'Vazia', 'Lactante');

                $_SESSION['message'] = 'Parto registrado com sucesso! O status da vaca foi atualizado para Vazia e o grupo para Lactante.';
                // Redireciona para a pÃ¡gina de visualizaÃ§Ã£o do parto recÃ©m-criado
                header('Location: ../views/partos/view.php?id=' . $parto->id);
                exit();
            } else {
                $_SESSION['message'] = 'Erro ao registrar o parto.';
                header('Location: ../views/partos/create.php');
                exit();
            }
        }
        break;
    
    // O restante do switch (case 'delete', etc.) continua igual...


    case 'update':
        error_log("PartoController: Processando aÃ§Ã£o 'update'.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $parto->id = $_POST['id'] ?? die('ID do parto nÃ£o especificado.');
            $parto->id_vaca = $_POST['id_vaca'] ?? die('ID da vaca nÃ£o especificado.');
            $parto->id_touro = $_POST['id_touro'] ?? null;
            $parto->sexo_cria = $_POST['sexo_cria'] ?? die('Sexo da cria nÃ£o especificado.');
            $parto->data_parto = $_POST['data_parto'] ?? date('Y-m-d');
            $parto->observacoes = $_POST['observacoes'] ?? null;

            if ($parto->update()) {
                $_SESSION['message'] = "Parto atualizado com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao atualizar parto.";
            }
            header('Location: ../partos/index.php');
            exit();
        }
        break;

    case 'delete':
        error_log("PartoController: Processando aÃ§Ã£o 'delete'.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $parto->id = $_POST['id'] ?? die('ID do parto nÃ£o especificado para exclusÃ£o.');
            if ($parto->delete()) {
                $_SESSION['message'] = "Parto excluÃ­do com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao excluir parto.";
            }
            header('Location: ../partos/index.php');
            exit();
        }
        break;
case 'get_last_insemination_touro':
        header('Content-Type: application/json');
        $id_vaca = $_GET['id_vaca'] ?? null;
        $response = ['success' => false, 'id_touro' => null];

        if ($id_vaca) {
            $query_last_insem = "SELECT i.id_touro FROM inseminacoes i
                                 JOIN gado g ON i.id_vaca = g.id
                                 WHERE i.id_vaca = :id_vaca 
                                   AND g.status = 'Prenha'
                                   AND i.ativo = 1 
                                   AND i.tipo IN ('IATF', 'Cio')
                                 ORDER BY i.data_inseminacao DESC LIMIT 1";
            
            $stmt_last_insem = $pdo->prepare($query_last_insem);
            $stmt_last_insem->bindParam(':id_vaca', $id_vaca, PDO::PARAM_INT);
            $stmt_last_insem->execute();
            $result = $stmt_last_insem->fetch(PDO::FETCH_ASSOC);

            if ($result && !empty($result['id_touro'])) {
                $response['success'] = true;
                $response['id_touro'] = $result['id_touro'];
            }
        }
        
        echo json_encode($response);
        exit();

    default:
        error_log("PartoController: AÃ§Ã£o padrÃ£o ou nÃ£o reconhecida: " . ($action ?? 'NULO'));
        break;
}