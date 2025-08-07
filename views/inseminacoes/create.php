<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php'; 
require_once __DIR__ . '/../../models/Gado.php'; 
require_once __DIR__ . '/../../models/Touro.php'; 
require_once __DIR__ . '/../../models/Inseminador.php'; 
require_once __DIR__ . '/../../models/Inseminacao.php'; 

$gado = new Gado($pdo); 
$touro = new Touro($pdo); 
$inseminador = new Inseminador($pdo); 

// IDs e Brincos das vacas para o select
$vacas_para_select = [];
$query_vacas = "SELECT id, brinco FROM gado WHERE ativo = 1 ORDER BY brinco ASC";
$stmt_vacas = $pdo->prepare($query_vacas);
$stmt_vacas->execute();
while ($row = $stmt_vacas->fetch(PDO::FETCH_ASSOC)) {
    $vacas_para_select[$row['id']] = htmlspecialchars($row['brinco']); // Armazena ID => Brinco
}

// IDs e Nomes dos touros para o select
$touros_para_select = [];
$stmt_touros = $touro->read();
while ($row = $stmt_touros->fetch(PDO::FETCH_ASSOC)) {
    $touros_para_select[$row['id']] = htmlspecialchars($row['nome']);
}

// IDs e Nomes dos inseminadores para o select
$inseminadores_para_select = [];
$stmt_inseminadores = $inseminador->read();
while ($row = $stmt_inseminadores->fetch(PDO::FETCH_ASSOC)) {
    $inseminadores_para_select[$row['id']] = htmlspecialchars($row['nome']);
}
?>

<h2 class="page-title">Cadastrar Inseminação</h2>

<form action="../../controllers/InseminacaoController.php" method="post">
    <input type="hidden" name="action" value="create">

    <div>
        <label>Tipo:</label>
        <div class="form-group-radio-inline">
            <div>
                <input type="radio" id="tipo_iatf" name="tipo" value="IATF" checked> <label for="tipo_iatf">IATF</label> </div>
            <div>
                <input type="radio" id="tipo_cio" name="tipo" value="Cio">
                <label for="tipo_cio">Cio</label>
            </div>
        </div>
    </div>

    <div>
        <label for="id_vaca_select">Brinco da Vaca:</label>
        <select id="id_vaca_select" name="id_vaca" required>
            <option value="">Selecione o Brinco</option>
            <?php foreach ($vacas_para_select as $id_vaca => $brinco_vaca): ?>
                <option value="<?php echo $id_vaca; ?>"><?php echo $brinco_vaca; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="id_touro_select">Touro/Sêmen:</label>
        <select id="id_touro_select" name="id_touro" required>
            <option value="">Selecione o Touro/Sêmen</option>
            <?php foreach ($touros_para_select as $id_touro => $nome_touro): ?>
                <option value="<?php echo $id_touro; ?>"><?php echo $nome_touro; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="data_inseminacao">Data da Inseminação:</label>
        <input type="date" id="data_inseminacao" name="data_inseminacao" required>
    </div>

    <div>
        <label for="id_inseminador_select">Inseminador:</label>
        <select id="id_inseminador_select" name="id_inseminador" required>
            <option value="">Selecione o Inseminador</option>
            <?php foreach ($inseminadores_para_select as $id_inseminador => $nome_inseminador): ?>
                <option value="<?php echo $id_inseminador; ?>"><?php echo $nome_inseminador; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="4"></textarea>
    </div>

    <div>
        <label for="status_inseminacao">Status da Inseminação:</label>
        <select id="status_inseminacao" name="status_inseminacao" required>
            <option value="Aguardando Diagnostico" selected>Aguardando Diagnóstico</option>
            <option value="Confirmada (Prenha)">Confirmada (Prenha)</option>
            <option value="Falha">Falha</option>
            <option value="Aborto">Aborto</option>
        </select>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar Inseminação</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const day = String(today.getDate()).padStart(2, '0');
        document.getElementById('data_inseminacao').value = `${year}-${month}-${day}`;

        // Inicializar Select2 nos selects
        $('#id_vaca_select').select2({
            placeholder: "Pesquisar ou Selecionar Brinco...",
            allowClear: true 
        });
        $('#id_touro_select').select2({
            placeholder: "Pesquisar ou Selecionar Touro/Sêmen...",
            allowClear: true
        });
        $('#id_inseminador_select').select2({
            placeholder: "Pesquisar ou Selecionar Inseminador...",
            allowClear: true
        });
        $('#status_inseminacao').select2({
            placeholder: "Selecione o Status...",
            minimumResultsForSearch: Infinity 
        });
    });
</script>