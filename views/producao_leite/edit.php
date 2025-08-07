<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/ProducaoLeite.php';
require_once __DIR__ . '/../../models/Gado.php';

$producao = new ProducaoLeite($pdo);
$producao->id = $_GET['id'] ?? die('ID não fornecido.');
if (!$producao->readOne()) {
    die('Registro de produção não encontrado.');
}

$gado = new Gado($pdo);
$stmt_gado = $gado->readBrincos();
?>

<h2 class="page-title">Editar Produção Diária</h2>

<form action="../../controllers/ProducaoLeiteController.php" method="post">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($producao->id); ?>">

    <div>
        <label for="id_gado">Animal:</label>
        <select id="id_gado" name="id_gado" required>
            <option value="">Selecione um animal...</option>
            <?php while ($row = $stmt_gado->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo $row['id']; ?>" <?php echo ($producao->id_gado == $row['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($row['brinco'] . ' - ' . $row['nome']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div>
        <label for="data_producao">Data da Produção:</label>
        <input type="date" id="data_producao" name="data_producao" value="<?php echo htmlspecialchars($producao->data_producao); ?>" required>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
        <div>
            <label for="ordenha_1">1ª Ordenha (L):</label>
            <input type="number" id="ordenha_1" name="ordenha_1" step="0.1" value="<?php echo htmlspecialchars($producao->ordenha_1); ?>" required>
        </div>
        <div>
            <label for="ordenha_2">2ª Ordenha (L):</label>
            <input type="number" id="ordenha_2" name="ordenha_2" step="0.1" value="<?php echo htmlspecialchars($producao->ordenha_2); ?>" required>
        </div>
        <div>
            <label for="ordenha_3">3ª Ordenha (L):</label>
            <input type="number" id="ordenha_3" name="ordenha_3" step="0.1" value="<?php echo htmlspecialchars($producao->ordenha_3); ?>" required>
        </div>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($producao->observacoes); ?></textarea>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Atualizar Produção</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script src="../../js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#id_gado').select2({
        placeholder: "Pesquisar ou selecionar animal..."
    });
});
</script>