<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Manejo.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$manejo = new Manejo($pdo);

switch ($action) {
    case 'create':
    case 'update':
        if ($action === 'update') {
            $manejo->id = $_POST['id'];
        }
        $manejo->nome = $_POST['nome'];
        $manejo->tipo = $_POST['tipo'];
        $manejo->ativo = isset($_POST['ativo']) ? 1 : 0;
        
        // ***** CORREÇÃO AQUI: Capturando ambos os campos de recorrência *****
        $manejo->recorrencia_meses = $_POST['recorrencia_meses'] ?? null;
        $manejo->recorrencia_dias = $_POST['recorrencia_dias'] ?? null;
        
        // Atribuição para campos de eventos personalizados
        $manejo->evento_dias_1 = $_POST['evento_dias_1'] ?? null;
        $manejo->evento_titulo_1 = $_POST['evento_titulo_1'] ?? null;
        $manejo->evento_dias_2 = $_POST['evento_dias_2'] ?? null;
        $manejo->evento_titulo_2 = $_POST['evento_titulo_2'] ?? null;
        $manejo->evento_dias_3 = $_POST['evento_dias_3'] ?? null;
        $manejo->evento_titulo_3 = $_POST['evento_titulo_3'] ?? null;
        $manejo->evento_dias_4 = $_POST['evento_dias_4'] ?? null;
        $manejo->evento_titulo_4 = $_POST['evento_titulo_4'] ?? null;
        $manejo->evento_dias_5 = $_POST['evento_dias_5'] ?? null;
        $manejo->evento_titulo_5 = $_POST['evento_titulo_5'] ?? null;
        $manejo->evento_dias_6 = $_POST['evento_dias_6'] ?? null;
        $manejo->evento_titulo_6 = $_POST['evento_titulo_6'] ?? null;
        
        $result = ($action === 'update') ? $manejo->update() : $manejo->create();

        if ($result) {
            $_SESSION['message'] = "Tipo de manejo salvo com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao salvar tipo de manejo.";
        }
        header("Location: ../views/manejos/index.php");
        exit();

    case 'get_all':
        header('Content-Type: application/json');
        $manejos = $manejo->readAll();
        echo json_encode(['success' => true, 'manejos' => $manejos]);
        exit();

    case 'get_by_type':
        header('Content-Type: application/json');
        $tipo = $_GET['tipo'] ?? '';
        if ($tipo) {
            $manejos = $manejo->readByType($tipo);
            echo json_encode(['success' => true, 'manejos' => $manejos]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tipo não especificado.']);
        }
        exit();

    default:
        header("Location: ../views/manejos/index.php");
        exit();
}
?>