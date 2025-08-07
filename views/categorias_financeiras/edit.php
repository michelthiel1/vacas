<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php'; // <-- LINHA ADICIONADA
require_once __DIR__ . '/../../models/CategoriaFinanceira.php';

$id = $_GET['id'] ?? null;
if (!$id) { die("ID não fornecido."); }

$categoriaModel = new CategoriaFinanceira($pdo);
$categoriaModel->readOne($id);
$todasCategorias = $categoriaModel->read();
?>
<h2 class="page-title">Editar Categoria: <?php echo htmlspecialchars($categoriaModel->nome); ?></h2>
<form action="../../controllers/CategoriaFinanceiraController.php" method="POST" class="form-compacto">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo $categoriaModel->id; ?>">
    
    <div>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($categoriaModel->nome); ?>" required>
    </div>
    
    <div>
        <label>Tipo:</label>
        <div class="form-group-radio-inline">
            <div> <input type="radio" id="receber" name="tipo" value="RECEBER" <?php echo ($categoriaModel->tipo == 'RECEBER') ? 'checked' : ''; ?>> <label for="receber">Receita</label> </div>
            <div> <input type="radio" id="pagar" name="tipo" value="PAGAR" <?php echo ($categoriaModel->tipo == 'PAGAR') ? 'checked' : ''; ?>> <label for="pagar">Despesa</label> </div>
        </div>
    </div>

    <div>
        <label for="parent_id">Categoria Pai (Opcional):</label>
        <select id="parent_id" name="parent_id">
            <option value="">Nenhuma (Categoria Principal)</option>
            <?php foreach ($todasCategorias as $cat): ?>
                <?php if ($cat['id'] != $categoriaModel->id): // Impede que uma categoria seja pai de si mesma ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo ($cat['id'] == $categoriaModel->parent_id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['nome']); ?> (<?php echo $cat['tipo']; ?>)
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="index.php" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
<script src="../../js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#parent_id').select2({ placeholder: "Selecione se for uma sub-categoria", allowClear: true });
});
</script>