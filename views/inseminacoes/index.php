<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Inseminacao.php'; 

$inseminacao = new Inseminacao($pdo); 

// Captura o termo de pesquisa da URL (se existir)
$searchQuery = $_GET['search_query'] ?? ''; 

$stmt = $inseminacao->read($searchQuery); // CORREÇÃO AQUI: Passa o termo de pesquisa para o método read()
$num = $stmt->rowCount();

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<h2 class="page-title">Inseminações</h2>

<?php echo $message; ?>

<div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php">
        <div class="filter-row top-row">
            <div class="search-input-group">
                <input type="text" id="search_query" name="search_query" placeholder="Pesquisar (Brinco, Vaca, Touro, Inseminador, Grupo, Status):" value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            
            <div class="button-controls-group">
                <a href="create.php" class="btn btn-primary add-animal-inline-btn" title="Nova Inseminação">+</a>
                </div>
        </div>
        </form>
</div>

<div class="button-group">
    <?php
    // Detecta se há algum filtro ativo para exibir o botão Limpar Filtros
    $anyFilterActive = !empty($searchQuery); // Apenas a pesquisa de texto
    if ($anyFilterActive):
    ?>
        <button id="clearAllFiltersBtn" class="btn btn-danger">Limpar Filtros</button>
    <?php endif; ?>
</div>


<?php if ($num > 0) : ?>
    <table class="cow-list-table">
        <tbody>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                <tr class="clickable-row" data-href="view.php?id=<?php echo $row['id']; ?>">
                    <td class="col-main-info"> <span class="brinco-display"><?php echo htmlspecialchars($row['brinco_vaca_display']); ?></span>
                        <span class="details-line-left"><strong>Grupo:</strong> <?php echo htmlspecialchars($row['grupo_vaca_display']); ?></span> 
                        <span class="details-line-left"><strong>Touro:</strong> <?php echo htmlspecialchars($row['nome_touro_display']); ?></span> 
                    </td>
                    <td class="col-secondary-info"> <span class="date-display-right"><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['data_inseminacao']))); ?></span>
                        <span class="details-line-right"><?php echo htmlspecialchars($row['tipo']); ?></span> 
                        <span class="details-line-right"><?php echo htmlspecialchars($row['status_inseminacao']); ?></span> 
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="alert alert-info">Nenhuma inseminação encontrada com os filtros aplicados.</div>
<?php endif; ?>

<div id="filterModal" class="modal" style="display: none;"> <div class="modal-content">
        <div class="modal-header">
            <h3>Mais Filtros</h3>
            <span class="close-button">&times;</span>
        </div>
        <div class="modal-body">
            <form id="filterForm" onsubmit="return false;">
                <div class="filter-group">
                    <label>Idade (meses):</label>
                    <div class="filter-input-range">
                        <input type="number" id="idade_min" name="idade_min" placeholder="Mínimo" min="0">
                        <span>a</span>
                        <input type="number" id="idade_max" name="idade_max" placeholder="Máximo" min="0">
                    </div>
                </div>

                <div class="filter-group">
                    <label>Status:</label>
                    <div class="filter-checkbox-group">
                        <div>
                            <input type="checkbox" id="status_vazia" name="status[]" value="Vazia">
                            <label for="status_vazia">Vazia</label>
                        </div>
                        <div>
                            <input type="checkbox" id="status_inseminada" name="status[]" value="Inseminada">
                            <label for="status_inseminada">Inseminada</label>
                        </div>
                        <div>
                            <input type="checkbox" id="status_prenha" name="status[]" value="Prenha">
                            <label for="status_prenha">Prenha</label>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Escore:</label>
                    <div class="filter-input-range">
                        <input type="number" id="escore_min" name="escore_min" placeholder="Mínimo" step="0.25" min="1.0" max="5.0">
                        <span>a</span>
                        <input type="number" id="escore_max" name="escore_max" placeholder="Máximo" step="0.25" min="1.0" max="5.0">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button id="applyModalFiltersBtn" class="btn btn-primary">Aplicar Filtros</button>
            <button id="closeFilterModalBtn" class="btn btn-secondary">Fechar</button>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script src="../../js/filter.js"></script>