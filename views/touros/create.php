<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';

$racaOptions = ['Holandês', 'Angus', 'Jersey', 'Gir', 'Nelore', 'Outra'];
?>

<h2 class="page-title">Adicionar Novo Touro</h2>

<form action="../../controllers/TouroController.php" method="post">
    <input type="hidden" name="action" value="create">
    
    <div>
        <label for="nome">Nome do Touro:</label>
        <input type="text" id="nome" name="nome" required>
    </div>

    <div>
        <label for="raca">Raça:</label>
        <select id="raca" name="raca">
            <option value="">Selecione a Raça</option>
            <?php foreach ($racaOptions as $raca): ?>
                <option value="<?php echo htmlspecialchars($raca); ?>"><?php echo htmlspecialchars($raca); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="3"></textarea>
    </div>

    <div>
        <input type="checkbox" name="ativo" id="ativo" value="1" checked>
        <label for="ativo">Ativo</label>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>