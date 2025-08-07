<?php

// ### INÍCIO DA MODIFICAÇÃO ###
// Define o tempo de vida do cookie da sessão para 1 semana (em segundos)
$cookie_lifetime = 60 * 60 * 24 * 7; // 60s * 60m * 24h * 7d
session_set_cookie_params($cookie_lifetime);
// ### FIM DA MODIFICAÇÃO ###


session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../views/auth/login.php');
    exit();
}
header('Content-type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kivitz</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/select2.min.css"> 
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> 
	 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.alert = function(message) {
            console.warn('ALERT Bloqueado (Mensagem original):', message);
        };
    </script>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <div class="header-nav-left">
                    <button id="backButton" class="header-icon-btn" type="button"><i class="fas fa-arrow-left"></i></button>
                    <a href="../../index.php" class="header-icon-btn home-link"><i class="fas fa-home"></i></a>
                </div>
                <h1>Kivitz</h1> 
            </div>
        </header>
        <main>