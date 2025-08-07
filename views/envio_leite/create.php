<?php 
include_once __DIR__ . '/../../includes/header.php'; 

// Verifica se uma data foi passada pela URL. Se não, usa a data atual.
$data_envio = $_GET['data'] ?? date('Y-m-d');
?>

<h2 class="page-title">Registrar Novo Envio de Leite</h2>

<div class="card">
    <div class="card-body">
        <form action="../../controllers/EnvioLeiteController.php?action=create" method="post">
            <div class="form-group">
                <label for="data_envio">Data do Envio</label>
                <input type="date" class="form-control" id="data_envio" name="data_envio" value="<?php echo htmlspecialchars($data_envio); ?>" readonly required>
            </div>
            <div class="form-group">
                <label for="litros_enviados">Litros Enviados</label>
                <input type="number" class="form-control" id="litros_enviados" name="litros_enviados" placeholder="Ex: 850" required>
            </div>
			 <div class="form-group">
                <label for="leite_bezerros">Leite Bezerros (L)</label>
                <input type="number" class="form-control" id="leite_bezerros" name="leite_bezerros" placeholder="Ex: 50" value="0" required>
            </div>
            <div class="form-group">
                <label for="numero_vacas">Número de Vacas em Lactação</label>
                <input type="number" class="form-control" id="numero_vacas" name="numero_vacas" placeholder="Ex: 30" required>
            </div>
            <div class="form-group">
                <label for="observacoes">Observações</label>
                <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Alguma observação sobre o envio? (Opcional)"></textarea>
            </div>
            <button type="submit" class="btn btn-success">Salvar</button>
            <a href="index.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>