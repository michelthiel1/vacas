<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/ContatoFinanceiro.php';

$contatoModel = new ContatoFinanceiro($pdo);
$contatoModel->id = $_GET['id'] ?? die('ID não fornecido.');
$contato = $contatoModel->readOne();

if (!$contato) {
    die('Contato não encontrado.');
}
?>
<h2 class="page-title">Editar Contato</h2>

<form action="../../controllers/ContatoFinanceiroController.php" method="post" class="form-compacto">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($contato['id']); ?>">
    
    <div>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($contato['nome']); ?>" required>
    </div>
    
    <div>
        <label for="tipo">Tipo:</label>
        <select id="tipo" name="tipo" required>
            <option value="Fornecedor" <?php echo ($contato['tipo'] == 'Fornecedor') ? 'selected' : ''; ?>>Fornecedor</option>
            <option value="Cliente" <?php echo ($contato['tipo'] == 'Cliente') ? 'selected' : ''; ?>>Cliente</option>
            <option value="Outro" <?php echo ($contato['tipo'] == 'Outro') ? 'selected' : ''; ?>>Outro</option>
        </select>
    </div>
    
    <div>
        <label for="telefone">Telefone:</label>
        <input type="text" id="telefone" name="telefone" value="<?php echo htmlspecialchars($contato['telefone']); ?>">
    </div>
    
    <div>
        <label for="cpf_cnpj">CPF/CNPJ:</label>
        <input type="text" id="cpf_cnpj" name="cpf_cnpj" value="<?php echo htmlspecialchars($contato['cpf_cnpj']); ?>">
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Atualizar</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
