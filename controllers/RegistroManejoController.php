<?php
session_start();
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/RegistroManejo.php';
require_once __DIR__ . '/../models/Manejo.php'; 
require_once __DIR__ . '/../models/Gado.php';    
require_once __DIR__ . '/../models/Evento.php'; 

$action = $_POST['action'] ?? $_GET['action'] ?? null;
$registroManejo = new RegistroManejo($pdo);

// Função para mapear tipo de manejo para tipo de evento
function mapearTipoEvento($tipo_manejo) {
    switch ($tipo_manejo) {
        case 'Vacinas':
            return 'Vacina';
        case 'Diagnostico':
        case 'Protocolo de Saúde':
            return 'Saúde';
        case 'Pré-Parto': // ADICIONADO
            return 'Parto'; 
        case 'BST':
        case 'Secagem': // ADICIONADO
        default:
            return 'Geral';
    }
}

switch ($action) {
    case 'create':
        if (empty($_POST['id_manejo']) || !is_numeric($_POST['id_manejo'])) {
            $_SESSION['message'] = "Erro: Você deve selecionar uma opção em 'Escolha do Manejo' antes de salvar.";
            header("Location: ../views/registros_manejo/create.php");
            exit();
        }

        $id_gado_post = $_POST['id_gado'];
        if ($id_gado_post === 'rebanho') {
            $registroManejo->id_gado = null;
            $registroManejo->aplicado_rebanho = 1;
        } else {
            $registroManejo->id_gado = $id_gado_post;
            $registroManejo->aplicado_rebanho = 0;
        }
        $registroManejo->id_manejo = $_POST['id_manejo'];
        $registroManejo->data_aplicacao = $_POST['data_aplicacao'];
        $registroManejo->observacoes = $_POST['observacoes'];
        
        if ($registroManejo->create()) {
            $id_novo_registro = $pdo->lastInsertId();
            $data_base = new DateTime($registroManejo->data_aplicacao);

            $manejo = new Manejo($pdo);
            $manejo->id = $registroManejo->id_manejo;
            $manejo->readOne();
            
            $temEventosPersonalizados = false;
            if (is_numeric($manejo->evento_dias_1) && !empty($manejo->evento_titulo_1)) {
                $temEventosPersonalizados = true;
            }

            if ($temEventosPersonalizados) {
                for ($i = 1; $i <= 6; $i++) {
                    $dias_key = "evento_dias_{$i}";
                    $titulo_key = "evento_titulo_{$i}";

                    if (is_numeric($manejo->{$dias_key}) && !empty($manejo->{$titulo_key})) {
                        $evento = new Evento($pdo);
                        $data_evento = clone $data_base;
                        $intervalo = 'P' . intval($manejo->{$dias_key}) . 'D';
                        $data_evento->add(new DateInterval($intervalo));
                        
                        $titulo_final = $manejo->{$titulo_key};
                        $id_vaca_evento = null;

                        if ($registroManejo->aplicado_rebanho == 0) {
                            $gado = new Gado($pdo);
                            $gado->id = $registroManejo->id_gado;
                            $gado->readOne();
                            $titulo_final .= " (" . $gado->brinco . ")";
                            $id_vaca_evento = $gado->id;
                        } else {
                             $titulo_final .= " (REBANHO)";
                        }

                        $evento->titulo = $titulo_final;
                        $evento->data_evento = $data_evento->format('Y-m-d');
                        $evento->tipo_evento = mapearTipoEvento($manejo->tipo);
                        $evento->id_vaca = $id_vaca_evento;
                        $evento->create();
                    }
                }
                $_SESSION['message'] = "Protocolo de manejo registrado e eventos personalizados criados!";

            } else {
                $evento = new Evento($pdo);
                $titulo_final = $manejo->nome;
                $id_vaca_evento = null;

                if ($registroManejo->aplicado_rebanho == 0) {
                     $gado = new Gado($pdo);
                     $gado->id = $registroManejo->id_gado;
                     $gado->readOne();
                     $titulo_final = "Manejo (" . $gado->brinco . "): " . $manejo->nome;
                     $id_vaca_evento = $gado->id;
                } else {
                     $titulo_final = "Manejo (REBANHO): " . $manejo->nome;
                }
                
                $evento->titulo = $titulo_final;
                $evento->data_evento = $registroManejo->data_aplicacao;
                $evento->descricao = $registroManejo->observacoes;
                $evento->tipo_evento = mapearTipoEvento($manejo->tipo);
                $evento->id_vaca = $id_vaca_evento;
                $evento->id_registro_manejo = $id_novo_registro;
                $evento->create();
                
                $_SESSION['message'] = "Manejo registrado e evento único criado na agenda!";
            }
        } else {
            $_SESSION['message'] = "Erro ao registrar manejo.";
        }
        header("Location: ../views/registros_manejo/index.php");
        exit();

    case 'delete':
        $registroManejo->id = $_POST['id'];
        $evento = new Evento($pdo);
        $evento->deleteByIdRegistroManejo($registroManejo->id);
        
        if ($registroManejo->delete()) {
            $_SESSION['message'] = "Registro de manejo e evento(s) vinculado(s) foram excluídos.";
        } else {
            $_SESSION['message'] = "Erro ao excluir o registro de manejo do banco.";
        }
        header("Location: ../views/registros_manejo/index.php");
        exit();

    case 'update':
        $_SESSION['message'] = "Funcionalidade de editar ainda não implementada.";
        header("Location: ../views/registros_manejo/index.php");
        exit();
        
    default:
        header("Location: ../views/registros_manejo/index.php");
        exit();
}
?>