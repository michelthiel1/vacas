<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/LancamentoFinanceiro.php';
require_once __DIR__ . '/../models/ParcelaFinanceira.php';
require_once __DIR__ . '/../models/Estoque.php';
require_once __DIR__ . '/../models/Touro.php';

$action = $_POST['action'] ?? $_GET['action'] ?? null;

if (($_SESSION['username'] ?? '') !== 'michelthiel' && ($_SESSION['role'] ?? '') !== 'admin') {
    $_SESSION['message'] = "Você não tem permissão para acessar o módulo financeiro.";
    header('Location: ../../index.php');
    exit();
}

$lancamentoModel = new LancamentoFinanceiro($pdo);
$parcelaModel = new ParcelaFinanceira($pdo);
$estoqueModel = new Estoque($pdo);
$touroModel = new Touro($pdo);

function getIdProdutoSemen($pdo) {
    $stmt = $pdo->prepare("SELECT Id FROM estoque WHERE Produto = 'Sêmen' LIMIT 1");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['Id'] : null;
}

switch ($action) {
    case 'create':
        $lancamentoModel->descricao = $_POST['descricao'];
        $lancamentoModel->valor_total = $_POST['valor_total'];
        $lancamentoModel->observacoes = $_POST['observacoes'] ?? '';
        $lancamentoModel->tipo = ($_POST['tipo_lancamento'] === 'receita') ? 'RECEBER' : 'PAGAR';
        $lancamentoModel->id_contato = !empty($_POST['id_contato']) ? $_POST['id_contato'] : null;
        $lancamentoModel->id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null;
        
        $num_parcelas = (int)($_POST['numero_parcelas'] ?? 1);
        $data_vencimento_inicial = $_POST['data_vencimento_inicial'];
        $itens_compra = $_POST['itens'] ?? [];
        $forma_pagamento = $_POST['forma_pagamento'] ?? 'Boleto';

        if ($lancamentoModel->create($num_parcelas, $data_vencimento_inicial, $forma_pagamento, $itens_compra)) {
            $_SESSION['message'] = "Lançamento criado com sucesso!";
            
            if ($_POST['tipo_lancamento'] === 'despesa_estoque' && !empty($itens_compra)) {
                $id_produto_semen = getIdProdutoSemen($pdo);
                foreach ($itens_compra as $item) {
                    if ($id_produto_semen && $item['produto_id'] == $id_produto_semen && isset($item['touro_id'])) {
                        $touroModel->ajustarDosesEstoque($item['touro_id'], (int)$item['quantidade']);
                    } else {
                        $produto_info = $estoqueModel->readOneProduct($item['produto_id']);
                        if ($produto_info) {
                            $qtd_ajuste = (float)str_replace(',', '.', $item['quantidade']) * (float)($produto_info['fator_conversao'] ?? 1.0);
                            $estoqueModel->ajustarQuantidade($item['produto_id'], $qtd_ajuste);
                        }
                    }
                }
                $_SESSION['message'] .= " Estoque atualizado!";
            }
        } else {
            $_SESSION['message'] = "Erro ao criar lançamento.";
        }
        header("Location: ../views/financeiro/index.php");
        exit();

    case 'update':
        $id_lancamento = $_POST['id'];
        
        $lancamento_antigo = new LancamentoFinanceiro($pdo);
        $lancamento_antigo->id = $id_lancamento;
        $itens_antigos = $lancamento_antigo->getItens();
        $id_produto_semen = getIdProdutoSemen($pdo);

        if (!empty($itens_antigos)) {
            foreach ($itens_antigos as $item) {
                 if ($id_produto_semen && $item['id_produto_estoque'] == $id_produto_semen && isset($item['touro_id'])) {
                } else {
                    $produto_info = $estoqueModel->readOneProduct($item['id_produto_estoque']);
                    if ($produto_info) {
                       $qtd_reverter = (float)$item['quantidade'] * (float)($produto_info['fator_conversao'] ?? 1.0);
                       $estoqueModel->ajustarQuantidade($item['id_produto_estoque'], -$qtd_reverter);
                    }
                }
            }
        }

        $lancamentoModel->id = $id_lancamento;
        $lancamentoModel->descricao = $_POST['descricao'];
        $lancamentoModel->valor_total = $_POST['valor_total'];
        $lancamentoModel->observacoes = $_POST['observacoes'] ?? '';
        $lancamentoModel->tipo = ($_POST['tipo_lancamento'] === 'receita') ? 'RECEBER' : 'PAGAR';
        $lancamentoModel->id_contato = !empty($_POST['id_contato']) ? $_POST['id_contato'] : null;
        $lancamentoModel->id_categoria = !empty($_POST['id_categoria']) ? $_POST['id_categoria'] : null;

        $num_parcelas_novas = (int)($_POST['numero_parcelas'] ?? 1);
        $data_vencimento_nova = $_POST['data_vencimento_inicial'];
        $forma_pagamento_nova = $_POST['forma_pagamento'] ?? 'Boleto';
        $itens_novos = $_POST['itens'] ?? [];
        
        if ($lancamentoModel->update($num_parcelas_novas, $data_vencimento_nova, $forma_pagamento_nova, $itens_novos)) {
             $_SESSION['message'] = "Lançamento atualizado com sucesso!";
             if ($_POST['tipo_lancamento'] === 'despesa_estoque' && !empty($itens_novos)) {
                foreach ($itens_novos as $item) {
                    if ($id_produto_semen && $item['produto_id'] == $id_produto_semen && isset($item['touro_id'])) {
                       $touroModel->ajustarDosesEstoque($item['touro_id'], (int)$item['quantidade']);
                    } else {
                        $produto_info = $estoqueModel->readOneProduct($item['produto_id']);
                        if ($produto_info) {
                            $qtd_ajuste = (float)str_replace(',', '.', $item['quantidade']) * (float)($produto_info['fator_conversao'] ?? 1.0);
                            $estoqueModel->ajustarQuantidade($item['produto_id'], $qtd_ajuste);
                        }
                    }
                }
                $_SESSION['message'] .= " Estoque reajustado!";
            }
        } else {
            $_SESSION['message'] = "Erro ao atualizar lançamento.";
        }
        header("Location: ../views/financeiro/index.php");
        exit();

    case 'marcar_paga':
        $id_parcela = $_POST['id_parcela'] ?? null;
        $id_lancamento = $_POST['id_lancamento'] ?? null;
        $data_pagamento = $_POST['data_pagamento'] ?? null;
        $forma_pagamento_paga = $_POST['forma_pagamento'] ?? null;
        $valor_pago = $_POST['valor_pago'] ?? null; 

        if ($id_parcela && $id_lancamento && $data_pagamento) {
            if ($parcelaModel->marcarComoPaga($id_parcela, $data_pagamento, $forma_pagamento_paga, $valor_pago)) {
                $_SESSION['message'] = "Parcela marcada como paga com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao marcar a parcela como paga.";
            }
            header("Location: ../views/financeiro/view.php?id=" . $id_lancamento);
        } else {
            $_SESSION['message'] = "Erro: Dados insuficientes para processar o pagamento.";
            header("Location: ../views/financeiro/index.php");
        }
        exit();
        
    case 'delete':
        $id_lancamento = $_POST['id_lancamento'] ?? null;
        if ($id_lancamento) {
            $lancamentoModel->id = $id_lancamento;
            $itens_a_reverter = $lancamentoModel->getItens();
            $id_produto_semen = getIdProdutoSemen($pdo);

            if (!empty($itens_a_reverter)) {
                foreach ($itens_a_reverter as $item) {
                    if ($id_produto_semen && $item['id_produto_estoque'] == $id_produto_semen && isset($item['touro_id'])) {
                    } else {
                        $produto_info = $estoqueModel->readOneProduct($item['id_produto_estoque']);
                        if ($produto_info) {
                           $qtd_reverter = (float)$item['quantidade'] * (float)($produto_info['fator_conversao'] ?? 1.0);
                           $estoqueModel->ajustarQuantidade($item['id_produto_estoque'], -$qtd_reverter);
                        }
                    }
                }
            }

            if ($lancamentoModel->delete($id_lancamento)) {
                $_SESSION['message'] = "Lançamento excluído e estoque revertido com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao excluir o lançamento.";
            }
        } else {
            $_SESSION['message'] = "Erro: ID do lançamento não fornecido.";
        }
        header("Location: ../views/financeiro/index.php");
        exit();
        
    default:
        header("Location: ../views/financeiro/index.php");
        exit();
}