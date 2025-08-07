<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/LancamentoFinanceiro.php';

$lancamento = new LancamentoFinanceiro($pdo);
$searchQuery = $_GET['search'] ?? '';
$stmt = $lancamento->read(['search_query' => $searchQuery]);

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<h2 class="page-title">Painel Financeiro</h2>
<?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>

<div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php" style="display: flex; gap: 8px; align-items: center;">
        <input type="text" id="search_query" name="search" placeholder="Pesquisar por descrição, categoria, contato..." value="<?php echo htmlspecialchars($searchQuery); ?>" style="flex-grow: 1; min-width: 100px;">
        <div style="display: flex; flex-shrink: 0; gap: 8px;">
            <?php if ($searchQuery): ?>
                <a href="index.php" class="btn btn-danger">Limpar</a>
            <?php endif; ?>
            <a href="create.php" class="btn btn-primary" title="Novo Lançamento">+</a>
        </div>
    </form>
</div>

<div style="margin-top: 20px;">
    <?php if ($stmt->rowCount() > 0): ?>
        <table class="cow-list-table">
            <tbody>
                <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr class="clickable-row" data-href="view.php?id=<?php echo $row['id']; ?>">
                        <td class="col-main-info">
                            <span class="brinco-display" style="color: <?php echo ($row['tipo'] == 'PAGAR') ? 'var(--error-red)' : 'var(--success-green)'; ?>;">
                                 <?php 
                                    if (!empty($row['nome_contato'])) {
                                        echo  htmlspecialchars($row['nome_contato']);
                                    }
                                ?>
                            </span>
							
<span class="details-line-left">
<?php 
// PRIMEIRO: Verifica se é uma compra de estoque
if ($row['item_count'] > 0) {
    echo '<span class="details-line-left"><strong style="color:var(--dark-orange);">Compra de Produtos</strong></span>';
} 
// SEGUNDO: Se não for, verifica se tem categoria pai para quebrar a linha
elseif (!empty($row['nome_categoria_pai'])) {
    echo '<span class="details-line-left">' . htmlspecialchars($row['nome_categoria_pai']) . '</span>';
    echo '<br><span class="details-line-left" style="padding-left: 15px;">↳ ' . htmlspecialchars($row['nome_categoria'] ?? 'Sem Categoria') . '</span>';
} 
// TERCEIRO: Se não tiver pai, exibe a categoria principal
else {
    echo '<span class="details-line-left">' . htmlspecialchars($row['nome_categoria'] ?? 'Sem Categoria') . '</span>';
}
?>
</span>

                            <span class="details-line-left">
							<?php echo htmlspecialchars($row['descricao']); ?>
                               
                            </span>
                        </td>
                        
                        <td class="col-secondary-info">
                            <div class="secondary-info-container">
                                <div class="data-block">
                                    <span class="date-display-right" style="color: <?php echo ($row['tipo'] == 'PAGAR') ? 'var(--error-red)' : 'var(--success-green)'; ?>;">
                                        R$ <?php echo number_format($row['valor_total'], 2, ',', '.'); ?>
                                    </span>
                                    <span class="details-line-right"><?php echo htmlspecialchars($row['tipo']); ?></span>
                                    <span class="details-line-right"><?php echo "{$row['parcelas_pagas']} / {$row['total_parcelas']}"; ?></span>
                                </div>
              <div class="actions-block" onclick="event.stopPropagation();">
    <a href="edit.php?id=<?php echo $row['id']; ?>" class="action-icon edit-icon" title="Alterar">
        <i class="fas fa-pencil-alt"></i>
    </a>

    <form id="delete-form-<?php echo $row['id']; ?>" action="../../controllers/FinanceiroController.php" method="POST" style="display:none;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id_lancamento" value="<?php echo $row['id']; ?>">
    </form>
    
    <a href="#" class="action-icon delete-icon delete-link-js" data-form-id="delete-form-<?php echo $row['id']; ?>" title="Excluir">
        <i class="fas fa-times"></i>
    </a>
</div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info" style="text-align: center;">Nenhum lançamento encontrado.</div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Código existente do auto-submit da pesquisa...
    const searchQueryInput = document.getElementById('search_query');
    if (searchQueryInput) {
        let searchTimeout;
        searchQueryInput.addEventListener('keyup', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filter-form').submit();
            }, 500);
        });
    }
    
    document.querySelectorAll('.clickable-row').forEach(row => {
        row.addEventListener('click', function() {
            window.location.href = this.dataset.href;
        });
    });

    // ### INÍCIO DO NOVO CÓDIGO PARA O BOTÃO DE EXCLUIR ###
    document.querySelectorAll('.delete-link-js').forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault(); // Impede que o link navegue para '#'
            
            if (confirm('Tem certeza que deseja excluir este lançamento e todas as suas parcelas?')) {
                const formId = this.dataset.formId; // Pega o ID do formulário a ser enviado
                const formToSubmit = document.getElementById(formId);
                if (formToSubmit) {
                    formToSubmit.submit(); // Envia o formulário oculto
                }
            }
        });
    });
    // ### FIM DO NOVO CÓDIGO ###
});
</script>