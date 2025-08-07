<?php
// O início do arquivo é similar ao index.php, garantindo segurança e acesso ao banco.
include_once 'includes/header.php';
require_once 'config/database.php';

// --- LÓGICA DE CONTROLE DE ACESSO ---
// Apenas o usuário 'michelthiel' pode ver esta página.
$username = $_SESSION['username'] ?? '';
if ($username !== 'michelthiel') {
    $_SESSION['message'] = "Você não tem permissão para acessar esta área.";
    header('Location: index.php');
    exit();
}
?>

<h2 class="page-title">Configurações e Cadastros</h2>

<div class="main-dashboard-container">
    <div class="main-dashboard-grid">

        <a href="views/contatos/index.php" class="dashboard-menu-button">
            <span>Gerenciar Contatos</span>
        </a>

        <a href="views/categorias_financeiras/index.php" class="dashboard-menu-button">
            <span>Gerenciar Categorias</span>
        </a>

        <a href="views/estoque/list.php" class="dashboard-menu-button">
            <span>Gerenciar Produtos (Estoque)</span>
        </a>
        
        <a href="views/touros/index.php" class="dashboard-menu-button">
            <span>Gerenciar Touros</span>
        </a>

        <a href="views/manejos/index.php" class="dashboard-menu-button">
            <span>Gerenciar Tipos de Manejo</span>
        </a>

        <a href="views/financeiro/index.php" class="dashboard-menu-button">
            <span>Acessar Financeiro</span>
        </a>
        
        <a href="views/graficos/index.php" class="dashboard-menu-button">
            <span>Análises e Gráficos</span>
        </a>
		  <a href="views/dietas/gerenciar.php" class="dashboard-menu-button">
            <span>Gerenciar Dietas e Estoque</span>
        </a>
		 <a href="views/envio_leite/index.php" class="dashboard-menu-button">
            <span>Envios de leite</span>
        </a>

    </div>
</div>

<?php include_once 'includes/footer.php'; ?>