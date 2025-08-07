<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';

$gado = new Gado($pdo);
$stmt_gado = $gado->readBrincos();
?>

<h2 class="page-title">Registrar Nova Pesagem</h2>

<form action="../../controllers/PesagemController.php" method="post">
    <input type="hidden" name="action" value="create">

    <div>
        <label for="id_gado">Animal:</label>
        <select id="id_gado" name="id_gado" required>
            <option value="">Selecione um animal...</option>
            <?php while ($row = $stmt_gado->fetch(PDO::FETCH_ASSOC)): ?>
                <option value="<?php echo $row['id']; ?>">
                    <?php echo htmlspecialchars($row['brinco'] . ' - ' . $row['nome']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div>
        <label for="peso">Peso (kg):</label>
        <input type="number" id="peso" name="peso" step="1" required placeholder="Ex: 450">
    </div>

    <div>
        <label for="data_pesagem">Data da Pesagem:</label>
        <input type="date" id="data_pesagem" name="data_pesagem" value="<?php echo date('Y-m-d'); ?>" required>
    </div>
    
    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="3"></textarea>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar Pesagem</button>
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