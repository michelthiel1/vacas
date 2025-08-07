<?php

class Gado {
    private $conn;
    private $table_name = "gado";

    public $id;
    public $brinco;
    public $nome;
    public $nascimento;
    public $sexo; 
    public $raca; 
    public $observacoes;
    public $status;
    public $grupo;
    public $bst; 
    public $leite_descarte;
    public $cor_bastao;
    public $ativo; 
    public $created_at; 
    public $updated_at; 
    public $escore; 
    public $id_pai; 
    public $id_mae; 

    public $Data_secagem;
    public $Data_preparto;
    public $Data_parto;
    public $data_monitoramento_cio;
	
    public $ultima_inseminacao_data; 
    public $nome_pai_display;       
    public $brinco_mae_display;     
    public $nome_mae_display;       

    public function __construct($db) {
        $this->conn = $db;
    }

// Adicione esta nova função pública dentro da classe Gado
    public function getTableName() {
        return $this->table_name;
    }
	
    public function getIdByBrinco($brinco) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE brinco = :brinco AND ativo = 1 LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':brinco', $brinco);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['id'] : null;
    }

    public function read($filters = []) {
        $query = "SELECT
                    g.id, g.brinco, g.nome, g.nascimento, g.raca, g.observacoes, g.status, g.grupo, g.bst, g.ativo, g.data_monitoramento_cio,
                    g.created_at, g.updated_at, g.escore, g.sexo, g.id_pai, g.id_mae,
                    MAX(i.data_inseminacao) AS ultima_inseminacao_data,
                    DATEDIFF(CURDATE(), MAX(i.data_inseminacao)) AS dias_ultima_inseminacao,
                    DATEDIFF(CURDATE(), g.data_monitoramento_cio) AS dias_monitoramento_cio
                  FROM " . $this->table_name . " g
                  LEFT JOIN inseminacoes i ON g.id = i.id_vaca AND i.ativo = 1";
        
        $where_clauses = [];
        $having_clauses = []; 
        $queryParams = [];

        $where_clauses[] = "g.ativo = 1";

        if (!empty($filters['search_query'])) {
            $where_clauses[] = "(g.brinco LIKE :search_query OR g.nome LIKE :search_query OR g.status LIKE :search_query)";
            $queryParams[':search_query'] = '%' . $filters['search_query'] . '%';
        }
        if (!empty($filters['grupo'])) {
            $grupoPlaceholders = [];
            foreach ($filters['grupo'] as $key => $grupo) {
                $placeholder = ":grupo_" . $key;
                $grupoPlaceholders[] = $placeholder;
                $queryParams[$placeholder] = $grupo;
            }
            $where_clauses[] = "g.grupo IN (" . implode(",", $grupoPlaceholders) . ")";
        }
        if (!empty($filters['status'])) {
            $statusPlaceholders = [];
            foreach ($filters['status'] as $key => $status) {
                $placeholder = ":status_" . $key;
                $statusPlaceholders[] = $placeholder;
                $queryParams[$placeholder] = $status;
            }
            $where_clauses[] = "g.status IN (" . implode(",", $statusPlaceholders) . ")";
        }
        if (isset($filters['bst_filter']) && $filters['bst_filter'] !== '') {
            $where_clauses[] = "g.bst = :bst_filter";
            $queryParams[':bst_filter'] = $filters['bst_filter'];
        }
        
        if (isset($filters['previsao_cio_ciclos']) && $filters['previsao_cio_ciclos']) {
            $where_clauses[] = "g.status IN ('Vazia', 'Inseminada')";
            $having_clauses[] = "(
                (g.data_monitoramento_cio IS NOT NULL)
                OR
                (dias_ultima_inseminacao IS NOT NULL AND dias_ultima_inseminacao >= 18 AND (MOD(dias_ultima_inseminacao, 21) >= 18 OR MOD(dias_ultima_inseminacao, 21) <= 3))
            )";
        }
        
        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }

        $query .= " GROUP BY g.id";

        if (isset($filters['idade_min']) && $filters['idade_min'] !== '') {
            $having_clauses[] = "TIMESTAMPDIFF(MONTH, g.nascimento, CURDATE()) >= :idade_min";
            $queryParams[':idade_min'] = (int)$filters['idade_min'];
        }
        if (isset($filters['idade_max']) && $filters['idade_max'] !== '') {
            $having_clauses[] = "TIMESTAMPDIFF(MONTH, g.nascimento, CURDATE()) <= :idade_max";
            $queryParams[':idade_max'] = (int)$filters['idade_max'];
        }
        
        if (isset($filters['inseminacao_min']) && $filters['inseminacao_min'] !== '') {
            $having_clauses[] = "dias_ultima_inseminacao >= :inseminacao_min";
            $queryParams[':inseminacao_min'] = (int)$filters['inseminacao_min'];
        }
        if (isset($filters['inseminacao_max']) && $filters['inseminacao_max'] !== '') {
            $having_clauses[] = "dias_ultima_inseminacao <= :inseminacao_max";
            $queryParams[':inseminacao_max'] = (int)$filters['inseminacao_max'];
        }

        if ((isset($filters['del_min']) && $filters['del_min'] !== '') || (isset($filters['del_max']) && $filters['del_max'] !== '')) {
            if (isset($filters['del_min']) && $filters['del_min'] !== '') {
                 $having_clauses[] = "DATEDIFF(CURDATE(), (SELECT MAX(p.data_parto) FROM partos p WHERE p.id_vaca = g.id)) >= :del_min";
                 $queryParams[':del_min'] = (int)$filters['del_min'];
            }
            if (isset($filters['del_max']) && $filters['del_max'] !== '') {
                 $having_clauses[] = "DATEDIFF(CURDATE(), (SELECT MAX(p.data_parto) FROM partos p WHERE p.id_vaca = g.id)) <= :del_max";
                 $queryParams[':del_max'] = (int)$filters['del_max'];
            }
        }
        
        if (!empty($having_clauses)) {
            $query .= " HAVING " . implode(" AND ", $having_clauses);
        }

        $query .= " ORDER BY g.brinco ASC";

        $stmt = $this->conn->prepare($query);
        foreach ($queryParams as $param => $value) {
            $stmt->bindValue($param, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        
        $stmt->execute();
        return $stmt;
    }

    public function create() {
       $query = "INSERT INTO " . $this->table_name . " SET brinco=:brinco, nome=:nome, nascimento=:nascimento, raca=:raca, sexo=:sexo, observacoes=:observacoes, status=:status, grupo=:grupo, bst=:bst, leite_descarte=:leite_descarte, cor_bastao=:cor_bastao, ativo=:ativo, created_at=CURRENT_TIMESTAMP, updated_at=CURRENT_TIMESTAMP, escore=:escore, id_pai=:id_pai, id_mae=:id_mae";
        $stmt = $this->conn->prepare($query);
        
        $this->brinco=htmlspecialchars(strip_tags($this->brinco));
        $this->nome=htmlspecialchars(strip_tags($this->nome));
        $this->nascimento=htmlspecialchars(strip_tags($this->nascimento));
        $this->raca=htmlspecialchars(strip_tags($this->raca));
        $this->sexo=htmlspecialchars(strip_tags($this->sexo));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $this->status=htmlspecialchars(strip_tags($this->status));
        $this->grupo=htmlspecialchars(strip_tags($this->grupo));
        $this->bst=htmlspecialchars(strip_tags($this->bst));
        $this->leite_descarte=htmlspecialchars(strip_tags($this->leite_descarte));
        $this->cor_bastao=htmlspecialchars(strip_tags($this->cor_bastao));
        $this->ativo=htmlspecialchars(strip_tags($this->ativo));
        $this->escore=htmlspecialchars(strip_tags($this->escore));
        $this->id_pai=($this->id_pai === null || $this->id_pai === '') ? null : htmlspecialchars(strip_tags($this->id_pai));
        $this->id_mae=($this->id_mae === null || $this->id_mae === '') ? null : htmlspecialchars(strip_tags($this->id_mae));
        
        $stmt->bindParam(":brinco", $this->brinco);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":nascimento", $this->nascimento);
        $stmt->bindParam(":raca", $this->raca);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":observacoes", $this->observacoes);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":grupo", $this->grupo);
        $stmt->bindParam(":bst", $this->bst);
        $stmt->bindParam(":leite_descarte", $this->leite_descarte);
        $stmt->bindParam(":cor_bastao", $this->cor_bastao);
        $stmt->bindParam(":ativo", $this->ativo);
        $stmt->bindParam(":escore", $this->escore);
        $stmt->bindParam(":id_pai", $this->id_pai, PDO::PARAM_INT);
        $stmt->bindParam(":id_mae", $this->id_mae, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function readOne() {
       $query = "SELECT 
                    g.id, g.brinco, g.nome, g.nascimento, g.raca, g.sexo, g.observacoes, 
                    g.status, g.grupo, g.bst, g.leite_descarte, g.cor_bastao, g.ativo, 
                    g.created_at, g.updated_at, g.escore, g.id_pai, g.id_mae, 
                    g.Data_secagem, g.Data_preparto, g.Data_parto, g.data_monitoramento_cio,
                    p.nome as nome_pai_display, 
                    m.brinco as brinco_mae_display, 
                    m.nome as nome_mae_display 
                  FROM " . $this->table_name . " g 
                  LEFT JOIN touros p ON g.id_pai = p.id 
                  LEFT JOIN gado m ON g.id_mae = m.id 
                  WHERE g.id = ? LIMIT 0,1";

		$stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            foreach ($row as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
            return true;
        }
        return false;
    }

    public function update() {
      $query = "UPDATE " . $this->table_name . " SET 
                    brinco=:brinco, nome=:nome, nascimento=:nascimento, 
                    Data_secagem=:data_secagem, Data_preparto=:data_preparto, Data_parto=:data_parto,
                    raca=:raca, sexo=:sexo, observacoes=:observacoes, 
                    status=:status, grupo=:grupo, bst=:bst, leite_descarte=:leite_descarte, cor_bastao=:cor_bastao, 
                    ativo=:ativo, escore=:escore, 
                    updated_at=CURRENT_TIMESTAMP, id_pai=:id_pai, id_mae=:id_mae 
                  WHERE id = :id";
                  
        $stmt = $this->conn->prepare($query);

        $this->brinco=htmlspecialchars(strip_tags($this->brinco));
        $this->nome=htmlspecialchars(strip_tags($this->nome));
        $this->nascimento=htmlspecialchars(strip_tags($this->nascimento));
        $this->raca=htmlspecialchars(strip_tags($this->raca));
        $this->sexo=htmlspecialchars(strip_tags($this->sexo));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $this->status=htmlspecialchars(strip_tags($this->status));
        $this->grupo=htmlspecialchars(strip_tags($this->grupo));
        $this->bst=htmlspecialchars(strip_tags($this->bst));
        $this->leite_descarte=htmlspecialchars(strip_tags($this->leite_descarte));
        $this->cor_bastao=htmlspecialchars(strip_tags($this->cor_bastao));
        $this->ativo=htmlspecialchars(strip_tags($this->ativo));
        $this->escore=htmlspecialchars(strip_tags($this->escore));
        $this->id_pai = (empty($this->id_pai) && $this->id_pai !== 0) ? null : htmlspecialchars(strip_tags($this->id_pai));
        $this->id_mae = (empty($this->id_mae) && $this->id_mae !== 0) ? null : htmlspecialchars(strip_tags($this->id_mae));
        $this->id=htmlspecialchars(strip_tags($this->id));
        
        $this->Data_secagem = $this->Data_secagem ?? null;
        $this->Data_preparto = $this->Data_preparto ?? null;
        $this->Data_parto = $this->Data_parto ?? null;

        $stmt->bindParam(":brinco", $this->brinco);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":nascimento", $this->nascimento);
        $stmt->bindParam(":data_secagem", $this->Data_secagem);
        $stmt->bindParam(":data_preparto", $this->Data_preparto);
        $stmt->bindParam(":data_parto", $this->Data_parto);
        $stmt->bindParam(":raca", $this->raca);
        $stmt->bindParam(":sexo", $this->sexo);
        $stmt->bindParam(":observacoes", $this->observacoes);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":grupo", $this->grupo);
        $stmt->bindParam(":bst", $this->bst);
        $stmt->bindParam(":leite_descarte", $this->leite_descarte);
        $stmt->bindParam(":cor_bastao", $this->cor_bastao);
        $stmt->bindParam(":ativo", $this->ativo);
        $stmt->bindParam(":escore", $this->escore);
        $stmt->bindParam(":id_pai", $this->id_pai, PDO::PARAM_INT);
        $stmt->bindParam(":id_mae", $this->id_mae, PDO::PARAM_INT);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }
    
    public function toggleCioMonitoring($id_gado) {
        $this->id = $id_gado;
        if (!$this->readOne()) {
            return false;
        }
    
        if ($this->data_monitoramento_cio === null) {
            $query_update = "UPDATE " . $this->table_name . " SET data_monitoramento_cio = CURDATE() WHERE id = :id_gado";
            $stmt_update = $this->conn->prepare($query_update);
            return $stmt_update->execute([':id_gado' => $id_gado]);
        } else {
            $query_update = "UPDATE " . $this->table_name . " SET data_monitoramento_cio = NULL WHERE id = :id_gado";
            $stmt_update = $this->conn->prepare($query_update);
            return $stmt_update->execute([':id_gado' => $id_gado]);
        }
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }

    public function readBrincos() {
        $query = "SELECT id, brinco, nome FROM " . $this->table_name . " WHERE ativo = 1 ORDER BY brinco ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function updateStatusAndGroup($id_vaca, $new_status, $new_group) {
        $query = "UPDATE " . $this->table_name . " SET status = :new_status, grupo = :new_group, updated_at = CURRENT_TIMESTAMP WHERE id = :id_vaca";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':new_status', $new_status);
        $stmt->bindParam(':new_group', $new_group);
        $stmt->bindParam(':id_vaca', $id_vaca, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    public function getContagemPrevisaoCio() {
        $query = "
            SELECT COUNT(g.id) as total_cio
            FROM gado g
            LEFT JOIN (
                SELECT id_vaca, MAX(data_inseminacao) as data_inseminacao
                FROM inseminacoes
                WHERE ativo = 1
                GROUP BY id_vaca
            ) i ON g.id = i.id_vaca
            WHERE g.ativo = 1 AND g.status IN ('Vazia', 'Inseminada')
            AND (
                (
                    g.data_monitoramento_cio IS NOT NULL
                )
                OR
                (
                    g.data_monitoramento_cio IS NULL AND
                    i.data_inseminacao IS NOT NULL AND
                    DATEDIFF(CURDATE(), i.data_inseminacao) >= 18 AND
                    (MOD(DATEDIFF(CURDATE(), i.data_inseminacao), 21) >= 18 OR MOD(DATEDIFF(CURDATE(), i.data_inseminacao), 21) <= 3)
                )
            );
        ";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total_cio'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erro em getContagemPrevisaoCio: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getContagemToquePendente() {
        $query = "
            SELECT COUNT(*) as total_toque FROM (
                SELECT
                    g.id,
                    DATEDIFF(CURDATE(), MAX(i.data_inseminacao)) AS dias_desde_inseminacao
                FROM " . $this->table_name . " g
                INNER JOIN inseminacoes i ON g.id = i.id_vaca
                WHERE 
                    g.status = 'Inseminada' 
                    AND g.ativo = 1 
                    AND i.ativo = 1
                GROUP BY g.id
                HAVING 
                    dias_desde_inseminacao >= 30
            ) AS subquery;
        ";
        try {
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total_toque'] ?? 0;
        } catch (PDOException $e) {
            error_log("Erro em getContagemToquePendente: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getContagemStatusParaGrafico($filtros = []) {
        $query = "SELECT g.status, COUNT(g.id) as total 
                  FROM " . $this->table_name . " g 
                  WHERE g.ativo = 1";
        
        $params = [];

        if (!empty($filtros['search_query'])) {
            $query .= " AND (g.brinco LIKE :search_query OR g.nome LIKE :search_query)";
            $params[':search_query'] = '%' . $filtros['search_query'] . '%';
        }
        if (!empty($filtros['grupo'])) {
            $grupoPlaceholders = [];
            foreach ($filtros['grupo'] as $key => $grupo) {
                $placeholder = ":grupo_" . $key;
                $grupoPlaceholders[] = $placeholder;
                $params[$placeholder] = $grupo;
            }
            $query .= " AND g.grupo IN (" . implode(",", $grupoPlaceholders) . ")";
        }
        
        $query .= " GROUP BY g.status";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        $resultadoFormatado = [];
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($resultados as $row) {
            $resultadoFormatado[] = [
                'label' => $row['status'],
                'value' => (int)$row['total']
            ];
        }
        return $resultadoFormatado;
    }

    public function getContagemGrupoParaGrafico($filtros = []) {
        $query = "SELECT grupo, COUNT(*) as total FROM " . $this->table_name . " WHERE ativo = 1 GROUP BY grupo";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $resultadoFormatado = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultadoFormatado[] = [
                'label' => $row['grupo'],
                'value' => (int)$row['total']
            ];
        }
        return $resultadoFormatado;
    }

    public function getAnimaisPorStatus($status, $filtros = []) {
        $query = "SELECT id, brinco FROM " . $this->table_name . " WHERE status = :status AND ativo = 1 ORDER BY brinco ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAnimaisPorGrupo($grupo, $filtros = []) {
        $query = "SELECT id, brinco FROM " . $this->table_name . " WHERE grupo = :grupo AND ativo = 1 ORDER BY brinco ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':grupo', $grupo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getContagemPorEscore($filtros = []) {
        $query = "
            SELECT
                CASE
                    WHEN escore BETWEEN 2.00 AND 3.00 THEN '2.0 a 3.0'
                    WHEN escore BETWEEN 3.01 AND 3.50 THEN '3.1 a 3.5'
                    WHEN escore BETWEEN 3.51 AND 4.00 THEN '3.6 a 4.0'
                    WHEN escore BETWEEN 4.01 AND 5.00 THEN '4.1 a 5.0'
                    ELSE 'Não Classificado'
                END as faixa_escore,
                COUNT(*) as total
            FROM " . $this->table_name . "
            WHERE ativo = 1 AND escore > 0
            GROUP BY faixa_escore
            ORDER BY faixa_escore
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $resultadoFormatado = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultadoFormatado[] = [
                'label' => $row['faixa_escore'],
                'value' => (int)$row['total']
            ];
        }
        return $resultadoFormatado;
    }

    public function getAnimaisPorEscore($faixa, $filtros = []) {
        $query = "SELECT id, brinco FROM " . $this->table_name . " WHERE ativo = 1 AND escore > 0";

        switch ($faixa) {
            case '2.0 a 3.0': $query .= " AND escore BETWEEN 2.00 AND 3.00"; break;
            case '3.1 a 3.5': $query .= " AND escore BETWEEN 3.01 AND 3.50"; break;
            case '3.6 a 4.0': $query .= " AND escore BETWEEN 3.51 AND 4.00"; break;
            case '4.1 a 5.0': $query .= " AND escore BETWEEN 4.01 AND 5.00"; break;
            default: $query .= " AND (escore IS NULL OR escore = 0)"; break;
        }
        $query .= " ORDER BY brinco ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
    public function getAnimaisPorOrdemDeParto($ordemPartoLabel, $filtros = []) {
        $ordem = (int)filter_var($ordemPartoLabel, FILTER_SANITIZE_NUMBER_INT);

        if ($ordem <= 0) {
            return [];
        }

        $query = "
            SELECT g.id, g.brinco
            FROM gado g
            JOIN (
                SELECT id_vaca, COUNT(id) as total_partos
                FROM partos
                GROUP BY id_vaca
            ) as partos_contagem ON g.id = partos_contagem.id_vaca
            WHERE g.ativo = 1 AND partos_contagem.total_partos = :ordem
            ORDER BY g.brinco ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':ordem', $ordem, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
    public function getIntervaloEntrePartos($filtros = []) {
        $query_real = "
            SELECT p1.id_vaca, DATEDIFF(MIN(p2.data_parto), p1.data_parto) as iep
            FROM partos p1
            JOIN partos p2 ON p1.id_vaca = p2.id_vaca AND p2.data_parto > p1.data_parto
            WHERE p1.id IN (SELECT MIN(id) FROM partos GROUP BY id_vaca HAVING COUNT(id) > 1)
            GROUP BY p1.id_vaca, p1.data_parto
        ";
        $stmt_real = $this->conn->prepare($query_real);
        $stmt_real->execute();
        $ieps_reais = $stmt_real->fetchAll(PDO::FETCH_COLUMN, 1);

        $query_projetado = "
            SELECT DATEDIFF(g.Data_parto, p_ultimo.data_parto) as iep_proj
            FROM gado g
            JOIN (
                SELECT id_vaca, MAX(data_parto) as data_parto
                FROM partos
                GROUP BY id_vaca
            ) as p_ultimo ON g.id = p_ultimo.id_vaca
            WHERE g.status = 'Prenha' AND g.ativo = 1 AND g.Data_parto IS NOT NULL
        ";
        $stmt_projetado = $this->conn->prepare($query_projetado);
        $stmt_projetado->execute();
        $ieps_projetados = $stmt_projetado->fetchAll(PDO::FETCH_COLUMN, 0);

        $todos_ieps = array_merge($ieps_reais, $ieps_projetados);
        if (empty($todos_ieps)) {
            return 0;
        }
        return array_sum($todos_ieps) / count($todos_ieps);
    }

    public function getIntervaloPartoConcepcao($filtros = []) {
        $query = "
            SELECT AVG(DATEDIFF(i.data_inseminacao, p.data_parto)) as avg_intervalo
            FROM inseminacoes i
            JOIN (
                SELECT id_vaca, data_parto
                FROM (
                    SELECT id_vaca, data_parto, ROW_NUMBER() OVER(PARTITION BY id_vaca ORDER BY data_parto DESC) as rn
                    FROM partos
                ) as sub_partos
                WHERE rn = 1
            ) p ON i.id_vaca = p.id_vaca AND i.data_inseminacao > p.data_parto
            WHERE i.status_inseminacao = 'Confirmada (Prenha)'
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['avg_intervalo'] ?? 0;
    }
	// ### INÍCIO DO NOVO MÉTODO PARA GRÁFICO DE LACTAÇÃO ###
    /**
     * Busca todas as datas de parto, tanto as já ocorridas quanto as previstas.
     * @return array Uma lista de datas no formato Y-m-d.
     */
    public function getAllCalvingDates() {
        // Busca partos históricos da tabela 'partos'
        $query_partos = "SELECT data_parto FROM partos WHERE ativo = 1";
        $stmt_partos = $this->conn->prepare($query_partos);
        $stmt_partos->execute();
        $partos_reais = $stmt_partos->fetchAll(PDO::FETCH_COLUMN, 0);

        // Busca previsões de parto da tabela 'gado' para vacas prenhas
        $query_previsoes = "SELECT Data_parto FROM " . $this->table_name . " WHERE ativo = 1 AND status = 'Prenha' AND Data_parto IS NOT NULL";
        $stmt_previsoes = $this->conn->prepare($query_previsoes);
        $stmt_previsoes->execute();
        $partos_previstos = $stmt_previsoes->fetchAll(PDO::FETCH_COLUMN, 0);

        // Combina os dois arrays e retorna
        return array_merge($partos_reais, $partos_previstos);
    }
    // ### FIM DO NOVO MÉTODO ###
	
	// ### INÍCIO DO NOVO MÉTODO - ESSENCIAL PARA O GRÁFICO ###
    /**
     * Retorna a contagem total de animais para um grupo específico.
     * @param string $grupoNome O nome do grupo (ex: 'Lactante').
     * @return int A contagem de animais.
     */
    public function getContagemPorGrupoEspecifico($grupoNome) {
        $query = "SELECT COUNT(id) as total FROM " . $this->table_name . " WHERE grupo = :grupo AND ativo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':grupo', $grupoNome);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }
    // ### FIM DO NOVO MÉTODO ###
	
 
    /**
     * Pega todas as previsões de parto futuras, agrupadas por mês.
     * ESSENCIAL PARA O GRÁFICO DE LACTAÇÃO.
     * @return array Um array associativo (ex: ['2025-08' => 2, '2025-09' => 1]).
     */
    public function getContagemDePrevisoesPorMes() {
        $query = "SELECT DATE_FORMAT(Data_parto, '%Y-%m') as mes, COUNT(id) as total
                  FROM " . $this->table_name . "
                  WHERE ativo = 1 AND status = 'Prenha' AND Data_parto IS NOT NULL
                  GROUP BY mes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
	
	
}
?>