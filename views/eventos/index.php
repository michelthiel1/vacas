<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Evento.php';
require_once __DIR__ . '/../../models/Gado.php'; // Para brinco na listagem

$evento = new Evento($pdo);
$gado = new Gado($pdo);

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Lógica para o calendário: mês e ano atual, ou do GET
$current_month = isset($_GET['mes']) ? (int)$_GET['mes'] : (int)date('m');
$current_year = isset($_GET['ano']) ? (int)$_GET['ano'] : (int)date('Y');

// Certifica-se de que o mês e o ano estão dentro de um range razoável
if ($current_month < 1 || $current_month > 12) $current_month = (int)date('m');
if ($current_year < 1900 || $current_year > 2100) $current_year = (int)date('Y');

// Nomes dos meses para exibição
$meses = [
    1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
    5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
    9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
];

// Dias com eventos para destacar no mini-calendário
$event_days_in_month = $evento->getEventDaysInMonth($current_month, $current_year);

// Lógica para obter todos os eventos do mês e ano atual/selecionado
$searchQuery = $_GET['search_query'] ?? '';
// Adiciona a variável para o novo filtro
$showCompleted = isset($_GET['show_completed']) && $_GET['show_completed'] == '1';

// Passa a variável para a função
$stmt_eventos = $evento->read($searchQuery, $current_month, $current_year, $showCompleted);

$num_eventos = $stmt_eventos->rowCount();

// Para navegação de meses
$prev_month = $current_month - 1;
$prev_year = $current_year;
if ($prev_month < 1) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $current_month + 1;
$next_year = $current_year;
if ($next_month > 12) {
    $next_month = 1;
    $next_year++;
}
?>

<h2 class="page-title">Agenda de Eventos</h2>

<?php echo $message; ?>


<div class="calendar-container">
    <div class="calendar-header">
          <a href="?mes=<?php echo $prev_month; ?>&ano=<?php echo $prev_year; ?>&search_query=<?php echo urlencode($searchQuery); ?>" class="nav-button prev-month"><i class="fas fa-chevron-left"></i></a>
    
    <span class="current-month-year"><?php echo $meses[$current_month] . ' ' . $current_year; ?></span>
    
        <a href="?mes=<?php echo $next_month; ?>&ano=<?php echo $next_year; ?>&search_query=<?php echo urlencode($searchQuery); ?>" class="nav-button next-month"><i class="fas fa-chevron-right"></i></a>
	</div>
    <div class="mini-calendar">
        <?php
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $current_month, $current_year);
        $first_day_of_month = (new DateTime("$current_year-$current_month-01"))->format('N'); // 1 (for Monday) through 7 (for Sunday)
        $first_day_of_month = ($first_day_of_month == 7) ? 0 : $first_day_of_month; // Ajusta para 0 para Domingo

        // Cabeçalho dos dias da semana
        $dias_semana_curto = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        foreach ($dias_semana_curto as $dia_curto) {
            echo '<div class="day-name">' . $dia_curto . '</div>';
        }

        // Preenche espaços em branco antes do primeiro dia
        for ($i = 0; $i < $first_day_of_month; $i++) {
            echo '<div class="empty-day"></div>';
        }

        // Exibe os dias do mês
        for ($day = 1; $day <= $days_in_month; $day++) {
            $has_event_class = in_array($day, $event_days_in_month) ? 'has-event' : '';
            $current_day_class = ($day == date('d') && $current_month == date('m') && $current_year == date('Y')) ? 'current-day' : '';
            echo '<div class="day ' . $has_event_class . ' ' . $current_day_class . '">' . $day . '</div>';
        }
        ?>
    </div>
