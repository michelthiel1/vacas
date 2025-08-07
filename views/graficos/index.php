<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';

$grupoOptions = ['Bezerra', 'Novilha', 'Lactante', 'Seca', 'Corte'];
$statusOptions = ['Vazia', 'Inseminada', 'Prenha'];
// Recupera os filtros da URL para manter o estado dos checkboxes
$grupos_selecionados = isset($_GET['grupo']) ? explode(',', $_GET['grupo']) : [];
?>

<style>
    /* Estilos específicos para a página de gráficos */
    .graficos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .grafico-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: var(--shadow-light);
        padding: 15px;
        display: flex;
        flex-direction: column;
    }
    .grafico-card h3 {
        margin-top: 0;
        margin-bottom: 15px;
        font-size: 1rem;
        color: var(--dark-orange);
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 8px;
        text-align: center;
    }
    .grafico-canvas-container {
        position: relative;
        height: 300px;
        flex-grow: 1;
    }
    .modal-body .filter-group {
        background-color: var(--background-medium);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 15px;
    }
    .modal-body .filter-group label {
        font-weight: 700;
        color: var(--dark-orange);
        margin-bottom: 8px;
        display: block;
        font-size: 0.95rem;
    }
    .modal-body .filter-input-range {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .modal-body .filter-checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px 15px;
    }
    .modal-body .filter-checkbox-group > div {
        display: flex;
        align-items: center;
        gap: 5px;
    }
</style>

<h2 class="page-title">Análises do Rebanho</h2>

<div class="filter-controls">
    <form id="filter-form-graficos">
        <div class="top-filter-bar">
            <div class="search-input-group">
                <input type="text" id="search_query" name="search_query" placeholder="Pesquisar por brinco/nome..." value="<?php echo htmlspecialchars($_GET['search_query'] ?? ''); ?>">
            </div>
           <div class="filter-buttons-group">
                <button id="openFilterBtn" class="btn btn-secondary" type="button">Filtros</button>
                <button id="clearAllFiltersBtn" class="btn btn-danger" type="button">Limpar</button>
            </div>
        </div>
        <div class="filter-group-checkboxes-container">
            <div class="checkbox-options-group">
                <?php foreach ($grupoOptions as $option): ?>
                    <div>
                        <input type="checkbox" id="grupo_<?php echo strtolower($option); ?>" name="grupo[]" value="<?php echo htmlspecialchars($option); ?>" <?php echo in_array($option, $grupos_selecionados) ? 'checked' : ''; ?>>
                        <label for="grupo_<?php echo strtolower($option); ?>"><?php echo htmlspecialchars($option); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </form>
</div>

<div class="graficos-grid">
    <div class="grafico-card"><h3>Distribuição por Status</h3><div class="grafico-canvas-container"><canvas id="graficoStatus"></canvas></div></div>
    <div class="grafico-card"><h3>Distribuição por Grupo</h3><div class="grafico-canvas-container"><canvas id="graficoGrupo"></canvas></div></div>
    <div class="grafico-card"><h3>Distribuição por Ordem de Parto</h3><div class="grafico-canvas-container"><canvas id="graficoOrdemParto"></canvas></div></div>
    <div class="grafico-card"><h3>Distribuição de Escore Corporal</h3><div class="grafico-canvas-container"><canvas id="graficoEscore"></canvas></div></div>
    <div class="grafico-card"><h3>Eficiência de Inseminação (Mensal)</h3><div class="grafico-canvas-container"><canvas id="graficoEficienciaMensal"></canvas></div></div>
    <div class="grafico-card"><h3>Top Vacas com Falhas de Inseminação</h3><div class="grafico-canvas-container"><canvas id="graficoFalhasInseminacao"></canvas></div></div>
    <div class="grafico-card"><h3>Intervalo Entre Partos (IEP) Médio</h3><div class="grafico-canvas-container"><canvas id="graficoIEP"></canvas></div></div>
    <div class="grafico-card"><h3>Intervalo Parto-Concepção Médio</h3><div class="grafico-canvas-container"><canvas id="graficoPartoConcepcao"></canvas></div></div>
    <div class="grafico-card"><h3>Produção Média por Faixa de DEL</h3><div class="grafico-canvas-container"><canvas id="graficoProducaoPorDEL"></canvas></div></div>
    <div class="grafico-card"><h3>Produção Média por Ordem de Parto</h3><div class="grafico-canvas-container"><canvas id="graficoProducaoPorParto"></canvas></div></div>
	 <div class="grafico-card" style="grid-column: 1 / -1;">
            <h3>Contagem de Vacas em Lactação (Histórico e Previsão)</h3>
            <div class="grafico-canvas-container" style="height: 350px;">
                <canvas id="graficoLactacao"></canvas>
            </div>
        </div>
</div>

<div id="filterModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Mais Filtros</h3>
            <span class="modal-close-button">&times;</span>
        </div>
        <div class="modal-body">
            <form id="advanced-filter-form">
                <div class="filter-group">
                    <label>Status:</label>
                    <div class="filter-checkbox-group">
                        <?php foreach($statusOptions as $status): ?>
                        <div>
                            <input type="checkbox" id="status_<?php echo strtolower($status); ?>" name="status[]" value="<?php echo $status; ?>">
                            <label for="status_<?php echo strtolower($status); ?>"><?php echo $status; ?></label>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                 <div class="filter-group">
                    <label>Idade (meses):</label>
                    <div class="filter-input-range">
                        <input type="number" name="idade_min" placeholder="Mínimo" min="0">
                        <span>a</span>
                        <input type="number" name="idade_max" placeholder="Máximo" min="0">
                    </div>
                </div>
                <div class="filter-group">
                    <label>DEL (dias):</label>
                    <div class="filter-input-range">
                        <input type="number" name="del_min" placeholder="Mínimo" min="0">
                        <span>a</span>
                        <input type="number" name="del_max" placeholder="Máximo" min="0">
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button id="clearModalFiltersBtn" class="btn btn-secondary">Limpar</button>
            <button id="applyModalFiltersBtn" class="btn btn-primary">Aplicar Filtros</button>
        </div>
    </div>
</div>

<div id="listaAnimaisModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Lista de Animais</h3>
            <span class="modal-close-button">&times;</span>
        </div>
        <div class="modal-body" id="modalBody" style="max-height: 400px; overflow-y: auto;"></div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
<script src="../../js/graficos.js"></script>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>