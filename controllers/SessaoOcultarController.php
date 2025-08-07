<?php
// controllers/SessaoOcultarController.php
session_start();
header('Content-Type: application/json');

const HIDE_SESSION_LIFETIME = 1800; // 30 minutos

if (isset($_SESSION['animais_ocultos']['timestamp']) && (time() - $_SESSION['animais_ocultos']['timestamp'] > HIDE_SESSION_LIFETIME)) {
    unset($_SESSION['animais_ocultos']);
}

if (!isset($_SESSION['animais_ocultos'])) {
    $_SESSION['animais_ocultos'] = ['timestamp' => time(), 'ids' => []];
}

$response = ['success' => false, 'message' => 'Ação inválida.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
    $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING);

    if ($animal_id && $action === 'ocultar') {
        if (!is_array($_SESSION['animais_ocultos']['ids'])) {
            $_SESSION['animais_ocultos']['ids'] = [];
        }
        if (!in_array($animal_id, $_SESSION['animais_ocultos']['ids'])) {
            $_SESSION['animais_ocultos']['ids'][] = $animal_id;
        }
        
        $_SESSION['animais_ocultos']['timestamp'] = time();
        $response['success'] = true;
        $response['hidden_count'] = count($_SESSION['animais_ocultos']['ids']);
    } else {
        $response['message'] = 'Dados inválidos recebidos.';
    }
}

echo json_encode($response);
exit();
?>