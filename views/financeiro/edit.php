<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/LancamentoFinanceiro.php';
require_once __DIR__ . '/../../models/ParcelaFinanceira.php';
require_once __DIR__ . '/../../models/ContatoFinanceiro.php';
require_once __DIR__ . '/../../models/CategoriaFinanceira.php';
require_once __DIR__ . '/../../models/Estoque.php';

// --- Validação e Segurança ---
if (($_SESSION['username'] ?? '') !== 'michelthiel' && ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php');
    exit();
}
$id_lancamento = $_GET['id'] ?? null;
if (!$id_lancamento) {
    die('ID do lançamento não fornecido.');
}

// --- Carregamento de Dados ---
$lancamento = new LancamentoFinanceiro($pdo);
$lancamento->id = $id_lancamento;
if (!$lancamento->readOne()) {
    die('Lançamento não encontrado.');
}

// Carregar itens associados para determinar o tipo inicial
$itens_lancamento = $lancamento->getItens();
if (!empty($itens_lancamento)) {
    $tipo_lancamento_inicial = 'despesa_estoque';
} else {
    $tipo_lancamento_inicial = ($lancamento->tipo === 'RECEBER') ? 'receita' : 'despesa';
}

// Carregar dados de suporte para os selects
$categoriaModel = new CategoriaFinanceira($pdo);
$todasCategorias = $categoriaModel->read();
$categorias_json = json_encode($todasCategorias);

$contatoModel = new ContatoFinanceiro($pdo);
$todosContatos = $contatoModel->read()->fetchAll(PDO::FETCH_ASSOC);
$contatos_json = json_encode($todosContatos);

$estoqueModel = new Estoque($pdo);
$produtosEstoque = $estoqueModel->readAllProducts();
$produtos_estoque_json = json_encode($produtosEstoque);

$parcelaModel = new ParcelaFinanceira($pdo);
$parcelas = $parcelaModel->readByLancamentoId($id_lancamento);
$num_parcelas = count($parcelas);
$data_vencimento_inicial = !empty($parcelas) ? $parcelas[0]['data_vencimento'] : date('Y-m-d');
// Pega a forma de pagamento da primeira parcela para pré-selecionar
$forma_pagamento_salva = !empty($parcelas) ? $parcelas[0]['forma_pagamento'] : 'Boleto';

?>

<h2 class="page-title">Editar Lançamento: <?php echo htmlspecialchars($lancamento->descricao); ?></h2>

