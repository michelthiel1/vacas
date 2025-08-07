<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Inseminacao.php';
require_once __DIR__ . '/../../models/Gado.php'; 
require_once __DIR__ . '/../../models/Touro.php'; 
require_once __DIR__ . '/../../models/Inseminador.php'; 

$inseminacao = new Inseminacao($pdo);
$gado = new Gado($pdo); 
$touro = new Touro($pdo); 
$inseminador = new Inseminador($pdo); 

$inseminacao->id = $_GET['id'] ?? die('ID da inseminação não especificado.');

if ($inseminacao->readOne()) {
    $brinco_da_vaca_display = htmlspecialchars($inseminacao->brinco_vaca_display);
    $nome_da_vaca_display = htmlspecialchars($inseminacao->nome_vaca_display);
    $nome_touro_display = htmlspecialchars($inseminacao->nome_touro_display);
    $nome_inseminador_display = htmlspecialchars($inseminacao->nome_inseminador_display);

} else {
    $_SESSION['message'] = "Inseminação não encontrada.";
    header('Location: index.php');
    exit();
}
?>

<h2 class="page-title">Detalhes da Inseminação</h2>

<div class="details-card">
    <ul class="details-list"> <li><strong>ID:</strong> <?php echo htmlspecialchars($inseminacao->id); ?></li>
        <li><strong>Tipo:</strong> <?php echo htmlspecialchars($inseminacao->tipo); ?></li>
        <li><strong>Brinco da Vaca:</strong> <?php echo $brinco_da_vaca_display; ?></li> <li><strong>Nome da Vaca:</strong> <?php echo $nome_da_vaca_display; ?></li>
        <li><strong>Touro/Sêmen:</strong> <?php echo $nome_touro_display; ?></li> <li><strong>Inseminador:</strong> <?php echo $nome_inseminador_display; ?></li> <li><strong>Data da Inseminação:</strong> <?php echo htmlspecialchars(date('d/m/Y', strtotime($inseminacao->data_inseminacao))); ?></li>
        <li><strong>Observações:</strong> <?php echo nl2br(htmlspecialchars($inseminacao->observacoes)); ?></li>
        <li><strong>Status:</strong> <?php echo htmlspecialchars($inseminacao->status_inseminacao); ?></li>
        <li><strong>Ativo:</strong> <?php echo ($inseminacao->ativo == 1) ? 'Sim' : 'Não'; ?></li>
        <li><strong>Criado em:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($inseminacao->created_at))); ?></li> <li><strong>Última Atualização:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($inseminacao->updated_at))); ?></li> </ul>
</div>

<div class="button-group">
    <a href="edit.php?id=<?php echo htmlspecialchars($inseminacao->id); ?>" class="btn btn-primary">Editar</a>
    <a href="index.php" class="btn btn-secondary">Voltar à Lista</a>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>