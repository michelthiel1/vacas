<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';
require_once __DIR__ . '/../../models/Touro.php'; 

$gado = new Gado($pdo);
$touro = new Touro($pdo);

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Opções de raça para o select
$racaOptions = ['Holandês', 'Angus', 'Jersey'];

// --- Lógica de pré-preenchimento para filhotes de parto ---
$prefill = [
    'brinco' => '',
    'nome' => '',
    'nascimento' => date('Y-m-d'), // Data atual como padrão
    'sexo' => '', 
    'raca' => '',
    'observacoes' => '',
    'status' => 'Vazia', 
    'grupo' => 'Bezerra', 
    'bst' => 0, 
    'escore' => '',
    'id_mae' => '',
    'brinco_mae' => '',
    'id_pai' => '',
    'nome_pai' => '',
    'num_filhotes_restantes' => 0, 
    'parto_id' => null, 
];

$is_prefilled = false; 

// Lógica para carregar opções de touros (todos os ativos)
$touros_para_select = [];
$stmt_touros = $touro->readAllNames(); 
while ($row = $stmt_touros->fetch(PDO::FETCH_ASSOC)) {
    $touros_para_select[$row['id']] = htmlspecialchars($row['nome']);
}

// Lógica para carregar opções de vacas (todas as ativas)
$vacas_para_select = [];
$stmt_vacas = $gado->readBrincos(); 
while ($row = $stmt_vacas->fetch(PDO::FETCH_ASSOC)) {
    $vacas_para_select[$row['id']] = htmlspecialchars($row['brinco'] . ' - ' . $row['nome']);
}


// --- Lógica de pré-preenchimento: Prioriza SESSION para múltiplos, senão GET para único ---
if (isset($_GET['multiples']) && $_GET['multiples'] === 'true' && isset($_SESSION['filhotes_multiplos']) && !empty($_SESSION['filhotes_multiplos'])) {
    $session_data = array_shift($_SESSION['filhotes_multiplos']); 
    $prefill = array_merge($prefill, $session_data);
    $is_prefilled = true;
    error_log("GadoCreate: Carregando pré-preenchimento da SESSÃO: " . print_r($prefill, true));
} elseif (!empty($_GET)) { 
    foreach ($_GET as $key => $value) {
        $prefill[$key] = htmlspecialchars($value);
    }
    $is_prefilled = true;
    error_log("GadoCreate: Carregando pré-preenchimento do GET: " . print_r($prefill, true));
}

// Garante que 'sexo' sempre existe em $prefill, mesmo que vazio
$prefill['sexo'] = $prefill['sexo'] ?? '';

// Captura os parâmetros para o action do formulário
$volta_parto_flag = $_GET['volta_parto'] ?? null;
$parto_id_from_url = $_GET['parto_id'] ?? null;

error_log("GadoCreate: Conteúdo FINAL de \$prefill: " . print_r($prefill, true));
error_log("GadoCreate: is_prefilled FLAG: " . ($is_prefilled ? 'true' : 'false'));
error_log("GadoCreate: volta_parto_flag: " . ($volta_parto_flag ?? 'NULO') . ", parto_id_from_url: " . ($parto_id_from_url ?? 'NULO'));
?>

<h2 class="page-title">Cadastrar Novo Animal</h2>

<?php if ($message): ?>
    <div class="alert alert-success"><?php echo $message; ?></div>
<?php endif; ?>

