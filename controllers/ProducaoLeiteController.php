<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProducaoLeite.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$producao = new ProducaoLeite($pdo);

switch ($action) {
    case 'create':
        $producao->id_gado = $_POST['id_gado'];
        $producao->data_producao = $_POST['data_producao'];
        $producao->ordenha_1 = $_POST['ordenha_1'] ?? 0;
        $producao->ordenha_2 = $_POST['ordenha_2'] ?? 0;
        $producao->ordenha_3 = $_POST['ordenha_3'] ?? 0;
        $producao->observacoes = $_POST['observacoes'];
        
        if ($producao->create()) {
            $_SESSION['message'] = "Produção registrada com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao registrar produção.";
        }
        header("Location: ../views/producao_leite/index.php");
        exit();

    case 'update':
        $producao->id = $_POST['id'];
        $producao->id_gado = $_POST['id_gado'];
        $producao->data_producao = $_POST['data_producao'];
        $producao->ordenha_1 = $_POST['ordenha_1'] ?? 0;
        $producao->ordenha_2 = $_POST['ordenha_2'] ?? 0;
        $producao->ordenha_3 = $_POST['ordenha_3'] ?? 0;
        $producao->observacoes = $_POST['observacoes'];

        if ($producao->update()) {
            $_SESSION['message'] = "Produção atualizada com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao atualizar produção.";
        }
        header("Location: ../views/producao_leite/index.php");
        exit();

    case 'delete':
        $producao->id = $_POST['id'];
        if ($producao->delete()) {
            $_SESSION['message'] = "Registro de produção excluído com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao excluir registro.";
        }
        header("Location: ../views/producao_leite/index.php");
        exit();
        
    default:
        header("Location: ../views/producao_leite/index.php");
        exit();
}
?>