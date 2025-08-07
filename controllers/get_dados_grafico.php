<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Pesagem.php';
require_once __DIR__ . '/../models/Gado.php';

// Validação básica do ID do gado
$id_gado = isset($_GET['id_gado']) ? (int)$_GET['id_gado'] : 0;
if ($id_gado <= 0) {
    echo json_encode(['error' => 'ID do animal inválido.']);
    exit;
}

// --- INÍCIO DA MODIFICAÇÃO: Curva de referência mês a mês até 24 meses ---
$curva_referencia = [
    ['idade' => 0, 'peso' => 40],   // Peso ao nascer
    ['idade' => 1, 'peso' => 63],
    ['idade' => 2, 'peso' => 86],
    ['idade' => 3, 'peso' => 110],
    ['idade' => 4, 'peso' => 135],
    ['idade' => 5, 'peso' => 158],
    ['idade' => 6, 'peso' => 180],
    ['idade' => 7, 'peso' => 202],
    ['idade' => 8, 'peso' => 225],
    ['idade' => 9, 'peso' => 248],
    ['idade' => 10, 'peso' => 270],
    ['idade' => 11, 'peso' => 292],
    ['idade' => 12, 'peso' => 315],
    ['idade' => 13, 'peso' => 338],
    ['idade' => 14, 'peso' => 360],
    ['idade' => 15, 'peso' => 382], // Idade ideal para primeira inseminação
    ['idade' => 16, 'peso' => 405],
    ['idade' => 17, 'peso' => 428],
    ['idade' => 18, 'peso' => 450],
    ['idade' => 19, 'peso' => 470],
    ['idade' => 20, 'peso' => 490],
    ['idade' => 21, 'peso' => 510],
    ['idade' => 22, 'peso' => 528],
    ['idade' => 23, 'peso' => 545],
    ['idade' => 24, 'peso' => 560]  // Peso ao primeiro parto
];
// --- FIM DA MODIFICAÇÃO ---

// O restante do script permanece o mesmo
$pesagemModel = new Pesagem($pdo);
$pesagens_animal = $pesagemModel->readByGadoId($id_gado);

$gadoModel = new Gado($pdo);
$gadoModel->id = $id_gado;
$gadoModel->readOne();

// Preparar os dados para o formato do gráfico
$dados_formatados = [
    'labels' => array_column($curva_referencia, 'idade'),
    'referenceData' => array_column($curva_referencia, 'peso'),
    'animalData' => array_map(function($p) {
        return ['x' => (int)$p['idade_em_meses'], 'y' => (float)$p['peso']];
    }, $pesagens_animal),
    'animalBrinco' => $gadoModel->brinco
];

echo json_encode($dados_formatados);
?>