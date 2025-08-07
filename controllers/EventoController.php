<?php
// ######################################################################
// # ATENÇÃO: ESTE BLOCO É PARA DEPURACAO. REMOVA EM PRODUÇÃO!        #
// ######################################################################
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/evento_controller_error.log'); // Log específico para o EventoController
// ######################################################################

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/Gado.php'; // Para buscar vacas
require_once __DIR__ . '/../models/Touro.php'; // Se houver lógica de touro em eventos

$evento = new Evento($pdo);
$gado = new Gado($pdo);
$touro = new Touro($pdo); // Se for necessário

$action = $_POST['action'] ?? $_GET['action'] ?? null;

error_log("EventoController: Ação recebida: " . ($action ?? 'NULO - Nenhuma ação recebida'));

switch ($action) {
	
	// Dentro do switch ($action) em EventoController.php

case 'mark_complete':
        header('Content-Type: application/json');
        $response = ['success' => false, 'message' => 'Erro ao processar a requisição.'];
        error_log("==================================================");
        error_log("EventoController: Ação 'mark_complete' iniciada.");

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            $evento->id = $_POST['id'];
            error_log("EventoController: Tentando concluir evento ID: {$evento->id}");

            if ($evento->readOne()) {
                error_log("EventoController: Evento ID {$evento->id} lido com sucesso. Verificando recorrência...");
                
                if ($evento->id_registro_manejo) {
                    error_log("EventoController: Evento vinculado ao registro de manejo ID: {$evento->id_registro_manejo}. Buscando detalhes do manejo...");

                    $queryManejo = "SELECT m.tipo, m.recorrencia_meses, m.recorrencia_dias FROM registros_manejos rm JOIN manejos m ON rm.id_manejo = m.id WHERE rm.id = :id_registro_manejo";
                    $stmtManejo = $pdo->prepare($queryManejo);
                    $stmtManejo->bindParam(':id_registro_manejo', $evento->id_registro_manejo);
                    $stmtManejo->execute();
                    $manejoDetails = $stmtManejo->fetch(PDO::FETCH_ASSOC);

                    if ($manejoDetails) {
                        error_log("EventoController: Detalhes do manejo: Tipo='{$manejoDetails['tipo']}', Rec_Meses='{$manejoDetails['recorrencia_meses']}', Rec_Dias='{$manejoDetails['recorrencia_dias']}'");
                        
                        $intervalo = null;
                        $tipoNovoEvento = '';
                        $tituloNovoEvento = $evento->titulo;

                        // Lógica para VACINAS (em meses)
                        if ($manejoDetails['tipo'] === 'Vacinas' && !empty($manejoDetails['recorrencia_meses']) && $manejoDetails['recorrencia_meses'] > 0) {
                            $intervalo = 'P' . intval($manejoDetails['recorrencia_meses']) . 'M';
                            $tipoNovoEvento = 'Vacina';
                        } 
                        // Lógica para BST (em dias)
                        else if ($manejoDetails['tipo'] === 'BST' && !empty($manejoDetails['recorrencia_dias']) && $manejoDetails['recorrencia_dias'] > 0) {
                            $intervalo = 'P' . intval($manejoDetails['recorrencia_dias']) . 'D';
                            $tipoNovoEvento = 'Geral'; // BST é mapeado para 'Geral'
                        }

                        // Se um intervalo foi definido, cria o novo evento
                        if ($intervalo) {
                            error_log("EventoController: CONDIÇÕES ATENDIDAS! Criando próximo evento recorrente com intervalo de {$intervalo}.");
                            
                            $newEvent = new Evento($pdo);
                            $newEvent->titulo = $tituloNovoEvento;
                            $newEvent->descricao = "Recorrência automática de {$manejoDetails['tipo']} a partir de " . date('d/m/Y', strtotime($evento->data_evento));
                            $newEvent->tipo_evento = $tipoNovoEvento;
                            $newEvent->id_vaca = $evento->id_vaca;
                            $newEvent->id_registro_manejo = $evento->id_registro_manejo;

                            $dataOriginal = new DateTime($evento->data_evento);
                            $dataOriginal->add(new DateInterval($intervalo));
                            $newEvent->data_evento = $dataOriginal->format('Y-m-d');

                            if ($newEvent->create()) {
                                error_log("EventoController: SUCESSO! Novo evento recorrente criado.");
                            } else {
                                error_log("EventoController: FALHA ao chamar newEvent->create().");
                            }
                        } else {
                            error_log("EventoController: Condições para recorrência não atendidas. Nenhuma ação tomada.");
                        }
                    } else {
                        error_log("EventoController: Nenhum detalhe de manejo encontrado para o registro ID: {$evento->id_registro_manejo}");
                    }
                } else {
                    error_log("EventoController: Evento ID {$evento->id} não está vinculado a um registro de manejo. Nenhuma recorrência a ser criada.");
                }
            } else {
                 error_log("EventoController: FALHA ao ler os dados do evento ID: {$evento->id}");
            }

            if ($evento->deactivate()) {
                $response['success'] = true;
                $response['message'] = 'Evento marcado como concluído!';
            } else {
                $response['message'] = 'Falha ao atualizar o status do evento no banco de dados.';
                error_log("EventoController: FALHA ao desativar o evento ID: {$evento->id}");
            }

        } else {
            $response['message'] = 'Requisição inválida ou ID não fornecido.';
            error_log("EventoController: Requisição para 'mark_complete' foi inválida (não era POST ou não tinha ID).");
        }
        
        error_log("==================================================");
        echo json_encode($response);
        exit();
    case 'delete':
        // ... o restante do código continua aqui ...
    case 'create':
        error_log("EventoController: Processando ação 'create'.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $evento->titulo = $_POST['titulo'] ?? die('Título do evento não especificado.');
            $evento->data_evento = $_POST['data_evento'] ?? die('Data do evento não especificada.');
            $evento->descricao = $_POST['descricao'] ?? null;
            $evento->tipo_evento = $_POST['tipo_evento'] ?? 'Geral';
            $evento->id_vaca = $_POST['id_vaca'] ?? null;
            $evento->ativo = isset($_POST['ativo']) ? 1 : 0; // Campo ativo (checkbox)

            if ($evento->create()) {
                $_SESSION['message'] = "Evento cadastrado com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao cadastrar evento.";
                error_log("EventoController: ERRO ao cadastrar evento: " . ($pdo->errorInfo()[2] ?? 'Desconhecido'));
            }
            header('Location: ../views/eventos/index.php');
            exit();
        }
        break;

    case 'update':
        error_log("EventoController: Processando ação 'update'.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $evento->id = $_POST['id'] ?? die('ID do evento não especificado para atualização.');
            $evento->titulo = $_POST['titulo'] ?? die('Título do evento não especificado.');
            $evento->data_evento = $_POST['data_evento'] ?? die('Data do evento não especificada.');
            $evento->descricao = $_POST['descricao'] ?? null;
            $evento->tipo_evento = $_POST['tipo_evento'] ?? 'Geral';
            $evento->id_vaca = $_POST['id_vaca'] ?? null;
            $evento->ativo = isset($_POST['ativo']) ? 1 : 0;

            if ($evento->update()) {
                $_SESSION['message'] = "Evento atualizado com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao atualizar evento.";
                error_log("EventoController: ERRO ao atualizar evento: " . ($pdo->errorInfo()[2] ?? 'Desconhecido'));
            }
            header('Location: ../views/eventos/index.php');
            exit();
        }
        break;

    case 'delete':
        error_log("EventoController: Processando ação 'delete'.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $evento->id = $_POST['id'] ?? die('ID do evento não especificado para exclusão.');
            if ($evento->delete()) {
                $_SESSION['message'] = "Evento excluído com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao excluir evento.";
                error_log("EventoController: ERRO ao excluir evento: " . ($pdo->errorInfo()[2] ?? 'Desconhecido'));
            }
            header('Location: ../views/eventos/index.php');
            exit();
        }
        break;

    case 'get_event_days_in_month': // Ação para AJAX para o mini-calendário
        header('Content-Type: application/json');
        $mes = $_GET['mes'] ?? null;
        $ano = $_GET['ano'] ?? null;
        $response = ['success' => false, 'days' => [], 'message' => ''];

        if ($mes && $ano) {
            try {
                $days_with_events = $evento->getEventDaysInMonth($mes, $ano);
                $response['success'] = true;
                $response['days'] = $days_with_events;
            } catch (PDOException $e) {
                $response['message'] = "Erro ao buscar dias com eventos: " . $e->getMessage();
                error_log("EventoController (AJAX): ERRO de PDO ao buscar dias: " . $e->getMessage());
            }
        } else {
            $response['message'] = "Mês ou ano não fornecidos.";
        }
        echo json_encode($response);
        exit();

    default:
        error_log("EventoController: Ação padrão ou não reconhecida: " . ($action ?? 'NULO'));
        break;
}