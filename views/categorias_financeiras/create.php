<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/CategoriaFinanceira.php';

$categoriaModel = new CategoriaFinanceira($pdo);
$todasCategorias = $categoriaModel->read();
// Transforma o array de categorias em um objeto JSON para ser usado pelo JavaScript
$categorias_json = json_encode($todasCategorias);
?>
<h2 class="page-title">Nova Categoria Financeira</h2>
<form action="../../controllers/CategoriaFinanceiraController.php" method="POST" class="form-compacto">
    <input type="hidden" name="action" value="create">
    
    <div>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
    </div>
    
    <div>
        <label>Tipo:</label>
        <div class="form-group-radio-inline">
            <div> <input type="radio" id="receber" name="tipo" value="RECEBER" checked> <label for="receber">Receita</label> </div>
            <div> <input type="radio" id="pagar" name="tipo" value="PAGAR"> <label for="pagar">Despesa</label> </div>
        </div>
    </div>

    <div>
        <label for="parent_id">Categoria Pai (Opcional):</label>
        <select id="parent_id" name="parent_id">
            <option value="">Nenhuma (Categoria Principal)</option>
        </select>
    </div>
    
    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script src="../../js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Pega a lista completa de categorias do PHP
    const todasCategorias = <?php echo $categorias_json; ?>;

    /**
     * Função que atualiza o dropdown de "Categoria Pai"
     * @param {string} tipoSelecionado - O tipo ('RECEBER' ou 'PAGAR')
     */
    function atualizarParentSelect(tipoSelecionado) {
        const $parentSelect = $('#parent_id');

        // Para garantir que o Select2 seja atualizado corretamente,
        // destruímos a instância antiga antes de mudar as opções.
        if ($parentSelect.hasClass('select2-hidden-accessible')) {
            $parentSelect.select2('destroy');
        }

        // Limpa as opções antigas e adiciona a opção padrão
        $parentSelect.empty();
        $parentSelect.append('<option value="">Nenhuma (Categoria Principal)</option>');

        // Filtra as categorias para mostrar apenas as do tipo selecionado
        const categoriasFiltradas = todasCategorias.filter(cat => cat.tipo === tipoSelecionado);

        // Popula o select com as categorias filtradas
        categoriasFiltradas.forEach(cat => {
            $parentSelect.append(new Option(cat.nome, cat.id));
        });

        // Recria o Select2 no elemento agora populado e estilizado
        $parentSelect.select2({
            placeholder: "Selecione se for uma sub-categoria",
            allowClear: true,
            width: '100%' // Garante que o estilo ocupe toda a largura
        });
    }

    // Listener que observa mudanças nos botões de rádio
    $('input[name="tipo"]').on('change', function() {
        atualizarParentSelect(this.value);
    });

    // Execução inicial: Popula o dropdown pela primeira vez
    // com base no rádio que já vem marcado ('RECEBER')
    atualizarParentSelect($('input[name="tipo"]:checked').val());
});
</script>