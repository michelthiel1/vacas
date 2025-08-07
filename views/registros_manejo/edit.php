<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/RegistroManejo.php';
require_once __DIR__ . '/../../models/Gado.php';

$registro = new RegistroManejo($pdo);
$registro->id = $_GET['id'] ?? die('ID não fornecido.');
if (!$registro->readOne()) {
    die('Registro de manejo não encontrado.');
}

$gado = new Gado($pdo);
$stmt_vacas = $gado->readBrincos();
$tipos = ['BST','Diagnostico','Protocolo de Saúde','Secagem','Pré-Parto','Vacinas'];
?>

<h2 class="page-title">Editar Registro de Manejo</h2>

<form action="../../controllers/RegistroManejoController.php" method="post">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($registro->id); ?>">

    <div>
        <label for="id_gado">Animal / Rebanho:</label>
        <select name="id_gado" id="id_gado" required>
            <option value="">Selecione um animal...</option>
            <option value="rebanho" <?php echo $registro->aplicado_rebanho ? 'selected' : ''; ?> style="font-weight: bold; background-color: #eee;">REBANHO COMPLETO</option>
            <?php while ($row = $stmt_vacas->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo ($registro->id_gado == $row['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['brinco'] . ' - ' . $row['nome']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div>
        <label>Tipo de Manejo:</label>
        <div class="form-group-radio-inline">
            <?php foreach ($tipos as $tipo): ?>
                <div>
                    <input type="radio" id="tipo_<?php echo strtolower(str_replace(' ', '', $tipo)); ?>" name="tipo_manejo" value="<?php echo $tipo; ?>" <?php echo ($registro->tipo_manejo == $tipo) ? 'checked' : ''; ?>>
                    <label for="tipo_<?php echo strtolower(str_replace(' ', '', $tipo)); ?>"><?php echo $tipo; ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div>
        <label for="id_manejo">Escolha do Manejo:</label>
        <select name="id_manejo" id="id_manejo" required>
            <option value="">Aguardando seleção do tipo...</option>
        </select>
    </div>

    <div>
        <label for="data_aplicacao">Data de Aplicação:</label>
        <input type="date" name="data_aplicacao" id="data_aplicacao" value="<?php echo htmlspecialchars($registro->data_aplicacao); ?>" required>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea name="observacoes" id="observacoes" rows="3"><?php echo htmlspecialchars($registro->observacoes); ?></textarea>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Atualizar Registro</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    $('#id_gado').select2({ placeholder: "Pesquisar ou selecionar..." });

    const radiosTipoManejo = document.querySelectorAll('input[name="tipo_manejo"]');
    const selectManejo = $('#id_manejo');
    
    const tipoInicial = "<?php echo $registro->tipo_manejo; ?>";
    const manejoInicialId = "<?php echo $registro->id_manejo; ?>";

    function carregarManejos(tipo, idSelecionado = null) {
        selectManejo.html('<option value="">Carregando...</option>').prop('disabled', true);

        fetch(`../../controllers/ManejoController.php?action=get_by_type&tipo=${tipo}`)
            .then(response => response.json())
            .then(data => {
                selectManejo.html('<option value="">Selecione o manejo...</option>');
                
                if (data.success && data.manejos.length > 0) {
                    data.manejos.forEach(manejo => {
                        const isSelected = (idSelecionado && manejo.id == idSelecionado);
                        const option = new Option(manejo.nome, manejo.id, isSelected, isSelected);
                        selectManejo.append(option);
                    });
                    selectManejo.prop('disabled', false);
                } else {
                    selectManejo.html('<option value="">Nenhum manejo cadastrado</option>').prop('disabled', true);
                }
                
                // Sempre reinicializa o Select2
                if (selectManejo.hasClass("select2-hidden-accessible")) {
                    selectManejo.select2('destroy');
                }
                selectManejo.select2({
                    placeholder: "Selecione o manejo..."
                });
                
                // Garante que o valor correto seja exibido
                selectManejo.val(idSelecionado).trigger('change');
            });
    }

    radiosTipoManejo.forEach(radio => {
        radio.addEventListener('change', function() {
            carregarManejos(this.value, null);
        });
    });

    if (tipoInicial) {
        carregarManejos(tipoInicial, manejoInicialId);
    }
});
</script>