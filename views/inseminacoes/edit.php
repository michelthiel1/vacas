<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Inseminacao.php';
require_once __DIR__ . '/../../models/Gado.php'; 
require_once __DIR__ . '/../../models/Touro.php'; 
require_once __DIR__ . '/../../models/Inseminador.php'; 

$inseminacao = new Inseminacao($pdo);
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

$inseminacao->id = $_GET['id'] ?? die('ID da inseminação não especificado.');

if ($inseminacao->readOne()) {
    // Dados da inseminação carregados
} else {
    $_SESSION['message'] = "Inseminação não encontrada.";
    header('Location: index.php');
    exit();
}
?>

<h2 class="page-title">Editar Inseminação</h2>

<form action="../../controllers/InseminacaoController.php" method="post">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($inseminacao->id); ?>">

    <div>
        <label>Tipo:</label>
        <div class="form-group-radio-inline">
            <div>
                <input type="radio" id="tipo_iatf" name="tipo" value="IATF" <?php echo ($inseminacao->tipo == 'IATF') ? 'checked' : ''; ?>> <label for="tipo_iatf">IATF</label> </div>
            <div>
                <input type="radio" id="tipo_cio" name="tipo" value="Cio" <?php echo ($inseminacao->tipo == 'Cio') ? 'checked' : ''; ?>>
                <label for="tipo_cio">Cio</label>
            </div>
        </div>
    </div>

    <div>
        <label for="id_vaca_select">Brinco da Vaca:</label>
        <select id="id_vaca_select" name="id_vaca" required>
            <option value="">Selecione o Brinco</option>
            <?php foreach ($vacas_para_select as $id_vaca_opt => $brinco_vaca_opt): ?>
                <option value="<?php echo $id_vaca_opt; ?>" <?php echo ($inseminacao->id_vaca == $id_vaca_opt) ? 'selected' : ''; ?>>
                    <?php echo $brinco_vaca_opt; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="id_touro_select">Touro/Sêmen:</label>
        <select id="id_touro_select" name="id_touro" required>
            <option value="">Selecione o Touro/Sêmen</option>
            <?php foreach ($touros_para_select as $id_touro_opt => $nome_touro_opt): ?>
                <option value="<?php echo $id_touro_opt; ?>" <?php echo ($inseminacao->id_touro == $id_touro_opt) ? 'selected' : ''; ?>>
                    <?php echo $nome_touro_opt; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="data_inseminacao">Data da Inseminação:</label>
        <input type="date" id="data_inseminacao" name="data_inseminacao" value="<?php echo htmlspecialchars($inseminacao->data_inseminacao); ?>" required>
    </div>

    <div>
        <label for="id_inseminador_select">Inseminador:</label>
        <select id="id_inseminador_select" name="id_inseminador" required>
            <option value="">Selecione o Inseminador</option>
            <?php foreach ($inseminadores_para_select as $id_inseminador_opt => $nome_inseminador_opt): ?>
                <option value="<?php echo $id_inseminador_opt; ?>" <?php echo ($inseminacao->id_inseminador == $id_inseminador_opt) ? 'selected' : ''; ?>>
                    <?php echo $nome_inseminador_opt; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="4"><?php echo htmlspecialchars($inseminacao->observacoes); ?></textarea>
    </div>

    <div>
        <label for="status_inseminacao">Status da Inseminação:</label>
        <select id="status_inseminacao" name="status_inseminacao" required>
            <option value="Aguardando Diagnostico" <?php echo ($inseminacao->status_inseminacao == 'Aguardando Diagnostico') ? 'selected' : ''; ?>>Aguardando Diagnóstico</option>
            <option value="Confirmada (Prenha)" <?php echo ($inseminacao->status_inseminacao == 'Confirmada (Prenha)') ? 'selected' : ''; ?>>Confirmada (Prenha)</option>
            <option value="Falha" <?php echo ($inseminacao->status_inseminacao == 'Falha') ? 'selected' : ''; ?>>Falha</option>
            <option value="Aborto" <?php echo ($inseminacao->status_inseminacao == 'Aborto') ? 'selected' : ''; ?>>Aborto</option>
        </select>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Atualizar Inseminação</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Select2 nos selects
        $('#id_vaca_select').select2({ 
            placeholder: "Pesquisar ou Selecionar Brinco...",
            allowClear: true 
        });