<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/ContatoFinanceiro.php';

$contatoModel = new ContatoFinanceiro($pdo);

// Captura o termo de pesquisa da URL e passa para o modelo
$searchQuery = $_GET['search'] ?? '';
$contatos = $contatoModel->read($searchQuery)->fetchAll(PDO::FETCH_ASSOC);

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>
<h2 class="page-title">Gerenciar Contatos (Clientes/Fornecedores)</h2>
<?php if ($message) echo "<div class='alert alert-success'>$message</div>"; ?>

<!-- ### BARRA DE PESQUISA E BOTÕES ATUALIZADA ### -->
<div class="filter-controls">
    <form id="filter-form" method="GET" action="index.php" style="display: flex; gap: 8px; align-items: center;">
        
        <input type="text" id="search_query" name="search" placeholder="Pesquisar por nome, tipo, telefone..." value="<?php echo htmlspecialchars($searchQuery); ?>" style="flex-grow: 1; min-width: 100px;">
        
        <div style="display: flex; flex-shrink: 0; gap: 8px;">
            <?php if ($searchQuery): ?>
                <a href="index.php" class="btn btn-danger">Limpar</a>
            <?php endif; ?>
            <a href="create.php" class="btn btn-primary" title="Novo Contato">+</a>
        </div>

    </form>
</div>

<table class="data-table" style="margin-top: 20px;">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Tipo</th>
            <th>Telefone</th>
            <th>CPF/CNPJ</th>
            <th class="actions-column-condensed">Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($contatos)): ?>
            <?php foreach ($contatos as $contato): ?>
                <tr>
                    <td><?php echo htmlspecialchars($contato['nome']); ?></td>
                    <td><?php echo htmlspecialchars($contato['tipo']); ?></td>
                    <td><?php echo htmlspecialchars($contato['telefone']); ?></td>
                    <td><?php echo htmlspecialchars($contato['cpf_cnpj']); ?></td>
                    <td class="actions-column-condensed">
                        <div class="button-group">
                            <a href="edit.php?id=<?php echo $contato['id']; ?>" class="btn btn-secondary">Alterar</a>
                            <form action="../../controllers/ContatoFinanceiroController.php" method="POST" onsubmit="return confirm('Tem certeza?');" style="display:inline-block;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $contato['id']; ?>">
                                <button type="submit" class="btn btn-danger">Excluir</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align: center;">Nenhum contato encontrado.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<!-- ### SCRIPT DE AUTOSUBMIT ### -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchQueryInput = document.getElementById('search_query');
    if (searchQueryInput) {
        let searchTimeout;
        searchQueryInput.addEventListener('keyup', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filter-form').submit();
            }, 500); // Aguarda meio segundo após parar de digitar
        });
    }
});
</script>
