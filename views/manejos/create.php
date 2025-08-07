<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
?>

<h2 class="page-title">Adicionar Novo Tipo de Manejo</h2>

<form action="../../controllers/ManejoController.php" method="post">
    <input type="hidden" name="action" value="create">
    
    <div>
        <label for="nome">Nome do Manejo:</label>
        <input type="text" id="nome" name="nome" required>
    </div>

    <div>
        <label for="tipo">Tipo:</label>
        <select id="tipo" name="tipo" required>
            <option value="">Selecione um tipo</option>
            <option value="BST">BST</option>
            <option value="Diagnostico">Diagnóstico</option>
            <option value="Protocolo de Saúde">Protocolo de Saúde</option>
            <option value="Secagem">Secagem</option> <option value="Pré-Parto">Pré-Parto</option> <option value="Vacinas">Vacinas</option>
        </select>
    </div>
	<div id="recorrencia-container" style="display: none;">
        <label for="recorrencia_meses">Recorrência (meses):</label>
        <input type="number" name="recorrencia_meses" id="recorrencia_meses" placeholder="Ex: 6" min="1" >
        <p style="font-size:0.8em; color: #666;">Deixe em branco se não for uma vacina recorrente.</p>
    </div>
    <div id="recorrencia-dias-container" style="display: none;">
    <label for="recorrencia_dias">Recorrência (dias):</label>
    <input type="number" name="recorrencia_dias" id="recorrencia_dias" placeholder="Ex: 14" min="1"  >
    <p style="font-size:0.8em; color: #666;">Deixe em branco se não for um BST recorrente.</p>
</div>
    <div style="background-color: #e9ecef; padding: 15px; border-radius: 8px; margin-top: 20px;">
        <h4 style="margin-top: 0; color: var(--dark-orange);">Eventos Personalizados (Opcional)</h4>
        <p style="font-size: 0.9em; margin-bottom: 15px;">Defina eventos futuros que serão criados automaticamente a partir da data de aplicação deste manejo.</p>
        
        <?php for ($i = 1; $i <= 6; $i++): ?>
        <div style="display: grid; grid-template-columns: 100px 1fr; gap: 10px; margin-bottom: 10px; align-items: center;">
            <label for="evento_dias_<?php echo $i; ?>" style="margin: 0;">Dias Após:</label>
            <input type="number" name="evento_dias_<?php echo $i; ?>" id="evento_dias_<?php echo $i; ?>" placeholder="Ex: 8">
            
            <label for="evento_titulo_<?php echo $i; ?>" style="margin: 0;">Título Evento:</label>
            <input type="text" name="evento_titulo_<?php echo $i; ?>" id="evento_titulo_<?php echo $i; ?>" placeholder="Ex: Retirar Dispositivo">
        </div>
        <?php if ($i < 6) echo '<hr style="border: 1px dashed var(--border-color);">'; ?>
        <?php endfor; ?>
    </div>

    <div>
        <input type="checkbox" name="ativo" id="ativo" value="1" checked>
        <label for="ativo">Ativo</label>
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar</button>
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