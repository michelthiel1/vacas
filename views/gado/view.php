<?php
include_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Gado.php';
require_once __DIR__ . '/../../models/Parto.php';
require_once __DIR__ . '/../../models/Inseminacao.php';
require_once __DIR__ . '/../../models/ProducaoLeite.php';
require_once __DIR__ . '/../../models/Pesagem.php';

$gado = new Gado($pdo);
$gado->id = $_GET['id'] ?? null;

if (!$gado->id || !$gado->readOne()) {
    $_SESSION['message'] = "Animal não encontrado.";
    header('Location: index.php');
    exit();
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

$partoModel = new Parto($pdo);
$inseminacaoModel = new Inseminacao($pdo);
$producaoLeiteModel = new ProducaoLeite($pdo);
$pesagemModel = new Pesagem($pdo);

$fotos_do_gado = [];
$query_fotos = "SELECT id, caminho_arquivo, legenda FROM gado_fotos WHERE id_gado = :id_gado ORDER BY foto_principal DESC, created_at DESC";
$stmt_fotos = $pdo->prepare($query_fotos);
$stmt_fotos->execute([':id_gado' => $gado->id]);
$fotos_do_gado = $stmt_fotos->fetchAll(PDO::FETCH_ASSOC);

$ultimo_parto_info = $partoModel->getUltimoParto($gado->id);
$total_partos = $partoModel->countByVacaId($gado->id);
$ultimas_producoes = $producaoLeiteModel->getLastTwoByGadoId($gado->id);
$ultima_pesagem = $pesagemModel->getLastByGadoId($gado->id);
$ultima_inseminacao = $inseminacaoModel->getLastInseminationDetails($gado->id);

function formatarData($data, $formato = 'd/m/Y') {
    if (empty($data) || $data === '0000-00-00' || $data === null) {
        return 'Não informado';
    }
    try {
        return (new DateTime($data))->format($formato);
    } catch (Exception $e) {
        return 'Data inválida';
    }
}

$hoje = new DateTime();

$dados_cadastrais = [
    'BRINCO' => htmlspecialchars($gado->brinco),
    'NASCIMENTO' => formatarData($gado->nascimento),
    'SEXO' => htmlspecialchars($gado->sexo),
    'PAI (TOURO)' => htmlspecialchars($gado->nome_pai_display ?: 'Não informado'),
    'MÃE' => htmlspecialchars($gado->brinco_mae_display ?: 'Não informado'),
    'GRUPO' => htmlspecialchars($gado->grupo),
    'STATUS REPRODUTIVO' => htmlspecialchars($gado->status),
    'ESCORE CORPORAL' => htmlspecialchars($gado->escore ?: 'Não informado'),
    'BST' => ($gado->bst == 1) ? 'Sim' : 'Não',
	 // --- INÍCIO DA MODIFICAÇÃO ---
    'DESCARTE DE LEITE' => htmlspecialchars($gado->leite_descarte),
    'MARCAÇÃO BASTÃO' => htmlspecialchars($gado->cor_bastao ?: 'Nenhuma'),
    // --- FIM DA MODIFICAÇÃO ---
];

$dados_inseminacao = [];
if ($ultima_inseminacao) {
    $dados_inseminacao['TOURO'] = htmlspecialchars($ultima_inseminacao['nome_touro'] ?: 'Não informado');
    $dados_inseminacao['DATA INSEMINAÇÃO'] = formatarData($ultima_inseminacao['data_inseminacao']);
    $data_insem = new DateTime($ultima_inseminacao['data_inseminacao']);
    $dados_inseminacao['DIAS DESDE A INSEMINAÇÃO'] = $data_insem->diff($hoje)->days . ' dias';
} else {
    $dados_inseminacao['TOURO'] = 'Nenhum registro';
    $dados_inseminacao['DATA INSEMINAÇÃO'] = 'Nenhum registro';
    $dados_inseminacao['DIAS DESDE A INSEMINAÇÃO'] = 'Nenhum registro';
}

$dados_prenhez = [];
$dias_prenhez = 'Não informado';
if ($gado->status == 'Prenha' && $ultima_inseminacao) {
    $data_insem_prenhez = new DateTime($ultima_inseminacao['data_inseminacao']);
    $dias_prenhez = $data_insem_prenhez->diff($hoje)->days . ' dias';
}
$dados_prenhez['DIAS DE PRENHEZ'] = $dias_prenhez;

$del = 'Não informado';
if (in_array($gado->status, ['Vazia', 'Inseminada', 'Prenha']) && $ultimo_parto_info) {
    $data_parto_del = new DateTime($ultimo_parto_info['data_parto']);
    $del = $data_parto_del->diff($hoje)->days . ' dias';
}
$dados_prenhez['DEL'] = $del;

$dados_prenhez['DATA DE SECAGEM'] = formatarData($gado->Data_secagem);
$dados_prenhez['DATA PRÉ-PARTO'] = formatarData($gado->Data_preparto);
$dados_prenhez['PREVISÃO DE PARTO'] = formatarData($gado->Data_parto);
$dados_prenhez['ÚLTIMO PARTO'] = formatarData($ultimo_parto_info['data_parto'] ?? null);
$dados_prenhez['TOTAL DE PARTOS'] = $total_partos;

$dados_producao = [
    'PRODUÇÃO 1' => isset($ultimas_producoes[0])
        ? number_format($ultimas_producoes[0]['producao_total'], 2, ',', '.') . ' L em ' . formatarData($ultimas_producoes[0]['data_producao'])
        : 'Nenhum registro',
    'PRODUÇÃO 2' => isset($ultimas_producoes[1])
        ? number_format($ultimas_producoes[1]['producao_total'], 2, ',', '.') . ' L em ' . formatarData($ultimas_producoes[1]['data_producao'])
        : 'Nenhum registro',
];

$dados_pesagem = [
    'ÚLTIMA PESAGEM' => $ultima_pesagem
        ? number_format($ultima_pesagem['peso'], 0, ',', '.') . ' kg em ' . formatarData($ultima_pesagem['data_pesagem'])
        : 'Nenhum registro',
];

$cioMonitorado = !empty($gado->data_monitoramento_cio);
?>

<h2 class="page-title">Detalhes do Animal</h2>

<?php if ($message): ?>
    <div class="alert alert-info" style="margin-bottom: 15px;"><?php echo $message; ?></div>
<?php endif; ?>

<div class="main-details-grid" style="display: grid; grid-template-columns: 3fr 1fr; gap: 20px; align-items: flex-start;">

    <div class="details-column" style="display: flex; flex-direction: column; gap: 15px;">
        
        <?php
        function renderizar_bloco($titulo, $dados) {
            echo '<div class="details-block" style="border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">';
            echo '<h4 style="background-color: var(--background-medium); color: var(--dark-orange); margin: 0; padding: 6px 10px; font-size: 0.9em; text-transform: uppercase;">' . $titulo . '</h4>';
            if (empty($dados)) {
                echo '<div style="padding: 10px; background-color: #fff;">Nenhum registro.</div>';
            } else {
                $rowIndex = 0;
                foreach ($dados as $rotulo => $valor) {
                    $bgColor = ($rowIndex % 2 == 0) ? '#fff' : 'var(--background-light)';
                    echo '<div style="display: flex; align-items: baseline; background-color: ' . $bgColor . '; padding: 4px 10px; border-bottom: 1px solid #eee;">';
                    echo '<span style="font-size: 8pt; color: var(--dark-orange); width: 110px; flex-shrink: 0; text-transform: uppercase; font-weight: 600;">' . $rotulo . '</span>';
                    echo '<span>' . ($valor ?: 'Não informado') . '</span>';
                    echo '</div>';
                    $rowIndex++;
                }
            }
            echo '</div>';
        }
        ?>

        <?php renderizar_bloco('Dados Cadastrais', $dados_cadastrais); ?>
        <?php renderizar_bloco('Última Inseminação', $dados_inseminacao); ?>
        <?php renderizar_bloco('Dados de Prenhez e Parto', $dados_prenhez); ?>
        <?php renderizar_bloco('Produção de Leite', $dados_producao); ?>
        <?php renderizar_bloco('Pesagem', $dados_pesagem); ?>
        
        <div class="details-block" style="border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden;">
            <h4 style="background-color: var(--background-medium); color: var(--dark-orange); margin: 0; padding: 6px 10px; font-size: 0.9em; text-transform: uppercase;">Observações</h4>
            <div style="padding: 10px; background-color: #fff;">
                <span style="font-size: 1.1em; color: var(--neutral-text); font-family: Arial, sans-serif;"><?php echo nl2br(htmlspecialchars($gado->observacoes ?: 'Nenhuma observação.')); ?></span>
            </div>
        </div>
    </div>

    <div class="action-buttons-column">
        
        <div class="button-group" style="margin-bottom: 15px;">
           <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="upload_foto.php?id_gado=<?php echo $gado->id; ?>" class="btn btn-primary" style="font-size: 0.9em; padding: 8px 12px;">Adicionar Foto</a>
            <?php else: ?>
                <span class="btn btn-primary disabled-link" style="font-size: 0.9em; padding: 8px 12px;">Adicionar Foto</span>
            <?php endif; ?>
        </div>

        <?php if (!empty($fotos_do_gado)): ?>
            <div class="photo-gallery">
                <?php foreach ($fotos_do_gado as $foto): ?>
                    <div class="photo-item" style="position: relative;">
                        <img class="lightbox-trigger" src="../../<?php echo htmlspecialchars($foto['caminho_arquivo']); ?>" alt="<?php echo htmlspecialchars($foto['legenda']); ?>">
                        
                     <?php if ($_SESSION['role'] === 'admin'): ?>
                        <form action="../../controllers/FotoController.php" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir esta foto?');" style="position: absolute; top: 5px; right: 5px; z-index: 10;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id_foto" value="<?php echo $foto['id']; ?>">
                            <input type="hidden" name="id_gado" value="<?php echo $gado->id; ?>">
                            <button type="submit" class="btn-delete-photo" title="Excluir Foto" style="background-color: rgba(0,0,0,0.5); border-radius: 50%; width: 25px; height: 25px; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-trash-alt" style="font-size: 0.8em;"></i>
                            </button>
                        </form>
                    <?php endif; ?>

                        <div class="photo-actions">
                            <p class="photo-caption"><?php echo htmlspecialchars($foto['legenda']); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center; margin-top: 10px; font-size: 0.9em; color: #777;">Nenhuma foto registrada.</p>
        <?php endif; ?>

      <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 20px;">
    
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="edit.php?id=<?php echo $gado->id; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">Editar Dados</a>
        <?php else: ?>
            <span class="btn btn-primary disabled-link" style="width: 100%; text-align: center;">Editar Dados</span>
        <?php endif; ?>

        <?php if (!in_array($gado->grupo, ['Bezerra', 'Corte'])): ?>
            <?php 
                $btn_class = $cioMonitorado ? 'btn-success' : 'btn-danger';
                $btn_text = $cioMonitorado ? 'Parar Monitoramento' : 'Monitorar CIO';
            ?>
            <button id="cio-monitoring-btn" class="btn <?php echo $btn_class; ?>" data-id-gado="<?php echo $gado->id; ?>" style="width: 100%; text-align: center;"><?php echo $btn_text; ?></button>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="../registros_manejo/index.php?search_query=<?php echo urlencode($gado->brinco); ?>" class="btn btn-secondary" style="width: 100%; text-align: center;">Saúde</a>
            <a href="../inseminacoes/index.php?search_query=<?php echo urlencode($gado->brinco); ?>" class="btn btn-secondary" style="width: 100%; text-align: center;">Inseminações</a>
            <a href="../partos/index.php?search_query=<?php echo urlencode($gado->brinco); ?>" class="btn btn-secondary" style="width: 100%; text-align: center;">Partos</a>
            <a href="../pesagens/index.php?search_query=<?php echo urlencode($gado->brinco); ?>" class="btn btn-secondary" style="width: 100%; text-align: center;">Pesagens</a>
        <?php else: ?>
            <span class="btn btn-secondary disabled-link" style="width: 100%; text-align: center;">Saúde</span>
            <span class="btn btn-secondary disabled-link" style="width: 100%; text-align: center;">Inseminações</span>
            <span class="btn btn-secondary disabled-link" style="width: 100%; text-align: center;">Partos</span>
            <span class="btn btn-secondary disabled-link" style="width: 100%; text-align: center;">Pesagens</span>
        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'admin'): ?>
            <button id="showGrowthChartBtn" class="btn btn-primary" data-id-gado="<?php echo $gado->id; ?>" style="width: 100%; text-align: center;">Curva de Crescimento</button>
        <?php else: ?>
            <button class="btn btn-primary disabled-link" style="width: 100%; text-align: center;" disabled>Crescimento</button>
        <?php endif; ?>
    
    </div>
    </div>