<form id="lancamentoForm" action="../../controllers/FinanceiroController.php" method="post" class="form-compacto">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($lancamento->id); ?>">

    <div>
        <label>Tipo de Lançamento (não pode ser alterado):</label>
        <div class="form-group-radio-inline" style="grid-template-columns: repeat(3, 1fr);">
            <div> <input type="radio" id="tipo_receita" name="tipo_lancamento" value="receita" <?php echo ($tipo_lancamento_inicial == 'receita') ? 'checked' : ''; ?> disabled> <label for="tipo_receita">Receita</label> </div>
            <div> <input type="radio" id="tipo_despesa" name="tipo_lancamento" value="despesa" <?php echo ($tipo_lancamento_inicial == 'despesa') ? 'checked' : ''; ?> disabled> <label for="tipo_despesa">Despesa</label> </div>
            <div> <input type="radio" id="tipo_despesa_estoque" name="tipo_lancamento" value="despesa_estoque" <?php echo ($tipo_lancamento_inicial == 'despesa_estoque') ? 'checked' : ''; ?> disabled> <label for="tipo_despesa_estoque">Despesa (Estoque)</label> </div>
        </div>
        <input type="hidden" name="tipo_lancamento" value="<?php echo $tipo_lancamento_inicial; ?>">
    </div>

    <div> <label for="descricao">Descrição:</label> <input type="text" id="descricao" name="descricao" value="<?php echo htmlspecialchars($lancamento->descricao); ?>" required> </div>

    <div id="container-categoria">
        <label for="id_categoria">Categoria:</label>
        <select id="id_categoria" name="id_categoria" required></select>
    </div>
    
    <div id="container-contato">
        <label id="label_contato" for="id_contato">Cliente/Fornecedor:</label>
        <select id="id_contato" name="id_contato"  required></select>
    </div>

    <div id="container-estoque" style="display: none;">
        <fieldset> 
            <legend>Itens da Compra</legend> 
            <div id="itens-container">
                <?php foreach ($itens_lancamento as $index => $item): ?>
                    <div class="finance-item-row" style="display: grid; grid-template-columns: 3fr 1fr 1.5fr 40px; gap: 10px; align-items: center; margin-bottom: 10px;">
                        <select name="itens[<?php echo $index; ?>][produto_id]" class="produto-select" required>
                            <?php foreach ($produtosEstoque as $p): ?>
                                <option value="<?php echo $p['Id']; ?>" <?php echo ($p['Id'] == $item['id_produto_estoque']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($p['Produto']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" name="itens[<?php echo $index; ?>][quantidade]" class="item-input" placeholder="Qtd" value="<?php echo htmlspecialchars($item['quantidade']); ?>" required />
                        <input type="text" name="itens[<?php echo $index; ?>][valor_unitario]" class="item-input" placeholder="Valor Unit." value="<?php echo htmlspecialchars($item['valor_unitario']); ?>" required />
                        <button type="button" class="btn btn-danger remove-item-btn" style="padding: 5px 10px; font-size:0.8em;">X</button>
                    </div>
                <?php endforeach; ?>
            </div> 
            <button type="button" id="add-item-btn" class="btn btn-secondary" style="margin-top: 10px;">+ Adicionar Produto</button> 
        </fieldset>
    </div>
    
    <div class="form-grid-3col">
        <div> <label for="valor_total">Valor Total (R$):</label> <input type="number" id="valor_total" name="valor_total" step="0.01" value="<?php echo htmlspecialchars($lancamento->valor_total); ?>" required> </div>
        <div> <label for="numero_parcelas">Nº de Parcelas:</label> <input type="number" id="numero_parcelas" name="numero_parcelas" value="<?php echo $num_parcelas; ?>" min="1" required> </div>
        <div> <label for="data_vencimento_inicial">Venc. da 1ª Parcela:</label> <input type="date" id="data_vencimento_inicial" name="data_vencimento_inicial" value="<?php echo htmlspecialchars($data_vencimento_inicial); ?>" required> </div>
    </div>
    
    <div>
        <label for="forma_pagamento">Forma de Pagamento Padrão:</label>
        <select id="forma_pagamento" name="forma_pagamento" required>
            <option value="Boleto" <?php echo ($forma_pagamento_salva == 'Boleto') ? 'selected' : ''; ?>>Boleto</option>
            <option value="PIX" <?php echo ($forma_pagamento_salva == 'PIX') ? 'selected' : ''; ?>>PIX</option>
            <option value="Dinheiro" <?php echo ($forma_pagamento_salva == 'Dinheiro') ? 'selected' : ''; ?>>Dinheiro</option>
            <option value="Desconto no Leite" <?php echo ($forma_pagamento_salva == 'Desconto no Leite') ? 'selected' : ''; ?>>Desconto no Leite</option>
            <option value="Outro" <?php echo ($forma_pagamento_salva == 'Outro') ? 'selected' : ''; ?>>Outro</option>
        </select>
        <p style="font-size:0.8em; color: #666;">Esta forma de pagamento será aplicada a todas as parcelas ao salvar.</p>
    </div>

    <div> <label for="observacoes">Observações Gerais:</label> <textarea name="observacoes" id="observacoes" rows="3"><?php echo htmlspecialchars($lancamento->observacoes); ?></textarea> </div>

    <div class="button-group"> <button type="submit" class="btn btn-primary">Atualizar Lançamento</button> <a href="index.php" class="btn btn-secondary">Voltar</a> </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script src="../../js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // --- DADOS VINDOS DO PHP ---
    const todasCategorias = <?php echo $categorias_json; ?>;
    const todosContatos = <?php echo $contatos_json; ?>;
    const produtosEstoque = <?php echo $produtos_estoque_json; ?>;
    let itemIndex = <?php echo count($itens_lancamento); ?>;

    const tipoLancamentoInicial = '<?php echo $tipo_lancamento_inicial; ?>';
    const contatoSalvoId = '<?php echo $lancamento->id_contato; ?>';
    const categoriaSalvaId = '<?php echo $lancamento->id_categoria; ?>';
    
    // --- FUNÇÕES DE LÓGICA ---
    function formatarOpcaoCategoria(categoria) {
        if (!categoria.id) { return categoria.text; }
        var $opcao = $(categoria.element);
        var nivel = $opcao.data('level');
        if (nivel === 'parent') { return $('<span class="select2-result-parent">' + categoria.text + '</span>'); }
        if (nivel === 'child') { return $('<span class="select2-result-child">↳ ' + categoria.text + '</span>'); }
        return categoria.text;
    }

    function recalcularTotal() {
        if ($('input[name="tipo_lancamento"]:checked').val() === 'despesa_estoque') {
            let total = 0;
            $('.finance-item-row').each(function() {
                const qtd = parseFloat($(this).find('input[name*="[quantidade]"]').val().replace(',', '.')) || 0;
                const valor = parseFloat($(this).find('input[name*="[valor_unitario]"]').val().replace(',', '.')) || 0;
                total += qtd * valor;
            });
            $('#valor_total').val(total.toFixed(2));
        }
    }
    
    function construirHierarquia(categorias) {
        const map = {};
        categorias.forEach(cat => map[cat.id] = { ...cat, children: [] });
        const roots = [];
        categorias.forEach(cat => {
            if (cat.parent_id && map[cat.parent_id]) {
                map[cat.parent_id].children.push(map[cat.id]);
            } else if (!cat.parent_id) {
                roots.push(map[cat.id]);
            }
        });
        return roots;
    }

    function atualizarFormulario(tipo) {
        const $selectCategoria = $('#id_categoria');
        const $containerCategoria = $('#container-categoria');
        const $containerEstoque = $('#container-estoque');
        const $selectContato = $('#id_contato');
        const $labelContato = $('#label_contato');

        if ($selectCategoria.hasClass('select2-hidden-accessible')) $selectCategoria.select2('destroy');
        if ($selectContato.hasClass('select2-hidden-accessible')) $selectContato.select2('destroy');
        
        $selectCategoria.empty();
        $selectContato.empty();
        
        if (tipo === 'despesa_estoque') {
            $containerEstoque.show();
            $containerCategoria.hide();
            $selectCategoria.prop('required', false);
            $('#valor_total').prop('readonly', true).css('background-color', '#e9ecef');
            recalcularTotal();
        } else {
            $containerEstoque.hide();
            $containerCategoria.show();
            $selectCategoria.prop('required', true);
            $('#valor_total').prop('readonly', false).css('background-color', 'white');
        }
        
        if (tipo === 'receita') {
            $labelContato.text('Cliente:');
            $selectContato.append(new Option('Selecione um Cliente (Opcional)', ''));
            todosContatos.filter(c => c.tipo === 'Cliente' || c.tipo === 'Outro').forEach(c => $selectContato.append(new Option(c.nome, c.id)));

            $selectCategoria.append(new Option('Selecione uma Categoria de Receita', ''));
            const categoriasReceita = construirHierarquia(todasCategorias.filter(c => c.tipo === 'RECEBER'));
            categoriasReceita.forEach(p => {
                $selectCategoria.append($('<option>', { value: p.id, 'data-level': 'parent', text: p.nome }));
                p.children.forEach(c => $selectCategoria.append($('<option>', { value: c.id, 'data-level': 'child', text: c.nome })));
            });

        } else {
            $labelContato.text('Fornecedor:');
            $selectContato.append(new Option('Selecione um Fornecedor (Opcional)', ''));
            todosContatos.filter(c => c.tipo === 'Fornecedor' || c.tipo === 'Outro').forEach(c => $selectContato.append(new Option(c.nome, c.id)));

            if (tipo === 'despesa') {
                $selectCategoria.append(new Option('Selecione uma Categoria de Despesa', ''));
                const categoriasDespesa = construirHierarquia(todasCategorias.filter(c => c.tipo === 'PAGAR'));
                categoriasDespesa.forEach(p => {
                    $selectCategoria.append($('<option>', { value: p.id, 'data-level': 'parent', text: p.nome }));
                    p.children.forEach(c => $selectCategoria.append($('<option>', { value: c.id, 'data-level': 'child', text: c.nome })));
                });
            }
        }
        
        $selectContato.val(contatoSalvoId);
        $selectCategoria.val(categoriaSalvaId);

        $selectCategoria.select2({ placeholder: "Selecione...", allowClear: true, width: '100%', templateResult: formatarOpcaoCategoria });
        $selectContato.select2({ placeholder: "Selecione...", allowClear: true, width: '100%' });
    }

    // --- EVENT LISTENERS ---
    $('#add-item-btn').on('click', function() {
        const itemHtml = `<div class="finance-item-row" style="display: grid; grid-template-columns: 3fr 1fr 1.5fr 40px; gap: 10px; align-items: center; margin-bottom: 10px;"><select name="itens[${itemIndex}][produto_id]" class="produto-select" required><option value="">Selecione...</option>${produtosEstoque.map(p => `<option value="${p.Id}">${p.Produto}</option>`).join('')}</select><input type="text" name="itens[${itemIndex}][quantidade]" class="item-input" placeholder="Qtd" required /><input type="text" name="itens[${itemIndex}][valor_unitario]" class="item-input" placeholder="Valor Unit." required /><button type="button" class="btn btn-danger remove-item-btn" style="padding: 5px 10px; font-size:0.8em;">X</button></div>`;
        $('#itens-container').append(itemHtml);
        $('.produto-select:last').select2({ placeholder: "Selecione...", width: '100%' });
        itemIndex++;
    });

    $('#itens-container').on('click', '.remove-item-btn', function() { $(this).closest('.finance-item-row').remove(); recalcularTotal(); });
    $('#itens-container').on('input', '.item-input', recalcularTotal);
    
    // --- EXECUÇÃO INICIAL ---
    $('.produto-select').select2({ placeholder: "Selecione...", width: '100%' });
    atualizarFormulario(tipoLancamentoInicial);
});
</script>