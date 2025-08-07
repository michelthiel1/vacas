/**
 * Versão: 2.1
 * Descrição: Reativado o auto-submit para o campo de pesquisa de gado.
 * Gerencia todos os filtros das views, submetendo o formulário
 * automaticamente quando os valores são alterados.
 */
document.addEventListener('DOMContentLoaded', function() {
    // --- DECLARAÇÕES DE VARIÁVEIS ---
    const filterModal = document.getElementById('filterModal');
    
    const openFilterBtn = document.getElementById('openFilterBtn');
    const closeButton = document.querySelector('.close-button');
    const applyModalFiltersBtn = document.getElementById('applyModalFiltersBtn');
    const clearAllFiltersBtn = document.getElementById('clearAllFiltersBtn');
    const clearModalFiltersBtn = document.getElementById('clearModalFiltersBtn');
    const closeFilterModalBtn = document.getElementById('closeFilterModalBtn');

    const searchQueryInput = document.getElementById('search_query'); // Pode existir em outras views
    const grupoCheckboxes = document.querySelectorAll('input[name="grupo[]"]'); // Presente na view gado
    
    const filterLote = document.getElementById('filter_lote');
    const filterVacas = document.getElementById('filter_vacas');

    const idadeMinInput = document.getElementById('idade_min');
    const idadeMaxInput = document.getElementById('idade_max');
    const statusCheckboxes = document.querySelectorAll('input[name="status[]"]');
    const escoreMinInput = document.getElementById('escore_min');
    const escoreMaxInput = document.getElementById('escore_max');
    const delMinInput = document.getElementById('del_min');
    const delMaxInput = document.getElementById('del_max');
    const bstFilterRadios = document.querySelectorAll('input[name="bst_filter"]');
const mostrarConcluidosCheckbox = document.getElementById('mostrarConcluidos');
const previsaoCioCheckbox = document.getElementById('previsao_cio_ciclos'); // Novo checkbox

 // ### INÍCIO DA CORREÇÃO 1: Mapeando os novos elementos do formulário ###
    const iatfFilterCheckbox = document.getElementById('iatf_filter');
    const descarteFilterCheckbox = document.getElementById('descarte_filter');
    const corBastaoCheckboxes = document.querySelectorAll('input[name="cor_bastao_filter[]"]');
    // ### FIM DA CORREÇÃO 1 ###
	

    const ligarDispositivoHABtn = document.getElementById('acionarRotinaHA');
    const desligarDispositivoHABtn = document.getElementById('desligarRotinaHA');

 
    const loadingIndicator = document.getElementById('loading-indicator');

    // --- VARIÁVEL GLOBAL PARA ARMAZENAR O ESTADO DOS FILTROS DO MODAL ---
    let currentModalFilters = {
        idade_min: '',
        idade_max: '',
        status: [],
        escore_min: '',
        escore_max: '',
        del_min: '',
        del_max: '',
        bst_filter: '',
		 previsao_cio_ciclos: false, // Nova propriedade
		 inseminacao_min: '', // ADICIONAR
        inseminacao_max: '',  // ADICIONAR
		
		 // ### INÍCIO DA CORREÇÃO 2: Adicionando os novos filtros ao estado ###
        iatf_filter: false,
        descarte_filter: false,
        cor_bastao_filter: []
        // ### FIM DA CORREÇÃO 2 ###
		
		
    };

    // --- FUNÇÕES AUXILIARES ---

    function submitFormAndShowLoading() {
        if (loadingIndicator) {
            loadingIndicator.style.display = 'flex';
            console.log("DEBUG: Indicador de carregamento MOSTRADO (submitFormAndShowLoading).");
        }
        const mainFilterForm = document.getElementById('filter-form');
        if (mainFilterForm) {
            mainFilterForm.submit();
        } else {
            console.error("Erro: Formulário 'filter-form' não encontrado para submeter.");
        }
    }

    function hideLoadingIndicator() {
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
            console.log("DEBUG: Indicador de carregamento ESCONDIDO (hideLoadingIndicator).");
        }
    }

    function initializeModalFiltersFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);

        currentModalFilters.idade_min = urlParams.get('idade_min') || '';
        currentModalFilters.idade_max = urlParams.get('idade_max') || '';

        if (urlParams.has('status')) {
            currentModalFilters.status = urlParams.get('status').split(',').map(s => s.trim());
        } else {
            currentModalFilters.status = [];
        }

        currentModalFilters.escore_min = urlParams.get('escore_min') || '';
        currentModalFilters.escore_max = urlParams.get('escore_max') || '';

        currentModalFilters.del_min = urlParams.get('del_min') || '';
        currentModalFilters.del_max = urlParams.get('del_max') || '';

        currentModalFilters.bst_filter = urlParams.get('bst_filter') || '';
		  // --- INÍCIO DA MODIFICAÇÃO: Inicializa o novo filtro ---
        currentModalFilters.previsao_cio_ciclos = urlParams.get('previsao_cio_ciclos') === '1';
        // --- FIM DA MODIFICAÇÃO ---
		
		// ADICIONAR ESTAS LINHAS
        currentModalFilters.inseminacao_min = urlParams.get('inseminacao_min') || '';
        currentModalFilters.inseminacao_max = urlParams.get('inseminacao_max') || '';
		
		  // ### INÍCIO DA CORREÇÃO 3: Lendo os novos filtros da URL ###
        currentModalFilters.iatf_filter = urlParams.get('iatf_filter') === '1';
        currentModalFilters.descarte_filter = urlParams.get('descarte_filter') === '1';
        currentModalFilters.cor_bastao_filter = urlParams.has('cor_bastao_filter') ? urlParams.get('cor_bastao_filter').split(',') : [];
        // ### FIM DA CORREÇÃO 3 ###
		
		
    }

    initializeModalFiltersFromUrl();

    function applyFiltersAndReload() {
        const urlParams = new URLSearchParams();

        // **CORREÇÃO AQUI**: Lógica de pesquisa de texto agora é adicionada se o campo existir
        if (searchQueryInput && searchQueryInput.value.trim() !== '') {
             urlParams.set('search_query', searchQueryInput.value.trim());
        }

        // Adiciona mes e ano do calendário para a URL se estiver na página de eventos
        const mesInput = document.querySelector('input[name="mes"]');
        const anoInput = document.querySelector('input[name="ano"]');
        if (mesInput && anoInput) {
            urlParams.set('mes', mesInput.value);
            urlParams.set('ano', anoInput.value);
        }

        let selectedGrupoValues = [];
        if (grupoCheckboxes) {
            grupoCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedGrupoValues.push(checkbox.value);
                }
            });
        }
        if (selectedGrupoValues.length > 0) {
            urlParams.set('grupo', selectedGrupoValues.join(','));
        }

        // Filtros do modal (Gado)
        if (currentModalFilters.idade_min !== '') {
            urlParams.set('idade_min', currentModalFilters.idade_min);
        }
        if (currentModalFilters.idade_max !== '') {
            urlParams.set('idade_max', currentModalFilters.idade_max);
        }
        if (currentModalFilters.status.length > 0) {
            urlParams.set('status', currentModalFilters.status.join(','));
        } else {
            urlParams.delete('status');
        }
        if (currentModalFilters.escore_min !== '') {
            urlParams.set('escore_min', currentModalFilters.escore_min);
        }
        if (currentModalFilters.escore_max !== '') {
            urlParams.set('escore_max', currentModalFilters.escore_max);
        }
        if (currentModalFilters.del_min !== '') {
            urlParams.set('del_min', currentModalFilters.del_min);
        }
        if (currentModalFilters.del_max !== '') {
            urlParams.set('del_max', currentModalFilters.del_max);
        }
        if (currentModalFilters.bst_filter === '1' || currentModalFilters.bst_filter === '0') {
            urlParams.set('bst_filter', currentModalFilters.bst_filter);
        } else {
            urlParams.delete('bst_filter');
        }
		 
        // Adiciona o novo filtro de cio se estiver marcado
        if (currentModalFilters.previsao_cio_ciclos) {
            urlParams.set('previsao_cio_ciclos', '1');
        }
        // --- FIM DA MODIFICAÇÃO ---

 // ADICIONAR ESTE BLOCO
        if (currentModalFilters.inseminacao_min !== '') {
            urlParams.set('inseminacao_min', currentModalFilters.inseminacao_min);
        }
        if (currentModalFilters.inseminacao_max !== '') {
            urlParams.set('inseminacao_max', currentModalFilters.inseminacao_max);
        }
		
		 // ### INÍCIO DA CORREÇÃO 4: Adicionando os novos filtros à URL ###
        if (currentModalFilters.iatf_filter) {
            urlParams.set('iatf_filter', '1');
        }
        if (currentModalFilters.descarte_filter) {
            urlParams.set('descarte_filter', '1');
        }
        if (currentModalFilters.cor_bastao_filter.length > 0) {
            urlParams.set('cor_bastao_filter', currentModalFilters.cor_bastao_filter.join(','));
        }
        // ### FIM DA CORREÇÃO 4 ###
		
		

        if (loadingIndicator) {
            loadingIndicator.style.display = 'flex';
            console.log("DEBUG: Indicador de carregamento MOSTRADO antes do redirecionamento (applyFiltersAndReload).");
        }
        window.location.search = urlParams.toString();
    }

    // --- EVENT LISTENERS ---




