<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';

$gado = new Gado($pdo); // Instancia o modelo Gado

error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

// Opções de raça para o select (correspondendo ao ENUM do MySQL)
$racaOptions = ['Holandês', 'Angus', 'Jersey'];

// Captura o ID da URL
$gado->id = $_GET['id'] ?? null; 
// error_log("GadoEdit: ID recebido na URL: " . ($gado->id ?? 'NULO')); // Descomente para depurar

// Verifica se o ID foi passado e tenta ler o animal
if ($gado->id) {
    if (!$gado->readOne()) {
        $_SESSION['message'] = "Animal não encontrado ou ID inválido.";
        // error_log("GadoEdit: Falha ao encontrar animal com ID: " . $gado->id); // Descomente para depurar
        header('Location: index.php'); 
        exit();
    }
} else {
    $_SESSION['message'] = "ID do animal não especificado.";
    // error_log("GadoEdit: ID do animal não especificado na URL."); // Descomente para depurar
    header('Location: index.php'); 
    exit();
}

// Se chegou até aqui, o animal foi encontrado e seus dados estão em $gado->propriedade
?>

<h2 class="page-title">Editar Animal</h2>

<?php if ($message): ?>
    <div class="alert alert-danger"><?php echo $message; ?></div>
<?php endif; ?>

<form action="../../controllers/GadoController.php" method="post">
    <input type="hidden" name="action" value="update">
    <input type="hidden" name="id" value="<?php echo htmlspecialchars($gado->id); ?>">

    <div>
        <label for="brinco">Brinco:</label>
        <input type="text" id="brinco" name="brinco" value="<?php echo htmlspecialchars($gado->brinco); ?>" required>
    </div>

    <div>
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($gado->nome); ?>">
    </div>

    <div>
        <label for="nascimento">Data de Nascimento:</label>
        <input type="date" id="nascimento" name="nascimento" value="<?php echo htmlspecialchars($gado->nascimento); ?>">
    </div>

    <div>
        <label for="sexo">Sexo:</label>
        <select id="sexo" name="sexo" required>
            <option value="">Selecione</option>
            <option value="Macho" <?php echo ($gado->sexo == 'Macho') ? 'selected' : ''; ?>>Macho</option>
            <option value="Fêmea" <?php echo ($gado->sexo == 'Fêmea') ? 'selected' : ''; ?>>Fêmea</option>
        </select>
    </div>

    <div>
        <label for="raca">Raça:</label>
        <select id="raca" name="raca" required>
            <option value="">Selecione a Raça</option>
            <?php foreach ($racaOptions as $raca): ?>
                <option value="<?php echo htmlspecialchars($raca); ?>" <?php echo ($gado->raca == $raca) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($raca); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <label for="observacoes">Observações:</label>
        <textarea id="observacoes" name="observacoes" rows="4"><?php echo htmlspecialchars($gado->observacoes); ?></textarea>
    </div>

    <div>
        <label for="status">Status:</label>
        <select id="status" name="status">
            <option value="Vazia" <?php echo ($gado->status == 'Vazia') ? 'selected' : ''; ?>>Vazia</option>
            <option value="Inseminada" <?php echo ($gado->status == 'Inseminada') ? 'selected' : ''; ?>>Inseminada</option>
            <option value="Prenha" <?php echo ($gado->status == 'Prenha') ? 'selected' : ''; ?>>Prenha</option>
            <option value="Parida" <?php echo ($gado->status == 'Parida') ? 'selected' : ''; ?>>Parida</option>
            <option value="Descartada" <?php echo ($gado->status == 'Descartada') ? 'selected' : ''; ?>>Descartada</option>
        </select>
    </div>

    <div>
        <label for="grupo">Grupo:</label>
        <select id="grupo" name="grupo">
            <option value="Bezerra" <?php echo ($gado->grupo == 'Bezerra') ? 'selected' : ''; ?>>Bezerra</option>
            <option value="Novilha" <?php echo ($gado->grupo == 'Novilha') ? 'selected' : ''; ?>>Novilha</option>
            <option value="Lactante" <?php echo ($gado->grupo == 'Lactante') ? 'selected' : ''; ?>>Lactante</option>
            <option value="Seca" <?php echo ($gado->grupo == 'Seca') ? 'selected' : ''; ?>>Seca</option>
            <option value="Corte" <?php echo ($gado->grupo == 'Corte') ? 'selected' : ''; ?>>Corte</option>
        </select>
    </div>

    <div>
        <label for="bst">BST:</label>
        <select id="bst" name="bst">
            <option value="0" <?php echo ($gado->bst == 0) ? 'selected' : ''; ?>>Não</option>
            <option value="1" <?php echo ($gado->bst == 1) ? 'selected' : ''; ?>>Sim</option>
        </select>
    </div>

    <div>
        <input type="checkbox" id="ativo" name="ativo" value="1" <?php echo ($gado->ativo == 1) ? 'checked' : ''; ?>>
        <label for="ativo">Ativo</label>
    </div>

    <div>
        <label for="escore">Escore (1.0 - 5.0):</label>
        <input type="number" id="escore" name="escore" step="0.25" min="1.0" max="5.0" value="<?php echo htmlspecialchars($gado->escore); ?>">
    </div>

  <div class="form-grid-2col" style="align-items: end;">
        <div>
            <label for="leite_descarte">Descarte de Leite:</label>
            <select id="leite_descarte" name="leite_descarte">
                <option value="Não" <?php echo ($gado->leite_descarte == 'Não') ? 'selected' : ''; ?>>Não</option>
                <option value="Sim" <?php echo ($gado->leite_descarte == 'Sim') ? 'selected' : ''; ?>>Sim</option>
            </select>
        </div>
        <div>
            <label for="cor_bastao">Marcação Bastão:</label>
            <select id="cor_bastao" name="cor_bastao">
                <option value="" <?php echo ($gado->cor_bastao == '') ? 'selected' : ''; ?>>Nenhuma</option>
                <option value="Azul" <?php echo ($gado->cor_bastao == 'Azul') ? 'selected' : ''; ?>>Azul</option>
                <option value="Verde" <?php echo ($gado->cor_bastao == 'Verde') ? 'selected' : ''; ?>>Verde</option>
                <option value="Vermelho" <?php echo ($gado->cor_bastao == 'Vermelho') ? 'selected' : ''; ?>>Vermelho</option>
            </select>
        </div>
    </div>
    <div class="button-group">
        <button type="submit" class="btn btn-primary">Atualizar Animal</button>
        <a href="index.php" class="btn btn-secondary">Voltar</a>
    </div>
</form>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>