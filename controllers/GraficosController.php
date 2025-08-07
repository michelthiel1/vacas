<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

error_reporting(0);
ini_set('display_errors', 0);

try {
    require_once __DIR__ . '/../config/database.php';

    if (!isset($pdo) || !$pdo) {
        throw new Exception("Falha na conexão com o banco de dados.");
    }

    require_once __DIR__ . '/../models/Gado.php';
    require_once __DIR__ . '/../models/Parto.php';
    require_once __DIR__ . '/../models/Inseminacao.php';
    require_once __DIR__ . '/../models/ProducaoLeite.php';

    $action = $_GET['action'] ?? null;
    $response = ['success' => false, 'data' => null, 'message' => 'Ação inválida.'];

    $filtros = [];
    if (!empty($_GET['search_query'])) $filtros['search_query'] = trim($_GET['search_query']);
    if (!empty($_GET['grupo'])) $filtros['grupo'] = explode(',', $_GET['grupo']);
    if (!empty($_GET['status'])) $filtros['status'] = explode(',', $_GET['status']);
    if (!empty($_GET['idade_min'])) $filtros['idade_min'] = $_GET['idade_min'];
    if (!empty($_GET['idade_max'])) $filtros['idade_max'] = $_GET['idade_max'];
    if (!empty($_GET['del_min'])) $filtros['del_min'] = $_GET['del_min'];
    if (!empty($_GET['del_max'])) $filtros['del_max'] = $_GET['del_max'];

    $gadoModel = new Gado($pdo);
    $partoModel = new Parto($pdo);
    $inseminacaoModel = new Inseminacao($pdo);
    $producaoLeiteModel = new ProducaoLeite($pdo);

    switch ($action) {
        case 'getDadosGraficos':
            $dados_graficos = [
                'status' => $gadoModel->getContagemStatusParaGrafico($filtros),
                'grupo' => $gadoModel->getContagemGrupoParaGrafico($filtros),
                'ordemParto' => $partoModel->getContagemPorOrdemDeParto($filtros),
                'escore' => $gadoModel->getContagemPorEscore($filtros),
                'eficienciaMensal' => $inseminacaoModel->getEficienciaMensal($filtros),
                'falhasInseminacao' => $inseminacaoModel->getFalhasInseminacaoPorVaca($filtros),
                'iep' => $gadoModel->getIntervaloEntrePartos($filtros),
                'partoConcepcao' => $gadoModel->getIntervaloPartoConcepcao($filtros),
                'producaoPorDEL' => $producaoLeiteModel->getMediaProducaoPorFaixaDEL($filtros),
                'producaoPorParto' => $producaoLeiteModel->getMediaProducaoPorOrdemDeParto($filtros),
            ];

            // --- LÓGICA DE PROJEÇÃO SIMPLIFICADA E CORRIGIDA ---

            // 1. Pega a contagem atual de lactantes como base
            $contagemLactantesAtual = $gadoModel->getContagemPorGrupoEspecifico('Lactante');

            // 2. Pega todos os partos históricos, agrupados por mês
            $partosPorMes = $partoModel->getContagemDePartosPorMes();

            // 3. Pega todas as previsões de parto futuras, agrupadas por mês
            $previsoesPorMes = $gadoModel->getContagemDePrevisoesPorMes();
            
            // 4. Monta a projeção
            $projecao = [];
            $dataReferencia = new DateTimeImmutable();

            // 4.1. Projeção para o FUTURO (soma cumulativa das previsões por 10 meses)
            $runningCountFuturo = $contagemLactantesAtual;
            for ($i = 1; $i <= 10; $i++) { // <-- ALTERADO DE 6 PARA 10
                $mesFuturo = $dataReferencia->modify("+$i month")->format('Y-m');
                $runningCountFuturo += ($previsoesPorMes[$mesFuturo] ?? 0);
                $projecao[$mesFuturo] = $runningCountFuturo;
            }

            // 4.2. Projeção para o PASSADO (subtração cumulativa dos partos por 3 meses)
            $runningCountPassado = $contagemLactantesAtual;
            for ($i = 0; $i < 3; $i++) { // <-- ALTERADO DE 6 PARA 3
                $mesCorrenteParaCalculo = $dataReferencia->modify("-$i month")->format('Y-m');
                $mesAnteriorAlvo = $dataReferencia->modify("-" . ($i + 1) . " month")->format('Y-m');
                
                $runningCountPassado -= ($partosPorMes[$mesCorrenteParaCalculo] ?? 0);
                $projecao[$mesAnteriorAlvo] = $runningCountPassado;
            }

            // 4.3. Adiciona o mês atual, que é a "âncora"
            $projecao[$dataReferencia->format('Y-m')] = $contagemLactantesAtual;

            // 5. Formata para o gráfico
            ksort($projecao);

            $labels = [];
            foreach (array_keys($projecao) as $mes) {
                $labels[] = DateTime::createFromFormat('Y-m', $mes)->format('m/Y');
            }

            $dados_graficos['lactacaoHistorico'] = [
                'labels' => $labels,
                'values' => array_values($projecao)
            ];
            
            $response['data'] = $dados_graficos;
            $response['success'] = true;
            break;

        case 'getAnimaisPorSegmento':
            $tipoGrafico = $_GET['tipo'] ?? '';
            $segmento = $_GET['segmento'] ?? '';

            if (empty($tipoGrafico) || empty($segmento)) {
                 throw new Exception("Tipo de gráfico ou segmento não especificado.");
            }

            $animais = [];
            switch($tipoGrafico) {
                case 'status':
                    $animais = $gadoModel->getAnimaisPorStatus($segmento, $filtros);
                    break;
                case 'grupo':
                    $animais = $gadoModel->getAnimaisPorGrupo($segmento, $filtros);
                    break;
                case 'escore':
                    $animais = $gadoModel->getAnimaisPorEscore($segmento, $filtros);
                    break;
                case 'ordemParto':
                    $animais = $gadoModel->getAnimaisPorOrdemDeParto($segmento, $filtros);
                    break;
            }

            $response['success'] = true;
            $response['data'] = $animais;
            break;
        
        default:
            $response['message'] = 'Nenhuma ação especificada.';
            break;
    }

} catch (Exception $e) {
    http_response_code(500);
    error_log("Erro em GraficosController: " . $e->getMessage());
    $response['message'] = 'Erro interno no servidor: ' . $e->getMessage();
}

echo json_encode($response);