<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Estoque.php';
require_once __DIR__ . '/../../models/Dieta.php';

$dietaModel = new Dieta($pdo);

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Lógica dinâmica para os seletores
$loteOptions = $dietaModel->getLotesAtivos();
$selectedLote = '';
$vacas_salvas_no_banco = 0; 

if (!empty($loteOptions)) {
    $lote_from_url = $_GET['lote'] ?? null;
    $selectedLote = in_array($lote_from_url, $loteOptions) ? $lote_from_url : $loteOptions[0];

    $dietaSelecionada = $dietaModel->getDietaPorLote($selectedLote);
    $vacas_salvas_no_banco = $dietaSelecionada ? (int)$dietaSelecionada['Vacas'] : 0;
}

$selectedVacas = isset($_GET['vacas']) ? (int)$_GET['vacas'] : $vacas_salvas_no_banco;

$start_range = max(0, $vacas_salvas_no_banco - 10); // Alterado de 1 para 0
$end_range = $vacas_salvas_no_banco + 10;
$vacasOptions = range($start_range, $end_range);

// Garante que o número 0 sempre esteja disponível como opção
if (!in_array(0, $vacasOptions)) {
    array_unshift($vacasOptions, 0);
}

if (!in_array($selectedVacas, $vacasOptions)) {
    $vacasOptions[] = $selectedVacas;
    sort($vacasOptions);
}

// Busca os dados da dieta principal para a tabela
$dietaData = $dietaModel->getDietaPorLoteEVaca($selectedLote, $selectedVacas, $vacas_salvas_no_banco);
$consumo10DiasTotais = $dietaModel->getConsumo10DiasTotalPorIngrediente();

// Prepara os dados da tabela principal, aplicando a regra de negócio das refeições
if (!empty($dietaData)) {
    foreach ($dietaData as $key => &$item) {
        $consumo_por_vaca_diario = $item['consumo_por_vaca_kg'];
        $consumo_total_diario = $item['consumo_total_kg'];

        if ($selectedLote !== 'Campo') {
            $item['consumo_por_vaca_exibido'] = $consumo_por_vaca_diario / 2;
            $item['consumo_total_exibido'] = $consumo_total_diario / 2;
        } else {
            $item['consumo_por_vaca_exibido'] = $consumo_por_vaca_diario;
            $item['consumo_total_exibido'] = $consumo_total_diario;
        }
    }
    unset($item);
}

// Lógica de cálculo de custo
$custoTotalPorVaca = 0;
if (!empty($dietaData)) {
    foreach ($dietaData as $item) {
        $custoTotalPorVaca += $item['consumo_por_vaca_kg'] * $item['valor_kg'];
    }
}
?>

<h2 class="page-title">Estoque & Dieta</h2>

<?php if ($message): ?>
    <div class="alert alert-success" style="margin-bottom: 15px;"><?php echo $message; ?></div>
<?php endif; ?>

<div class="filter-controls">
    <form id="estoque-filters-form" method="POST" action="../../controllers/EstoqueController.php">
        <div class="filter-row top-row">
            <div class="filter-group-select">
                <label for="filter_lote">Lote:</label>
                <select id="filter_lote" name="lote" onchange="changeLote(this.value)">
                    <?php if (!empty($loteOptions)): ?>
                        <?php foreach ($loteOptions as $lote): ?>
                            <option value="<?php echo htmlspecialchars($lote); ?>" <?php echo ($selectedLote == $lote) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($lote); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="">Nenhuma dieta ativa</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="filter-group-select">
                <label for="filter_vacas">Vacas:</label>
                <select id="filter_vacas" name="vacas" onchange="submitVacasForm()">
                    <?php foreach ($vacasOptions as $num): ?>
                        <option value="<?php echo htmlspecialchars($num); ?>" <?php echo ($selectedVacas == $num) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($num); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
