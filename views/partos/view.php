<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Parto.php';
require_once __DIR__ . '/../../models/Gado.php'; // Adicionado para buscar brinco da vaca
require_once __DIR__ . '/../../models/Touro.php'; // Adicionado para buscar nome do touro

$parto = new Parto($pdo);
$gado = new Gado($pdo);
$touro = new Touro($pdo); // Instancia Touro para buscar nome

$parto->id = $_GET['id'] ?? die('ID do parto não especificado.');

if ($parto->readOne()) {
    // Dados do parto carregados pelo Parto->readOne() já com os nomes via JOIN
    $brinco_vaca_display = htmlspecialchars($parto->brinco_vaca_display);
    $nome_vaca_display = htmlspecialchars($parto->nome_vaca_display);
    $nome_touro_display = htmlspecialchars($parto->nome_touro_display);

} else {
    $_SESSION['message'] = "Parto não encontrado.";
    header('Location: index.php');
    exit();
}

// --- Lógica para buscar os filhotes associados a este parto ---
$filhotes_do_parto = [];
$query_filhotes = "SELECT id, brinco, nome, sexo, raca FROM gado 
                   WHERE id_mae = :id_vaca_parto 
                     AND nascimento = :data_parto 
                   ORDER BY brinco ASC";
$stmt_filhotes = $pdo->prepare($query_filhotes);
$stmt_filhotes->bindParam(':id_vaca_parto', $parto->id_vaca, PDO::PARAM_INT);
$stmt_filhotes->bindParam(':data_parto', $parto->data_parto, PDO::PARAM_STR);
$stmt_filhotes->execute();
$filhotes_do_parto = $stmt_filhotes->fetchAll(PDO::FETCH_ASSOC);

// Prepara os parâmetros para o botão "Adicionar Bezerro"
$params_add_bezerro = http_build_query([
    'grupo' => 'Bezerra',
    'status' => 'Vazia', // Bezerro nasce vazio
    'nascimento' => $parto->data_parto,
    'id_mae' => $parto->id_vaca,
    'brinco_mae' => $brinco_vaca_display,
    'id_pai' => $parto->id_touro,
    'nome_pai' => $nome_touro_display,
    'raca' => (isset($gado->raca) ? $gado->raca : ''), // Raça da mãe
    'observacoes' => "Filhote do parto da vaca " . $brinco_vaca_display . " em " . date('d/m/Y', strtotime($parto->data_parto)) . ($parto->observacoes ? " / Obs. Parto: " . $parto->observacoes : ""),
    'volta_parto' => 1, // NOVO: Flag para voltar ao parto
    'parto_id' => $parto->id // NOVO: ID do parto para redirecionar
]);
?>

<h2 class="page-title">Detalhes do Parto</h2>

<div class="details-card">
    <ul class="details-list">
        <li><strong>ID do Parto:</strong> <span><?php echo htmlspecialchars($parto->id); ?></span></li>
        <li><strong>Brinco da Vaca:</strong> <span><?php echo $brinco_vaca_display; ?></span></li>
        <li><strong>Nome da Vaca:</strong> <span><?php echo $nome_vaca_display; ?></span></li>
        <li><strong>Touro:</strong> <span><?php echo $nome_touro_display; ?></span></li>
        <li><strong>Sexo da Cria:</strong> <span><?php echo htmlspecialchars($parto->sexo_cria); ?></span></li>
        <li><strong>Data do Parto:</strong> <span><?php echo htmlspecialchars(date('d/m/Y', strtotime($parto->data_parto))); ?></span></li>
        <li><strong>Observações:</strong> <span><?php echo nl2br(htmlspecialchars($parto->observacoes)); ?></span></li>
        <li><strong>Ativo:</strong> <span><?php echo ($parto->ativo == 1) ? 'Sim' : 'Não'; ?></span></li>
        <li><strong>Criado em:</strong> <span><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($parto->created_at))); ?></span></li>
        <li><strong>Última Atualização:</strong> <span><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($parto->updated_at))); ?></span></li>
    </ul>
</div>

<h3 class="section-title">Filhotes Deste Parto</h3>
<div class="button-group">
    <a href="../gado/create.php?<?php echo $params_add_bezerro; ?>" class="btn btn-primary">Adicionar Bezerro</a>
</div>

<?php if (!empty($filhotes_do_parto)) : ?>
    <table class="cow-list-table">
        <tbody>
            <?php foreach ($filhotes_do_parto as $filhote) : ?>
                <tr class="clickable-row" data-href="../gado/view.php?id=<?php echo $filhote['id']; ?>">
                    <td class="col-main-info">
                        <span class="brinco-display"><?php echo htmlspecialchars($filhote['brinco']); ?></span>
                        <span class="details-line-left"><strong>Nome:</strong> <?php echo htmlspecialchars($filhote['nome']); ?></span>
                        <span class="details-line-left"><strong>Raça:</strong> <?php echo htmlspecialchars($filhote['raca']); ?></span>
                    </td>
                    <td class="col-secondary-info">
                        <span class="date-display-right"><strong>Sexo:</strong> <?php echo htmlspecialchars($filhote['sexo']); ?></span>
                        </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else : ?>
    <div class="alert alert-info">Nenhum filhote registrado para este parto.</div>
<?php endif; ?>


<div class="button-group" style="margin-top: 20px;">
    <a href="edit.php?id=<?php echo htmlspecialchars($parto->id); ?>" class="btn btn-primary">Editar Parto</a>
    <a href="index.php" class="btn btn-secondary">Voltar à Lista de Partos</a>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>