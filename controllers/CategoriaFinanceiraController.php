<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CategoriaFinanceira.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$role = $_SESSION['role'] ?? 'user';
$username = $_SESSION['username'] ?? '';

// Segurança: Apenas admins podem gerenciar categorias
if ($username !== 'michelthiel' && $role !== 'admin') {
    $_SESSION['message'] = "Você não tem permissão para gerenciar categorias.";
    header('Location: ../views/financeiro/index.php');
    exit();
}

$categoriaModel = new CategoriaFinanceira($pdo);

switch ($action) {
    case 'create':
        $nome = $_POST['nome'];
        $tipo = $_POST['tipo'];
        $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

        if ($categoriaModel->create($nome, $tipo, $parent_id)) {
            $_SESSION['message'] = "Categoria criada com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao criar categoria.";
        }
        header("Location: ../views/categorias_financeiras/index.php");
        exit();

    case 'update':
        $id = $_POST['id'];
        $nome = $_POST['nome'];
        $tipo = $_POST['tipo'];
        $parent_id = !empty($_POST['parent_id']) ? $_POST['parent_id'] : null;

        if ($categoriaModel->update($id, $nome, $tipo, $parent_id)) {
            $_SESSION['message'] = "Categoria atualizada com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao atualizar categoria.";
        }
        header("Location: ../views/categorias_financeiras/index.php");
        exit();

    case 'delete':
        $id = $_POST['id'];
        
        // Verificação para não deletar categoria com filhos
        if ($categoriaModel->hasChildren($id)) {
             $_SESSION['message'] = "Erro: Não é possível excluir uma categoria que possui sub-categorias.";
        } else {
            if ($categoriaModel->delete($id)) {
                $_SESSION['message'] = "Categoria excluída com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao excluir categoria. Verifique se ela não está sendo usada em algum lançamento.";
            }
        }
        header("Location: ../views/categorias_financeiras/index.php");
        exit();
        
    default:
        header("Location: ../views/categorias_financeiras/index.php");
        exit();
}