</div>
 <div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap; margin-bottom: 15px;">
        
        <input type="hidden" name="mes" value="<?php echo $current_month; ?>">
        <input type="hidden" name="ano" value="<?php echo $current_year; ?>">
        

        <div class="search-input-group">
            <input type="text" id="search_query" name="search_query" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($searchQuery); ?>" style="width: 150px;">
        </div>

        <?php if (!empty($searchQuery)): ?>
            <a href="index.php?mes=<?php echo $current_month; ?>&ano=<?php echo $current_year; ?>&show_completed=<?php echo isset($_GET['show_completed']) ? '1' : '0'; ?>" class="btn btn-danger">Limpar</a>
        <?php endif; ?>

        <div class="filter-checkbox-inline" style="margin-left: auto; display: flex; align-items: center; gap: 5px;">
            <input type="checkbox" id="mostrar_concluidos" name="show_completed" value="1" <?php if (isset($_GET['show_completed']) && $_GET['show_completed'] == '1') echo 'checked'; ?>>
            <label for="mostrar_concluidos" style="margin-bottom: 0; font-weight: normal; white-space: nowrap;">Concluídos</label>
        </div>
        
        <a href="create.php" class="btn btn-primary add-event-button">+ Evento</a>
    </form>
</div>


<?php if ($num_eventos > 0) : ?>
    <div class="event-list">
        <?php
        $row_counter = 0;
        while ($evento_row = $stmt_eventos->fetch(PDO::FETCH_ASSOC)) :
            $row_counter++;
            $row_class = ($row_counter % 2 == 0) ? 'even-row' : 'odd-row';
            ?>
            <div class="event-item clickable-row <?php echo $row_class; ?>" data-href="view.php?id=<?php echo $evento_row['id']; ?>">
                <div class="event-day-display">
                    <span class="event-day-number"><?php echo (new DateTime($evento_row['data_evento']))->format('d'); ?></span>
                </div>
                <div class="event-details-content">
                    <div class="event-brinco-type-group">
                        <?php if (!empty($evento_row['brinco_vaca_display'])): ?>
                            <a href="../gado/view.php?id=<?php echo htmlspecialchars($evento_row['id_vaca']); ?>" class="event-brinco-highlight-large">
                                <?php echo htmlspecialchars($evento_row['brinco_vaca_display']); ?>
                            </a>
                        <?php endif; ?>
                        <span class="event-type-extra-small"><?php echo htmlspecialchars($evento_row['tipo_evento']); ?></span>
                    </div>
                    <span class="event-title-display"><?php echo htmlspecialchars($evento_row['titulo']); ?></span>
                </div>
                <div class="event-actions">
                    <a href="edit.php?id=<?php echo $evento_row['id']; ?>" class="action-icon edit-icon" title="Editar Evento"><i class="fas fa-pencil-alt"></i></a>
                    <button class="action-icon complete-icon" data-event-id="<?php echo $evento_row['id']; ?>" title="Marcar como Concluído"><i class="fas fa-check"></i></button>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else : ?>
    <div class="alert alert-info">Nenhum evento encontrado para este mês.</div>
<?php endif; ?>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

 
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Encontra o checkbox pelo seu novo ID: 'mostrar_concluidos'
    const checkboxConcluidos = document.getElementById('mostrar_concluidos');
    
    // Encontra o formulário de filtros
    const formFiltros = document.getElementById('filter-form');

    // Verifica se os dois elementos existem na página
    if (checkboxConcluidos && formFiltros) {
        
        // Adiciona um "escutador" que dispara toda vez que o checkbox é alterado
        checkboxConcluidos.addEventListener('change', function() {
            // Quando o checkbox mudar, o formulário será enviado.
            // Isso recarrega a página com o parâmetro ?show_completed=1 na URL.
            formFiltros.submit();
        });
    }

    // Adiciona a classe 'clickable-row' para as linhas de evento
    const clickableRows = document.querySelectorAll('.clickable-row');
    clickableRows.forEach(row => {
        row.addEventListener('click', function() {
            const href = this.dataset.href;
            if (href) {
                window.location.href = href;
            }
        });
    });
});
</script>