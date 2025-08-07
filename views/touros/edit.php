<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Touro.php';

$touro = new Touro($pdo);
$touro->id = $_GET['id'] ?? die('ID do touro não fornecido.');

if (!$touro->readOne()) {
    die('Touro não encontrado.');
}

$racaOptions = ['Holandês', 'Angus', 'Jersey', 'Gir', 'Nelore', 'Outra'];
?>

<h2 class="page-title">Editar Touro</h2>
<form action="../../controllers/TouroController.php" method="post">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($touro->id); ?>">
    
    <div>
        <label for="nome">Nome do Touro:</label>
        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($touro->nome); ?>" required>
    </div>
    <div>
        <label for="raca">Raça:</label>
        <select id="raca" name="raca">
            <option value="">Selecione a Raça</option>
            <?php foreach ($racaOptions as $raca): ?>
                <option value="<?php echo htmlspecialchars($raca); ?>" <?php echo ($touro->raca == $raca) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($raca); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($touro->observacoes); ?></textarea>
    </div>

    <div>
        <input type="checkbox" name="ativo" id="ativo" value="1" <?php echo ($touro->ativo) ? 'checked' : ''; ?>>
        <label for="ativo">Ativo</label>
    </div>
    <div class="button-group">
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>