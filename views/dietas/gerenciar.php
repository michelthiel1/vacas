<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Dieta.php';
require_once __DIR__ . '/../../models/Estoque.php';

$dietaModel = new Dieta($pdo);
$estoqueModel = new Estoque($pdo);

// Usando as funções corretas para buscar os dados
$dietas = $dietaModel->readAllActive(); 
$produtosEstoque = $estoqueModel->readAllWithCategory();

$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Define a ordem dos ingredientes e seus nomes amigáveis
$ingredientes = [
    'Vacas' => 'Nº de Vacas',
    'Silagem' => 'Silagem',
    'Milho' => 'Milho',
    'Soja' => 'Soja',
    'Casca' => 'Casca',
    'Polpa' => 'Polpa',
    'Caroco' => 'Caroço',
    'Feno' => 'Feno',
    'Mineral' => 'Mineral',
    'Equalizer' => 'Equalizer',
    'Notox' => 'Notox',
    'Ureia' => 'Ureia',
    'Ice' => 'Ice'
];
?>

<style>
    /* Estilos para tornar as tabelas editáveis e mais claras */
    .gerenciar-tabela {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    .gerenciar-tabela th, .gerenciar-tabela td {
        border: 1px solid var(--border-color);
        padding: 8px;
        text-align: center;
        font-size: 0.9em;
    }
    .gerenciar-tabela th {
        background-color: var(--highlight-orange);
        color: white;
    }
    .gerenciar-tabela tbody tr:nth-child(even) {
        background-color: var(--background-medium);
    }
    .gerenciar-tabela td:first-child {
        text-align: left;
        font-weight: bold;
        background-color: #f7f7f7;
    }
    .gerenciar-tabela input[type="number"] {
        width: 90px;
        padding: 5px;
        text-align: center;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
    }
    .gerenciar-tabela input[type="number"]:focus {
        border-color: var(--dark-orange);
        outline: none;
    }
    .section-container {
        margin-bottom: 30px;
        padding: 15px;
        border-radius: 8px;
        background-color: var(--background-light);
        box-shadow: var(--shadow-light);
    }
</style>

<h2 class="page-title">Gerenciar Dietas e Estoque</h2>

<?php if ($message): ?>
    <div class="alert alert-success" style="margin-bottom: 15px;"><?php echo $message; ?></div>
<?php endif; ?>

<form action="../../controllers/DietaController.php" method="POST">
    <div class="section-container">
        <h3 class="section-title" style="margin-top:0;">Composição das Dietas (kg/vaca/dia)</h3>
        <input type="hidden" name="action" value="update_dietas">
        
        <div style="overflow-x: auto;">
            <table class="gerenciar-tabela">
                <thead>
                    <tr>
                        <th>Ingrediente</th>
                        <?php foreach ($dietas as $dieta): ?>
                            <th><?php echo htmlspecialchars($dieta['Lote']); ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($ingredientes as $key => $nomeAmigavel): ?>
                    <tr>
                        <td><?php echo $nomeAmigavel; ?></td>
                        <?php foreach ($dietas as $dieta): ?>
                            <td>
                                <input 
                                    type="number" 
                                    name="dieta[<?php echo $dieta['Id']; ?>][<?php echo $key; ?>]" 
                                    value="<?php echo htmlspecialchars($dieta[$key]); ?>" 
                                    step="<?php echo ($key === 'Vacas') ? '1' : '0.01'; ?>"
                                >
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="button-group" style="justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">Salvar Alterações nas Dietas</button>
        </div>
    </div>
</form>

<form action="../../controllers/DietaController.php" method="POST">
    <div class="section-container">
        <h3 class="section-title" style="margin-top:0;">Ajuste de Estoque</h3>
        <input type="hidden" name="action" value="update_estoque">
        <table class="gerenciar-tabela">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Estoque Atual (Unid. de Consumo)</th>
                    <th>Novo Estoque (Unid. de Consumo)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($produtosEstoque)): ?>
                    <?php foreach ($produtosEstoque as $produto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($produto['Produto']); ?></td>
                        <td><?php echo htmlspecialchars(number_format($produto['Quantidade'], 2, ',', '.')); ?></td>
                        <td>
                            <input type="number" name="estoque[<?php echo $produto['Id']; ?>]" placeholder="Deixar em branco para não alterar" step="0.01">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3">Nenhum produto encontrado no estoque.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div class="button-group" style="justify-content: flex-end;">
            <button type="submit" class="btn btn-primary">Salvar Alterações no Estoque</button>
        </div>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>