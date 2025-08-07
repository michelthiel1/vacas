<?php
session_start(); 

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/gado_controller_error.log');

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Gado.php';
require_once __DIR__ . '/../models/Touro.php'; 
require_once __DIR__ . '/../models/Inseminacao.php'; 
require_once __DIR__ . '/../models/Evento.php';
require_once __DIR__ . '/../models/RegistroManejo.php'; 

$gado = new Gado($pdo);
$inseminacaoModel = new Inseminacao($pdo);
$eventoModel = new Evento($pdo);
$registroManejoModel = new RegistroManejo($pdo);

$action = $_POST['action'] ?? $_GET['action'] ?? null;

$role = $_SESSION['role'] ?? '';
$restricted_actions = ['create', 'update', 'delete', 'toggle_cio_monitoring'];
if (in_array($action, $restricted_actions) && $role !== 'admin') {
    $_SESSION['message'] = "Você não tem permissão para executar esta ação.";
    header('Location: ../views/gado/index.php'); 
    exit();
}

error_log("GadoController: Ação recebida: " . ($action ?? 'NULO'));

switch ($action) {
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gado->brinco = $_POST['brinco'] ?? '';
            $gado->nome = $_POST['nome'] ?? '';
            $gado->nascimento = $_POST['nascimento'] ?? '';
            $gado->raca = $_POST['raca'] ?? '';
            $gado->observacoes = $_POST['observacoes'] ?? '';
            $gado->status = $_POST['status'] ?? 'Vazia';
            $gado->grupo = $_POST['grupo'] ?? '';
            $gado->bst = $_POST['bst'] ?? 0;
            $gado->ativo = 1; 
            $gado->escore = $_POST['escore'] ?? null; 
            $gado->sexo = $_POST['sexo'] ?? ''; 
            $gado->id_mae = $_POST['id_mae'] ?? $_POST['id_mae_hidden'] ?? null;
            $gado->id_pai = $_POST['id_pai'] ?? $_POST['id_pai_hidden'] ?? null;

            // --- INÍCIO DA CORREÇÃO ---
            $gado->leite_descarte = $_POST['leite_descarte'] ?? 'Não';
            $gado->cor_bastao = $_POST['cor_bastao'] ?? '';
            // --- FIM DA CORREÇÃO ---

            if ($gado->create()) {
                $id_novo_animal = $pdo->lastInsertId();
                $brinco_novo_animal = $gado->brinco;
                $_SESSION['message'] = "Animal '{$brinco_novo_animal}' cadastrado com sucesso!";

                if ($gado->grupo === 'Bezerra' && !empty($gado->nascimento)) {
                    
                    function criarManejoEEvento($pdo, $id_animal, $brinco, $nascimento, $id_manejo, $offset, $titulo_evento, $tipo_evento) {
                        $registroManejo = new RegistroManejo($pdo);
                        $evento = new Evento($pdo);
                        
                        $data_base = new DateTime($nascimento);
                        $data_evento_calculada = $data_base->add(new DateInterval($offset))->format('Y-m-d');
                        
                        $registroManejo->id_gado = $id_animal;
                        $registroManejo->id_manejo = $id_manejo;
                        $registroManejo->aplicado_rebanho = 0;
                        $registroManejo->data_aplicacao = $data_evento_calculada;
                        $registroManejo->observacoes = 'Agendamento automático na criação do bezerro.';
                        
                        if ($registroManejo->create()) {
                            $id_novo_registro = $pdo->lastInsertId();
                            
                            $evento->titulo = "{$titulo_evento} ({$brinco})";
                            $evento->data_evento = $data_evento_calculada;
                            $evento->tipo_evento = $tipo_evento;
                            $evento->id_vaca = $id_animal;
                            $evento->id_registro_manejo = $id_novo_registro;
                            $evento->create();
                        }
                    }

                    try {
                        criarManejoEEvento($pdo, $id_novo_animal, $brinco_novo_animal, $gado->nascimento, 49, 'P2M', 'Desmame', 'Protocolo de Saúde');
                        criarManejoEEvento($pdo, $id_novo_animal, $brinco_novo_animal, $gado->nascimento, 50, 'P3M', 'Brucelose', 'Vacinas');
                        criarManejoEEvento($pdo, $id_novo_animal, $brinco_novo_animal, $gado->nascimento, 51, 'P3M', 'Mochar', 'Protocolo de Saúde');
                        
                        $_SESSION['message'] .= " Eventos futuros de manejo para o bezerro foram agendados automaticamente!";

                    } catch (Exception $e) {
                         $_SESSION['message'] .= " Erro ao agendar eventos automáticos: " . $e->getMessage();
                         error_log("GadoController: Erro na automação de eventos para bezerro ID {$id_novo_animal}. Erro: " . $e->getMessage());
                    }
                }

            } else {
                $_SESSION['message'] = "Erro ao cadastrar animal.";
            }
            
            $redirect_url = '../views/gado/index.php';
            
            if (isset($_POST['parto_id_redirect']) && !empty($_POST['parto_id_redirect'])) {
                $parto_id = $_POST['parto_id_redirect'];
                $redirect_url = '../views/partos/view.php?id=' . $parto_id;
            }

            header('Location: ' . $redirect_url); 
            exit();
        }
        break;

    case 'update':
        error_log("GadoController: Processando ação 'update'.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_gado = $_POST['id'] ?? die('ID do animal não especificado.');
            $novo_status = $_POST['status'] ?? '';
            
            $gado_atual = new Gado($pdo);
            $gado_atual->id = $id_gado;
            $gado_atual->readOne();
            $status_antigo = $gado_atual->status;

            $gado->id = $id_gado;
            $gado->brinco = $_POST['brinco'] ?? '';
            $gado->nome = $_POST['nome'] ?? '';
            $gado->nascimento = $_POST['nascimento'] ?? '';
            $gado->raca = $_POST['raca'] ?? '';
            $gado->observacoes = $_POST['observacoes'] ?? '';
            $gado->status = $novo_status;
            $gado->grupo = $_POST['grupo'] ?? '';
            $gado->bst = $_POST['bst'] ?? 0;
            $gado->ativo = isset($_POST['ativo']) ? 1 : 0; 
            $gado->escore = $_POST['escore'] ?? null; 
            $gado->sexo = $_POST['sexo'] ?? ''; 
            $gado->id_mae = $_POST['id_mae'] ?? $_POST['id_mae_hidden'] ?? null;
            $gado->id_pai = $_POST['id_pai'] ?? $_POST['id_pai_hidden'] ?? null;

            // --- INÍCIO DA CORREÇÃO ---
            $gado->leite_descarte = $_POST['leite_descarte'] ?? 'Não';
            $gado->cor_bastao = $_POST['cor_bastao'] ?? '';
            // --- FIM DA CORREÇÃO ---

            if ($novo_status === 'Prenha' && $status_antigo !== 'Prenha') {
                error_log("GadoController: Detectada mudança de status para 'Prenha' para o animal ID: {$id_gado}.");

                $ultima_inseminacao = $inseminacaoModel->getUltimaInseminacao($id_gado);
                
                if ($ultima_inseminacao) {
                    $registroManejoModel->id_gado = $id_gado;
                    $registroManejoModel->id_manejo = 7; 
                    $registroManejoModel->aplicado_rebanho = 0;
                    $registroManejoModel->data_aplicacao = date('Y-m-d'); 
                    $registroManejoModel->observacoes = 'Diagnóstico de gestação positivo. Eventos futuros gerados automaticamente.';
                    
                    $id_novo_registro_manejo = 0;
                    if ($registroManejoModel->create()) {
                        $id_novo_registro_manejo = $pdo->lastInsertId();
                        error_log("GadoController: Registro de manejo automático (ID: {$id_novo_registro_manejo}) criado para o diagnóstico.");
                    } else {
                        error_log("GadoController: ERRO ao criar o registro de manejo automático.");
                    }

                    $data_base = new DateTime($ultima_inseminacao['data_inseminacao']);
                    
                    $gado->Data_parto = (clone $data_base)->add(new DateInterval('P276D'))->format('Y-m-d');
                    $gado->Data_secagem = (clone $data_base)->add(new DateInterval('P216D'))->format('Y-m-d');
                    $gado->Data_preparto = (clone $data_base)->add(new DateInterval('P255D'))->format('Y-m-d');
                    
                    $eventoModel->titulo = "Previsão de Parto (" . $gado->brinco . ")";
                    $eventoModel->data_evento = $gado->Data_parto;
                    $eventoModel->tipo_evento = 'Parto';
                    $eventoModel->id_vaca = $id_gado;
                    $eventoModel->id_registro_manejo = $id_novo_registro_manejo;
                    $eventoModel->create();

                    $eventoModel->titulo = "Previsão de Secagem (" . $gado->brinco . ")";
                    $eventoModel->data_evento = $gado->Data_secagem;
                    $eventoModel->tipo_evento = 'Geral';
                    $eventoModel->id_vaca = $id_gado;
                    $eventoModel->id_registro_manejo = $id_novo_registro_manejo;
                    $eventoModel->create();
                    
                    $eventoModel->titulo = "Protocolo Pré-parto (" . $gado->brinco . ")";
                    $eventoModel->data_evento = $gado->Data_preparto;
                    $eventoModel->tipo_evento = 'Saúde';
                    $eventoModel->id_vaca = $id_gado;
                    $eventoModel->id_registro_manejo = $id_novo_registro_manejo;
                    $eventoModel->create();

                    $inseminacaoModel->confirmarUltimaInseminacao($id_gado);

                    $_SESSION['message'] = "Status da vaca atualizado! Registro de manejo e eventos futuros vinculados foram criados na agenda.";
                    error_log("GadoController: Eventos e datas de prenhez criados com sucesso para o animal ID: {$id_gado}.");

                } else {
                     $_SESSION['message'] = "Status atualizado, mas não foi possível criar eventos (nenhuma inseminação ativa encontrada).";
                     error_log("GadoController: ERRO ao criar eventos de prenhez para o animal ID: {$id_gado}. Nenhuma inseminação válida encontrada.");
                }
            }
           
            if ($gado->update()) {
                if (!isset($_SESSION['message'])) {
                    $_SESSION['message'] = "Animal atualizado com sucesso!";
                }
            } else {
                $_SESSION['message'] = "Erro ao atualizar animal.";
            }

            header('Location: ../views/gado/index.php'); 
            exit();
        }
        break;

    case 'toggle_cio_monitoring':
        header('Content-Type: application/json');
        $id_gado = $_POST['id'] ?? null;
        $response = ['success' => false, 'is_monitored' => false];

        if ($id_gado) {
            if ($gado->toggleCioMonitoring($id_gado)) {
                $gado_atualizado = new Gado($pdo);
                $gado_atualizado->id = $id_gado;
                $gado_atualizado->readOne();

                $response['success'] = true;
                $response['is_monitored'] = ($gado_atualizado->data_monitoramento_cio !== null);
            }
        }
        echo json_encode($response);
        exit();

    case 'delete':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $gado->id = $_POST['id'];
            if ($gado->delete()) {
                $_SESSION['message'] = "Animal excluído com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao excluir o animal.";
            }
            header('Location: ../views/gado/index.php');
            exit();
        }
        break;

    default:
        header('Location: ../views/gado/index.php');
        exit();
}
?>