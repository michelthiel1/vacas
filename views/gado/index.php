<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';
require_once __DIR__ . '/../../models/Inseminacao.php';
require_once __DIR__ . '/../../models/Parto.php';

$gado = new Gado($pdo);
$inseminacaoModel = new Inseminacao($pdo);
$partoModel = new Parto($pdo);

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Lógica de Filtros (mantida)
$filters = [];
if (!empty($_GET['search_query'])) $filters['search_query'] = trim($_GET['search_query']);
if (!empty($_GET['grupo'])) $filters['grupo'] = explode(',', $_GET['grupo']);
if (!empty($_GET['status'])) $filters['status'] = explode(',', $_GET['status']);
if (isset($_GET['idade_min']) && $_GET['idade_min'] !== '') $filters['idade_min'] = $_GET['idade_min'];
if (isset($_GET['idade_max']) && $_GET['idade_max'] !== '') $filters['idade_max'] = $_GET['idade_max'];
if (isset($_GET['del_min']) && $_GET['del_min'] !== '') $filters['del_min'] = $_GET['del_min'];
if (isset($_GET['del_max']) && $_GET['del_max'] !== '') $filters['del_max'] = $_GET['del_max'];
if (isset($_GET['escore_min']) && $_GET['escore_min'] !== '') $filters['escore_min'] = $_GET['escore_min'];
if (isset($_GET['escore_max']) && $_GET['escore_max'] !== '') $filters['escore_max'] = $_GET['escore_max'];
if (isset($_GET['bst_filter']) && $_GET['bst_filter'] !== '') $filters['bst_filter'] = $_GET['bst_filter'];
if (isset($_GET['previsao_cio_ciclos']) && $_GET['previsao_cio_ciclos'] == '1') {
    $filters['previsao_cio_ciclos'] = true;
}
if (isset($_GET['inseminacao_min']) && $_GET['inseminacao_min'] !== '') $filters['inseminacao_min'] = $_GET['inseminacao_min'];
if (isset($_GET['inseminacao_max']) && $_GET['inseminacao_max'] !== '') $filters['inseminacao_max'] = $_GET['inseminacao_max'];

// Novos filtros
if (isset($_GET['iatf_filter']) && $_GET['iatf_filter'] == '1') $filters['iatf_filter'] = true;
if (isset($_GET['descarte_filter']) && $_GET['descarte_filter'] == '1') $filters['descarte_filter'] = true;
if (!empty($_GET['cor_bastao_filter'])) $filters['cor_bastao_filter'] = explode(',', $_GET['cor_bastao_filter']);

// ### INÍCIO DA QUERY CORRIGIDA ###
$query = "
    WITH LatestInsemination AS (
        SELECT id_vaca, data_inseminacao, tipo, ROW_NUMBER() OVER(PARTITION BY id_vaca ORDER BY data_inseminacao DESC, id DESC) as rn FROM inseminacoes WHERE ativo = 1
    ),
    LatestIatfManejo AS (
        SELECT id_gado, data_aplicacao, ROW_NUMBER() OVER(PARTITION BY id_gado ORDER BY data_aplicacao DESC, id DESC) as rn FROM registros_manejos WHERE id_manejo = 3
    )
    SELECT
        g.id, g.brinco, g.nome, g.nascimento, g.raca, g.observacoes, g.status, g.grupo, g.bst, g.ativo, g.data_monitoramento_cio,
        g.leite_descarte, g.cor_bastao,
        g.created_at, g.updated_at, g.escore, g.sexo, g.id_pai, g.id_mae,
        li.data_inseminacao AS ultima_inseminacao_data,
        DATEDIFF(CURDATE(), li.data_inseminacao) AS dias_ultima_inseminacao,
        DATEDIFF(CURDATE(), lim.data_aplicacao) AS dias_ultimo_manejo_iatf,
        DATEDIFF(CURDATE(), g.data_monitoramento_cio) AS dias_monitoramento_cio
    FROM gado g
    LEFT JOIN LatestInsemination li ON g.id = li.id_vaca AND li.rn = 1
    LEFT JOIN LatestIatfManejo lim ON g.id = lim.id_gado AND lim.rn = 1
";

