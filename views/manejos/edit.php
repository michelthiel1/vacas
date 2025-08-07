<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Manejo.php';

$manejo = new Manejo($pdo);
$manejo->id = $_GET['id'] ?? die('ID não fornecido.');
$manejo->readOne();
$tipos = ['BST','Diagnostico','Protocolo de Saúde','Secagem','Pré-Parto','Vacinas']; // ADICIONADO AQUI
?>
<h2 class="page-title">Editar Tipo de Manejo</h2>
<form action="../../controllers/ManejoController.php" method="post">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($manejo->id); ?>">
    
    <div>
        <label for="nome">Nome do Manejo:</label>
        <input type="text" name="nome" id="nome" value="<?php echo htmlspecialchars($manejo->nome); ?>" required>
    </div>
    <div>
        <label for="tipo">Tipo:</label>
        <select name="tipo" id="tipo" required>
            <?php foreach($tipos as $tipo): ?>
                <option value="<?php echo $tipo; ?>" <?php echo ($manejo->tipo == $tipo) ? 'selected' : ''; ?>><?php echo $tipo; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div id="recorrencia-container" style="display: none;">
        <label for="recorrencia_meses">Recorrência (meses):</label>
        <input type="number" name="recorrencia_meses" id="recorrencia_meses" placeholder="Ex: 6" min="1" value="<?php echo htmlspecialchars($manejo->recorrencia_meses ?? ''); ?>">
        <p style="font-size:0.8em; color: #666;">Deixe em branco se não for uma vacina recorrente.</p>
    </div>
	<div id="recorrencia-dias-container" style="display: none;">
    <label for="recorrencia_dias">Recorrência (dias):</label>
    <input type="number" name="recorrencia_dias" id="recorrencia_dias" placeholder="Ex: 14" min="1" value="<?php echo htmlspecialchars($manejo->recorrencia_dias ?? ''); ?>">
    <p style="font-size:0.8em; color: #666;">Deixe em branco se não for um BST recorrente.</p>
</div>
    <div style="background-color: #e9ecef; padding: 15px; border-radius: 8px; margin-top: 20px;">
        <h4 style="margin-top: 0; color: var(--dark-orange);">Eventos Personalizados (Opcional)</h4>
        <p style="font-size: 0.9em; margin-bottom: 15px;">Defina eventos futuros que serão criados automaticamente a partir da data de aplicação deste manejo.</p>
        
        <?php for ($i = 1; $i <= 6; $i++): 
            $dias_key = "evento_dias_{$i}";
            $titulo_key = "evento_titulo_{$i}";
        ?>
        <div style="display: grid; grid-template-columns: 100px 1fr; gap: 10px; margin-bottom: 10px; align-items: center;">
            <label for="<?php echo $dias_key; ?>" style="margin: 0;">Dias Após:</label>
            <input type="number" name="<?php echo $dias_key; ?>" id="<?php echo $dias_key; ?>" value="<?php echo htmlspecialchars($manejo->{$dias_key} ?? ''); ?>" placeholder="Ex: 8">
            
            <label for="<?php echo $titulo_key; ?>" style="margin: 0;">Título Evento:</label>
            <input type="text" name="<?php echo $titulo_key; ?>" id="<?php echo $titulo_key; ?>" value="<?php echo htmlspecialchars($manejo->{$titulo_key} ?? ''); ?>" placeholder="Ex: Retirar Dispositivo">
        </div>
        <?php if ($i < 6) echo '<hr style="border: 1px dashed var(--border-color);">'; ?>
        <?php endfor; ?>
    </div>

    <div>
        <input type="checkbox" name="ativo" id="ativo" value="1" <?php echo ($manejo->ativo) ? 'checked' : ''; ?>>
        <label for="ativo">Ativo</label>
    </div>
    <div class="button-group">
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipoSelect = document.getElementById('tipo');
    const recorrenciaMesesContainer = document.getElementById('recorrencia-container');
    const recorrenciaDiasContainer = document.getElementById('recorrencia-dias-container');

    function toggleRecorrenciaFields() {
        const tipoSelecionado = tipoSelect.value;

        // Mostra o campo de meses apenas se for 'Vacinas'
        recorrenciaMesesContainer.style.display = (tipoSelecionado === 'Vacinas') ? 'block' : 'none';

        // Mostra o campo de dias apenas se for 'BST'
        recorrenciaDiasContainer.style.display = (tipoSelecionado === 'BST') ? 'block' : 'none';
    }

    // Executa a função quando a página carrega para verificar o valor inicial
    toggleRecorrenciaFields();

    // Adiciona o "ouvinte" para futuras mudanças no campo "Tipo"
    tipoSelect.addEventListener('change', toggleRecorrenciaFields);
});
</script>