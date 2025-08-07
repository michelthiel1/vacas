<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';

$id_gado = $_GET['id_gado'] ?? null;
if (!$id_gado) {
    die('ID do animal não fornecido.');
}

$gado = new Gado($pdo);
$gado->id = $id_gado;
$gado->readOne(); // Carrega os dados do animal
?>

<h2 class="page-title">Adicionar Foto para: <?php echo htmlspecialchars($gado->brinco); ?></h2>

<form action="../../controllers/FotoController.php" method="post" enctype="multipart/form-data">
    <input type="hidden" name="action" value="upload">
    <input type="hidden" name="id_gado" value="<?php echo htmlspecialchars($id_gado); ?>">

    <input type="file" id="foto" name="foto" accept="image/*" required style="display: none;">

    <input type="file" id="camera-input" accept="image/*" capture="environment" style="display: none;">
    
    <input type="file" id="gallery-input" accept="image/*" style="display: none;">

    <div>
        <label>Origem da Foto:</label>
        <div class="button-group" style="margin-top: 5px; justify-content: space-between;">
            <button type="button" id="open-camera-btn" class="btn btn-secondary" style="width: 48%;">
                <i class="fas fa-camera"></i> Usar a Câmera
            </button>
            <button type="button" id="open-gallery-btn" class="btn btn-secondary" style="width: 48%;">
                <i class="fas fa-images"></i> Escolher da Galeria
            </button>
        </div>
        <p style="font-size:0.8em; color: #666;">Tamanho máximo: 5MB. Formatos: JPG, PNG, GIF.</p>
    </div>

    <div id="image-preview-container" style="margin-top: 15px; display: none; text-align: center;">
        <label>Pré-visualização:</label>
        <img id="image-preview" src="#" alt="Pré-visualização da imagem" style="max-width: 100%; height: auto; margin-top: 5px; border: 1px solid #ddd; border-radius: 8px;">
    </div>

    <div>
        <label for="legenda">Legenda (opcional):</label>
        <input type="text" id="legenda" name="legenda" placeholder="Ex: Foto de perfil, Foto de lado...">
    </div>

    <div class="button-group">
        <button type="submit" class="btn btn-primary">Enviar Foto</button>
        <a href="view.php?id=<?php echo htmlspecialchars($id_gado); ?>" class="btn btn-secondary">Cancelar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mainFileInput = document.getElementById('foto');
    const cameraInput = document.getElementById('camera-input');
    const galleryInput = document.getElementById('gallery-input');
    
    const openCameraBtn = document.getElementById('open-camera-btn');
    const openGalleryBtn = document.getElementById('open-gallery-btn');
    
    const previewContainer = document.getElementById('image-preview-container');
    const previewImage = document.getElementById('image-preview');

    // Botão da CÂMERA aciona o input da CÂMERA
    openCameraBtn.addEventListener('click', function() {
        cameraInput.click();
    });

    // Botão da GALERIA aciona o input da GALERIA
    openGalleryBtn.addEventListener('click', function() {
        galleryInput.click();
    });

    // Função que lida com a seleção de um arquivo (de qualquer um dos inputs gatilho)
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) {
            return;
        }

        // Para submeter o arquivo no formulário principal, criamos um DataTransfer
        // e o colocamos no input 'foto' que será de fato enviado.
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        mainFileInput.files = dataTransfer.files;

        // Mostra a pré-visualização da imagem
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }

    // Adiciona o mesmo "ouvinte" para os dois inputs gatilho
    cameraInput.addEventListener('change', handleFileSelect);
    galleryInput.addEventListener('change', handleFileSelect);
});
</script>