<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/CategoriaFinanceira.php';

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
<h2 class="page-title">Adicionar Novo Produto ao Estoque</h2>

<form action="../../controllers/EstoqueController.php" method="post" class="form-compacto">
    <input type="hidden" name="action" value="store">

    <div>
        <label for="produto">Nome do Produto:</label>
        <input type="text" id="produto" name="produto" required>
    </div>
    
    <div class="form-grid-3col">
        <div>
            <label for="unidade_compra">Unidade de Compra</label>
            <input type="text" id="unidade_compra" name="unidade_compra" placeholder="Ex: Saco, Fardo, Galão" value="kg" required>
        </div>
        <div>
            <label for="unidade_consumo">Unidade de Consumo</label>
            <input type="text" id="unidade_consumo" name="unidade_consumo" placeholder="Ex: kg, ml, pacote" value="kg" required>
        </div>
        <div>
            <label for="fator_conversao">Fator de Conversão</label>
            <input type="number" id="fator_conversao" name="fator_conversao" step="0.0001" value="1.0" required title="Quantas 'unidades de consumo' existem em 1 'unidade de compra'">
        </div>
    </div>
    <p style="font-size:0.8em; color: #666; margin-top: -10px;">Ex: Compra em Saco, consumo em kg -> Fator: 50. Compra em Fardo, consumo em Pacote -> Fator: 6.</p>

    <div class="form-grid-2col">
        <div>
            <label for="quantidade">Estoque Inicial (em Unid. de Consumo):</label>
            <input type="number" id="quantidade" name="quantidade" step="0.01" value="0" required>
        </div>
        <div>
            <label for="valor_compra">Valor da Compra (por Unid. de Compra):</label>
            <input type="number" id="valor_compra" name="valor_compra" step="0.01" value="0.00" required>
        </div>
    </div>
    
    <div>
        <label for="id_categoria_financeira">Categoria Financeira Padrão (para compras):</label>
        <select name="id_categoria_financeira" id="id_categoria_financeira">
            <option value="">Nenhuma</option>
            <?php
            foreach ($categorias_hierarquia as $id_pai => $dados) {
                $parent = $dados['parent'];
                echo '<option value="' . htmlspecialchars($parent['id']) . '" data-level="parent">' . htmlspecialchars($parent['nome']) . '</option>';
                if (isset($dados['children'])) {
                    foreach ($dados['children'] as $child) {
                        echo '<option value="' . htmlspecialchars($child['id']) . '" data-level="child">' . htmlspecialchars($child['nome']) . '</option>';
                    }
                }
            }
            ?>
        </select>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar Produto</button>
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
