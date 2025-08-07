<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/CategoriaFinanceira.php';
require_once __DIR__ . '/../../models/ContatoFinanceiro.php';
require_once __DIR__ . '/../../models/Estoque.php';
require_once __DIR__ . '/../../models/Touro.php'; // Adicionado para buscar touros

// Segurança
if (($_SESSION['username'] ?? '') !== 'michelthiel' && ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Carrega TODOS os dados necessários uma única vez
$categoriaModel = new CategoriaFinanceira($pdo);
$todasCategorias = $categoriaModel->read();
$categorias_json = json_encode($todasCategorias);

$contatoModel = new ContatoFinanceiro($pdo);
$todosContatos = $contatoModel->read()->fetchAll(PDO::FETCH_ASSOC);
$contatos_json = json_encode($todosContatos);

$estoqueModel = new Estoque($pdo);
$produtosEstoque = $estoqueModel->readAllProducts();
$produtos_estoque_json = json_encode($produtosEstoque);

// Busca a lista de touros para o seletor dinâmico
$touroModel = new Touro($pdo);
$todosTouros = $touroModel->readAllNames()->fetchAll(PDO::FETCH_ASSOC);
$touros_json = json_encode($todosTouros);

// LÓGICA PHP PARA RENDERIZAR O ESTADO INICIAL
$categoriasIniciais = array_filter($todasCategorias, fn($cat) => $cat['tipo'] === 'RECEBER');
$contatosIniciais = array_filter($todosContatos, fn($c) => $c['tipo'] === 'Cliente' || $c['tipo'] === 'Outro');

function construirHierarquia($categorias) {
    $map = [];
    foreach ($categorias as $categoria) {
        $map[$categoria['id']] = $categoria;
        $map[$categoria['id']]['children'] = [];
    }
    $roots = [];
    foreach ($map as $id => &$categoria) {
        if (isset($categoria['parent_id']) && isset($map[$categoria['parent_id']])) {
            $map[$categoria['parent_id']]['children'][] =& $categoria;
        } else {
            $roots[] =& $categoria;
        }
    }
    unset($categoria);
    return $roots;
}

$categoriasHierarquiaInicial = construirHierarquia($categoriasIniciais);
?>

<h2 class="page-title">Novo Lançamento Financeiro</h2>

<form id="lancamentoForm" action="../../controllers/FinanceiroController.php" method="post" class="form-compacto">
    <input type="hidden" name="action" value="create">

    <div>
        <label>Tipo de Lançamento:</label>
        <div class="form-group-radio-inline" style="grid-template-columns: repeat(3, 1fr);">
            <div> <input type="radio" id="tipo_receita" name="tipo_lancamento" value="receita" checked> <label for="tipo_receita">Receita</label> </div>
            <div> <input type="radio" id="tipo_despesa" name="tipo_lancamento" value="despesa"> <label for="tipo_despesa">Despesa</label> </div>
            <div> <input type="radio" id="tipo_despesa_estoque" name="tipo_lancamento" value="despesa_estoque"> <label for="tipo_despesa_estoque">Despesa (Estoque)</label> </div>
        </div>
    </div>

    <div> <label for="descricao">Descrição:</label> <input type="text" id="descricao" name="descricao" required> </div>

    <div id="container-categoria">
        <label for="id_categoria">Categoria:</label>
        <select id="id_categoria" name="id_categoria" required>
            <option value="">Selecione uma Categoria de Receita</option>
            <?php
            foreach ($categoriasHierarquiaInicial as $parent) {
                echo '<option value="' . htmlspecialchars($parent['id']) . '" data-level="parent">' . htmlspecialchars($parent['nome']) . '</option>';
                if (!empty($parent['children'])) {
                    foreach ($parent['children'] as $child) {
                        echo '<option value="' . htmlspecialchars($child['id']) . '" data-level="child">' . htmlspecialchars($child['nome']) . '</option>';
                    }
                }
            }
            ?>
        </select>
    </div>
    
    <div id="container-contato">
        <label id="label_contato" for="id_contato">Cliente:</label>
        <select id="id_contato" name="id_contato">
            <option value="">Selecione um Cliente (Opcional)</option>
            <?php foreach ($contatosIniciais as $c): ?>
                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['nome']); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="container-estoque" style="display: none;">
        <fieldset> <legend>Itens da Compra</legend> <div id="itens-container"></div> <button type="button" id="add-item-btn" class="btn btn-secondary" style="margin-top: 10px;">+ Adicionar Produto</button> </fieldset>
    </div>
    
    <div class="form-grid-3col">
        <div> <label for="valor_total">Valor Total (R$):</label> <input type="number" id="valor_total" name="valor_total" step="0.01" required placeholder="0.00"> </div>
        <div> <label for="numero_parcelas">Nº de Parcelas:</label> <input type="number" id="numero_parcelas" name="numero_parcelas" value="1" min="1" required> </div>
        <div> <label for="data_vencimento_inicial">Vencimento da 1ª Parcela:</label> <input type="date" id="data_vencimento_inicial" name="data_vencimento_inicial" value="<?php echo date('Y-m-d'); ?>" required> </div>
    </div>
 <div>
        <label for="forma_pagamento">Forma de Pagamento:</label>
        <select id="forma_pagamento" name="forma_pagamento" required>
            <option value="Boleto">Boleto</option>
            <option value="PIX">PIX</option>
            <option value="Dinheiro">Dinheiro</option>
            <option value="Desconto no Leite">Desconto no Leite</option>
            <option value="Outro">Outro</option>
        </select>
    </div>
    <div> <label for="observacoes">Observações Gerais:</label> <textarea name="observacoes" id="observacoes" rows="3"></textarea> </div>

    <div class="button-group"> <button type="submit" class="btn btn-primary">Salvar Lançamento</button> <a href="index.php" class="btn btn-secondary">Voltar</a> </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script src="../../js/select2.min.js"></script>
