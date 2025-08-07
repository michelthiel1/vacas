<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/RegistroManejo.php';

// --- Lógica para ler os filtros da URL ---
$filters = [];
if (!empty($_GET['search_query'])) {
    $filters['search_query'] = $_GET['search_query'];
}
if (!empty($_GET['data_inicio'])) {
    $filters['data_inicio'] = $_GET['data_inicio'];
}
if (!empty($_GET['data_fim'])) {
    $filters['data_fim'] = $_GET['data_fim'];
}
if (!empty($_GET['tipos_manejo'])) {
    $filters['tipos_manejo'] = explode(',', $_GET['tipos_manejo']);
}
if (!empty($_GET['id_manejo'])) {
    $filters['id_manejo'] = $_GET['id_manejo'];
}

$registroManejo = new RegistroManejo($pdo);
$stmt = $registroManejo->read($filters);

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

$tipoManejoOptions = ['BST','Diagnostico','Protocolo de Saúde','Secagem','Pré-Parto','Vacinas'];
$selectedTipos = $filters['tipos_manejo'] ?? [];
?>

<h2 class="page-title">Registros de Manejo</h2>
<?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>

<div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php">
        <div class="top-filter-bar">
            <div class="search-input-group">
                <input type="text" id="search_brinco" name="search_query" placeholder="Pesquisar por Brinco, Tipo ou Manejo..." value="<?php echo htmlspecialchars($_GET['search_query'] ?? ''); ?>">
            </div>
            <div class="filter-buttons-group">
    <a href="create.php<?php echo !empty($filters['search_query']) ? '?brinco=' . urlencode($filters['search_query']) : ''; ?>" class="btn btn-primary" title="Registrar Novo Manejo" style="padding: 5px 12px; font-size: 1.2em;">+</a>
    <button id="openManejoFilterBtn" class="btn btn-secondary" type="button">Filtros</button>
    <a href="index.php" class="btn btn-danger" title="Limpar Filtros">Limpar</a>
</div>
        </div>
    </form>
</div>

<table class="data-table" style="margin-top: 20px;">
    <thead>
        <tr>
            <th>Data</th>
            <th>Animal/Alvo</th>
            <th>Tipo</th>
            <th>Manejo Aplicado</th>
            <th class="actions-column-condensed">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($stmt->rowCount() > 0): ?>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo date('d/m/Y', strtotime($row['data_aplicacao'])); ?></td>
                    <td>
                        <?php 
                            if ($row['aplicado_rebanho']) {
                                echo '<strong>REBANHO COMPLETO</strong>';
                            } else {
                                echo htmlspecialchars($row['brinco_gado']);
                            }
                        ?>
                    </td>
                    <td><?php echo htmlspecialchars($row['tipo_manejo']); ?></td>
                    <td><?php echo htmlspecialchars($row['nome_manejo']); ?></td>
                    <td class="actions-column-condensed">
                        <div class="button-group">
                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Editar</a>
                            <form action="../../controllers/RegistroManejoController.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este registro?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" style="text-align: center;">Nenhum registro de manejo encontrado.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="manejoFilterModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Filtros de Manejo</h3>
            <span class="close-button">&times;</span>
        </div>
        <div class="modal-body">
            <div class="filter-group">
                <label>Período de Aplicação:</label>
                <div class="filter-input-range">
                    <input type="date" id="data_inicio_filter" value="<?php echo htmlspecialchars($_GET['data_inicio'] ?? ''); ?>">
                    <span>a</span>
                    <input type="date" id="data_fim_filter" value="<?php echo htmlspecialchars($_GET['data_fim'] ?? ''); ?>">
                </div>
            </div>
            <div class="filter-group">
                <label>Tipo de Manejo:</label>
                <div class="filter-checkbox-group">
                    <?php foreach($tipoManejoOptions as $tipo): ?>
                    <div>
                        <input type="checkbox" id="tipo_<?php echo strtolower(str_replace(' ', '', $tipo)); ?>" name="tipos_manejo[]" value="<?php echo $tipo; ?>" <?php echo in_array($tipo, $selectedTipos) ? 'checked' : ''; ?>>
                        <label for="tipo_<?php echo strtolower(str_replace(' ', '', $tipo)); ?>"><?php echo $tipo; ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="filter-group">
                <label for="id_manejo_filter">Manejo Aplicado:</label>
                <select id="id_manejo_filter" name="id_manejo" style="width: 100%;">
                    <option value="">Todos os Manejos</option>
                    </select>
            </div>
        </div>
        <div class="modal-footer">
            <button id="applyManejoFiltersBtn" class="btn btn-primary">Aplicar Filtros</button>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../js/select2.min.js"></script>
<script src="../../js/manejo-filters.js"></script>