<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Parto.php';
require_once __DIR__ . '/../../models/Gado.php';
require_once __DIR__ . '/../../models/Touro.php';

$parto = new Parto($pdo);
$gado = new Gado($pdo);
$touro = new Touro($pdo);

// Opções de sexo da cria
$sexoCriaOptions = ['Aborto', 'Indução', 'Macho', 'Fêmea', 'Gêmeos Machos', 'Gêmeos Fêmea', 'Gêmeos macho e fêmea'];

// Buscar todas as vacas ativas (prenhes ou não, para permitir corrigir)
$vacas_para_select = [];
$query_vacas = "SELECT id, brinco, nome FROM gado WHERE ativo = 1 ORDER BY brinco ASC";
$stmt_vacas = $pdo->prepare($query_vacas);
$stmt_vacas->execute();
while ($row = $stmt_vacas->fetch(PDO::FETCH_ASSOC)) {
    $vacas_para_select[$row['id']] = htmlspecialchars($row['brinco'] . ' - ' . $row['nome']);
}

// Buscar todos os touros
$touros_para_select = [];
$stmt_touros = $touro->read();
while ($row = $stmt_touros->fetch(PDO::FETCH_ASSOC)) {
    $touros_para_select[$row['id']] = htmlspecialchars($row['nome']);
}

$parto->id = $_GET['id'] ?? null;

if ($parto->id) {
    if (!$parto->readOne()) {
        $_SESSION['message'] = "Parto não encontrado ou ID inválido.";
        header('Location: index.php');
        exit();
    }
} else {
    $_SESSION['message'] = "ID do parto não especificado.";
    header('Location: index.php');
    exit();
}
?>

<h2 class="page-title">Editar Parto</h2>

<form action="../../controllers/PartoController.php" method="post">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($parto->id); ?>">

    <div>
        <label for="id_vaca_select">Brinco da Vaca:</label>
        <select id="id_vaca_select" name="id_vaca" required>
            <option value="">Selecione a Vaca</option>
            <?php foreach ($vacas_para_select as $id_vaca => $brinco_nome_vaca): ?>
                <option value="<?php echo $id_vaca; ?>" <?php echo ($parto->id_vaca == $id_vaca) ? 'selected' : ''; ?>>
                    <?php echo $brinco_nome_vaca; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="id_touro_select">Touro:</label>
        <select id="id_touro_select" name="id_touro">
            <option value="">Selecione o Touro (Opcional)</option>
            <?php foreach ($touros_para_select as $id_touro => $nome_touro): ?>
                <option value="<?php echo $id_touro; ?>" <?php echo ($parto->id_touro == $id_touro) ? 'selected' : ''; ?>>
                    <?php echo $nome_touro; ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="sexo_cria">Sexo da Cria:</label>
        <select id="sexo_cria" name="sexo_cria" required>
            <option value="">Selecione</option>
            <?php foreach ($sexoCriaOptions as $sexo): ?>
                <option value="<?php echo htmlspecialchars($sexo); ?>" <?php echo ($parto->sexo_cria == $sexo) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($sexo); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="data_parto">Data do Parto:</label>
        <input type="date" id="data_parto" name="data_parto" value="<?php echo htmlspecialchars($parto->data_parto); ?>" required>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="4"><?php echo htmlspecialchars($parto->observacoes); ?></textarea>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Atualizar Parto</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Select2
        $('#id_vaca_select').select2({
            placeholder: "Pesquisar ou Selecionar Vaca...",
            allowClear: true
        });
        $('#id_touro_select').select2({
            placeholder: "Pesquisar ou Selecionar Touro...",
            allowClear: true
        });
        $('#sexo_cria').select2({
            placeholder: "Selecione o Sexo da Cria...",
            minimumResultsForSearch: Infinity
        });
    });
</script>