<?php
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type: application/json');

define('HA_API_URL_BASE', 'http://10.1.1.125:8123/api'); 
define('HA_ACCESS_TOKEN', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiI0MzEyMjRmNjYwOTI0Y2I3YjVjMDRhYmNiMzBkYzFjMyIsImlhdCI6MTc0OTE0MTU2OSwiZXhwIjoyMDY0NTAxNTY5fQ.mhwJpxXSlpfPEnUcyJqviwjZ8hNHygATJNwWu7GIUeM'); 

$entity_ids = $_GET['entities'] ?? null;

if (empty($entity_ids)) {
    echo json_encode(['success' => false, 'message' => 'Nenhuma entidade fornecida.']);
    exit;
}

$entities_array = explode(',', $entity_ids);
$states = [];
$success = true;

foreach ($entities_array as $entity_id) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, HA_API_URL_BASE . '/states/' . trim($entity_id));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . HA_ACCESS_TOKEN,
        'Content-Type: application/json',
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($curl_error || $http_code !== 200) {
        $states[trim($entity_id)] = 'error';
        $success = false;
        continue;
    }

    $data = json_decode($response, true);
    $states[trim($entity_id)] = $data['state'] ?? 'unavailable';
}

echo json_encode(['success' => $success, 'states' => $states]);
?>