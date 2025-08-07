<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ContatoFinanceiro.php';

// Segurança: Apenas usuários autorizados podem gerenciar contatos
if (($_SESSION['username'] ?? '') !== 'michelthiel' && ($_SESSION['role'] ?? '') !== 'admin') {
    $_SESSION['message'] = "Você não tem permissão para acessar esta área.";
    header('Location: ../../index.php');
    exit();
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'index';
$contatoModel = new ContatoFinanceiro($pdo);

switch ($action) {
    case 'create':
        $contatoModel->nome = $_POST['nome'];
        $contatoModel->tipo = $_POST['tipo'];
        $contatoModel->telefone = $_POST['telefone'];
        $contatoModel->cpf_cnpj = $_POST['cpf_cnpj'];
        
        if ($contatoModel->create()) {
            $_SESSION['message'] = "Contato cadastrado com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao cadastrar contato.";
        }
        header("Location: ../views/contatos/index.php");
        exit();

    case 'update':
        $contatoModel->id = $_POST['id'];
        $contatoModel->nome = $_POST['nome'];
        $contatoModel->tipo = $_POST['tipo'];
        $contatoModel->telefone = $_POST['telefone'];
        $contatoModel->cpf_cnpj = $_POST['cpf_cnpj'];

        if ($contatoModel->update()) {
            $_SESSION['message'] = "Contato atualizado com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao atualizar contato.";
        }
        header("Location: ../views/contatos/index.php");
        exit();

    case 'delete':
        $contatoModel->id = $_POST['id'];
        if ($contatoModel->delete()) {
            $_SESSION['message'] = "Contato excluído com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao excluir contato. Verifique se ele não está sendo usado em algum lançamento.";
        }
        header("Location: ../views/contatos/index.php");
        exit();
        
    default:
        // Ação padrão é redirecionar para a lista
        header("Location: ../views/contatos/index.php");
        exit();
}
