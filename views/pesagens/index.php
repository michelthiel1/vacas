<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Pesagem.php';

$pesagem = new Pesagem($pdo);
$searchQuery = $_GET['search_query'] ?? '';
$stmt = $pesagem->read($searchQuery);

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<h2 class="page-title">Registros de Pesagem</h2>
<?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>

<div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php">
        <div class="top-filter-bar">
            <div class="search-input-group">
                <input type="text" id="search_query" name="search_query" placeholder="Pesquisar por Brinco..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="filter-buttons-group">
                 <a href="create.php" class="btn btn-primary">+ Nova Pesagem</a>
                 <?php if (!empty($searchQuery)): ?>
                    <a href="index.php" class="btn btn-danger">Limpar</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<table class="data-table" style="margin-top: 20px;">
    <thead>
        <tr>
            <th>Data</th>
            <th>Brinco</th>
            <th>Peso (kg)</th>
            <th class="actions-column-condensed">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($stmt->rowCount() > 0): ?>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($row['data_pesagem'])); ?></td>
                    <td><?php echo htmlspecialchars($row['brinco_gado']); ?></td>
                    <td><?php echo htmlspecialchars(number_format($row['peso'], 0, ',', '.')); ?></td>
                    <td class="actions-column-condensed">
                        <div class="button-group">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Editar</a>
                            <form action="../../controllers/PesagemController.php" method="POST" onsubmit="return confirm('Tem certeza?');" style="display:inline-block;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4" style="text-align: center;">Nenhum registro encontrado.</td></tr>
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