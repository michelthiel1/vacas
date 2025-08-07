<?php
// config/database.php - Configurações de Conexão PDO
// Este arquivo cria a conexão PDO ($pdo) e a disponibiliza globalmente.

$db_host = "localhost"; // Endereço do seu servidor MySQL
$db_name = "kivitz";     // Nome do seu banco de dados
$db_user = "root";      // Nome de usuário do banco de dados (padrão XAMPP é root)
$db_pass = "0319tS@@";          // Senha do banco de dados (padrão XAMPP é vazio)

try {
    // Cria uma nova instância PDO para a conexão com o banco de dados
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    // Define o modo de erro do PDO para lançar exceções em caso de erros
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // error_log("Conexão com o banco de dados \$pdo bem-sucedida!"); // Opcional para depuração
} catch (PDOException $e) {
    // Em caso de erro na conexão, exibe a mensagem de erro e encerra o script
    // error_log("Erro FATAL na conexão com o banco de dados: " . $e->getMessage()); // Opcional para depuração
    //die("Erro na conexão com o banco de dados: " . $e->getMessage());
}
?>