<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Dieta.php';
require_once __DIR__ . '/../models/Estoque.php';

// Bloco de Segurança: Apenas administradores podem acessar esta funcionalidade.
if (($_SESSION['username'] ?? '') !== 'michelthiel' && ($_SESSION['role'] ?? '') !== 'admin') {
    $_SESSION['message'] = "Você não tem permissão para acessar esta área.";
    header('Location: ../../index.php');
    exit();
}

$action = $_POST['action'] ?? null;

switch ($action) {
    case 'update_dietas':
        $dietasData = $_POST['dieta'] ?? [];
        if (!empty($dietasData)) {
            $dietaModel = new Dieta($pdo);
            if ($dietaModel->updateBatch($dietasData)) {
                $_SESSION['message'] = "Dietas atualizadas com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao atualizar uma ou mais dietas.";
            }
        }
        header("Location: ../views/dietas/gerenciar.php");
        exit();

    case 'update_estoque':
        $estoqueData = $_POST['estoque'] ?? [];
        if (!empty($estoqueData)) {
            $estoqueModel = new Estoque($pdo);
            if ($estoqueModel->updateEstoqueBatch($estoqueData)) {
                $_SESSION['message'] = "Estoque atualizado com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao atualizar o estoque.";
            }
        }
        header("Location: ../views/dietas/gerenciar.php");
        exit();

    default:
        // Redireciona para a página de gerenciamento se nenhuma ação válida for fornecida.
        header("Location: ../views/dietas/gerenciar.php");
        exit();
}