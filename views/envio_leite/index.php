<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/EnvioLeite.php';

// --- LÓGICA DO CALENDÁRIO ---
$mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('m');
$ano = isset($_GET['ano']) ? (int)$_GET['ano'] : date('Y');
$data_referencia = new DateTime("$ano-$mes-01");

// --- LÓGICA PARA BUSCAR EVENTOS DE BST ---
$bst_dias = [];
$query_bst = "SELECT DAY(data_evento) as dia FROM eventos WHERE titulo LIKE '%BST%' AND YEAR(data_evento) = :ano AND MONTH(data_evento) = :mes";
$stmt_bst = $pdo->prepare($query_bst);
$stmt_bst->execute(['ano' => $ano, 'mes' => $mes]);
while ($row_bst = $stmt_bst->fetch(PDO::FETCH_ASSOC)) {
    $bst_dias[] = (int)$row_bst['dia'];
}

// Lógica de navegação e busca de envios de leite
$nome_mes = ucfirst($data_referencia->format('F'));
$total_dias_mes = (int)$data_referencia->format('t');
$primeiro_dia_semana = (int)$data_referencia->format('w');
$mes_anterior = (clone $data_referencia)->modify('-1 month');
$mes_proximo = (clone $data_referencia)->modify('+1 month');
$link_anterior = "index.php?mes=" . $mes_anterior->format('m') . "&ano=" . $mes_anterior->format('Y');
$link_proximo = "index.php?mes=" . $mes_proximo->format('m') . "&ano=" . $mes_proximo->format('Y');
$envioLeite = new EnvioLeite($pdo);
$stmt = $envioLeite->readByMonthYear($ano, $mes);

// --- LÓGICA DE CÁLCULO DOS TOTAIS ---
$dados_envios = [];
$total_litros_mes = 0;
$total_leite_bezerros_mes = 0;
$total_vacas_acumulado = 0;
$soma_das_medias_diarias_individuais = 0;
$numero_de_envios = 0;

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $dia = (int)date('d', strtotime($row['data_envio']));
    $dados_envios[$dia] = $row;
    
    $total_litros_mes += $row['litros_enviados'];
    $total_leite_bezerros_mes += $row['leite_bezerros'];
    $total_vacas_acumulado += $row['numero_vacas'];
    $numero_de_envios++;

    if ($row['numero_vacas'] > 0) {
        $leite_total_envio = $row['litros_enviados'] + $row['leite_bezerros'];
        $media_do_envio_2_dias = $leite_total_envio / $row['numero_vacas'];
        $media_diaria_do_envio = $media_do_envio_2_dias / 2;
        $soma_das_medias_diarias_individuais += $media_diaria_do_envio;
    }
}

$media_vacas_mes = ($numero_de_envios > 0) ? ($total_vacas_acumulado / $numero_de_envios) : 0;
$media_diaria_real_no_mes = ($numero_de_envios > 0) ? ($soma_das_medias_diarias_individuais / $numero_de_envios) : 0;
$meses_pt = ['January'=>'Janeiro', 'February'=>'Fevereiro', 'March'=>'Março', 'April'=>'Abril', 'May'=>'Maio', 'June'=>'Junho', 'July'=>'Julho', 'August'=>'Agosto', 'September'=>'Setembro', 'October'=>'Outubro', 'November'=>'Novembro', 'December'=>'Dezembro'];
$nome_mes_pt = $meses_pt[$nome_mes];

?>

