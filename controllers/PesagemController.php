<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pesagem.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$pesagem = new Pesagem($pdo);

switch ($action) {
    case 'create':
        $pesagem->id_gado = $_POST['id_gado'];
        $pesagem->peso = $_POST['peso'];
        $pesagem->data_pesagem = $_POST['data_pesagem'];
        $pesagem->observacoes = $_POST['observacoes'];
        
        if ($pesagem->create()) {
            $_SESSION['message'] = "Pesagem registrada com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao registrar pesagem.";
        }
        header("Location: ../views/pesagens/index.php");
        exit();

    case 'update':
        $pesagem->id = $_POST['id'];
        $pesagem->id_gado = $_POST['id_gado'];
        $pesagem->peso = $_POST['peso'];
        $pesagem->data_pesagem = $_POST['data_pesagem'];
        $pesagem->observacoes = $_POST['observacoes'];

        if ($pesagem->update()) {
            $_SESSION['message'] = "Pesagem atualizada com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao atualizar pesagem.";
        }
        header("Location: ../views/pesagens/index.php");
        exit();

    case 'delete':
        $pesagem->id = $_POST['id'];
        if ($pesagem->delete()) {
            $_SESSION['message'] = "Pesagem excluída com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao excluir pesagem.";
        }
        header("Location: ../views/pesagens/index.php");
        exit();
        
    default:
        header("Location: ../views/pesagens/index.php");
        exit();
}
?>