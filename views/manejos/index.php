<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Manejo.php';

// --- Lógica para ler os filtros da URL ---
$filters = [];
if (!empty($_GET['search_query'])) {
    $filters['search_query'] = trim($_GET['search_query']);
}
if (!empty($_GET['tipos'])) {
    // Garante que 'tipos' seja um array
    $filters['tipos'] = is_array($_GET['tipos']) ? $_GET['tipos'] : explode(',', $_GET['tipos']);
}

$manejo = new Manejo($pdo);
$stmt = $manejo->read($filters); // Passa os filtros para a função read

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

$tipoManejoOptions = ['BST','Diagnostico','Protocolo de Saúde','Secagem','Pré-Parto','Vacinas'];
$selectedTipos = $filters['tipos'] ?? [];
?>
<h2 class="page-title">Tipos de Manejo Cadastrados</h2>
<?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>

<div class="filter-controls">
    <form id="manejo-filter-form" method="GET" action="index.php">
        <div class="top-filter-bar">
            <div class="search-input-group">
                <input type="text" id="search_query" name="search_query" placeholder="Pesquisar por Nome ou Tipo..." value="<?php echo htmlspecialchars($_GET['search_query'] ?? ''); ?>">
            </div>
            <div class="filter-buttons-group">
                <a href="create.php" class="btn btn-primary add-animal-inline-btn" title="Adicionar Novo Manejo">+</a>
                <button id="openFilterBtn" class="btn btn-secondary" type="button">Filtros</button>
                <?php if (!empty($filters)): ?>
                    <a href="index.php" class="btn btn-danger" title="Limpar Filtros">Limpar</a>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<table class="data-table" style="margin-top: 15px;">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Ativo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($stmt->rowCount() > 0): ?>
            <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nome']); ?></td>
                    <td><?php echo htmlspecialchars($row['tipo']); ?></td>
                    <td><?php echo $row['ativo'] ? 'Sim' : 'Não'; ?></td>
                    <td><a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size: 0.8em;">Editar</a></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center;">Nenhum tipo de manejo encontrado com os filtros aplicados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<div id="filterModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Filtrar por Tipo</h3>
            <span class="close-button">&times;</span>
        </div>
        <div class="modal-body">
            <div class="filter-group">
                <label>Tipo de Manejo:</label>
                <div class="filter-checkbox-group">
                    <?php foreach($tipoManejoOptions as $tipo): ?>
                    <div>
                        <input type="checkbox" id="tipo_<?php echo strtolower(str_replace(' ', '', $tipo)); ?>" name="tipos[]" value="<?php echo $tipo; ?>" <?php echo in_array($tipo, $selectedTipos) ? 'checked' : ''; ?>>
                        <label for="tipo_<?php echo strtolower(str_replace(' ', '', $tipo)); ?>"><?php echo $tipo; ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button id="applyModalFiltersBtn" class="btn btn-primary">Aplicar Filtros</button>
        </div>
    </div>
</div>


<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterModal = document.getElementById('filterModal');
    const openFilterBtn = document.getElementById('openFilterBtn');
    const closeButton = filterModal.querySelector('.close-button');
    const applyFiltersBtn = document.getElementById('applyModalFiltersBtn');
    const mainForm = document.getElementById('manejo-filter-form');
    const searchQueryInput = document.getElementById('search_query');

    // Abre o modal
    openFilterBtn.addEventListener('click', function() {
        filterModal.style.display = 'flex';
    });

    // Fecha o modal
    closeButton.addEventListener('click', function() {
        filterModal.style.display = 'none';
    });
    window.addEventListener('click', function(event) {
        if (event.target == filterModal) {
            filterModal.style.display = 'none';
        }
    });

    // Função para submeter o formulário principal
    function submitMainForm() {
        const urlParams = new URLSearchParams();
        
        // Pega o valor da busca de texto
        if (searchQueryInput.value) {
            urlParams.set('search_query', searchQueryInput.value);
        }

        // Pega os tipos selecionados no modal
        const tiposSelecionados = Array.from(document.querySelectorAll('#filterModal input[name="tipos[]"]:checked'))
                                     .map(cb => cb.value);
        
        if (tiposSelecionados.length > 0) {
            urlParams.set('tipos', tiposSelecionados.join(','));
        }

        // Reconstrói a URL e recarrega a página
        window.location.search = urlParams.toString();
    }

    // Aplica os filtros do modal e submete
    applyFiltersBtn.addEventListener('click', function() {
        submitMainForm();
    });

    // Submete ao pressionar Enter na busca de texto
    searchQueryInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Impede o comportamento padrão do formulário
            submitMainForm();
        }
    });
});
</script>