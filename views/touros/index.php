<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Touro.php';

$touro = new Touro($pdo);

// Captura o termo de pesquisa da URL
$searchQuery = $_GET['search_query'] ?? '';
$filters = [];
if ($searchQuery) {
    $filters['search_query'] = $searchQuery;
}

$stmt = $touro->read($filters);
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<h2 class="page-title">Touros Cadastrados</h2>
<?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>

<div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php">
        <div class="top-filter-bar">
            <div class="search-input-group">
                <input type="text" id="search_query" name="search_query" placeholder="Pesquisar por nome ou raça..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="filter-buttons-group">
                <a href="create.php" class="btn btn-primary" title="Adicionar Novo Touro" style="padding: 5px 12px; font-size: 1.2em;">+</a>
                <?php if ($searchQuery): ?>
                    <a href="index.php" class="btn btn-danger">Limpar</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>


<table class="data-table" style="margin-top: 20px;">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Raça</th>
            <th>Doses em Estoque</th><th>Ativo</th>
            <th class="actions-column-condensed">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($stmt->rowCount() > 0): ?>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                    <td><?php echo htmlspecialchars($row['raca']); ?></td>
                    <td style="font-weight: bold; color: <?php echo ($row['doses_estoque'] <= 5) ? 'var(--error-red)' : 'var(--neutral-text)'; ?>;">
        <?php echo htmlspecialchars($row['doses_estoque']); ?>
    </td><td><?php echo $row['ativo'] ? 'Sim' : 'Não'; ?></td>
                    <td class="actions-column-condensed">
                        <div class="button-group">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Editar</a>
                            <form action="../../controllers/TouroController.php" method="POST" onsubmit="return confirm('Tem certeza?');" style="display:inline-block;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align: center;">Nenhum touro encontrado.</td></tr>
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
            }, 500);
        });
    }
});
</script>