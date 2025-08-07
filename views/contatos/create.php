<?php
include_once __DIR__ . '/../../includes/header.php';
?>
<h2 class="page-title">Adicionar Novo Contato</h2>

<form action="../../controllers/ContatoFinanceiroController.php" method="post" class="form-compacto">
    <input type="hidden" name="action" value="create">
    
    <div>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required>
    </div>
    
    <div>
        <label for="tipo">Tipo:</label>
        <select id="tipo" name="tipo" required>
            <option value="Fornecedor">Fornecedor</option>
            <option value="Cliente">Cliente</option>
            <option value="Outro">Outro</option>
        </select>
    </div>
    
    <div>
        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone">
    </div>
    
    <div>
        <label for="cpf_cnpj">CPF/CNPJ:</label>
        <input type="text" id="cpf_cnpj" name="cpf_cnpj">
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
