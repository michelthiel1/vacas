<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Estoque.php';
require_once __DIR__ . '/../../models/CategoriaFinanceira.php';

$id_produto = $_GET['id'] ?? die('ID do produto não fornecido.');

$estoqueModel = new Estoque($pdo);
$produto = $estoqueModel->readOneProduct($id_produto);

if (!$produto) {
    die('Produto não encontrado.');
}

$categoriaModel = new CategoriaFinanceira($pdo);
$categorias_flat = $categoriaModel->read();

$categorias_hierarquia = [];
foreach ($categorias_flat as $categoria) {
    if ($categoria['tipo'] == 'PAGAR') {
        if (empty($categoria['parent_id'])) {
            $categorias_hierarquia[$categoria['id']]['parent'] = $categoria;
        } else {
            $categorias_hierarquia[$categoria['parent_id']]['children'][] = $categoria;
        }
    }
}
?>
<h2 class="page-title">Editar Produto do Estoque</h2>

<form action="../../controllers/EstoqueController.php" method="post" class="form-compacto">
    <input type="hidden" name="action" value="update_produto">
    <input type="hidden" name="id_produto" value="<?php echo htmlspecialchars($produto['Id']); ?>">
    <!-- Campo oculto para enviar o valor de consumo, que não é mais editável diretamente -->
    <input type="hidden" name="valor_consumo_hidden" value="<?php echo htmlspecialchars($produto['Valor'] ?? '0.00'); ?>">

    <div>
        <label for="produto">Nome do Produto:</label>
        <input type="text" id="produto" name="produto" value="<?php echo htmlspecialchars($produto['Produto']); ?>" required>
    </div>

    <div class="form-grid-3col">
        <div>
            <label for="unidade_compra">Unidade de Compra</label>
            <input type="text" id="unidade_compra" name="unidade_compra" value="<?php echo htmlspecialchars($produto['unidade_compra'] ?? 'kg'); ?>" required>
        </div>
        <div>
            <label for="unidade_consumo">Unidade de Consumo</label>
            <input type="text" id="unidade_consumo" name="unidade_consumo" value="<?php echo htmlspecialchars($produto['unidade_consumo'] ?? 'kg'); ?>" required>
        </div>
        <div>
            <label for="fator_conversao">Fator de Conversão</label>
            <input type="number" id="fator_conversao" name="fator_conversao" step="0.0001" value="<?php echo htmlspecialchars(number_format((float)($produto['fator_conversao'] ?? 1.0), 4, '.', '')); ?>" required title="Quantas 'unidades de consumo' existem em 1 'unidade de compra'">
        </div>
    </div>
    <p style="font-size:0.8em; color: #666; margin-top: -10px;">Ex: Compra em Saco (50kg), consumo em kg -> Fator: 50.</p>
    
    <div>
        <label for="valor_compra">Valor da Compra (por Unid. de Compra):</label>
        <input type="number" id="valor_compra" name="valor_compra" step="0.01" value="<?php echo htmlspecialchars($produto['valor_compra'] ?? '0.00'); ?>" required>
    </div>
    
    <div>
        <label for="id_categoria_financeira">Categoria Financeira Padrão:</label>
        <select name="id_categoria_financeira" id="id_categoria_financeira">
            <option value="">Nenhuma</option>
            <?php
            foreach ($categorias_hierarquia as $id_pai => $dados) {
                $parent = $dados['parent'];
                $selected = (($produto['id_categoria_financeira'] ?? null) == $parent['id']) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($parent['id']) . '" ' . $selected . ' data-level="parent">' . htmlspecialchars($parent['nome']) . '</option>';
                
                if (isset($dados['children'])) {
                    foreach ($dados['children'] as $child) {
                        $selected_child = (($produto['id_categoria_financeira'] ?? null) == $child['id']) ? 'selected' : '';
                        echo '<option value="' . htmlspecialchars($child['id']) . '" ' . $selected_child . ' data-level="child">' . htmlspecialchars($child['nome']) . '</option>';
                    }
                }
            }
            ?>
        </select>
        <p style="font-size:0.8em; color: #666;">Essa será a categoria usada automaticamente ao lançar uma compra deste item.</p>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="list.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script src="../../js/select2.min.js"></script>
<script>
$(document).ready(function() {
    
    function formatarOpcaoCategoria(categoria) {
        if (!categoria.id) { return categoria.text; }
        var $opcao = $(categoria.element);
        var nivel = $opcao.data('level');

        if (nivel === 'parent') {
            return $('<span class="select2-result-parent">' + categoria.text + '</span>');
        } else {
            return $('<span class="select2-result-child">↳ ' + categoria.text + '</span>');
        }
    }

    $('#id_categoria_financeira').select2({
        placeholder: "Selecione uma categoria...",
        allowClear: true,
        templateResult: formatarOpcaoCategoria
    });
});
</script>
