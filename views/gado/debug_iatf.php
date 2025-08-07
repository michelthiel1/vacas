<?php
// Silencia erros de header, caso existam.
ob_start();

echo "<pre>"; // Formata a saída para ser mais legível

// Inclui a conexão com o banco de dados
require_once __DIR__ . '/../../config/database.php';

// --- IMPORTANTE: EDITE A LINHA ABAIXO ---
// Coloque aqui o NÚMERO DO BRINCO de uma vaca que você SABE
// que teve uma IATF registrada nos últimos 10 dias.
$brinco_para_testar = '299'; // <--- TROQUE ESTE VALOR

echo "--- DIAGNÓSTICO PARA O BRINCO: " . htmlspecialchars($brinco_para_testar) . " ---\n\n";

if (!$pdo) {
    die("!!! ERRO: Não foi possível conectar ao banco de dados. Verifique o arquivo config/database.php");
}

// Esta é a consulta que está sendo usada no index.php
$query = "
    WITH LatestInsemination AS (
        SELECT
            id_vaca,
            data_inseminacao,
            tipo,
            ROW_NUMBER() OVER(PARTITION BY id_vaca ORDER BY data_inseminacao DESC, id DESC) as rn
        FROM
            inseminacoes
        WHERE
            ativo = 1
    )
    SELECT
        g.id, g.brinco, g.nome,
        li.data_inseminacao AS ultima_inseminacao_data,
        li.tipo AS ultima_inseminacao_tipo,
        DATEDIFF(CURDATE(), li.data_inseminacao) AS dias_ultima_inseminacao
    FROM
        gado g
    LEFT JOIN
        LatestInsemination li ON g.id = li.id_vaca AND li.rn = 1
    WHERE
        g.brinco = :brinco
";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute([':brinco' => $brinco_para_testar]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "--- Resultado da Consulta Principal ---\n";
    if ($result) {
        var_dump($result);
    } else {
        echo "Nenhum resultado encontrado para este brinco.\n";
    }

    // Diagnóstico adicional: vamos ver TODAS as inseminações desta vaca
    $id_vaca = $result['id'] ?? null;
    if ($id_vaca) {
        echo "\n\n--- Verificando TODAS as inseminações para o Gado ID: $id_vaca ---\n";
        $inseminacoes_query = "SELECT * FROM inseminacoes WHERE id_vaca = :id_vaca ORDER BY data_inseminacao DESC";
        $inseminacoes_stmt = $pdo->prepare($inseminacoes_query);
        $inseminacoes_stmt->execute([':id_vaca' => $id_vaca]);
        $todas_inseminacoes = $inseminacoes_stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($todas_inseminacoes) {
            var_dump($todas_inseminacoes);
        } else {
            echo "Nenhuma inseminação encontrada para esta vaca no banco de dados.\n";
        }
    }

} catch (PDOException $e) {
    echo "!!! ERRO NA EXECUÇÃO DA CONSULTA: " . $e->getMessage();
}

echo "</pre>";