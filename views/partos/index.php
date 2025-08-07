<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Parto.php';

$parto = new Parto($pdo);

// Captura o termo de pesquisa da URL (se existir)
$searchQuery = $_GET['search_query'] ?? '';

$stmt = $parto->read($searchQuery); // Passa o termo de pesquisa para o método read()
$num = $stmt->rowCount();

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<h2 class="page-title">Partos</h2>

<?php echo $message; ?>

<div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php">
        <div class="filter-row top-row">
            <div class="search-input-group">
                <input type="text" id="search_query" name="search_query" placeholder="Pesquisar (Brinco Vaca, Nome Vaca, Touro, Sexo Cria):" value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            
            <div class="button-controls-group">
                <a href="create.php" class="btn btn-primary add-animal-inline-btn" title="Registrar Novo Parto">+</a>
            </div>
        </div>
        </form>
</div>

<div class="button-group">
    <?php
    $anyFilterActive = !empty($searchQuery);
    if ($anyFilterActive):
    ?>
        <button id="clearAllFiltersBtn" class="btn btn-danger">Limpar Filtros</button>
    <?php endif; ?>
</div>

<?php if ($num > 0) : ?>
    <table class="cow-list-table"> <tbody>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                <tr class="clickable-row" data-href="view.php?id=<?php echo $row['id']; ?>">
                    <td class="col-main-info"> <span class="brinco-display"><?php echo htmlspecialchars($row['brinco_vaca_display']); ?></span>
                        <span class="details-line-left"><strong>Vaca:</strong> <?php echo htmlspecialchars($row['nome_vaca_display']); ?></span> 
                        <span class="details-line-left"><strong>Touro:</strong> <?php echo htmlspecialchars($row['nome_touro_display']); ?></span> 
                    </td>
                    <td class="col-secondary-info"> <span class="date-display-right"><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_parto']))); ?></span>
                        <span class="details-line-right"><strong>Cria:</strong> <?php echo htmlspecialchars($row['sexo_cria']); ?></span> 
                    </td>
                    </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="alert alert-info">Nenhum parto encontrado com os filtros aplicados.</div>
<?php endif; ?>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>