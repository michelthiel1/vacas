<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Estoque.php';
require_once __DIR__ . '/../models/Dieta.php';

$action = $_POST['action'] ?? $_GET['action'] ?? 'list';

$estoqueModel = new Estoque($pdo);

switch ($action) {
   case 'store':
        $estoqueModel->Produto = $_POST['produto'];
        $estoqueModel->unidade_compra = $_POST['unidade_compra'];
        $estoqueModel->unidade_consumo = $_POST['unidade_consumo'];
        $estoqueModel->fator_conversao = $_POST['fator_conversao'];
        $estoqueModel->valor_compra = $_POST['valor_compra']; // Campo novo
        $estoqueModel->Quantidade = $_POST['quantidade'];
        $estoqueModel->id_categoria_financeira = $_POST['id_categoria_financeira'] ?: null;
        $estoqueModel->ativo = 1;
        
        if ($estoqueModel->create()) {
            $_SESSION['message'] = "Produto cadastrado com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao cadastrar produto.";
        }
        header("Location: ../views/estoque/list.php");
        exit();

  case 'update_produto':
        $id_produto = $_POST['id_produto'];
        $estoqueModel->Produto = $_POST['produto'];
        $estoqueModel->unidade_compra = $_POST['unidade_compra'];
        $estoqueModel->unidade_consumo = $_POST['unidade_consumo'];
        $estoqueModel->fator_conversao = $_POST['fator_conversao'];
        $estoqueModel->valor_compra = $_POST['valor_compra']; // Campo novo
        $estoqueModel->Valor = $_POST['valor_consumo_hidden']; // Envia o valor de consumo atualizado
        $estoqueModel->id_categoria_financeira = $_POST['id_categoria_financeira'] ?: null;
        
        if ($estoqueModel->update($id_produto)) {
            $_SESSION['message'] = "Produto atualizado com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao atualizar o produto.";
        }
        header("Location: ../views/estoque/list.php");
        exit();

    case 'delete':
        $id_produto = $_POST['id_produto'];
        if ($estoqueModel->delete($id_produto)) {
            $_SESSION['message'] = "Produto excluído com sucesso!";
        } else {
            $_SESSION['message'] = "Erro ao excluir o produto. Verifique se ele não está em uso.";
        }
        header("Location: ../views/estoque/list.php");
        exit();
        
    case 'update_vacas_count':
        $dietaModel = new Dieta($pdo);
        $lote = $_POST['lote'] ?? null;
        $vacas = $_POST['vacas'] ?? null;
    
        if ($lote && is_numeric($vacas)) {
            $dietaModel->updateVacasPorLote($lote, (int)$vacas);
        }
    
        $redirect_url = "../views/estoque/index.php?lote=" . urlencode($lote) . "&vacas=" . urlencode($vacas);
        header("Location: " . $redirect_url);
        exit();

    default:
        header("Location: ../views/estoque/list.php");
        exit();
}
