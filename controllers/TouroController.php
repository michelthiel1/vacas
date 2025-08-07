<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Touro.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$touro = new Touro($pdo);

switch ($action) {
    case 'create':
        $touro->nome = $_POST['nome'];
        $touro->raca = $_POST['raca'];
        $touro->observacoes = $_POST['observacoes'];
        $touro->ativo = isset($_POST['ativo']) ? 1 : 0;
        
        if ($touro->create()) {
            $_SESSION['message'] = "Touro cadastrado com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao cadastrar touro.";
        }
        header("Location: ../views/touros/index.php");
        exit();

    case 'update':
        $touro->id = $_POST['id'];
        $touro->nome = $_POST['nome'];
        $touro->raca = $_POST['raca'];
        $touro->observacoes = $_POST['observacoes'];
        $touro->ativo = isset($_POST['ativo']) ? 1 : 0;

        if ($touro->update()) {
            $_SESSION['message'] = "Touro atualizado com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao atualizar touro.";
        }
        header("Location: ../views/touros/index.php");
        exit();

    case 'delete':
        $touro->id = $_POST['id'];
        if ($touro->delete()) {
            $_SESSION['message'] = "Touro excluído com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao excluir touro.";
        }
        header("Location: ../views/touros/index.php");
        exit();
        
    default:
        header("Location: ../views/touros/index.php");
        exit();
}
?>