<form action="../../controllers/GadoController.php" method="post">
    <input type="hidden" name="action" value="create">
    
    <?php 
    // Passar num_filhotes_restantes e is_multiple_filhotes_session (se existirem)
    if ($is_prefilled) : 
        if (isset($prefill['num_filhotes_restantes']) && (int)$prefill['num_filhotes_restantes'] > 0) : 
            ?>
            <input type="hidden" name="num_filhotes_restantes" value="<?php echo htmlspecialchars($prefill['num_filhotes_restantes']); ?>">
        <?php endif; 
        if (isset($_GET['multiples']) && $_GET['multiples'] === 'true') : 
            ?>
            <input type="hidden" name="is_multiple_filhotes_session" value="true">
        <?php endif; 
        // Passa o ID do parto para o controlador, se ele existir
        if ($parto_id_from_url) : ?>
            <input type="hidden" name="parto_id_redirect" value="<?php echo htmlspecialchars($parto_id_from_url); ?>">
        <?php endif;
    endif; ?>

    <div>
        <label for="brinco">Brinco:</label>
        <input type="text" id="brinco" name="brinco" value="<?php echo htmlspecialchars($prefill['brinco']); ?>" required>
    </div>

    <div>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($prefill['nome']); ?>">
    </div>

    <div>
        <label for="nascimento">Data de Nascimento:</label>
        <input type="date" id="nascimento" name="nascimento" value="<?php echo htmlspecialchars($prefill['nascimento']); ?>">
    </div>

    <div>
        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo" required>
            <option value="">Selecione</option>
            <option value="Macho" <?php echo ($prefill['sexo'] == 'Macho') ? 'selected' : ''; ?>>Macho</option>
            <option value="Fêmea" <?php echo ($prefill['sexo'] == 'Fêmea') ? 'selected' : ''; ?>>Fêmea</option>
        </select>
    </div>

    <div>
        <label for="raca">Raça:</label>
        <select id="raca" name="raca" required>
            <option value="">Selecione a Raça</option>
            <?php foreach ($racaOptions as $raca): ?>
                <option value="<?php echo htmlspecialchars($raca); ?>" <?php echo ($prefill['raca'] == $raca) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($raca); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="id_mae_select">Mãe:</label>
        <select id="id_mae_select" name="id_mae" > 
            <option value="">Selecione a Mãe</option>
            <?php foreach ($vacas_para_select as $vaca_id => $brinco_nome): ?>
                <option value="<?php echo $vaca_id; ?>" <?php echo (isset($prefill['id_mae']) && $prefill['id_mae'] == $vaca_id) ? 'selected' : ''; ?>>
                    <?php echo $brinco_nome; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($is_prefilled && isset($prefill['id_mae'])): ?>
            <input type="hidden" name="id_mae_hidden" value="<?php echo htmlspecialchars($prefill['id_mae']); ?>"> 
        <?php endif; ?>
    </div>

    <div>
        <label for="id_pai_select">Pai:</label>
        <select id="id_pai_select" name="id_pai" > 
            <option value="">Selecione o Pai</option>
            <?php foreach ($touros_para_select as $touro_id => $touro_nome): ?>
                <option value="<?php echo $touro_id; ?>" <?php echo (isset($prefill['id_pai']) && $prefill['id_pai'] == $touro_id) ? 'selected' : ''; ?>>
                    <?php echo $touro_nome; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($is_prefilled && isset($prefill['id_pai'])): ?>
            <input type="hidden" name="id_pai_hidden" value="<?php echo htmlspecialchars($prefill['id_pai']); ?>"> 
        <?php endif; ?>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="4"><?php echo htmlspecialchars($prefill['observacoes'] ?? ''); ?></textarea>
    </div>

    <div>
        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="Vazia" <?php echo ($prefill['status'] == 'Vazia') ? 'selected' : ''; ?>>Vazia</option>
            <option value="Inseminada" <?php echo ($prefill['status'] == 'Inseminada') ? 'selected' : ''; ?>>Inseminada</option>
            <option value="Prenha" <?php echo ($prefill['status'] == 'Prenha') ? 'selected' : ''; ?>>Prenha</option>
            <option value="Parida" <?php echo ($prefill['status'] == 'Parida') ? 'selected' : ''; ?>>Parida</option>
            <option value="Descartada" <?php echo ($prefill['status'] == 'Descartada') ? 'selected' : ''; ?>>Descartada</option>
        </select>
    </div>

    <div>
        <label for="grupo">Grupo:</label>
        <select id="grupo" name="grupo">
            <option value="Bezerra" <?php echo ($prefill['grupo'] == 'Bezerra') ? 'selected' : ''; ?>>Bezerra</option>
            <option value="Novilha" <?php echo (isset($prefill['grupo']) && $prefill['grupo'] == 'Novilha') ? 'selected' : ''; ?>>Novilha</option>
            <option value="Lactante" <?php echo (isset($prefill['grupo']) && $prefill['grupo'] == 'Lactante') ? 'selected' : ''; ?>>Lactante</option>
            <option value="Seca" <?php echo (isset($prefill['grupo']) && $prefill['grupo'] == 'Seca') ? 'selected' : ''; ?>>Seca</option>
            <option value="Corte" <?php echo (isset($prefill['grupo']) && $prefill['grupo'] == 'Corte') ? 'selected' : ''; ?>>Corte</option>
        </select>
    </div>

    <div>
        <label for="bst">BST:</label>
        <select id="bst" name="bst">
            <option value="0" <?php echo ($prefill['bst'] == 0) ? 'selected' : ''; ?>>Não</option>
            <option value="1" <?php echo ($prefill['bst'] == 1) ? 'selected' : ''; ?>>Sim</option>
        </select>
    </div>

    <div>
        <label for="escore">Escore (1.0 - 5.0):</label>
        <input type="number" id="escore" name="escore" step="0.25" min="1.0" max="5.0" value="<?php echo htmlspecialchars($prefill['escore'] ?? ''); ?>">
    </div>
	  <div class="form-grid-2col" style="align-items: end;">
        <div>
            <label for="leite_descarte">Descarte de Leite:</label>
            <select id="leite_descarte" name="leite_descarte">
                <option value="Não" selected>Não</option>
                <option value="Sim">Sim</option>
            </select>
        </div>
        <div>
            <label for="cor_bastao">Marcação Bastão:</label>
            <select id="cor_bastao" name="cor_bastao">
                <option value="" selected>Nenhuma</option>
                <option value="Azul">Azul</option>
                <option value="Verde">Verde</option>
                <option value="Vermelho">Vermelho</option>
            </select>
        </div>
    </div>

    <div class="button-group">
        <button type="submit" class="btn primary">Salvar Animal</button>
        <a href="index.php" class="btn secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script src="../../js/select2.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Funcao para inicializar Select2 em um elemento
        function initializeSelect2(selector, placeholderText, allowClear = false) {
            $(selector).select2({
                placeholder: placeholderText,
                allowClear: allowClear,
                // Ocultar a caixa de pesquisa para selects pequenos ou com poucas opções
                minimumResultsForSearch: selector === '#sexo' || selector === '#status' || selector === '#grupo' || selector === '#bst' ? Infinity : 1
            });
        }

        // Inicializar Select2 nos campos comuns
        initializeSelect2('#sexo', 'Selecione o Sexo');
        initializeSelect2('#raca', 'Selecione a Raça');
        initializeSelect2('#status', 'Selecione o Status');
        initializeSelect2('#grupo', 'Selecione o Grupo');
        initializeSelect2('#bst', 'Selecione o BST');
        
        // Obter referências para os selects de Pai e Mãe
        const idMaeSelect = $('#id_mae_select');
        const idPaiSelect = $('#id_pai_select');

        // Valores pré-preenchidos vindos do PHP
        const prefillIdMae = '<?php echo htmlspecialchars($prefill['id_mae']); ?>'; 
        const prefillIdPai = '<?php echo htmlspecialchars($prefill['id_pai']); ?>'; 
        const isPrefilledFlag = <?php echo $is_prefilled ? 'true' : 'false'; ?>;

        console.log("DEBUG JS: prefillIdMae (from PHP):", prefillIdMae, "prefillIdPai (from PHP):", prefillIdPai, "isPrefilledFlag (from PHP):", isPrefilledFlag);

        // --- Lógica de Pré-preenchimento e Desabilitar Selects Pai/Mãe ---
        // Primeiro, tente definir o valor nativo ANTES da inicialização do Select2
        if (prefillIdMae) {
            idMaeSelect.val(prefillIdMae);
            console.log("DEBUG JS: Valor nativo Mãe setado para:", idMaeSelect.val());
        }
        if (prefillIdPai) {
            idPaiSelect.val(prefillIdPai);
            console.log("DEBUG JS: Valor nativo Pai setado para:", idPaiSelect.val());
        }

        // Inicializar Select2 APÓS tentar definir os valores nativos
        initializeSelect2(idMaeSelect, 'Selecione a Mãe', true);
        initializeSelect2(idPaiSelect, 'Selecione o Pai', true);
        
        // Agora, se a flag de pré-preenchimento está ativa, desabilitar e forçar a seleção no Select2
        if (isPrefilledFlag) {
            idMaeSelect.prop('disabled', true);
            idPaiSelect.prop('disabled', true);
            console.log("DEBUG JS: Mãe e Pai selects desabilitados (isPrefilledFlag é true).");
            
            // Trigger 'change' no Select2 para que ele visualize o valor pré-selecionado corretamente
            // (Isso é importante para que o Select2 exiba o texto do ID pré-selecionado)
            if (prefillIdMae) idMaeSelect.trigger('change');
            if (prefillIdPai) idPaiSelect.trigger('change');

        } else {
            // Se não for pré-preenchido, Select2 já foi inicializado como habilitado.
            console.log("DEBUG JS: Mãe e Pai selects inicializados como HABILITADOS (isPrefilledFlag é false).");
        }


        // Preencher data de nascimento com data atual por padrão (se não vier pré-preenchida)
        const nascimentoInput = document.getElementById('nascimento');
        if (!nascimentoInput.value) { // Só preenche se estiver vazio (não pré-preenchido)
            const today = new Date();
            const year = today.getFullYear();
            const month = String(today.getMonth() + 1).padStart(2, '0');
            const day = String(today.getDate()).padStart(2, '0');
            nascimentoInput.value = `${year}-${month}-${day}`;
        }
    });
</script>