$where_clauses = ["g.ativo = 1"];
$having_clauses = []; 
$queryParams = [];

// Construção dinâmica das cláusulas WHERE e HAVING (sem alterações aqui)
if (!empty($filters['search_query'])) {
    $where_clauses[] = "(g.brinco LIKE :search_query OR g.nome LIKE :search_query OR g.status LIKE :search_query)";
    $queryParams[':search_query'] = '%' . $filters['search_query'] . '%';
}
if (!empty($filters['grupo'])) {
    $grupoPlaceholders = [];
    foreach ($filters['grupo'] as $key => $grupo) {
        $placeholder = ":grupo_" . $key;
        $grupoPlaceholders[] = $placeholder;
        $queryParams[$placeholder] = $grupo;
    }
    $where_clauses[] = "g.grupo IN (" . implode(",", $grupoPlaceholders) . ")";
}
if (!empty($filters['status'])) {
    $statusPlaceholders = [];
    foreach ($filters['status'] as $key => $status) {
        $placeholder = ":status_" . $key;
        $statusPlaceholders[] = $placeholder;
        $queryParams[$placeholder] = $status;
    }
    $where_clauses[] = "g.status IN (" . implode(",", $statusPlaceholders) . ")";
}
if (isset($filters['bst_filter']) && $filters['bst_filter'] !== '') {
    $where_clauses[] = "g.bst = :bst_filter";
    $queryParams[':bst_filter'] = $filters['bst_filter'];
}

// Filtro de Descarte
if (isset($filters['descarte_filter']) && $filters['descarte_filter']) {
    $where_clauses[] = "g.leite_descarte = 'Sim'";
}

// Filtro de Cor de Bastão
if (!empty($filters['cor_bastao_filter'])) {
    $bastaoPlaceholders = [];
    foreach ($filters['cor_bastao_filter'] as $key => $cor) {
        $placeholder = ":cor_bastao_" . $key;
        $bastaoPlaceholders[] = $placeholder;
        $queryParams[$placeholder] = $cor;
    }
    $where_clauses[] = "g.cor_bastao IN (" . implode(",", $bastaoPlaceholders) . ")";
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

if (isset($filters['previsao_cio_ciclos']) && $filters['previsao_cio_ciclos']) {
    $having_clauses[] = "(
        (g.data_monitoramento_cio IS NOT NULL)
        OR
        (dias_ultima_inseminacao IS NOT NULL AND dias_ultima_inseminacao >= 18 AND (MOD(dias_ultima_inseminacao, 21) >= 18 OR MOD(dias_ultima_inseminacao, 21) <= 3))
    )";
}

// Filtro de IATF
if (isset($filters['iatf_filter']) && $filters['iatf_filter']) {
    $having_clauses[] = "(dias_ultimo_manejo_iatf IS NOT NULL AND dias_ultimo_manejo_iatf <= 10)";
}

if (!empty($having_clauses)) {
    $query .= " HAVING " . implode(" AND ", $having_clauses);
}

$query .= " ORDER BY g.brinco ASC";

$stmt = $pdo->prepare($query);
foreach ($queryParams as $param => $value) {
    $stmt->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
}
$stmt->execute();
$num = $stmt->rowCount();
// ### FIM DA QUERY ###

$grupoOptions = ['Bezerra', 'Novilha', 'Lactante', 'Seca', 'Corte'];
?>

