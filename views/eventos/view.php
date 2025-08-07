<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Evento.php';
require_once __DIR__ . '/../../models/Gado.php'; // Para buscar vaca

$evento = new Evento($pdo);
$gado = new Gado($pdo);

$evento->id = $_GET['id'] ?? die('ID do evento não especificado.');

if ($evento->readOne()) {
    // Dados do evento carregados
    $brinco_vaca_display = htmlspecialchars($evento->brinco_vaca_display);
    $nome_vaca_display = htmlspecialchars($evento->nome_vaca_display);
} else {
    $_SESSION['message'] = "Evento não encontrado.";
    header('Location: index.php');
    exit();
}
?>

<h2 class="page-title">Detalhes do Evento</h2>

<div class="details-card">
    <ul class="details-list">
        <li><strong>ID do Evento:</strong> <span><?php echo htmlspecialchars($evento->id); ?></span></li>
        <li><strong>Título:</strong> <span><?php echo htmlspecialchars($evento->titulo); ?></span></li>
        <li><strong>Data:</strong> <span><?php echo htmlspecialchars(date('d/m/Y', strtotime($evento->data_evento))); ?></span></li>
        <li><strong>Tipo:</strong> <span><?php echo htmlspecialchars($evento->tipo_evento); ?></span></li>
        <?php if (!empty($evento->id_vaca)): ?>
            <li><strong>Vaca:</strong> <span><?php echo $brinco_vaca_display; ?> (<?php echo $nome_vaca_display; ?>)</span></li>
        <?php endif; ?>
        <li><strong>Descrição:</strong> <span><?php echo nl2br(htmlspecialchars($evento->descricao)); ?></span></li>
        <li><strong>Ativo:</strong> <span><?php echo ($evento->ativo == 1) ? 'Sim' : 'Não'; ?></span></li>
        <li><strong>Criado em:</strong> <span><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($evento->created_at))); ?></span></li>
        <li><strong>Última Atualização:</strong> <span><?php echo htmlspecialchars(date('d/m/Y H:i:s', strtotime($evento->updated_at))); ?></span></li>
    </ul>
</div>

<div class="button-group">
    <a href="edit.php?id=<?php echo htmlspecialchars($evento->id); ?>" class="btn btn-primary">Editar</a>
    <form action="../../controllers/EventoController.php" method="post" style="display:inline-block;">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($evento->id); ?>">
        <button type="submit" class="btn btn-danger" onclick="return confirm('Tem certeza que deseja excluir este evento?');">Excluir</button>
    </form>
    <a href="index.php" class="btn btn-secondary">Voltar à Lista</a>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>