document.addEventListener('DOMContentLoaded', function() {
    // Elementos específicos da página de filtros de manejo
    const manejoFilterModal = document.getElementById('manejoFilterModal');
    const openManejoFilterBtn = document.getElementById('openManejoFilterBtn');
    const closeManejoModalBtn = document.querySelector('#manejoFilterModal .close-button');
    const applyManejoFiltersBtn = document.getElementById('applyManejoFiltersBtn');
    const searchBrincoInput = document.getElementById('search_brinco');

    // Função para aplicar APENAS os filtros de manejo
    function applyManejoFilters() {
        const urlParams = new URLSearchParams();

        if (searchBrincoInput && searchBrincoInput.value.trim() !== '') {
            urlParams.set('search_query', searchBrincoInput.value.trim());
        }

        const dataInicio = document.getElementById('data_inicio_filter');
        if (dataInicio && dataInicio.value) {
            urlParams.set('data_inicio', dataInicio.value);
        }
        
        const dataFim = document.getElementById('data_fim_filter');
        if (dataFim && dataFim.value) {
            urlParams.set('data_fim', dataFim.value);
        }

        const tiposManejo = Array.from(document.querySelectorAll('input[name="tipos_manejo[]"]:checked')).map(cb => cb.value);
        if (tiposManejo.length > 0) {
            urlParams.set('tipos_manejo', tiposManejo.join(','));
        }

        const idManejo = $('#id_manejo_filter').val();
        if (idManejo) {
            urlParams.set('id_manejo', idManejo);
        }

        window.location.search = urlParams.toString();
    }

    // --- EVENT LISTENERS PARA MANEJO ---

    if (openManejoFilterBtn) {
        openManejoFilterBtn.addEventListener('click', () => {
            const selectManejo = $('#id_manejo_filter');

            // Carrega a lista de manejos via AJAX apenas uma vez
            if (!selectManejo.data('loaded')) {
                fetch(`../../controllers/ManejoController.php?action=get_all`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.manejos.length > 0) {
                            const urlParams = new URLSearchParams(window.location.search);
                            const selectedId = urlParams.get('id_manejo');
                            
                            data.manejos.forEach(m => {
                                const option = new Option(m.nome, m.id, false, selectedId == m.id);
                                selectManejo.append(option);
                            });
                            
                            // **CORREÇÃO AQUI**: Inicializa o Select2 DEPOIS de adicionar as opções
                            selectManejo.select2({
                                placeholder: "Todos os Manejos",
                                allowClear: true,
                                dropdownParent: $('#manejoFilterModal') // Essencial para funcionar dentro do modal
                            });
                            
                            // Força o Select2 a renderizar o valor que já estava selecionado (se houver)
                            selectManejo.trigger('change'); 
                            selectManejo.data('loaded', true);
                        }
                    })
                    .catch(error => console.error('Erro ao carregar manejos para o filtro:', error));
            }
            if (manejoFilterModal) manejoFilterModal.style.display = 'flex';
        });
    }

    // O resto dos listeners permanece o mesmo...
    if (closeManejoModalBtn) {
        closeManejoModalBtn.addEventListener('click', () => manejoFilterModal.style.display = 'none');
    }
    if (manejoFilterModal) {
        manejoFilterModal.addEventListener('click', (e) => {
            if (e.target === manejoFilterModal) {
                manejoFilterModal.style.display = 'none';
            }
        });
    }

    if (applyManejoFiltersBtn) {
        applyManejoFiltersBtn.addEventListener('click', applyManejoFilters);
    }

    if (searchBrincoInput) {
        let searchTimeout;
        searchBrincoInput.addEventListener('keyup', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyManejoFilters, 500);
        });
    }
});