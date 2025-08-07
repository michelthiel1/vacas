document.addEventListener('DOMContentLoaded', function() {
    // Paleta de cores e registro do plugin de rótulos
    const CORES_GRAFICO_PIE = ['#E96A4B', '#D68A3D', '#6B5F5F', '#66BB6A', '#81C784', '#FCE0C3'];
    const COR_BARRA_1 = 'rgba(233, 106, 75, 0.8)';
    const COR_BARRA_2 = 'rgba(102, 187, 106, 0.8)';
    Chart.register(ChartDataLabels);

    const chartInstances = {};

    // Elementos do DOM
    const filterForm = document.getElementById('filter-form-graficos');
    const advancedFilterForm = document.getElementById('advanced-filter-form');
    const animaisModal = document.getElementById('listaAnimaisModal');
    const filtrosModal = document.getElementById('filterModal');

    // --- LÓGICA DE EVENTOS ---
    
    // Previne o envio padrão que recarrega a página
    if(filterForm) {
        filterForm.addEventListener('submit', (e) => e.preventDefault());
    }
    if (advancedFilterForm) {
        advancedFilterForm.addEventListener('submit', (e) => e.preventDefault());
    }

    // Função unificada para lidar com mudanças nos filtros
    let debounceTimer;
    function handleFilterChange() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(carregarDadosDosGraficos, 500);
    }
    
    if(filterForm) {
        filterForm.addEventListener('input', handleFilterChange);
        filterForm.addEventListener('change', handleFilterChange);
    }

    // Botão para abrir o modal de filtros avançados
    const openFilterBtn = document.getElementById('openFilterBtn');
    if(openFilterBtn) {
        openFilterBtn.addEventListener('click', () => {
            if (filtrosModal) filtrosModal.style.display = 'flex';
        });
    }
    
    // Botão para APLICAR filtros do modal
    const applyModalFiltersBtn = document.getElementById('applyModalFiltersBtn');
    if(applyModalFiltersBtn) {
        applyModalFiltersBtn.addEventListener('click', () => {
            if (filtrosModal) filtrosModal.style.display = 'none';
            carregarDadosDosGraficos();
        });
    }

    // Botão para LIMPAR TODOS os filtros
    const clearAllFiltersBtn = document.getElementById('clearAllFiltersBtn');
    if(clearAllFiltersBtn) {
        clearAllFiltersBtn.addEventListener('click', () => {
            if(filterForm) filterForm.reset();
            if (advancedFilterForm) advancedFilterForm.reset();
            carregarDadosDosGraficos();
        });
    }
    
    // Botão para LIMPAR filtros APENAS do modal
    const clearModalFiltersBtn = document.getElementById('clearModalFiltersBtn');
    if(clearModalFiltersBtn) {
        clearModalFiltersBtn.addEventListener('click', () => {
            if (advancedFilterForm) advancedFilterForm.reset();
        });
    }

    // Funções para fechar os modais
    document.querySelectorAll('.modal .close-button, .modal .modal-close-button').forEach(btn => {
        btn.onclick = () => { btn.closest('.modal').style.display = 'none'; };
    });
    window.onclick = (event) => {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    };
    
    // --- FUNÇÕES DE GRÁFICOS E DADOS ---

    function abrirModalComAnimais(tipoGrafico, segmento) {
        const modalTitle = animaisModal.querySelector('#modalTitle');
        const modalBody = animaisModal.querySelector('#modalBody');
        const queryString = getFiltrosAtivosQueryString();
        
        modalTitle.innerText = `Animais - ${segmento}`;
        modalBody.innerHTML = '<p>Carregando lista...</p>';
        animaisModal.style.display = 'flex';

        fetch(`../../controllers/GraficosController.php?action=getAnimaisPorSegmento&tipo=${tipoGrafico}&segmento=${encodeURIComponent(segmento)}&${queryString}`)
            .then(response => response.json())
            .then(result => {
                if (result.success && result.data.length > 0) {
                    let listaHtml = '<ul style="list-style: none; padding: 0;">';
                    result.data.forEach(animal => {
                        listaHtml += `<li style="padding: 5px 0; border-bottom: 1px solid #eee;"><a href="../gado/view.php?id=${animal.id}" target="_blank">${animal.brinco}</a></li>`;
                    });
                    listaHtml += '</ul>';
                    modalBody.innerHTML = listaHtml;
                } else {
                    modalBody.innerHTML = '<p>Nenhum animal encontrado para este segmento.</p>';
                }
            })
            .catch(error => {
                console.error('Erro ao buscar animais para o modal:', error);
                modalBody.innerHTML = '<p>Ocorreu um erro ao carregar a lista.</p>';
            });
    }

    function renderizarGrafico(canvasId, tipoGrafico, chartType, data) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;
        if (chartInstances[canvasId]) chartInstances[canvasId].destroy();
        
        let chartData, options = {};

        if (chartType === 'bar' && tipoGrafico === 'eficienciaMensal') {
            chartData = {
                labels: data.map(d => d.mes),
                datasets: [
                    { label: 'Inseminações', data: data.map(d => d.total_inseminacoes), backgroundColor: COR_BARRA_1 },
                    { label: 'Confirmadas', data: data.map(d => d.total_confirmadas), backgroundColor: COR_BARRA_2 }
                ]
            };
            options = {
                scales: { y: { beginAtZero: true } },
                plugins: {
                    legend: { position: 'top' },
                    datalabels: {
                        display: true,
                        color: '#333',
                        font: { weight: 'bold' },
                        anchor: 'end',
                        align: 'top'
                    }
                }
            };
        } else {
            chartData = {
                labels: data.map(d => d.label),
                datasets: [{ data: data.map(d => d.value), backgroundColor: chartType === 'pie' ? CORES_GRAFICO_PIE : COR_BARRA_1 }]
            };
            options = {
                onClick: (event, elements) => {
                    if (elements.length > 0) {
                        const index = elements[0].index;
                        const segmentoClicado = chartData.labels[index];
                        if (tipoGrafico !== 'eficienciaMensal' && tipoGrafico.indexOf('producao') === -1) { abrirModalComAnimais(tipoGrafico, segmentoClicado); }
                    }
                },
                plugins: {
                    legend: { display: chartType === 'pie' || tipoGrafico === 'eficienciaMensal' },
                    datalabels: {
                        display: true, color: chartType === 'pie' ? 'white' : '#333', font: { weight: 'bold' },
                        formatter: (value, context) => {
                             if (chartType === 'pie') {
                                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                if (total === 0) return '';
                                const percentage = (value / total * 100).toFixed(1) + '%';
                                return context.chart.data.datasets[0].data[context.dataIndex] > total * 0.04 ? `${value}\n(${percentage})` : '';
                             }
                             return value;
                        },
                        textStrokeColor: chartType === 'pie' ? '#444' : null, textStrokeWidth: chartType === 'pie' ? 2 : 0,
                    }
                }
            };
            if (chartType === 'bar') { options.indexAxis = (tipoGrafico === 'falhasInseminacao') ? 'y' : 'x'; }
        }
        
        options.responsive = true;
        options.maintainAspectRatio = false;
        chartInstances[canvasId] = new Chart(ctx, { type: chartType, data: chartData, options: options });
    }

    function renderizarGraficoMedidor(canvasId, value, ranges) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return;
        if (chartInstances[canvasId]) { chartInstances[canvasId].destroy(); }
        const [bom, aceitavel, ruim] = ranges;
        let corFundo = !value || value === 0 ? '#cccccc' : (value <= bom ? COR_BARRA_2 : (value <= aceitavel ? '#D68A3D' : '#E96A4B'));
        chartInstances[canvasId] = new Chart(ctx, {
            type: 'doughnut', data: { datasets: [{ data: [value, Math.max(0, (ruim * 1.1) - value)], backgroundColor: [corFundo, '#efefef'], borderWidth: 0, circumference: 180, rotation: 270, }] },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%', plugins: { legend: { display: false }, tooltip: { enabled: false }, datalabels: { display: false } } },
            plugins: [{
                id: 'gaugeText', beforeDraw(chart) {
                    const { width, height, ctx } = chart;
                    ctx.restore(); const fontSize = (height / 100).toFixed(2); ctx.font = `bold ${fontSize}em sans-serif`; ctx.textBaseline = 'middle';
                    const text = Math.round(value) + ' dias'; const textX = Math.round((width - ctx.measureText(text).width) / 2); const textY = height / 1.5;
                    ctx.fillText(text, textX, textY); ctx.save();
                }
            }]
        });
    }
    
    function getFiltrosAtivosQueryString() {
        const params = new URLSearchParams();
        
        if (filterForm) {
            const searchQuery = filterForm.querySelector('#search_query').value;
            if (searchQuery) {
                params.set('search_query', searchQuery);
            }
            const grupos = Array.from(filterForm.querySelectorAll('input[name="grupo[]"]:checked')).map(cb => cb.value);
            if (grupos.length > 0) {
                params.set('grupo', grupos.join(','));
            }
        }

        if (advancedFilterForm) {
             const advancedFormData = new FormData(advancedFilterForm);
             for (const [key, value] of advancedFormData) { if (value) params.append(key, value); }
             const status = Array.from(advancedFilterForm.querySelectorAll('input[name="status[]"]:checked')).map(cb => cb.value);
             params.delete('status[]');
             if (status.length) params.set('status', status.join(','));
        }
        
        return params.toString();
    }

    function carregarDadosDosGraficos() {
        const queryString = getFiltrosAtivosQueryString();
        fetch(`../../controllers/GraficosController.php?action=getDadosGraficos&${queryString}`)
            .then(response => {
                if (!response.ok) { throw new Error(`HTTP error! status: ${response.status}`); }
                return response.json();
            })
            .then(result => {
                if (result.success) {
                    const data = result.data;
                    if (data.status) renderizarGrafico('graficoStatus', 'status', 'pie', data.status);
                    if (data.grupo) renderizarGrafico('graficoGrupo', 'grupo', 'pie', data.grupo);
                    if (data.ordemParto) renderizarGrafico('graficoOrdemParto', 'ordemParto', 'bar', data.ordemParto);
                    if (data.escore) renderizarGrafico('graficoEscore', 'escore', 'pie', data.escore);
                    if (data.eficienciaMensal) renderizarGrafico('graficoEficienciaMensal', 'eficienciaMensal', 'bar', data.eficienciaMensal);
                    if (data.falhasInseminacao) renderizarGrafico('graficoFalhasInseminacao', 'falhasInseminacao', 'bar', data.falhasInseminacao.reverse());
                    if (data.iep) renderizarGraficoMedidor('graficoIEP', data.iep, [380, 420, 450]);
                    if (data.partoConcepcao) renderizarGraficoMedidor('graficoPartoConcepcao', data.partoConcepcao, [100, 130, 160]);
                    if (data.producaoPorDEL) renderizarGrafico('graficoProducaoPorDEL', 'producaoPorDEL', 'bar', data.producaoPorDEL);
                    if (data.producaoPorParto) renderizarGrafico('graficoProducaoPorParto', 'producaoPorParto', 'bar', data.producaoPorParto);
					 // ### INÍCIO DO CÓDIGO PARA O NOVO GRÁFICO ###
                    if (data.lactacaoHistorico) {
                        const canvasId = 'graficoLactacao';
                        const ctx = document.getElementById(canvasId);
                        if (ctx) {
                            if (chartInstances[canvasId]) {
                                chartInstances[canvasId].destroy();
                            }
                            chartInstances[canvasId] = new Chart(ctx, {
                                type: 'line', // Gráfico de linha para séries temporais
                                data: {
                                    labels: data.lactacaoHistorico.labels,
                                    datasets: [{
                                        label: 'Nº de Vacas em Lactação',
                                        data: data.lactacaoHistorico.values,
                                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 2,
                                        fill: true,
                                        tension: 0.1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                stepSize: 1 // Força o eixo Y a usar apenas números inteiros
                                            }
                                        }
                                    },
                                   plugins: {
    legend: { display: false },
    datalabels: {
        display: true,
        color: '#333',
        font: { weight: 'bold' },
        anchor: 'start',    // <-- Alterado para 'start'
        align: 'bottom',    // <-- Alterado para 'bottom'
        formatter: (value) => value > 0 ? value : ''
    }
}
                                }
                            });
                        }
                    }
                    // ### FIM DO CÓDIGO PARA O NOVO GRÁFICO ###
                } else {
                    console.error("Falha ao carregar dados dos gráficos:", result.message);
                }
            })
            .catch(error => console.error('Erro na requisição AJAX:', error));
    }
    
    // Carga inicial dos dados
    carregarDadosDosGraficos();
});