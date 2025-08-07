<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/EnvioLeite.php';

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

$envioLeite = new EnvioLeite($pdo);

switch ($action) {
    case 'create':
        // Ação de salvar um novo envio
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $envioLeite->data_envio = $_POST['data_envio'];
            $envioLeite->litros_enviados = $_POST['litros_enviados'];
            $envioLeite->numero_vacas = $_POST['numero_vacas'];
            $envioLeite->observacoes = $_POST['observacoes'];
			 $envioLeite->leite_bezerros = $_POST['leite_bezerros']; // <-- Adicionar esta linha
          

            if ($envioLeite->create()) {
                $_SESSION['message'] = "Envio de leite registrado com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao registrar o envio.";
            }
            header("Location: ../views/envio_leite/index.php");
            exit();
        }
        break;

    case 'update':
        // Ação de atualizar um envio existente
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $envioLeite->id = $_POST['id'];
            $envioLeite->data_envio = $_POST['data_envio'];
            $envioLeite->litros_enviados = $_POST['litros_enviados'];
            $envioLeite->numero_vacas = $_POST['numero_vacas'];
            $envioLeite->observacoes = $_POST['observacoes'];
			 $envioLeite->leite_bezerros = $_POST['leite_bezerros']; // <-- Adicionar esta linha
         

            if ($envioLeite->update()) {
                $_SESSION['message'] = "Registro atualizado com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao atualizar o registro.";
            }
            header("Location: ../views/envio_leite/index.php");
            exit();
        }
        break;

    case 'delete':
        // Ação de deletar um envio
        if ($id) {
            $envioLeite->id = $id;
            if ($envioLeite->delete()) {
                $_SESSION['message'] = "Registro excluído com sucesso!";
            } else {
                $_SESSION['error'] = "Erro ao excluir o registro.";
            }
        }
        header("Location: ../views/envio_leite/index.php");
        exit();

    case 'index':
    default:
        // Ação padrão: listar todos os envios
        $stmt = $envioLeite->read();
        include __DIR__ . '/../views/envio_leite/index.php';
        break;
}
?>