</div>

<div id="lightbox-overlay" class="lightbox-overlay">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
</div>

<div id="growthChartModal" class="modal">
    <div class="modal-content modal-content-wide">
        <div class="modal-header">
            <h3 id="chartModalTitle">Curva de Crescimento</h3>
            <span class="close-button">&times;</span>
        </div>
        <div class="modal-body">
            <canvas id="growthChartCanvas"></canvas>
        </div>
    </div>
</div>
<?php include_once __DIR__ . '/../../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.photo-item form').forEach(form => {
        form.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    const lightboxOverlay = document.getElementById('lightbox-overlay');
    const lightboxImg = document.getElementById('lightbox-img');
    const closeLightboxBtn = document.querySelector('.lightbox-close');
    const imageTriggers = document.querySelectorAll('.lightbox-trigger');

    function openLightbox(e) {
        if (lightboxOverlay && lightboxImg) {
            lightboxImg.src = e.target.src;
            lightboxOverlay.style.display = 'flex';
        }
    }

    function closeLightbox() {
        if (lightboxOverlay) {
            lightboxOverlay.style.display = 'none';
        }
    }

    imageTriggers.forEach(img => img.addEventListener('click', openLightbox));
    if (closeLightboxBtn) closeLightboxBtn.addEventListener('click', closeLightbox);
    if (lightboxOverlay) {
        lightboxOverlay.addEventListener('click', e => {
            if (e.target === lightboxOverlay) closeLightbox();
        });
    }

    const showChartBtn = document.getElementById('showGrowthChartBtn');
    const chartModal = document.getElementById('growthChartModal');
    if (chartModal) {
        const closeModalBtn = chartModal.querySelector('.close-button');
        const chartCanvas = document.getElementById('growthChartCanvas');
        const chartModalTitle = document.getElementById('chartModalTitle');
        let growthChart = null;

        if (showChartBtn) {
            showChartBtn.addEventListener('click', function() {
                const gadoId = this.dataset.idGado;
                chartModal.style.display = 'flex';

                fetch(`../../controllers/get_dados_grafico.php?id_gado=${gadoId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }
                        
                        chartModalTitle.innerText = `Curva de Crescimento - Brinco ${data.animalBrinco}`;

                        if (growthChart) {
                            growthChart.destroy();
                        }

                        growthChart = new Chart(chartCanvas, {
                            type: 'line',
                            data: {
                                labels: data.labels,
                                datasets: [
                                    {
                                        label: 'Peso Esperado (Raça Holandesa)',
                                        data: data.referenceData,
                                        borderColor: 'rgba(255, 99, 132, 0.5)',
                                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                                        borderWidth: 2,
                                        fill: false,
                                        pointRadius: 0,
                                        borderDash: [5, 5],
                                    },
                                    {
                                        label: `Peso Real (Brinco ${data.animalBrinco})`,
                                        data: data.animalData,
                                        borderColor: 'rgba(54, 162, 235, 1)',
                                        backgroundColor: 'rgba(54, 162, 235, 1)',
                                        borderWidth: 3,
                                        fill: false,
                                        showLine: true,
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                            }
                        });
                    })
                    .catch(error => console.error('Erro ao buscar dados do gráfico:', error));
            });
        }

        if(closeModalBtn) closeModalBtn.addEventListener('click', () => chartModal.style.display = 'none');
        window.addEventListener('click', (event) => {
            if (event.target == chartModal) chartModal.style.display = 'none';
        });
    }


    const cioBtn = document.getElementById('cio-monitoring-btn');
    if (cioBtn) {
        cioBtn.addEventListener('click', function() {
            const button = this; 
            const gadoId = button.dataset.idGado;
            
            const formData = new FormData();
            formData.append('action', 'toggle_cio_monitoring');
            formData.append('id', gadoId);

            button.disabled = true;
            button.textContent = 'Aguarde...';

            fetch('../../controllers/GadoController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_monitored) {
                        button.classList.remove('btn-danger');
                        button.classList.add('btn-success');
                        button.textContent = 'Parar Monitoramento';
                    } else {
                        button.classList.remove('btn-success');
                        button.classList.add('btn-danger');
                        button.textContent = 'Monitorar CIO';
                    }
                } else {
                    alert('Ocorreu um erro ao atualizar o monitoramento.');
                    // Reverte o texto para o estado anterior em caso de falha
                    button.textContent = button.classList.contains('btn-success') ? 'Parar Monitoramento' : 'Monitorar CIO';
                }
            })
            .catch(error => {
                console.error('Erro na requisição:', error);
                alert('Erro de comunicação. Tente novamente.');
            })
            .finally(() => {
                button.disabled = false;
            });
        });
    }
});
</script>