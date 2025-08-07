<?php
// ######################################################################
// # ATENÇÃO: ESTE BLOCO É PARA DEPURACAO. REMOVA EM PRODUÇÃO!        #
// ######################################################################
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/ha_routine_log.log'); // Log específico para HA
// ######################################################################

// Inicia o buffer de saída.
ob_start();

// --- CONFIGURAÇÕES DO HOME ASSISTANT ---
define('HA_API_URL', 'http://10.1.1.125:8123/api/services'); 
define('HA_ACCESS_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiI0MzEyMjRmNjYwOTI0Y2I3YjVjMDRhYmNiMzBkYzFjMyIsImlhdCI6MTc0OTE0MTU2OSwiZXhwIjoyMDY0NTAxNTY5fQ.mhwJpxXSlpfPEnUcyJqviwjZ8hNHygATJNwWu7GIUeM'); 

// --- Configuração para a resposta JSON ---
header('Content-Type: application/json');

// Inicializa a resposta padrão
$response = ['success' => false, 'message' => 'Método inválido ou erro desconhecido.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entity_id_from_js = $_POST['entity_id'] ?? ''; // Agora espera 'entity_id'
    $service_action_from_js = $_POST['service_action'] ?? ''; // NOVO: Espera 'service_action'

    error_log("HA_PHP_Routine: Requisição POST recebida. entity_id: '$entity_id_from_js', service_action: '$service_action_from_js'");

    if (empty($entity_id_from_js) || empty($service_action_from_js)) { 
        $response['message'] = 'ID da entidade e ação do serviço são obrigatórios.';
        error_log("HA_PHP_Routine: ERRO - Dados incompletos: entity_id ou service_action.");
    } else {
        $parts = explode('.', $entity_id_from_js, 2); 
        $domain = $parts[0] ?? 'homeassistant'; 
        
        $service = $service_action_from_js; 

        $full_service_path = $domain . '/' . $service; 
        $data_payload = ['entity_id' => $entity_id_from_js]; 
        $json_payload = json_encode($data_payload);

        error_log("HA_PHP_Routine: Preparando chamada cURL para HA. Dominio/Servico: " . $full_service_path);
        error_log("HA_PHP_Routine: Full API URL: " . HA_API_URL . '/' . $full_service_path . ", Payload: " . $json_payload);
        error_log("HA_PHP_Routine: HA_ACCESS_TOKEN (primeiros 10 chars): " . substr(HA_ACCESS_TOKEN, 0, 10) . "...");


        $ch = curl_init(); 
        curl_setopt($ch, CURLOPT_URL, HA_API_URL . '/' . $full_service_path); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . HA_ACCESS_TOKEN,
            'Content-Type: application/json',
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        curl_setopt($ch, CURLOPT_VERBOSE, true); 
        $verbose_log_file = fopen(__DIR__ . '/curl_verbose_ha.log', 'w');
        curl_setopt($ch, CURLOPT_STDERR, $verbose_log_file);

        $ha_response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        $curl_errno = curl_errno($ch);

        curl_close($ch);
        if (isset($verbose_log_file)) { fclose($verbose_log_file); }

        error_log("HA_PHP_Routine: RESPOSTA FINAL DO HA - HTTP Code: " . $http_code . 
                  ", Curl Error (Num): " . $curl_errno . 
                  ", Curl Error (Msg): '" . $curl_error . 
                  "', HA Body: '" . $ha_response . "'");

        if ($curl_error) {
            $response['message'] = 'Erro de rede ou cURL ao conectar com Home Assistant (cURL Error: ' . $curl_errno . '): ' . $curl_error;
        } elseif ($http_code >= 200 && $http_code < 300) {
            $decoded_ha_response = json_decode($ha_response, true);
            if (json_last_error() === JSON_ERROR_NONE || empty($ha_response)) {
                $response['success'] = true;
                $response['message'] = 'Comando enviado ao Home Assistant com sucesso.';
                $response['ha_data'] = $decoded_ha_response;
            } else {
                $response['message'] = 'Comando enviado, mas Home Assistant retornou resposta inválida: ' . $ha_response;
            }
        } else {
            $response['message'] = 'Home Assistant retornou erro HTTP ' . $http_code . ': ' . $ha_response;
            $response['ha_data'] = json_decode($ha_response, true);
        }
    }
}

ob_end_clean();
echo json_encode($response);
exit();
?>