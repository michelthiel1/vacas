<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Estoque.php';

$estoqueModel = new Estoque($pdo);
$searchQuery = $_GET['search'] ?? '';
$produtos = $estoqueModel->readAllWithCategory($searchQuery);

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<h2 class="page-title">Gerenciamento de Produtos do Estoque</h2>
<?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>

<div class="filter-controls">
    <form id="filter-form" method="GET" action="list.php">
        <div class="top-filter-bar">
            <div class="search-input-group">
                <input type="text" id="search_query" name="search" placeholder="Pesquisar por produto ou categoria..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="filter-buttons-group">
                <a href="create.php" class="btn btn-primary" title="Adicionar Novo Produto">+</a>
                <?php if ($searchQuery): ?>
                    <a href="list.php" class="btn btn-danger">Limpar</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<table class="data-table" style="margin-top: 20px;">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Categoria Financeira Padrão</th>
            <th class="actions-column-condensed">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($produtos)): ?>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($produto['Produto']); ?></td>
                    <td><?php echo htmlspecialchars($produto['categoria_nome'] ?? 'Nenhuma'); ?></td>
                    <td class="actions-column-condensed">
                        <div class="button-group">
                            <a href="edit.php?id=<?php echo $produto['Id']; ?>" class="btn btn-secondary">Editar</a>
                            <form action="../../controllers/EstoqueController.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este produto?');" style="display:inline-block;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id_produto" value="<?php echo $produto['Id']; ?>">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="3" style="text-align: center;">Nenhum produto encontrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchQueryInput = document.getElementById('search_query');
    if (searchQueryInput) {
        let searchTimeout;
        searchQueryInput.addEventListener('keyup', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filter-form').submit();
            }, 500); // Aguarda 500ms (meio segundo) após o usuário parar de digitar
        });
    }
});
</script>