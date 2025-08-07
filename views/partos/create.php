<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';
require_once __DIR__ . '/../../models/Touro.php';
require_once __DIR__ . '/../../models/Inseminacao.php';

$gado = new Gado($pdo);
$touro = new Touro($pdo);
$inseminacao = new Inseminacao($pdo);

// Opções de sexo da cria
$sexoCriaOptions = ['Aborto', 'Indução', 'Macho', 'Fêmea', 'Gêmeos Machos', 'Gêmeos Fêmea', 'Gêmeos macho e fêmea'];

// Buscar vacas com status 'Prenha'
$vacas_prenhas = [];
$query_vacas_prenhas = "SELECT id, brinco, nome FROM gado WHERE status = 'Prenha' AND ativo = 1 ORDER BY brinco ASC";
$stmt_vacas_prenhas = $pdo->prepare($query_vacas_prenhas);
$stmt_vacas_prenhas->execute();
while ($row = $stmt_vacas_prenhas->fetch(PDO::FETCH_ASSOC)) {
    $vacas_prenhas[$row['id']] = htmlspecialchars($row['brinco'] . ' - ' . $row['nome']);
}

// Buscar todos os touros para o select
$touros_para_select = [];
$stmt_touros = $touro->readAllNames();
while ($row = $stmt_touros->fetch(PDO::FETCH_ASSOC)) {
    $touros_para_select[$row['id']] = htmlspecialchars($row['nome']);
}

// Valor padrão para data do parto (dia atual)
$data_parto_default = date('Y-m-d');
?>

<h2 class="page-title">Registrar Parto</h2>

<form action="../../controllers/PartoController.php" method="post">
    <input type="hidden" name="action" value="create">

    <div>
        <label for="id_vaca_select">Brinco da Vaca:</label>
        <select id="id_vaca_select" name="id_vaca" required>
            <option value="">Selecione a Vaca Prenha</option>
            <?php foreach ($vacas_prenhas as $id_vaca => $brinco_nome_vaca): ?>
                <option value="<?php echo $id_vaca; ?>"><?php echo $brinco_nome_vaca; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="id_touro_select">Touro (Última Inseminação):</label>
        <select id="id_touro_select" name="id_touro" required>
            <option value="">Selecione o Touro</option>
            <?php foreach ($touros_para_select as $id_touro => $nome_touro): ?>
                <option value="<?php echo $id_touro; ?>"><?php echo $nome_touro; ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="sexo_cria">Sexo da Cria:</label>
        <select id="sexo_cria" name="sexo_cria" required>
            <option value="">Selecione</option>
            <?php foreach ($sexoCriaOptions as $sexo): ?>
                <option value="<?php echo htmlspecialchars($sexo); ?>"><?php echo htmlspecialchars($sexo); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="data_parto">Data do Parto:</label>
        <input type="date" id="data_parto" name="data_parto" value="<?php echo htmlspecialchars($data_parto_default); ?>" required>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="4"></textarea>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Registrar Parto</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../js/select2.min.js"></script>
<script>
// Usa $(document).ready() para garantir que tudo foi carregado antes de executar o script
$(document).ready(function() {

    // 1. Inicializa os seletores com a biblioteca Select2
    $('#id_vaca_select').select2({
        placeholder: "Pesquisar ou Selecionar Vaca...",
        allowClear: true
    });
    $('#id_touro_select').select2({
        placeholder: "Aguardando seleção da vaca...",
        allowClear: true
    });
    $('#sexo_cria').select2({
        placeholder: "Selecione o Sexo da Cria...",
        minimumResultsForSearch: Infinity
    });

    // 2. Anexa o "ouvinte" de eventos do Select2 para quando uma vaca é selecionada
    $('#id_vaca_select').on('select2:select', function(e) {
        var data = e.params.data;
        
        // Verifica se um item com ID foi selecionado
        if (data && data.id) {
            var vacaId = data.id;

            // Define um texto de "carregando" no campo do touro
            $('#id_touro_select').val(null).trigger('change');
            
            // Busca o touro no controller via AJAX
            fetch(`../../controllers/PartoController.php?action=get_last_insemination_touro&id_vaca=${vacaId}`)
                .then(response => response.json())
                .then(resultData => {
                    if (resultData.success && resultData.id_touro) {
                        // Se encontrou o touro, define o valor e atualiza o Select2
                        $('#id_touro_select').val(resultData.id_touro).trigger('change');
                    } else {
                        // Se não encontrou, avisa no console e deixa o campo para seleção manual
                        console.warn(resultData.message || "Touro da inseminação não encontrado.");
                        $('#id_touro_select').select2({ placeholder: 'Selecione o Touro' });
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar touro:', error);
                    $('#id_touro_select').select2({ placeholder: 'Erro ao buscar' });
                });
        }
    });

    // 3. Limpa o campo do touro se o usuário remover a seleção da vaca
    $('#id_vaca_select').on('select2:unselect', function(e) {
        $('#id_touro_select').val(null).trigger('change');
    });
});
</script>