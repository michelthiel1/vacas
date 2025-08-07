<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// --- CONFIGURAÇÕES ---
define('UPLOAD_DIR', __DIR__ . '/../uploads/fotos_gado/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif']);
define('RESIZE_WIDTH', 800); // Largura máxima da imagem em pixels
define('JPEG_QUALITY', 80); // Qualidade da imagem JPEG (0-100)

$action = $_POST['action'] ?? $_GET['action'] ?? null;

// --- ROTEAMENTO DE AÇÕES ---
switch ($action) {
    case 'upload':
        handle_upload($pdo);
        break;
    case 'delete':
        handle_delete($pdo);
        break;
    default:
        // Se nenhuma ação válida for fornecida, redireciona para a home.
        header('Location: ../../index.php');
        exit();
}

// --- FUNÇÃO PARA LIDAR COM UPLOAD ---
function handle_upload($pdo) {
    if (!isset($_FILES['foto'])) {
        $_SESSION['message'] = "Nenhum arquivo enviado.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    $foto = $_FILES['foto'];
    $id_gado = $_POST['id_gado'] ?? null;
    $legenda = $_POST['legenda'] ?? null;

    if (!$id_gado) {
        $_SESSION['message'] = "Erro: ID do animal não especificado.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Validações
    if ($foto['error'] !== UPLOAD_ERR_OK || $foto['size'] > MAX_FILE_SIZE) {
        $_SESSION['message'] = "Erro no upload ou arquivo muito grande.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($foto['tmp_name']);
    if (!in_array($mime_type, ALLOWED_TYPES)) {
        $_SESSION['message'] = "Erro: Tipo de arquivo inválido.";
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Processamento
    try {
        $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
        $novo_nome_arquivo = "animal_" . $id_gado . "_" . time() . "." . $extensao;
        $caminho_final_servidor = UPLOAD_DIR . $novo_nome_arquivo;

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0777, true);
        }

        redimensionar_e_salvar_imagem($foto['tmp_name'], $caminho_final_servidor, $mime_type, RESIZE_WIDTH, JPEG_QUALITY);

        $caminho_db = 'uploads/fotos_gado/' . $novo_nome_arquivo;
        $query = "INSERT INTO gado_fotos (id_gado, caminho_arquivo, legenda) VALUES (:id_gado, :caminho, :legenda)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([':id_gado' => $id_gado, ':caminho' => $caminho_db, ':legenda' => $legenda]);
        
        $_SESSION['message'] = "Foto enviada com sucesso!";

    } catch (Exception $e) {
        $_SESSION['message'] = "Erro no processamento da imagem: " . $e->getMessage();
    }

    header('Location: ../views/gado/view.php?id=' . $id_gado);
    exit();
}

// --- NOVA FUNÇÃO PARA DELETAR ---
function handle_delete($pdo) {
    $id_foto = $_POST['id_foto'] ?? null;
    $id_gado = $_POST['id_gado'] ?? null; // Para redirecionamento

    if (!$id_foto || !$id_gado) {
        $_SESSION['message'] = "ID da foto ou do animal não fornecido.";
        header('Location: ../views/gado/index.php');
        exit();
    }

    try {
        // 1. Buscar o caminho do arquivo no DB antes de deletar
        $stmt = $pdo->prepare("SELECT caminho_arquivo FROM gado_fotos WHERE id = :id_foto");
        $stmt->execute([':id_foto' => $id_foto]);
        $foto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($foto) {
            $caminho_no_servidor = __DIR__ . '/../' . $foto['caminho_arquivo'];

            // 2. Deletar o registro do banco de dados
            $stmt_delete = $pdo->prepare("DELETE FROM gado_fotos WHERE id = :id_foto");
            $stmt_delete->execute([':id_foto' => $id_foto]);

            // 3. Deletar o arquivo físico do servidor
            if (file_exists($caminho_no_servidor)) {
                unlink($caminho_no_servidor);
            }
            $_SESSION['message'] = "Foto excluída com sucesso!";
        } else {
            $_SESSION['message'] = "Erro: Foto não encontrada.";
        }

    } catch (PDOException $e) {
        $_SESSION['message'] = "Erro de banco de dados ao excluir a foto.";
    }

    header('Location: ../views/gado/view.php?id=' . $id_gado);
    exit();
}

/**
 * Função para redimensionar e salvar a imagem.
 */
function redimensionar_e_salvar_imagem($temp_path, $final_path, $mime_type, $max_width, $quality) {
    list($width, $height) = getimagesize($temp_path);

    if ($width > $max_width) {
        $new_width = $max_width;
        $new_height = floor($height * ($max_width / $width));
    } else {
        $new_width = $width;
        $new_height = $height;
    }

    $thumb = imagecreatetruecolor($new_width, $new_height);
    
    switch ($mime_type) {
        case 'image/jpeg': $source = imagecreatefromjpeg($temp_path); break;
        case 'image/png': 
            $source = imagecreatefrompng($temp_path);
            imagealphablending($thumb, false);
            imagesavealpha($thumb, true);
            break;
        case 'image/gif': $source = imagecreatefromgif($temp_path); break;
        default: throw new Exception("Tipo de imagem não suportado.");
    }
    
    imagecopyresampled($thumb, $source, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
    
    switch ($mime_type) {
        case 'image/jpeg': imagejpeg($thumb, $final_path, $quality); break;
        case 'image/png': imagepng($thumb, $final_path, 9); break;
        case 'image/gif': imagegif($thumb, $final_path); break;
    }

    imagedestroy($source);
    imagedestroy($thumb);
}

 
?>