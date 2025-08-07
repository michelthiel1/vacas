<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/EnvioLeite.php';

$id = $_GET['id'] ?? die('ID não fornecido.');

$envioLeite = new EnvioLeite($pdo);
$envioLeite->id = $id;
$envioLeite->readOne();
?>

<h2 class="page-title">Editar Registro de Envio</h2>

<div class="card">
    <div class="card-body">
        <form action="../../controllers/EnvioLeiteController.php?action=update" method="post">
            <input type="hidden" name="id" value="<?php echo $envioLeite->id; ?>">
            <div class="form-group">
                <label for="data_envio">Data do Envio</label>
                <input type="date" class="form-control" id="data_envio" name="data_envio" value="<?php echo $envioLeite->data_envio; ?>" required>
            </div>
            <div class="form-group">
                <label for="litros_enviados">Litros Enviados</label>
                <input type="number" step="0.01" class="form-control" id="litros_enviados" name="litros_enviados" value="<?php echo $envioLeite->litros_enviados; ?>" required>
            </div>
			 <div class="form-group">
                <label for="leite_bezerros">Leite Bezerros (L)</label>
                <input type="number" class="form-control" id="leite_bezerros" name="leite_bezerros" value="<?php echo $envioLeite->leite_bezerros; ?>" required>
            </div>
            <div class="form-group">
                <label for="numero_vacas">Número de Vacas em Lactação</label>
                <input type="number" class="form-control" id="numero_vacas" name="numero_vacas" value="<?php echo $envioLeite->numero_vacas; ?>" required>
            </div>
            <div class="form-group">
                <label for="observacoes">Observações</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="3"><?php echo htmlspecialchars($envioLeite->observacoes); ?></textarea>
            </div>
            <button type="submit" class="btn btn-success">Atualizar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>