<style>
    .calendar-wrapper { width: 100%; padding: 0; }
    .calendar-container {
        padding: 10px;
        border-radius: 10px;
        box-shadow: var(--shadow-light);
        width: 100%;
        box-sizing: border-box;
        /* ### CORREÇÃO: Tom de bege/laranja mais escuro ### */
        background-color: #e6dac8;
    }
    /* ### CORREÇÃO: Altura do cabeçalho reduzida ### */
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
        background-color: #fff;
        padding: 5px 10px; /* Padding vertical reduzido */
        border-radius: 5px;
    }
    .calendar-header h3 {
        margin: 0;
        font-size: 1.2rem; /* Fonte do mês reduzida */
        color: var(--dark-orange);
    }
    .calendar-nav a {
        font-size: 1.5rem; /* Setas de navegação reduzidas */
        color: var(--primary-color);
        text-decoration: none;
    }
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 1px;
    }
    .calendar-day-header {
        text-align: center;
        padding: 5px;
        font-weight: bold;
        background-color: #f8f9fa;
        font-size: 0.7rem;
        color: #666;
    }
    .calendar-day {
        min-height: 80px;
        background-color: #fff;
        display: flex;
        flex-direction: column;
        position: relative;
        padding: 3px;
    }
    .day-number-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        font-weight: bold;
        color: #6c757d;
        font-size: 0.7rem;
    }
    .bst-label {
        font-size: 0.6rem;
        font-weight: bold;
        color: #721c24;
        background-color: #f8d7da;
        padding: 1px 4px;
        border-radius: 3px;
    }
    .calendar-day.has-bst { background-color: #fff0f1; }
    .day-content {
        width: 100%;
        cursor: pointer;
        margin-top: auto;
    }
    .day-data {
        background-color: #f8f9fa;
        border-left: 3px solid #6c757d;
        padding: 3px;
        border-radius: 3px;
        width: 100%;
        margin-top: 2px;
        box-sizing: border-box;
        text-align: left;
    }
    .day-data:hover { background-color: #e2e6ea; }
    .day-data div {
        font-weight: normal;
        font-size: 0.8rem;
        line-height: 1.3;
    }
    /* ### CORREÇÃO: Novas classes de cores para os dados ### */
    .data-leite-enviado { color: #007bff; } /* Azul */
    .data-leite-bezerros { color: #28a745; } /* Verde */
    .data-vacas { color: #343a40; } /* Preto */
    .data-media { color: #dc3545; } /* Vermelho */

    .add-envio-btn {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2rem;
        color: #e0e0e0;
        text-decoration: none;
        display: none;
        line-height: 1;
    }
    .calendar-day:hover .add-envio-btn { display: block; }
    .add-envio-btn:hover { color: var(--primary-color); }
    .summary-container {
        background-color: #fff;
        padding: 15px;
        margin-top: 20px;
        border-radius: 10px;
        box-shadow: var(--shadow-light);
        display: flex;
        justify-content: space-around;
        align-items: center;
        text-align: center;
    }
    .summary-item { display: flex; flex-direction: column; }
    .summary-item .label {
        font-size: 0.7rem;
        color: #888;
        font-weight: 500;
        text-transform: uppercase;
        margin-bottom: 3px;
    }
    .summary-item .value {
        font-size: 1.2rem;
        font-weight: bold;
        color: var(--dark-orange);
    }
    .summary-item .value small {
        font-size: 0.7rem;
        color: #555;
        font-weight: normal;
    }
    /* ### CORREÇÃO: Estilo da legenda ### */
    .calendar-legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 15px;
        font-size: 0.75rem;
    }
    .legend-item {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .legend-color-box {
        width: 15px;
        height: 15px;
        border-radius: 3px;
    }
</style>

<div class="header-content">
    <h2 class="page-title">Controle de Envios de Leite</h2>
</div>

<div class="calendar-wrapper">
    <div class="calendar-container">
        <div class="calendar-header">
            <a href="<?php echo $link_anterior; ?>" class="calendar-nav">&#10094;</a>
            <h3><?php echo $nome_mes_pt . ' ' . $ano; ?></h3>
            <a href="<?php echo $link_proximo; ?>" class="calendar-nav">&#10095;</a>
        </div>

        <div class="calendar-grid">
            <div class="calendar-day-header">Dom</div><div class="calendar-day-header">Seg</div><div class="calendar-day-header">Ter</div><div class="calendar-day-header">Qua</div><div class="calendar-day-header">Qui</div><div class="calendar-day-header">Sex</div><div class="calendar-day-header">Sáb</div>

            <?php
            for ($i = 0; $i < $primeiro_dia_semana; $i++) { echo '<div class="calendar-day not-in-month"></div>'; }

            for ($dia = 1; $dia <= $total_dias_mes; $dia++) {
                $classe_bst = in_array($dia, $bst_dias) ? 'has-bst' : '';
                echo "<div class='calendar-day {$classe_bst}'>";
                
                echo '<div class="day-number-container">';
                echo '<span>' . $dia . '</span>';
                if ($classe_bst) {
                    echo '<span class="bst-label">BST</span>';
                }
                echo '</div>';

                if (isset($dados_envios[$dia])) {
                    $envio = $dados_envios[$dia];
                    $leite_total_envio = $envio['litros_enviados'] + $envio['leite_bezerros'];
                    $mediaDiaria = ($envio['numero_vacas'] > 0) ? ($leite_total_envio / $envio['numero_vacas']) / 2 : 0;
                    
                    echo "<div class='day-content' onclick=\"window.location.href='edit.php?id={$envio['id']}'\">";
                    echo '<div class="day-data">';
                    echo '<div class="data-leite-enviado">' . $envio['litros_enviados'] . '</div>';
                    echo '<div class="data-leite-bezerros">' . $envio['leite_bezerros'] . '</div>';
                    echo '<div class="data-vacas">' . $envio['numero_vacas'] . '</div>';
                    echo '<div class="data-media">' . number_format($mediaDiaria, 2, ',', '') . '</div>';
                    echo '</div>';
                    echo '</div>';
                } else {
                    $data_completa = sprintf('%d-%02d-%02d', $ano, $mes, $dia);
                    echo "<a href='create.php?data={$data_completa}' class='add-envio-btn'>+</a>";
                }
                echo '</div>';
            }

            $total_celulas = $primeiro_dia_semana + $total_dias_mes;
            $celulas_restantes = 7 - ($total_celulas % 7);
            if ($celulas_restantes < 7) { for ($i = 0; $i < $celulas_restantes; $i++) { echo '<div class="calendar-day not-in-month"></div>'; } }
            ?>
        </div>
        
        <div class="calendar-legend">
            <div class="legend-item"><div class="legend-color-box" style="background-color: #007bff;"></div> Leite Enviado</div>
            <div class="legend-item"><div class="legend-color-box" style="background-color: #28a745;"></div> Leite Bezerros</div>
            <div class="legend-item"><div class="legend-color-box" style="background-color: #343a40;"></div> Nº de Vacas</div>
            <div class="legend-item"><div class="legend-color-box" style="background-color: #dc3545;"></div> Média Diária</div>
        </div>
    </div>
</div>

<div class="summary-container">
    <div class="summary-item">
        <span class="label">Leite enviado</span>
        <span class="value"><?php echo number_format($total_litros_mes, 0, ',', '.'); ?> <small>L</small></span>
    </div>
    <div class="summary-item">
        <span class="label">Leite bezerros</span>
        <span class="value"><?php echo number_format($total_leite_bezerros_mes, 0, ',', '.'); ?> <small>L</small></span>
    </div>
    <div class="summary-item">
        <span class="label">Media de vacas</span>
        <span class="value"><?php echo number_format($media_vacas_mes, 0); ?></span>
    </div>
    <div class="summary-item">
        <span class="label">Media diaria</span>
        <span class="value"><?php echo number_format($media_diaria_real_no_mes, 2, ',', '.'); ?> <small>L/Vaca</small></span>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>