// Adicione este bloco de código junto com os outros "Event Listeners"
// =================================================================
// INÍCIO - LÓGICA PARA CHECKBOX 'MOSTRAR CONCLUÍDOS' (VIEW EVENTOS)
// =================================================================
if (mostrarConcluidosCheckbox) {
    // Função para atualizar a visibilidade dos eventos concluídos
    function atualizarVisibilidadeConcluidos() {
        const concluidosRows = document.querySelectorAll('.status-concluido');
        if (mostrarConcluidosCheckbox.checked) {
            concluidosRows.forEach(row => {
                // 'display' precisa ser 'table-row' para renderizar corretamente em tabelas
                row.style.display = 'table-row';
            });
        } else {
            concluidosRows.forEach(row => {
                row.style.display = 'none';
            });
        }
    }

    // Adiciona o listener para o evento de 'change'
    mostrarConcluidosCheckbox.addEventListener('change', atualizarVisibilidadeConcluidos);

    // Executa a função uma vez ao carregar a página para definir o estado inicial
    atualizarVisibilidadeConcluidos();
}
// =================================================================
// FIM - LÓGICA PARA CHECKBOX 'MOSTRAR CONCLUÍDOS'
// ==========================================



// =================================================================
// INÍCIO DO NOVO CÓDIGO PARA O BOTÃO 'CONCLUIR EVENTO' (versão AJAX)
// =================================================================