</div>
<div style="display: flex; justify-content: space-around; align-items: stretch; margin-top: 15px; margin-bottom: 15px; gap: 10px;">

   <div class="ha-device-control-group" style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background-color: var(--background-medium); box-shadow: var(--shadow-light); text-align: center; display: flex; flex-direction: column; justify-content: center;">
        <span class="device-name" style="font-weight: bold; color: var(--dark-orange);">Chupim Casca</span>
        <div class="device-buttons-stacked" style="margin-top: 8px;">
            <i id="chupim_casca_icon" class="fas fa-power-off ha-toggle-icon" 
               style="font-size: 2.5em; cursor: pointer; color: #ccc; text-shadow: 1px 1px 3px rgba(0,0,0,0.2); transition: transform 0.2s ease-in-out;"
               data-entity-id="switch.sonoff_10017a5aac_1"
               onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"></i>
        </div>
    </div>

    <div class="ha-device-control-group" style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background-color: var(--background-medium); box-shadow: var(--shadow-light); text-align: center; display: flex; flex-direction: column; justify-content: center;">
        <span class="device-name" style="font-weight: bold; color: var(--dark-orange);">Chupim Soja</span>
        <div class="device-buttons-stacked" style="margin-top: 8px;">
            <i id="chupim_soja_icon" class="fas fa-power-off ha-toggle-icon" 
               style="font-size: 2.5em; cursor: pointer; color: #ccc; text-shadow: 1px 1px 3px rgba(0,0,0,0.2); transition: transform 0.2s ease-in-out;"
               data-entity-id="switch.sonoff_10017a5aac_2"
               onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"></i>
        </div>
    </div>
    
    <div class="ha-device-control-group" style="flex: 1; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; background-color: var(--background-medium); box-shadow: var(--shadow-light); text-align: center; display: flex; flex-direction: column; justify-content: center;">
        <span class="device-name" style="font-weight: bold; color: var(--dark-orange);">Chupim Milho</span>
        <div class="device-buttons-stacked" style="margin-top: 8px;">
            <i id="chupim_milho_icon" class="fas fa-power-off ha-toggle-icon" 
               style="font-size: 2.5em; cursor: pointer; color: #ccc; text-shadow: 1px 1px 3px rgba(0,0,0,0.2); transition: transform 0.2s ease-in-out;"
               data-entity-id="switch.sonoff_10017a5aac_3"
               onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'"></i>
        </div>
    </div>
    
</div>

<div class="estoque-display">
    <table class="dieta-table">
        <thead>
            <tr>
                <th>Ingrediente</th>
                <th>Por vaca (kg/refeição)</th>
                <th>Consumo Total (kg/refeição)</th>
                <th>Estoque (kg)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($dietaData)): ?>
                <?php foreach ($dietaData as $item_index => $item): ?>
                    <?php
                    $row_class = '';
                    $consumo_total_diario_para_alerta = (float)($consumo10DiasTotais[$item['ingrediente_nome']] ?? 0);
                    $estoque_kg_valor = (float)($item['estoque_kg'] ?? 0);

                    if ($item['consumo_zero_por_vaca']) {
                        $row_class = 'esmaecido';
                    } elseif ($consumo_total_diario_para_alerta > $estoque_kg_valor) {
                        $row_class = 'estoque-baixo';
                    } else {
                        $row_class = ($item_index % 2 == 0) ? 'even-row' : 'odd-row';
                    }
                    ?>
                    <tr class="<?php echo $row_class; ?>">
                        <td><?php echo htmlspecialchars($item['ingrediente_nome'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(number_format($item['consumo_por_vaca_exibido'], 2, ',', '.')); ?></td>
                        <td><?php echo htmlspecialchars(number_format($item['consumo_total_exibido'], 2, ',', '.')); ?></td>
                        <td><?php echo htmlspecialchars(number_format($estoque_kg_valor, 2, ',', '.')); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">Nenhum dado de dieta encontrado para os filtros selecionados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr style="background-color: #f5f5f5; font-size: 1.1em;">
                <td colspan="3" style="text-align: right; font-weight: bold; padding: 12px;">Custo por Vaca/Dia:</td>
                <td style="font-weight: bold; font-size: 1.2em; padding: 12px; color: var(--dark-orange);">
                    R$ <?php echo number_format($custoTotalPorVaca, 2, ',', '.'); ?>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
function changeLote(selectedLote) {
    window.location.href = 'index.php?lote=' + encodeURIComponent(selectedLote);
}

function submitVacasForm() {
    const form = document.getElementById('estoque-filters-form');
    
    // Remove qualquer input 'action' antigo para evitar duplicatas
    const oldActionInput = form.querySelector('input[name="action"]');
    if (oldActionInput) {
        oldActionInput.remove();
    }
    
    // Adiciona o input 'action' com o valor correto
    const actionInput = document.createElement('input');
    actionInput.type = 'hidden';
    actionInput.name = 'action';
    actionInput.value = 'update_vacas_count'; // <-- LINHA CORRIGIDA
    form.appendChild(actionInput);
    
    // Envia o formulário para o controller, que agora reconhecerá a ação
    form.submit();
}
</script>