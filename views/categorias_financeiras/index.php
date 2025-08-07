<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/CategoriaFinanceira.php';

$categoriaModel = new CategoriaFinanceira($pdo);
$searchQuery = $_GET['search_query'] ?? '';
$categorias = $categoriaModel->read($searchQuery);

// A hierarquia só é montada se não houver pesquisa
if (empty($searchQuery)) {
    $categoriasHierarquia = [];
    foreach ($categorias as $cat) {
        $categoriasHierarquia[$cat['parent_id'] ?? 0][] = $cat;
    }
}
?>
<h2 class="page-title">Gerenciar Categorias Financeiras</h2>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-info" style="margin-bottom: 15px;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
<?php endif; ?>

<div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php">
        <div class="top-filter-bar">
            <div class="search-input-group">
                <input type="text" id="search_query" name="search_query" placeholder="Pesquisar por nome ou tipo..." value="<?php echo htmlspecialchars($searchQuery); ?>">
            </div>
            <div class="filter-buttons-group">
                <a href="create.php" class="btn btn-primary" title="Nova Categoria" style="padding: 5px 12px; font-size: 1.2em;">+</a>
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
            <th>Nome da Categoria</th>
            <th>Tipo</th>
            <th class="actions-column-condensed">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($searchQuery)) {
            // MODO DE EXIBIÇÃO SIMPLES (QUANDO PESQUISANDO)
            if (empty($categorias)) {
                echo '<tr><td colspan="3" style="text-align: center;">Nenhum resultado encontrado.</td></tr>';
            } else {
                foreach ($categorias as $cat) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($cat['nome']) . '</td>';
                    echo '<td>' . htmlspecialchars($cat['tipo']) . '</td>';
                    echo '<td class="actions-column-condensed"><div class="button-group">';
                    echo '<a href="edit.php?id=' . $cat['id'] . '" class="btn btn-secondary">Editar</a>';
                    echo '<form action="../../controllers/CategoriaFinanceiraController.php" method="POST" onsubmit="return confirm(\'Tem certeza?\');" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' . $cat['id'] . '"><button type="submit" class="btn btn-danger">Excluir</button></form>';
                    echo '</div></td>';
                    echo '</tr>';
                }
            }
        } else {
            // MODO DE EXIBIÇÃO HIERÁRQUICO (PADRÃO)
            function renderizarCategorias($categorias, $parentId = 0, $level = 0, $group_class = 'group-even') {
                if (!isset($categorias[$parentId])) return;
                
                static $parent_index = 0;

                foreach ($categorias[$parentId] as $cat) {
                    $current_group_class = $group_class;
                    $row_class = '';
                    
                    if ($level == 0) {
                        $current_group_class = ($parent_index % 2 == 0) ? 'group-even' : 'group-odd';
                        $row_class = 'categoria-pai';
                        $parent_index++;
                    }

                    echo '<tr class="' . $row_class . ' ' . $current_group_class . '">';
                    echo '<td><span style="padding-left: ' . ($level * 25) . 'px;">' . ($level > 0 ? '↳ ' : '') . htmlspecialchars($cat['nome']) . '</span></td>';
                    echo '<td>' . htmlspecialchars($cat['tipo']) . '</td>';
                    echo '<td class="actions-column-condensed"><div class="button-group">';
                    echo '<a href="edit.php?id=' . $cat['id'] . '" class="btn btn-secondary">Editar</a>';
                    echo '<form action="../../controllers/CategoriaFinanceiraController.php" method="POST" onsubmit="return confirm(\'Tem certeza?\');" style="display:inline;"><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="' . $cat['id'] . '"><button type="submit" class="btn btn-danger">Excluir</button></form>';
                    echo '</div></td>';
                    echo '</tr>';
                    
                    renderizarCategorias($categorias, $cat['id'], $level + 1, $current_group_class);
                }
            }
            renderizarCategorias($categoriasHierarquia);
        }
        ?>
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