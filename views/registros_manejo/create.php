<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';

$gado = new Gado($pdo);
$stmt_vacas = $gado->readBrincos();

// Bloco para pré-selecionar o animal vindo da pesquisa
$id_gado_selecionado = $_GET['id_gado'] ?? null;
if (empty($id_gado_selecionado)) {
    $brinco_selecionado = $_GET['brinco'] ?? null;
    if ($brinco_selecionado) {
        $id_gado_selecionado = $gado->getIdByBrinco($brinco_selecionado);
    }
}


?>

<h2 class="page-title">Registrar Aplicação de Manejo</h2>

<form action="../../controllers/RegistroManejoController.php" method="post">
    <input type="hidden" name="action" value="create">

    <div>
        <label for="id_gado">Animal / Rebanho:</label>
        <select name="id_gado" id="id_gado" required>
            <option value="">Selecione um animal...</option>
            <option value="rebanho" style="font-weight: bold; background-color: #eee;">REBANHO COMPLETO</option>
            <?php while ($row = $stmt_vacas->fetch(PDO::FETCH_ASSOC)): ?>
               <option value="<?php echo $row['id']; ?>" <?php echo ($id_gado_selecionado == $row['id']) ? 'selected' : ''; ?>>
    <?php echo htmlspecialchars($row['brinco'] . ' - ' . $row['nome']); ?>
</option>
            <?php endwhile; ?>
        </select>
    </div>

    <div>
        <label>Tipo de Manejo:</label>
        <div class="form-group-radio-inline">
            <div><input type="radio" id="tipo_bst" name="tipo_manejo" value="BST"><label for="tipo_bst">BST</label></div>
            <div><input type="radio" id="tipo_diag" name="tipo_manejo" value="Diagnostico"><label for="tipo_diag">Diagnóstico</label></div>
            <div><input type="radio" id="tipo_secagem" name="tipo_manejo" value="Secagem"><label for="tipo_secagem">Secagem</label></div>
            <div><input type="radio" id="tipo_preparto" name="tipo_manejo" value="Pré-Parto"><label for="tipo_preparto">Pré-Parto</label></div>
            <div><input type="radio" id="tipo_vac" name="tipo_manejo" value="Vacinas"><label for="tipo_vac">Vacinas</label></div>
            <div><input type="radio" id="tipo_proto" name="tipo_manejo" value="Protocolo de Saúde"><label for="tipo_proto">Protocolo de Saúde</label></div>
        </div>
    </div>

    <div>
        <label for="id_manejo">Escolha do Manejo:</label>
        <select name="id_manejo" id="id_manejo" required disabled>
            <option value="">Selecione um tipo de manejo primeiro</option>
        </select>
    </div>

    <div>
        <label for="data_aplicacao">Data de Aplicação:</label>
        <input type="date" name="data_aplicacao" id="data_aplicacao" value="<?php echo date('Y-m-d'); ?>" required>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea name="observacoes" id="observacoes" rows="3"></textarea>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar Registro</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#id_gado').select2({ placeholder: "Pesquisar ou selecionar..." });
    $('#id_manejo').select2({ placeholder: "Selecione um tipo de manejo primeiro" });

    const radiosTipoManejo = document.querySelectorAll('input[name="tipo_manejo"]');
    const selectManejo = $('#id_manejo');

    // Função para carregar os manejos baseados no tipo
    function carregarManejos(tipoSelecionado) {
        selectManejo.html('<option value="">Carregando...</option>').prop('disabled', true);
        fetch(`../../controllers/ManejoController.php?action=get_by_type&tipo=${tipoSelecionado}`)
            .then(response => response.json())
            .then(data => {
                selectManejo.html('<option value="">Selecione o manejo...</option>');
                if (data.success && data.manejos.length > 0) {
                    data.manejos.forEach(manejo => {
                        const option = new Option(manejo.nome, manejo.id);
                        selectManejo.append(option);
                    });
                    selectManejo.prop('disabled', false);
                } else {
                    selectManejo.html('<option value="">Nenhum manejo cadastrado para este tipo</option>').prop('disabled', true);
                }
            })
            .catch(error => {
                console.error('Erro ao buscar manejos:', error);
                selectManejo.html('<option value="">Erro ao carregar</option>').prop('disabled', true);
            });
    }

    radiosTipoManejo.forEach(radio => {
        radio.addEventListener('change', function() {
            carregarManejos(this.value);
        });
    });

    // --- INÍCIO DO NOVO SCRIPT PARA PRÉ-SELEÇÃO ---
    const urlParams = new URLSearchParams(window.location.search);
    const tipoFromUrl = urlParams.get('tipo');

    if (tipoFromUrl) {
        // Encontra o radio button correspondente ao valor da URL
        const radioParaMarcar = document.querySelector(`input[name="tipo_manejo"][value="${tipoFromUrl}"]`);
        if (radioParaMarcar) {
            radioParaMarcar.checked = true;
            // Dispara o evento 'change' para acionar o carregamento do select dependente
            radioParaMarcar.dispatchEvent(new Event('change'));
        }
    }
    // --- FIM DO NOVO SCRIPT ---
});
</script>