<style>
    /* Estilos existentes */
    .icon-group { display: flex; align-items: center; gap: 8px; }
    .bst-icon {
        display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;
        border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.1); font-size: 0.75em;
        color: white; font-weight: bold;
    }
    .bst-icon.bst-sim { background-color: var(--bst-green); }
    .bst-icon.bst-nao { background-color: var(--bst-gray); }
    .text-icon {
        display: flex; align-items: center; justify-content: center; width: 32px; height: 32px;
        border-radius: 50%; box-shadow: 0 1px 2px rgba(0,0,0,0.1); font-size: 0.75em;
        color: white; font-weight: bold;
    }
    .iatf-icon { background-color: var(--highlight-orange); }
    .descarte-icon { background-color: var(--error-red); }
    .bastao-icon.azul { background-color: #3498db; }
    .bastao-icon.verde { background-color: #2ecc71; }
    .bastao-icon.vermelho { background-color: #e74c3c; }
    .container { max-width: 100%; padding-left: 0; padding-right: 0; }
    .animal-list-container { width: 100%; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); }
    .animal-list-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid var(--border-color); }
    .animal-list-item.item-hidden { display: none !important; }
    .animal-list-item:last-child { border-bottom: none; }
    .animal-list-item:nth-child(even) { background-color: var(--background-medium); }
    .animal-list-item a.main-info-link { display: flex; flex-direction: column; flex-grow: 1; min-width: 0; text-decoration: none; color: inherit; }
    .animal-list-item a.main-info-link:hover .brinco-display { color: var(--primary-orange); }
    .animal-list-item .secondary-info { display: flex; flex-direction: column; align-items: flex-end; flex-shrink: 0; margin-left: 10px; cursor: pointer; }
    .animal-list-item .brinco-display { font-size: 1.5rem; font-weight: 800; color: var(--dark-orange); line-height: 1.2; transition: color 0.2s ease; }
    .animal-list-item .status-display { font-size: 0.9rem; color: var(--neutral-text); opacity: 0.9; line-height: 1.2; word-wrap: break-word; }
    .animal-list-item .grupo-display { font-size: 0.9rem; color: var(--neutral-text); opacity: 0.9; margin-top: 2px; }
    .page-title-container { display: flex; justify-content: space-between; align-items: center; width: 100%; }
    #reexibir-btn { display: none; font-size: 0.8em; font-weight: normal; padding: 4px 8px; background-color: var(--background-medium); color: var(--neutral-dark); border: 1px solid var(--border-color); }
    .hide-icon { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; background-color: #f0f0f0; color: #aaa; font-size: 1em; transition: all 0.2s ease; border: 1px solid #ddd; }
    .secondary-info:hover .hide-icon { background-color: var(--error-red); color: white; border-color: var(--dark-orange); }
</style>

<div class="page-title-container">
    <h2 class="page-title">Animais (<?php echo $num; ?>)</h2>
    <button id="reexibir-btn" class="btn">Reexibir Ocultos</button>
</div>

<?php echo $message; ?>

<div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php">
        <div class="top-filter-bar">
            <div class="search-input-group">
                <input type="text" id="search_query" name="search_query" placeholder="Pesquisar..." value="<?php echo htmlspecialchars($_GET['search_query'] ?? '', ENT_QUOTES); ?>">
            </div>
           <div class="filter-buttons-group">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="create.php" class="btn btn-primary add-animal-inline-btn" title="Adicionar Novo Animal">+</a>
                <?php endif; ?>
                <button id="openFilterBtn" class="btn btn-secondary more-filters-inline-btn" type="button">Filtros</button>
                <?php if (!empty($filters)): ?>
                    <button id="clearAllFiltersBtn" class="btn btn-danger" type="button">Limpar</button>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="filter-group-checkboxes-container">
            <div class="checkbox-options-group">
                <?php foreach ($grupoOptions as $option): ?>
                    <div>
                        <input type="checkbox" id="grupo_<?php echo strtolower($option); ?>" name="grupo[]" value="<?php echo htmlspecialchars($option, ENT_QUOTES); ?>" <?php echo in_array($option, $filters['grupo'] ?? []) ? 'checked' : ''; ?>>
                        <label for="grupo_<?php echo strtolower($option); ?>"><?php echo htmlspecialchars($option, ENT_QUOTES); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </form>
</div>

<?php if ($num > 0) : ?>
    <div class="animal-list-container">
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
            <div class="animal-list-item" id="animal-row-<?php echo $row['id']; ?>">
                <a class="main-info-link" href="view.php?id=<?php echo $row['id']; ?>">
                    <div class="main-info">
                        <span class="brinco-display"><?php echo htmlspecialchars($row['brinco'] ?? '', ENT_QUOTES); ?></span>
                        <span class="status-display">
                            <?php
                            echo htmlspecialchars($row['status'] ?? '', ENT_QUOTES);
                            $detalhes_status = [];
                            if (!empty($row['nascimento'])) {
                                try {
                                    $data_nascimento = new DateTime($row['nascimento']);
                                    $hoje = new DateTime();
                                    $diferenca_idade = $hoje->diff($data_nascimento);
                                    $anos = $diferenca_idade->y;
                                    $meses = $diferenca_idade->m;
                                    if ($anos > 0) {
                                        $detalhes_status[] = $anos . "a " . $meses . "m";
                                    } else {
                                        $detalhes_status[] = $meses . "m";
                                    }
                                } catch (Exception $e) {}
                            }
                            if (!in_array($row['grupo'], ['Novilha', 'Bezerra'])) {
                                $ultimoParto = $partoModel->getUltimoParto($row['id']);
                                if ($ultimoParto && !empty($ultimoParto['data_parto'])) {
                                    try {
                                        $data_parto = new DateTime($ultimoParto['data_parto']);
                                        $hoje = new DateTime();
                                        $diferenca_parto = $hoje->diff($data_parto);
                                        $detalhes_status[] = "DEL: " . $diferenca_parto->days . "d";
                                    } catch (Exception $e) {}
                                }
                            }
                            $dias_ref = null;
                            $prefixo_ref = "INSEM.:";
                            if (!empty($row['data_monitoramento_cio'])) {
                                $dias_ref = (new DateTime())->diff(new DateTime($row['data_monitoramento_cio']))->days;
                                $prefixo_ref = "MONIT.:";
                            } elseif ($row['status'] == 'Inseminada' || ($row['grupo'] == 'Novilha' && $row['status'] == 'Prenha')) {
                                if (!empty($row['ultima_inseminacao_data'])) {
                                    $dias_ref = $row['dias_ultima_inseminacao'];
                                }
                            }
                            if ($dias_ref !== null) {
                                $detalhes_status[] = $prefixo_ref . " " . $dias_ref . "d";
                            }
                            if (!empty($detalhes_status)) {
                                echo " (" . implode(" / ", $detalhes_status) . ")";
                            }
                            ?>
                        </span>
                    </div>
                </a>
                <div class="secondary-info" data-animal-id="<?php echo $row['id']; ?>">
                    <div class="icon-group">
                        
                        <?php if ($row['leite_descarte'] === 'Sim'): ?>
                            <div class="text-icon descarte-icon" title="Descarte de Leite: Sim">DES</div>
                        <?php endif; ?>

                        <?php if (!empty($row['cor_bastao'])):
                            $cor_class_bastao = strtolower($row['cor_bastao']);
                        ?>
                            <div class="text-icon bastao-icon <?php echo htmlspecialchars($cor_class_bastao, ENT_QUOTES); ?>" title="Bastão: <?php echo htmlspecialchars($row['cor_bastao'], ENT_QUOTES); ?>">BAS</div>
                        <?php endif; ?>
                        
                        <?php if (isset($row['dias_ultimo_manejo_iatf']) && $row['dias_ultimo_manejo_iatf'] <= 10): ?>
                            <div class="text-icon iatf-icon" title="Protocolo IATF realizado há <?php echo $row['dias_ultimo_manejo_iatf']; ?> dia(s)">IATF</div>
                        <?php endif; ?>

                        <span class="bst-icon bst-<?php echo ($row['bst'] == 1 ? 'sim' : 'nao'); ?>" title="BST">BST</span>

                        <div class="hide-trigger">
                            <i class="fas fa-eye-slash hide-icon" title="Ocultar temporariamente"></i>
                        </div>

                    </div>
                    <span class="grupo-display"><?php echo htmlspecialchars($row['grupo'] ?? '', ENT_QUOTES); ?></span>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
<?php else : ?>
    <div class="alert alert-info">Nenhum animal encontrado com os filtros aplicados.</div>
<?php endif; ?>

<div id="filterModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Mais Filtros</h3>
            <span class="close-button">&times;</span>
        </div>
        <div class="modal-body">
            <div class="filter-group">
                <label>Status:</label>
                <div class="filter-checkbox-group">
                    <div><input type="checkbox" id="status_vazia" name="status[]" value="Vazia" <?php echo in_array('Vazia', $filters['status'] ?? []) ? 'checked' : ''; ?>><label for="status_vazia">Vazia</label></div>
                    <div><input type="checkbox" id="status_inseminada" name="status[]" value="Inseminada" <?php echo in_array('Inseminada', $filters['status'] ?? []) ? 'checked' : ''; ?>><label for="status_inseminada">Inseminada</label></div>
                    <div><input type="checkbox" id="status_prenha" name="status[]" value="Prenha" <?php echo in_array('Prenha', $filters['status'] ?? []) ? 'checked' : ''; ?>><label for="status_prenha">Prenha</label></div>
                </div>
            </div>
            <div class="filter-group">
                <label>Previsão de Cio:</label>
                <div class="filter-checkbox-group">
                    <div><input type="checkbox" id="previsao_cio_ciclos" name="previsao_cio_ciclos" value="1" <?php echo isset($filters['previsao_cio_ciclos']) ? 'checked' : ''; ?>><label for="previsao_cio_ciclos">Apenas em Previsão de Cio (ciclos de 21d)</label></div>
                </div>
            </div>
            <div class="filter-group">
                <label>Inseminação (dias):</label>
                <div class="filter-input-range">
                    <input type="number" id="inseminacao_min" name="inseminacao_min" placeholder="Mínimo" min="0" value="<?php echo htmlspecialchars($_GET['inseminacao_min'] ?? '', ENT_QUOTES); ?>">
                    <span>a</span>
                    <input type="number" id="inseminacao_max" name="inseminacao_max" placeholder="Máximo" min="0" value="<?php echo htmlspecialchars($_GET['inseminacao_max'] ?? '', ENT_QUOTES); ?>">
                </div>
            </div>
            <div class="filter-group">
                <label>BST:</label>
                <div class="filter-radio-group">
                    <div><input type="radio" id="bst_todos" name="bst_filter" value="" <?php echo (!isset($filters['bst_filter']) || $filters['bst_filter'] === '') ? 'checked' : ''; ?>><label for="bst_todos">Todos</label></div>
                    <div><input type="radio" id="bst_sim_filter" name="bst_filter" value="1" <?php echo (isset($filters['bst_filter']) && $filters['bst_filter'] === '1') ? 'checked' : ''; ?>><label for="bst_sim_filter">Sim</label></div>
                    <div><input type="radio" id="bst_nao_filter" name="bst_filter" value="0" <?php echo (isset($filters['bst_filter']) && $filters['bst_filter'] === '0') ? 'checked' : ''; ?>><label for="bst_nao_filter">Não</label></div>
                </div>
            </div>
            <div class="filter-group">
                <label>DEL (dias):</label>
                <div class="filter-input-range">
                    <input type="number" id="del_min" name="del_min" placeholder="Mínimo" min="0" value="<?php echo htmlspecialchars($_GET['del_min'] ?? '', ENT_QUOTES); ?>">
                    <span>a</span>
                    <input type="number" id="del_max" name="del_max" placeholder="Máximo" min="0" value="<?php echo htmlspecialchars($_GET['del_max'] ?? '', ENT_QUOTES); ?>">
                </div>
            </div>
            <div class="filter-group">
                <label>Idade (meses):</label>
                <div class="filter-input-range">
                    <input type="number" id="idade_min" name="idade_min" placeholder="Mínimo" min="0" value="<?php echo htmlspecialchars($_GET['idade_min'] ?? '', ENT_QUOTES); ?>">
                    <span>a</span>
                    <input type="number" id="idade_max" name="idade_max" placeholder="Máximo" min="0" value="<?php echo htmlspecialchars($_GET['idade_max'] ?? '', ENT_QUOTES); ?>">
                </div>
            </div>
            <div class="filter-group">
                <label>Escore:</label>
                <div class="filter-input-range">
                    <input type="number" id="escore_min" name="escore_min" placeholder="Mínimo" step="0.25" min="1.0" max="5.0" value="<?php echo htmlspecialchars($_GET['escore_min'] ?? '', ENT_QUOTES); ?>">
                    <span>a</span>
                    <input type="number" id="escore_max" name="escore_max" placeholder="Máximo" step="0.25" min="1.0" max="5.0" value="<?php echo htmlspecialchars($_GET['escore_max'] ?? '', ENT_QUOTES); ?>">
                </div>
            </div>
			    <div class="filter-group">
                <label>Alertas e Manejos:</label>
                <div class="filter-checkbox-group">
                    <div>
                        <input type="checkbox" id="iatf_filter" name="iatf_filter" value="1" <?php echo (isset($filters['iatf_filter']) && $filters['iatf_filter']) ? 'checked' : ''; ?>>
                        <label for="iatf_filter">Com IATF (últimos 10 dias)</label>
                    </div>
                    <div>
                        <input type="checkbox" id="descarte_filter" name="descarte_filter" value="1" <?php echo (isset($filters['descarte_filter']) && $filters['descarte_filter']) ? 'checked' : ''; ?>>
                        <label for="descarte_filter">Com Descarte de Leite</label>
                    </div>
                </div>
            </div>

            <div class="filter-group">
                <label>Cor do Bastão:</label>
                <div class="filter-checkbox-group">
                    <?php 
                        $bastaoOptions = ['Azul', 'Verde', 'Vermelho']; 
                        foreach ($bastaoOptions as $cor): 
                    ?>
                        <div>
                            <input type="checkbox" id="cor_bastao_<?php echo strtolower($cor); ?>" name="cor_bastao_filter[]" value="<?php echo htmlspecialchars($cor, ENT_QUOTES); ?>" <?php echo in_array($cor, $filters['cor_bastao_filter'] ?? []) ? 'checked' : ''; ?>>
                            <label for="cor_bastao_<?php echo strtolower($cor); ?>"><?php echo htmlspecialchars($cor, ENT_QUOTES); ?></label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button id="applyModalFiltersBtn" class="btn btn-primary">Aplicar Filtros</button>
            <button id="clearModalFiltersBtn" class="btn btn-secondary">Limpar</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const HIDE_DURATION_MS = 30 * 60 * 1000;
    const HIDDEN_ANIMALS_KEY = 'hiddenGado';
    const reexibirBtn = document.getElementById('reexibir-btn');

    const getHiddenAnimals = () => {
        const data = sessionStorage.getItem(HIDDEN_ANIMALS_KEY);
        return data ? JSON.parse(data) : {};
    };

    const saveHiddenAnimals = (data) => {
        sessionStorage.setItem(HIDDEN_ANIMALS_KEY, JSON.stringify(data));
    };

    const updateReexibirBtnVisibility = () => {
        const hiddenAnimals = getHiddenAnimals();
        const count = Object.keys(hiddenAnimals).length;

        if (count > 0) {
            reexibirBtn.textContent = `Reexibir Ocultos (${count})`;
            reexibirBtn.style.display = 'inline-block';
        } else {
            reexibirBtn.style.display = 'none';
        }
    };

    const applyInitialState = () => {
        let hiddenAnimals = getHiddenAnimals();
        const now = Date.now();
        let needsUpdate = false;

        for (const animalId in hiddenAnimals) {
            if (hiddenAnimals[animalId] < now) {
                delete hiddenAnimals[animalId];
                needsUpdate = true;
            }
        }

        if (needsUpdate) {
            saveHiddenAnimals(hiddenAnimals);
        }

        for (const animalId in hiddenAnimals) {
            const row = document.getElementById(`animal-row-${animalId}`);
            if (row) {
                row.classList.add('item-hidden');
            }
        }
        
        updateReexibirBtnVisibility();
    };

    document.querySelector('.animal-list-container').addEventListener('click', function(e) {
        const hideTrigger = e.target.closest('.hide-trigger');
        if (hideTrigger) {
            const animalId = hideTrigger.closest('.secondary-info').dataset.animalId;
            if (animalId) {
                const row = document.getElementById(`animal-row-${animalId}`);
                if (row) {
                    row.classList.add('item-hidden');
                    
                    const hiddenAnimals = getHiddenAnimals();
                    const expirationTime = Date.now() + HIDE_DURATION_MS;
                    hiddenAnimals[animalId] = expirationTime;
                    saveHiddenAnimals(hiddenAnimals);
                    
                    updateReexibirBtnVisibility();
                }
            }
        }
    });

    reexibirBtn.addEventListener('click', function() {
        sessionStorage.removeItem(HIDDEN_ANIMALS_KEY);
        window.location.reload();
    });

    applyInitialState();
});
</script>

<script src="../../js/filter.js?v=2.2"></script>
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>