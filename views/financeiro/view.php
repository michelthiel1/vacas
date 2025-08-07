<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/LancamentoFinanceiro.php';
require_once __DIR__ . '/../../models/ParcelaFinanceira.php';

// --- Validação e Segurança ---
if (($_SESSION['username'] ?? '') !== 'michelthiel' && ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php');
    exit();
}
$id_lancamento = $_GET['id'] ?? null;
if (!$id_lancamento) {
    die('ID do lançamento não fornecido.');
}

// --- Carregamento de Dados ---
$lancamento = new LancamentoFinanceiro($pdo);
$lancamento->id = $id_lancamento;
if (!$lancamento->readOne()) {
    die('Lançamento não encontrado.');
}

$parcelaModel = new ParcelaFinanceira($pdo);
$parcelas = $parcelaModel->readByLancamentoId($lancamento->id);
$total_parcelas = count($parcelas);

$itens_lancamento = $lancamento->getItens();
?>
<style>
    /* Estilos para o status da parcela */
    .status-parcela {
        font-weight: bold;
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 0.8em;
        text-transform: uppercase;
        color: white;
        display: inline-block;
    }
    .status-pago { background-color: var(--success-green); }
    .status-aberto { background-color: #FFA726; }
    .status-atrasado { background-color: var(--error-red); }
</style>

<h2 class="page-title">Detalhes do Lançamento: <?php echo htmlspecialchars($lancamento->descricao); ?></h2>

<?php if (!empty($itens_lancamento)): ?>
    <h3 class="section-title">Itens da Compra</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Valor Unit. (R$)</th>
                <th>Subtotal (R$)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($itens_lancamento as $item): ?>
                <tr>
                    <td><?php echo htmlspecialchars($item['nome_produto']); ?></td>
                    <td><?php echo htmlspecialchars($item['quantidade']); ?></td>
                    <td><?php echo number_format($item['valor_unitario'], 2, ',', '.'); ?></td>
                    <td><?php echo number_format($item['quantidade'] * $item['valor_unitario'], 2, ',', '.'); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


<h3 class="section-title">Parcelas</h3>
<table class="cow-list-table">
    <tbody>
        <?php 
        foreach($parcelas as $p): 
            $status_class = '';
            $is_vencida = strtotime($p['data_vencimento']) < strtotime(date('Y-m-d')) && $p['status'] == 'Aberto';
            $status_display = $is_vencida ? 'Atrasado' : $p['status'];
            
            switch($status_display) {
                case 'Pago': $status_class = 'status-pago'; break;
                case 'Aberto': $status_class = 'status-aberto'; break;
                case 'Atrasado': $status_class = 'status-atrasado'; break;
            }

            $linha_pagamento = '';
            if($p['data_pagamento']) {
                $linha_pagamento = date('d/m/Y', strtotime($p['data_pagamento']));
                if(!empty($p['forma_pagamento'])) {
                    $linha_pagamento .= ' - ' . htmlspecialchars($p['forma_pagamento']);
                }
            }
        ?>
            <tr>
                <td class="col-main-info">
                    <span class="details-line-left" style="font-weight: bold; color: var(--dark-orange);">
                        <?php echo date('d/m/Y', strtotime($p['data_vencimento'])); ?>
                    </span>
                    
                    <?php if(!empty($linha_pagamento)): ?>
                    <span class="details-line-left">
                        <?php echo $linha_pagamento; ?>
                    </span>
                    <?php endif; ?>

                    <span class="details-line-left" style="font-size: 0.8em; opacity: 0.7;">
                        <?php echo $p['numero_parcela']; ?>/<?php echo $total_parcelas; ?>
                    </span>
                </td>
                
                <td class="col-secondary-info">
                    <div class="secondary-info-container">
                        <div class="data-block">
                            <span class="date-display-right" style="color: <?php echo ($lancamento->tipo == 'PAGAR') ? 'var(--error-red)' : 'var(--success-green)'; ?>;">
                                R$ <?php echo number_format($p['valor_parcela'], 2, ',', '.'); ?>
                            </span>
                            <span class="details-line-right">
                               <span class="status-parcela <?php echo $status_class; ?>"><?php echo $status_display; ?></span>
                            </span>
                        </div>
                        <div class="actions-block" onclick="event.stopPropagation();">
                             <?php if($status_display === 'Aberto' || $status_display === 'Atrasado'): ?>
                                <button class="action-icon pagar-btn" data-id-parcela="<?php echo $p['id']; ?>" data-valor-parcela="<?php echo $p['valor_parcela']; ?>" title="Pagar Parcela">
                                   <i class="fas fa-dollar-sign" style="color: var(--success-green); font-size: 1.2em;"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<div class="button-group" style="margin-top: 25px;">
    <a href="edit.php?id=<?php echo $lancamento->id; ?>" class="btn btn-primary">Editar Lançamento</a>
    <a href="index.php" class="btn btn-secondary">Voltar</a>
</div>


<div id="pagamentoModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h3 style="margin-top:0;">Confirmar Pagamento</h3>
        <form id="pagamentoForm" action="../../controllers/FinanceiroController.php" method="POST">
            <input type="hidden" name="action" value="marcar_paga">
            <input type="hidden" name="id_parcela" id="id_parcela_pagar">
            <input type="hidden" name="id_lancamento" value="<?php echo $lancamento->id; ?>">
            
            <div style="display: flex; flex-direction: column; gap: 15px;">
                <div>
                    <label for="data_pagamento">Data do Pagamento:</label>
                    <input type="date" name="data_pagamento" id="data_pagamento_modal" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div>
                    <label for="valor_pago">Valor Pago (R$):</label>
                    <input type="number" step="0.01" name="valor_pago" id="valor_pago_modal" placeholder="0.00" required>
                </div>
                
                <div>
                    <label for="forma_pagamento">Forma de Pagamento:</label>
                    <input type="text" name="forma_pagamento" id="forma_pagamento_modal" placeholder="Ex: PIX, Desconto Leite...">
                </div>
            </div>
            
            <div class="button-group" style="justify-content: flex-end; margin-top: 20px;">
                <button type="button" id="closeModalBtn" class="btn btn-secondary">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('pagamentoModal');
    const closeModalBtn = document.getElementById('closeModalBtn');
    const closeSpan = modal.querySelector('.close-button');
    const valorPagoInput = document.getElementById('valor_pago_modal');

    document.querySelectorAll('.pagar-btn').forEach(button => {
        button.addEventListener('click', function() {
            const idParcela = this.dataset.idParcela;
            const valorParcela = this.dataset.valorParcela;

            document.getElementById('id_parcela_pagar').value = idParcela;
            valorPagoInput.value = parseFloat(valorParcela).toFixed(2);
            
            modal.style.display = 'flex';
        });
    });

    const closeActions = [closeModalBtn, closeSpan];
    closeActions.forEach(btn => {
        btn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
    });

    window.addEventListener('click', function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
});
</script>
