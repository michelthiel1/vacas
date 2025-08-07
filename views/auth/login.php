<?php
// ### INÍCIO DA MODIFICAÇÃO ###
// Define o tempo de vida do cookie da sessão para 1 semana (em segundos)
$cookie_lifetime = 60 * 60 * 24 * 7; // 60s * 60m * 24h * 7d
session_set_cookie_params($cookie_lifetime);
// ### FIM DA MODIFICAÇÃO ###
session_start();
require_once __DIR__ . '/../../config/database.php';

// Se o usuário já está logado, redireciona para a página inicial
if (isset($_SESSION['user_id'])) {
    header('Location: ../../index.php');
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $query = "SELECT id, username, password, role FROM usuarios WHERE username = :username LIMIT 1";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        header('Location: ../../index.php');
        exit();
    } else {
        $message = '<div class="alert alert-error" style="text-align: center; background-color: var(--error-red); color: white; padding: 10px; border-radius: 8px; margin-bottom: 15px;">Usuário ou senha inválidos.</div>';
    }
}

// Inclui o cabeçalho público
include_once __DIR__ . '/../../includes/header_public.php';
?>

<h2 class="page-title">Acesso ao Sistema</h2>

<?php echo $message; ?>
<form action="login.php" method="post">
    <div>
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="button-group" style="justify-content: center;">
        <button type="submit" class="btn btn-primary">Entrar</button>
    </div>
</form>

<?php
// Inclui o rodapé público
include_once __DIR__ . '/../../includes/footer_public.php';
?>