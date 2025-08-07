<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Evento.php';
require_once __DIR__ . '/../../models/Gado.php'; // Para buscar vacas

$evento = new Evento($pdo);
$gado = new Gado($pdo);

// Opções de tipo de evento
$tipoEventoOptions = ['Saúde', 'Vacina', 'Parto', 'Cio', 'Geral'];

// Buscar todas as vacas ativas (para select opcional)
$vacas_para_select = [];
$stmt_vacas = $gado->readBrincos(); // readBrincos() retorna id, brinco, nome
while ($row = $stmt_vacas->fetch(PDO::FETCH_ASSOC)) {
    $vacas_para_select[$row['id']] = htmlspecialchars($row['brinco'] . ' - ' . $row['nome']);
}

// Valor padrão para data do evento (dia atual)
$data_evento_default = date('Y-m-d');
?>

<h2 class="page-title">Criar Novo Evento</h2>

<form action="../../controllers/EventoController.php" method="post">
    <input type="hidden" name="action" value="create">

    <div>
        <label for="titulo">Título do Evento:</label>
        <input type="text" id="titulo" name="titulo" required>
    </div>

    <div>
        <label for="data_evento">Data do Evento:</label>
        <input type="date" id="data_evento" name="data_evento" value="<?php echo htmlspecialchars($data_evento_default); ?>" required>
    </div>

    <div>
        <label for="tipo_evento">Tipo de Evento:</label>
        <select id="tipo_evento" name="tipo_evento" required>
            <?php foreach ($tipoEventoOptions as $tipo): ?>
                <option value="<?php echo htmlspecialchars($tipo); ?>" <?php echo ($tipo == 'Geral' ? 'selected' : ''); ?>><?php echo htmlspecialchars($tipo); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="id_vaca_select">Vaca (Opcional):</label>
        <select id="id_vaca_select" name="id_vaca">
            <option value="">Nenhuma Vaca</option>
            <?php foreach ($vacas_para_select as $vaca_id => $brinco_nome): ?>
                <option value="<?php echo $vaca_id; ?>"><?php echo $brinco_nome; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="descricao">Descrição/Observações:</label>
        <textarea id="descricao" name="descricao" rows="4"></textarea>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar Evento</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar Select2
        $('#tipo_evento').select2({
            placeholder: "Selecione o Tipo...",
            minimumResultsForSearch: Infinity
        });
        $('#id_vaca_select').select2({
            placeholder: "Pesquisar ou Selecionar Vaca...",
            allowClear: true
        });
    });
</script>