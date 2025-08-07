<?php
include_once 'includes/header.php';
require_once 'config/database.php';
require_once 'models/Evento.php'; 
require_once 'models/Gado.php';

$eventoModel = new Evento($pdo);
$gadoModel = new Gado($pdo);

// Busca as contagens para os alertas
$eventosEmAtraso = $eventoModel->getEventosEmAtraso();
$contagemCio = $gadoModel->getContagemPrevisaoCio();
$contagemToque = $gadoModel->getContagemToquePendente();

// --- LÓGICA DE CONTROLE DE ACESSO ---
$user_role = $_SESSION['role'] ?? 'user'; 
$username = $_SESSION['username'] ?? '';
?>

<h2 class="page-title">Bem-vindo ao Kivitz</h2>

<div class="main-dashboard-container">
    <div class="main-dashboard-grid">

        <!-- BOTÕES DE OPERAÇÃO DIÁRIA -->
        <a href="views/gado/index.php" class="dashboard-menu-button">
            <span>Animais</span>
        </a>

        <a href="views/inseminacoes/index.php" class="dashboard-menu-button">
            <span>Inseminações</span>
        </a>

        <a href="views/gado/index.php?previsao_cio_ciclos=1" class="dashboard-menu-button <?php echo ($contagemCio > 0) ? 'alert-pending' : ''; ?>">
            <span>Previsão de Cio</span>
            <?php if ($contagemCio > 0) : ?>
                <span class="pending-badge"><?php echo $contagemCio; ?></span>
            <?php endif; ?>
        </a>

        <a href="views/partos/index.php" class="dashboard-menu-button">
            <span>Partos</span>
        </a>

        <a href="views/gado/index.php?status=Inseminada&inseminacao_min=30" class="dashboard-menu-button <?php echo ($contagemToque > 0) ? 'alert-pending' : ''; ?>">
            <span>Toque</span>
            <?php if ($contagemToque > 0) : ?>
                <span class="pending-badge"><?php echo $contagemToque; ?></span>
            <?php endif; ?>
        </a>

        <a href="views/registros_manejo/create.php?tipo=Secagem" class="dashboard-menu-button">
            <span>Secagens</span>
        </a>

        <a href="views/pesagens/index.php" class="dashboard-menu-button">
            <span>Pesagens</span>
        </a>

        <a href="views/producao_leite/index.php" class="dashboard-menu-button">
            <span>Produção de Leite</span>
        </a>

        <a href="views/eventos/index.php" class="dashboard-menu-button <?php echo ($eventosEmAtraso > 0) ? 'alert-pending' : ''; ?>">
            <span>Agenda de Eventos</span>
            <?php if ($eventosEmAtraso > 0) : ?>
                <span class="pending-badge"><?php echo $eventosEmAtraso; ?></span>
            <?php endif; ?>
        </a>
        
        <a href="views/gado/index.php?bst_filter=1" class="dashboard-menu-button">
            <span>BST</span>
        </a>

        <a href="views/registros_manejo/index.php" class="dashboard-menu-button">
            <span>Registrar Manejo</span>
        </a>

        <a href="views/estoque/index.php" class="dashboard-menu-button">
            <span>Estoque & Dieta</span>
        </a>

        <!-- ### NOVO BOTÃO DE CONFIGURAÇÕES ### -->
        <!-- Visível apenas para o usuário 'michelthiel' -->
        <?php if ($username === 'michelthiel'): ?>
            <a href="configuracoes.php" class="dashboard-menu-button" >
                <span>Configurações</span>
            </a>
        <?php endif; ?>

    </div>
</div>

<?php include_once 'includes/footer.php'; ?>