const completeEventButtons = document.querySelectorAll('.complete-icon');

completeEventButtons.forEach(button => {
    button.addEventListener('click', function(event) {
        event.stopPropagation(); // Impede o clique na linha

        const eventId = this.getAttribute('data-event-id');
        if (!eventId) return;

        const confirmCompletion = confirm('Tem certeza que deseja marcar este evento como concluído?');

        if (confirmCompletion) {
            // Mostra o indicador de carregamento
            const loadingIndicator = document.getElementById('loading-indicator');
            if (loadingIndicator) loadingIndicator.style.display = 'flex';

            // Prepara os dados para enviar via POST
            const formData = new FormData();
            formData.append('action', 'mark_complete');
            formData.append('id', eventId);

            // Faz a requisição AJAX para o controller
            fetch('../../controllers/EventoController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Converte a resposta do PHP para JSON
            .then(data => {
                // 'data' é o array $response do seu PHP
                if (data.success) {
                    // Se deu tudo certo, recarrega a página para ver o resultado
                    alert(data.message); // Opcional: mostra a mensagem de sucesso
                    window.location.reload(); 
                } else {
                    // Se deu erro, mostra a mensagem de falha e esconde o loading
                    alert('Falha: ' + data.message);
                    if (loadingIndicator) loadingIndicator.style.display = 'none';
                }
            })
            .catch(error => {
                // Em caso de erro de rede, etc.
                console.error('Erro na requisição Fetch:', error);
                alert('Ocorreu um erro de comunicação. Verifique o console.');
                if (loadingIndicator) loadingIndicator.style.display = 'none';
            });
        }
    });
});
// =================================================================
// FIM DO NOVO CÓDIGO
// =================================================================


    const backButton = document.getElementById('backButton');
    if (backButton) {
        console.log("DEBUG: Botão 'Voltar' encontrado no DOM. History length:", history.length);
        if (history.length > 1) {
            backButton.addEventListener('click', function() {
                console.log("DEBUG: Clique no botÃ£o 'Voltar' detectado. Tentando history.back().");
                history.back();
            });
            backButton.style.opacity = '1';
            backButton.style.cursor = 'pointer';
        } else {
            backButton.style.opacity = '0.3';
            backButton.style.cursor = 'not-allowed';
            console.log("DEBUG: Botão 'Voltar' desativado (sem histórico para voltar).");
        }
    }

    // **CORREÇÃO AQUI**: Listener para o campo de pesquisa foi reativado.
    if (searchQueryInput) {
       let searchTimeout;
       searchQueryInput.addEventListener('keyup', function() {
           clearTimeout(searchTimeout);
           searchTimeout = setTimeout(applyFiltersAndReload, 2000); // 500ms de delay
       });
    }

    // Listener para os checkboxes de grupo (presente na view gado)
    if (grupoCheckboxes && grupoCheckboxes.length > 0) {
        grupoCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                applyFiltersAndReload();
            });
        });
    }

 
 

    // Listener para o botão "Mais Filtros" (abre o modal)
    if (openFilterBtn) {
        openFilterBtn.addEventListener('click', function(event) {
            event.preventDefault();
            if (filterModal) filterModal.style.display = 'flex';
            
            // Pré-preenche campos do modal COM base em currentModalFilters
            if (idadeMinInput) idadeMinInput.value = currentModalFilters.idade_min;
            if (idadeMaxInput) idadeMaxInput.value = currentModalFilters.idade_max;

            if (statusCheckboxes) {
                statusCheckboxes.forEach(checkbox => {
                    checkbox.checked = currentModalFilters.status.includes(checkbox.value);
                });
            }

            if (escoreMinInput) escoreMinInput.value = currentModalFilters.escore_min;
            if (escoreMaxInput) escoreMaxInput.value = currentModalFilters.escore_max;
            if (delMinInput) delMinInput.value = currentModalFilters.del_min;
            if (delMaxInput) delMaxInput.value = currentModalFilters.del_max;

            if (bstFilterRadios) {
                bstFilterRadios.forEach(radio => {
                    radio.checked = (radio.value === currentModalFilters.bst_filter);
                });
            }
			if (previsaoCioCheckbox) previsaoCioCheckbox.checked = currentModalFilters.previsao_cio_ciclos;
            // --- FIM DA MODIFICAÇÃO ---
			
			 // ### INÍCIO DA CORREÇÃO 5: Preenchendo os novos filtros no modal ###
            if (iatfFilterCheckbox) iatfFilterCheckbox.checked = currentModalFilters.iatf_filter;
            if (descarteFilterCheckbox) descarteFilterCheckbox.checked = currentModalFilters.descarte_filter;
            if (corBastaoCheckboxes) corBastaoCheckboxes.forEach(cb => cb.checked = currentModalFilters.cor_bastao_filter.includes(cb.value));
            // ### FIM DA CORREÇÃO 5 ###
			
			
        });
    }

    // Listeners para fechar o modal
    if (closeButton && filterModal) {
        closeButton.onclick = function() { filterModal.style.display = 'none'; }
    }
    if (closeFilterModalBtn && filterModal) {
        closeFilterModalBtn.onclick = function() { filterModal.style.display = 'none'; }
    }
    if (filterModal) {
        window.onclick = function(event) {
            if (event.target == filterModal) {
                filterModal.style.display = 'none';
            }
        }
    }

    // Listener para o botão "Aplicar Filtros" (dentro do modal)
    if (applyModalFiltersBtn) {
        applyModalFiltersBtn.addEventListener('click', function(event) {
            event.preventDefault();
            
            currentModalFilters.idade_min = idadeMinInput ? idadeMinInput.value : '';
            currentModalFilters.idade_max = idadeMaxInput ? idadeMaxInput.value : '';

            currentModalFilters.status = [];
            if (statusCheckboxes) {
                statusCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        currentModalFilters.status.push(checkbox.value);
                    }
                });
            }

            currentModalFilters.escore_min = escoreMinInput ? escoreMinInput.value : '';
            currentModalFilters.escore_max = escoreMaxInput ? escoreMaxInput.value : '';
            currentModalFilters.del_min = delMinInput ? delMinInput.value : '';
            currentModalFilters.del_max = delMaxInput ? delMaxInput.value : '';

            if (bstFilterRadios) {
                let selectedBst = '';
                bstFilterRadios.forEach(radio => {
                    if (radio.checked) {
                        selectedBst = radio.value;
                    }
                });
                currentModalFilters.bst_filter = selectedBst;
            }
			currentModalFilters.previsao_cio_ciclos = previsaoCioCheckbox ? previsaoCioCheckbox.checked : false;
            
			// ADICIONAR ESTAS LINHAS
            const inseminacaoMinInput = document.getElementById('inseminacao_min');
            const inseminacaoMaxInput = document.getElementById('inseminacao_max');
            currentModalFilters.inseminacao_min = inseminacaoMinInput ? inseminacaoMinInput.value : '';
            currentModalFilters.inseminacao_max = inseminacaoMaxInput ? inseminacaoMaxInput.value : '';
			
			 // ### INÍCIO DA CORREÇÃO 6: Capturando os novos filtros ao aplicar ###
            currentModalFilters.iatf_filter = iatfFilterCheckbox ? iatfFilterCheckbox.checked : false;
            currentModalFilters.descarte_filter = descarteFilterCheckbox ? descarteFilterCheckbox.checked : false;
            currentModalFilters.cor_bastao_filter = Array.from(corBastaoCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            // ### FIM DA CORREÇÃO 6 ###
			
			
            
            if (filterModal) filterModal.style.display = 'none';
            applyFiltersAndReload();
        });
    }

    // Listener para o botão "Limpar Filtros do Modal"
    if (clearModalFiltersBtn) {
        clearModalFiltersBtn.addEventListener('click', function(event) {
            event.preventDefault();

            currentModalFilters.idade_min = '';
            currentModalFilters.idade_max = '';
            currentModalFilters.status = [];
            currentModalFilters.escore_min = '';
            currentModalFilters.escore_max = '';
            currentModalFilters.del_min = '';
            currentModalFilters.del_max = '';
            currentModalFilters.bst_filter = '';
			currentModalFilters.previsao_cio_ciclos = false;
			// ### INÍCIO DA CORREÇÃO 7: Limpando o estado dos novos filtros ###
            currentModalFilters.iatf_filter = false;
            currentModalFilters.descarte_filter = false;
            currentModalFilters.cor_bastao_filter = [];
            // ### FIM DA CORREÇÃO 7 ###
			

            if (idadeMinInput) idadeMinInput.value = '';
            if (idadeMaxInput) idadeMaxInput.value = '';
            if (statusCheckboxes) statusCheckboxes.forEach(checkbox => checkbox.checked = false);
            if (escoreMinInput) escoreMinInput.value = '';
            if (escoreMaxInput) escoreMaxInput.value = '';
            if (delMinInput) delMinInput.value = '';
            if (delMaxInput) delMaxInput.value = '';
            if (bstFilterRadios) bstFilterRadios.forEach(radio => { if (radio.value === '') radio.checked = true; else radio.checked = false; });
             if (previsaoCioCheckbox) previsaoCioCheckbox.checked = false;
			  // ### INÍCIO DA CORREÇÃO 8: Limpando os novos campos do formulário ###
            if(iatfFilterCheckbox) iatfFilterCheckbox.checked = false;
            if(descarteFilterCheckbox) descarteFilterCheckbox.checked = false;
            if(corBastaoCheckboxes) corBastaoCheckboxes.forEach(cb => cb.checked = false);
            // ### FIM DA CORREÇÃO 8 ###
			 
			  // ADICIONAR ESTAS LINHAS
            const inseminacaoMinInput = document.getElementById('inseminacao_min');
            const inseminacaoMaxInput = document.getElementById('inseminacao_max');
            if(inseminacaoMinInput) inseminacaoMinInput.value = '';
            if(inseminacaoMaxInput) inseminacaoMaxInput.value = '';



            applyFiltersAndReload();
        });
    }

    // Listener para o botão "Limpar Todos os Filtros"
    if (clearAllFiltersBtn) {
        clearAllFiltersBtn.addEventListener('click', function(event) {
            event.preventDefault();
            currentModalFilters = {
                idade_min: '',
                idade_max: '',
                status: [],
                escore_min: '',
                escore_max: '',
                del_min: '',
                del_max: '',
                bst_filter: ''
            };
            if (searchQueryInput) searchQueryInput.value = '';
            if (grupoCheckboxes) grupoCheckboxes.forEach(checkbox => checkbox.checked = false);

            window.location.search = '';
        });
    }

    // --- Lógica para Acionar Rotina Home Assistant (se botões existirem) ---
    function acionarHomeAssistantService(entityIdToControl, serviceAction) {
        const formData = new FormData();
        formData.append('entity_id', entityIdToControl);
        formData.append('service_action', serviceAction);

        console.log(`Acionando HA: Enviando entity_id: '${entityIdToControl}' com ação: '${serviceAction}'`);

        fetch('../../controllers/acao_home_assistant.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    console.error(`Erro HTTP ${response.status} na API do Home Assistant (resposta PHP):`, text);
                    throw new Error(`Erro HTTP ${response.status} na API do Home Assistant (resposta PHP).`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Resposta Home Assistant (API - JSON):', data);
            if (data.success) {
                console.log(`Comando '${serviceAction}' para '${entityIdToControl}' enviado com sucesso para o HA.`);
            } else {
                alert(`Falha ao enviar comando '${serviceAction}' para '${entityIdToControl}': ` + (data.message || 'Erro desconhecido. Verifique o console.'));
            }
        })
        .catch(error => {
            console.error('Erro na requisição Fetch:', error);
            alert(`Erro de comunicação para acionar '${serviceAction}' em '${entityIdToControl}'. Verifique sua conexão ou console para mais detalhes.`);
        });
    }

    if (ligarDispositivoHABtn) {
        ligarDispositivoHABtn.addEventListener('click', function() {
            console.log("Botão 'Ligar Dispositivo HA' clicado! Disparando rotina...");
            const deviceId = 'switch.sonoff_10017a5aac_1';
            acionarHomeAssistantService(deviceId, 'turn_on');
        });
    }

    if (desligarDispositivoHABtn) {
        desligarDispositivoHABtn.addEventListener('click', function() {
            console.log("Botão 'Desligar Dispositivo HA' clicado! Disparando rotina...");
            const deviceId = 'switch.sonoff_10017a5aac_1';
            acionarHomeAssistantService(deviceId, 'turn_off');
        });
    }
	
	 // --- INÍCIO DA CORREÇÃO ---
    // Listeners separados para o NOVO CHUPIM
    const ligarChupimNovoBtn = document.getElementById('acionarChupimNovoBtn');
    const desligarChupimNovoBtn = document.getElementById('desligarChupimNovoBtn');

    if (ligarChupimNovoBtn) {
        ligarChupimNovoBtn.addEventListener('click', function() {
            const deviceId = 'switch.sonoff_10017a5aac_2'; // ID do Canal 2
            acionarHomeAssistantService(deviceId, 'turn_on');
        });
    }

    if (desligarChupimNovoBtn) {
        desligarChupimNovoBtn.addEventListener('click', function() {
            const deviceId = 'switch.sonoff_10017a5aac_2'; // ID do Canal 2
            acionarHomeAssistantService(deviceId, 'turn_off');
        });
    }
    // --- FIM DA CORREÇÃO ---
	  // --- INÍCIO DA MODIFICAÇÃO ---
    // Listeners para o CHUPIM MILHO (Canal 3)
    const ligarChupimMilhoBtn = document.getElementById('acionarChupimMilhoBtn');
    const desligarChupimMilhoBtn = document.getElementById('desligarChupimMilhoBtn');

    if (ligarChupimMilhoBtn) {
        ligarChupimMilhoBtn.addEventListener('click', function() {
            const deviceId = 'switch.sonoff_10017a5aac_3';
            acionarHomeAssistantService(deviceId, 'turn_on');
        });
    }

    if (desligarChupimMilhoBtn) {
        desligarChupimMilhoBtn.addEventListener('click', function() {
            const deviceId = 'switch.sonoff_10017a5aac_3';
            acionarHomeAssistantService(deviceId, 'turn_off');
        });
    }
    // --- FIM DA MODIFICAÇÃO ---
  // --- SEÇÃO DE CONTROLE DOS DISPOSITIVOS HOME ASSISTANT ---

    const colorOn = 'var(--success-green)';
    const colorOff = '#A9A9A9';
    const colorWaiting = '#FFD700';
    const colorError = '#B00020';

    function updateIconState(icon, state) {
        if (!icon) return;
        switch (state) {
            case 'on':
                icon.style.color = colorOn;
                break;
            case 'off':
                icon.style.color = colorOff;
                break;
            case 'waiting':
                icon.style.color = colorWaiting;
                break;
            default:
                icon.style.color = colorError;
                break;
        }
        icon.dataset.state = state;
    }

    function fetchAllDeviceStates() {
        const icons = document.querySelectorAll('.ha-toggle-icon');
        if (icons.length === 0) return;
        const entityIds = Array.from(icons).map(icon => icon.dataset.entityId).join(',');
        
        fetch(`../../controllers/get_ha_state.php?entities=${entityIds}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.states) {
                    for (const [entityId, state] of Object.entries(data.states)) {
                        const icon = document.querySelector(`.ha-toggle-icon[data-entity-id="${entityId}"]`);
                        updateIconState(icon, state);
                    }
                }
            })
            .catch(error => console.error('Erro ao buscar estados iniciais:', error));
    }

    function sendDeviceCommand(icon) {
        const entityId = icon.dataset.entityId;
        const currentState = icon.dataset.state;

        if (!entityId || currentState === 'waiting') return;

        // --- INÍCIO DA MODIFICAÇÃO: LÓGICA DE TURN_ON/TURN_OFF ---
        // Determina a ação oposta à atual
        const serviceAction = (currentState === 'on') ? 'turn_off' : 'turn_on';
        // --- FIM DA MODIFICAÇÃO ---

        updateIconState(icon, 'waiting');

        const formData = new FormData();
        formData.append('entity_id', entityId);
        formData.append('service_action', serviceAction);

        fetch('../../controllers/acao_home_assistant.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                pollForStateChange(icon, currentState);
            } else {
                console.error(`Falha ao enviar comando '${serviceAction}':`, data.message);
                updateIconState(icon, currentState);
            }
        })
        .catch(error => {
            console.error('Erro de comunicação ao enviar comando:', error);
            updateIconState(icon, currentState);
        });
    }

    function pollForStateChange(icon, originalState) {
        const entityId = icon.dataset.entityId;
        let attempts = 0;
        const maxAttempts = 10;
        const interval = 1000;

        const poller = setInterval(() => {
            attempts++;
            if (attempts > maxAttempts) {
                clearInterval(poller);
                console.error(`Timeout: Não foi possível confirmar a mudança de estado para ${entityId}.`);
                updateIconState(icon, originalState);
                return;
            }

            fetch(`../../controllers/get_ha_state.php?entities=${entityId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.states && data.states[entityId]) {
                        const newState = data.states[entityId];
                        if (newState !== 'unavailable' && newState !== 'error' && newState !== originalState) {
                            clearInterval(poller);
                            updateIconState(icon, newState);
                        }
                    }
                })
                .catch(error => {
                    console.error(`Erro durante o polling para ${entityId}:`, error);
                    clearInterval(poller);
                    updateIconState(icon, 'error');
                });
        }, interval);
    }

    document.querySelectorAll('.ha-toggle-icon').forEach(icon => {
        icon.addEventListener('click', () => sendDeviceCommand(icon));
    });

    fetchAllDeviceStates();


    // --- Lógica: Tornar as linhas da tabela clicáveis (para Gado e Inseminaçoes) ---
    const clickableRows = document.querySelectorAll('.clickable-row');
    console.log("DEBUG: Status de detecção de linhas clicáveis:", clickableRows);
    clickableRows.forEach(row => {
        row.addEventListener('click', function() {
            const href = this.dataset.href;
            if (href) {
                window.location.href = href;
            } else {
                console.warn("DEBUG: Atributo data-href não encontrado na linha clicada!");
            }
        });
    });

    // --- Lógica para esconder o indicador de carregamento ---
    if (loadingIndicator) {
        hideLoadingIndicator();
    }
    window.addEventListener('load', function() {
        if (loadingIndicator) {
            hideLoadingIndicator();
        }
    });
});