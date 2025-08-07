<?php
// ######################################################################
// # ATENÇÃO: ESTE BLOCO É PARA DEPURACAO. REMOVA EM PRODUÇÃO!        #
// ######################################################################
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/inseminacao_controller_error.log'); 
// ######################################################################

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Inseminacao.php';
require_once __DIR__ . '/../models/Gado.php'; 
require_once __DIR__ . '/../models/Touro.php';
require_once __DIR__ . '/../models/Inseminador.php';


$inseminacao = new Inseminacao($pdo);
$gado = new Gado($pdo); 
$touro = new Touro($pdo);
$inseminador = new Inseminador($pdo);

$action = $_POST['action'] ?? $_GET['action'] ?? null;

error_log("InseminacaoController: Ação recebida: " . ($action ?? 'NULO - Nenhuma ação recebida'));

switch ($action) {
    case 'create':
        error_log("InseminacaoController: Processando ação 'create'.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inseminacao->tipo = $_POST['tipo'] ?? 'Inseminacao';
            $inseminacao->id_vaca = $_POST['id_vaca'] ?? ''; 
            $inseminacao->id_touro = $_POST['id_touro'] ?? '';       
            $inseminacao->id_inseminador = $_POST['id_inseminador'] ?? ''; 
            $inseminacao->data_inseminacao = $_POST['data_inseminacao'] ?? '';
            $inseminacao->observacoes = $_POST['observacoes'] ?? '';
            $inseminacao->status_inseminacao = $_POST['status_inseminacao'] ?? 'Aguardando Diagnostico';
            $inseminacao->ativo = 1; // Salvar ATIVO como 1 por padrão na criação

            if ($inseminacao->create()) {
                // Atualizar o status da vaca para "Inseminada"
                $vaca_id_para_atualizar = $inseminacao->id_vaca;
                $novo_status_vaca = 'Inseminada';
                
                $query_update_status = "UPDATE gado SET status = :novo_status WHERE id = :id_vaca";
                $stmt_update_status = $pdo->prepare($query_update_status);
                $stmt_update_status->bindParam(':novo_status', $novo_status_vaca);
                $stmt_update_status->bindParam(':id_vaca', $vaca_id_para_atualizar);
                
                if ($stmt_update_status->execute()) {
                    $_SESSION['message'] = "Inseminação criada e status da vaca atualizado para 'Inseminada'!";
                } else {
                    $_SESSION['message'] = "Inseminação criada com sucesso, mas houve um erro ao atualizar o status da vaca.";
                }
            } else {
                $_SESSION['message'] = "Erro ao criar inseminação.";
            }
            header('Location: ../views/inseminacoes/index.php');
            exit();
        }
        break;

    case 'update':
        error_log("InseminacaoController: Processando ação 'update'.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inseminacao->id = $_POST['id'] ?? die('ID da inseminação não especificado para atualização.');
            $inseminacao->tipo = $_POST['tipo'] ?? 'Inseminacao';
            $inseminacao->id_vaca = $_POST['id_vaca'] ?? ''; 
            $inseminacao->id_touro = $_POST['id_touro'] ?? '';       
            $inseminacao->id_inseminador = $_POST['id_inseminador'] ?? ''; 
            $inseminacao->data_inseminacao = $_POST['data_inseminacao'] ?? '';
            $inseminacao->observacoes = $_POST['observacoes'] ?? '';
            $inseminacao->status_inseminacao = $_POST['status_inseminacao'] ?? 'Aguardando Diagnostico';
            // REMOVIDO: $inseminacao->ativo = isset($_POST['ativo']) ? 1 : 0; // Removida a atribuição de 'ativo'
            // O campo 'ativo' não será mais alterado na atualização, mantendo seu valor existente.

            if ($inseminacao->update()) {
                $_SESSION['message'] = "Inseminação atualizada com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao atualizar inseminação.";
            }
            header('Location: ../views/inseminacoes/index.php');
            exit();
        }
        break;

    case 'delete':
        error_log("InseminacaoController: Processando ação 'delete'.");
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inseminacao->id = $_POST['id'] ?? die('ID da inseminação não especificado para exclusão.');
            if ($inseminacao->delete()) {
                $_SESSION['message'] = "Inseminação excluída com sucesso!";
            } else {
                $_SESSION['message'] = "Erro ao excluir inseminação.";
            }
            header('Location: ../views/inseminacoes/index.php');
            exit();
        }
        break;

    case 'read_brincos': 
        header('Content-Type: application/json');
        $brincos_data = [];
        try {
            $stmt = $gado->readBrincos(); 
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $brincos_data[] = ['id' => $row['id'], 'brinco' => $row['brinco']];
            }
            echo json_encode(['success' => true, 'brincos' => $brincos_data]);
        } catch (PDOException $e) {
            error_log("InseminacaoController: ERRO ao buscar brincos: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar brincos: ' . $e->getMessage()]);
        }
        exit();

    case 'read_touros': 
        header('Content-Type: application/json');
        $touros_list = [];
        try {
            $stmt = $touro->read();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $touros_list[] = ['id' => $row['id'], 'nome' => $row['nome']]; 
            }
            echo json_encode(['success' => true, 'touros' => $touros_list]);
        } catch (PDOException $e) {
            error_log("InseminacaoController: ERRO ao buscar touros: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar touros: ' . $e->getMessage()]);
        }
        exit();

    case 'read_inseminadores': 
        header('Content-Type: application/json');
        $inseminadores_list = [];
        try {
            $stmt = $inseminador->read();
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $inseminadores_list[] = ['id' => $row['id'], 'nome' => $row['nome']]; 
            }
            echo json_encode(['success' => true, 'inseminadores' => $inseminadores_list]);
        } catch (PDOException $e) {
            error_log("InseminacaoController: ERRO ao buscar inseminadores: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar inseminadores: ' . $e->getMessage()]);
        }
        exit();
		
		// Dentro do switch, no case 'create':
if ($inseminacao->create()) {
    // ... (código existente que atualiza o status da vaca) ...
    $_SESSION['message'] = "Inseminação criada e status da vaca atualizado para 'Inseminada'!";

    // ### INÍCIO DA NOVA LÓGICA DE BAIXA DE ESTOQUE DE SÊMEN ###
    if (!empty($inseminacao->id_touro)) {
        $touroModel = new Touro($pdo);
        // Subtrai 1 dose do estoque do touro selecionado
        $touroModel->ajustarDosesEstoque($inseminacao->id_touro, -1);
    }
    // ### FIM DA NOVA LÓGICA ###

} else {
    $_SESSION['message'] = "Erro ao criar inseminação.";
}
header('Location: ../views/inseminacoes/index.php');
exit();
        
    default:
        error_log("InseminacaoController: Ação padrão ou não reconhecida: " . ($action ?? 'NULO'));
        break;
}
?>