<script>
$(document).ready(function() {
    const todasCategorias = <?php echo $categorias_json; ?>;
    const todosContatos = <?php echo $contatos_json; ?>;
    const produtosEstoque = <?php echo $produtos_estoque_json; ?>;
    const todosTouros = <?php echo $touros_json; ?>; // Variável com os touros
    let itemIndex = 0;

    // Encontra o ID do produto "Sêmen" para usar como gatilho
    const idProdutoSemen = produtosEstoque.find(p => p.Produto === 'Sêmen')?.Id;

    function formatarOpcaoCategoria(categoria) { if (!categoria.id) { return categoria.text; } var $opcao = $(categoria.element); var nivel = $opcao.data('level'); if (nivel === 'parent') { return $('<span class="select2-result-parent">' + categoria.text + '</span>'); } if (nivel === 'child') { return $('<span class="select2-result-child">↳ ' + categoria.text + '</span>'); } return categoria.text; }
    function construirHierarquia(categorias) { const map = {}; categorias.forEach(cat => map[cat.id] = { ...cat, children: [] }); const roots = []; categorias.forEach(cat => { if (cat.parent_id && map[cat.parent_id]) { map[cat.parent_id].children.push(map[cat.id]); } else if (!cat.parent_id) { roots.push(map[cat.id]); } }); return roots; }

    function recalcularTotal() {
        let total = 0;
        $('.finance-item-row').each(function() {
            const qtd = parseFloat($(this).find('input[name*="[quantidade]"]').val().replace(',', '.')) || 0;
            const valor = parseFloat($(this).find('input[name*="[valor_unitario]"]').val().replace(',', '.')) || 0;
            total += qtd * valor;
        });
        $('#valor_total').val(total.toFixed(2));
    }

    function atualizarFormulario(tipo) {
         const $selectCategoria = $('#id_categoria'); const $containerCategoria = $('#container-categoria'); const $containerEstoque = $('#container-estoque'); const $selectContato = $('#id_contato'); const $labelContato = $('#label_contato'); if ($selectCategoria.hasClass('select2-hidden-accessible')) $selectCategoria.select2('destroy'); if ($selectContato.hasClass('select2-hidden-accessible')) $selectContato.select2('destroy'); $selectCategoria.empty(); $selectContato.empty(); if (tipo === 'despesa_estoque') { $containerEstoque.show(); $containerCategoria.hide(); $selectCategoria.prop('required', false); $('#valor_total').prop('readonly', true).css('background-color', '#e9ecef'); recalcularTotal(); } else { $containerEstoque.hide(); $containerCategoria.show(); $selectCategoria.prop('required', true); $('#valor_total').prop('readonly', false).css('background-color', 'white'); } if (tipo === 'receita') { $labelContato.text('Cliente:'); $selectContato.append(new Option('Selecione um Cliente (Opcional)', '')); todosContatos.filter(c => c.tipo === 'Cliente' || c.tipo === 'Outro').forEach(c => $selectContato.append(new Option(c.nome, c.id))); $selectCategoria.append(new Option('Selecione uma Categoria de Receita', '')); const categoriasReceita = construirHierarquia(todasCategorias.filter(c => c.tipo === 'RECEBER')); categoriasReceita.forEach(p => { $selectCategoria.append($('<option>', { value: p.id, 'data-level': 'parent', text: p.nome })); p.children.forEach(c => $selectCategoria.append($('<option>', { value: c.id, 'data-level': 'child', text: c.nome }))); }); } else { $labelContato.text('Fornecedor:'); $selectContato.append(new Option('Selecione um Fornecedor (Opcional)', '')); todosContatos.filter(c => c.tipo === 'Fornecedor' || c.tipo === 'Outro').forEach(c => $selectContato.append(new Option(c.nome, c.id))); if (tipo === 'despesa') { $selectCategoria.append(new Option('Selecione uma Categoria de Despesa', '')); const categoriasDespesa = construirHierarquia(todasCategorias.filter(c => c.tipo === 'PAGAR')); categoriasDespesa.forEach(p => { $selectCategoria.append($('<option>', { value: p.id, 'data-level': 'parent', text: p.nome })); p.children.forEach(c => $selectCategoria.append($('<option>', { value: c.id, 'data-level': 'child', text: c.nome }))); }); } } $selectCategoria.select2({ placeholder: "Selecione...", allowClear: true, width: '100%', templateResult: formatarOpcaoCategoria }); $selectContato.select2({ placeholder: "Selecione...", allowClear: true, width: '100%' });
    }

    // --- EVENT LISTENERS ---
    $('input[name="tipo_lancamento"]').on('change', function() {
        atualizarFormulario(this.value);
        $('#itens-container').empty(); // Limpa itens ao mudar o tipo
    });

    $('#add-item-btn').on('click', function() {
        const itemHtml = `<div class="finance-item-row" style="margin-bottom: 10px;">
                            <div style="grid-column: 1 / -1;">
                                <select name="itens[${itemIndex}][produto_id]" class="produto-select-trigger" required>
                                    <option value="">Selecione um Produto...</option>
                                    ${produtosEstoque.map(p => `<option value="${p.Id}">${p.Produto}</option>`).join('')}
                                </select>
                            </div>
                            <div class="item-details-grid" style="grid-column: 1 / -1; display: none; grid-template-columns: 2fr 1fr 1fr auto; gap: 8px; align-items: center; margin-top: 5px;">
                                </div>
                          </div>`;
        $('#itens-container').append(itemHtml);
        $('.produto-select-trigger:last').select2({ placeholder: "Selecione...", width: '100%' });
        itemIndex++;
    });

    $('#itens-container').on('select2:select', '.produto-select-trigger', function() {
        const selectedProductId = $(this).val();
        const detailsGrid = $(this).closest('.finance-item-row').find('.item-details-grid');
        const currentIndex = $(this).closest('.finance-item-row').index();

        let detailsHtml = '';
        if (selectedProductId == idProdutoSemen) {
            // HTML para Sêmen
            detailsHtml = `<select name="itens[${currentIndex}][touro_id]" class="touro-select" required>
                             <option value="">Selecione o Touro...</option>
                             ${todosTouros.map(t => `<option value="${t.id}">${t.nome}</option>`).join('')}
                           </select>
                           <input type="number" name="itens[${currentIndex}][quantidade]" class="item-input" placeholder="Doses" required />
                           <input type="text" name="itens[${currentIndex}][valor_unitario]" class="item-input" placeholder="Valor p/ Dose" required />
                           <button type="button" class="btn btn-danger remove-item-btn" style="padding: 5px 10px; font-size:0.8em;">X</button>`;
        } else {
            // HTML para outros produtos
            detailsHtml = `<input type="text" value="${$(this).find('option:selected').text()}" readonly style="background:#f0f0f0; border:none;" />
                           <input type="text" name="itens[${currentIndex}][quantidade]" class="item-input" placeholder="Qtd" required />
                           <input type="text" name="itens[${currentIndex}][valor_unitario]" class="item-input" placeholder="Valor Unit." required />
                           <button type="button" class="btn btn-danger remove-item-btn" style="padding: 5px 10px; font-size:0.8em;">X</button>`;
        }
        
        detailsGrid.html(detailsHtml).show();
        if (selectedProductId == idProdutoSemen) {
            detailsGrid.find('.touro-select').select2({ placeholder: "Selecione o Touro...", width: '100%' });
        }
    });

    $('#itens-container').on('click', '.remove-item-btn', function() { $(this).closest('.finance-item-row').remove(); recalcularTotal(); });
    $('#itens-container').on('input', '.item-input', recalcularTotal);

    // --- EXECUÇÃO INICIAL ---
    $('#id_categoria, #id_contato').select2({ width: '100%', templateResult: formatarOpcaoCategoria });
    atualizarFormulario($('input[name="tipo_lancamento"]:checked').val()); // Garante que o form inicialize corretamente
});
</script>