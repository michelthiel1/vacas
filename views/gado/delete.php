<?php
session_start(); // Inicia a sessão para usar $_SESSION['message']
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';

// Verifica se o ID foi enviado via POST e se o usuário está logado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_SESSION['user_id'])) {
    $gado = new Gado($pdo);
    $id = htmlspecialchars(strip_tags($_POST['id']));

    if ($gado->delete($id)) {
        $_SESSION['message'] = '<div class="alert alert-success">Animal excluído com sucesso!</div>';
    } else {
        $_SESSION['message'] = '<div class="alert alert-error">Erro ao excluir animal.</div>';
    }
} else {
    $_SESSION['message'] = '<div class="alert alert-error">Requisição inválida ou ID não fornecido.</div>';
}

header('Location: index.php');
exit();
?>