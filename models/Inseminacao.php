<?php

class Inseminacao {
    private $conn;
    private $table_name = "inseminacoes";

    public $id;
    public $tipo;
    public $id_vaca;
    public $id_inseminador;
    public $id_touro;
    public $data_inseminacao;
    public $observacoes;
    public $status_inseminacao;
    public $ativo;
    public $created_at;
    public $updated_at;

    // Adicionados para JOINs e exibição
    public $brinco_vaca_display;
    public $nome_vaca_display;
    public $nome_inseminador_display;
    public $nome_touro_display;
    public $grupo_vaca_display;

    public function __construct($db) {
        $this->conn = $db;
    }


// models/Inseminacao.php


 // ### NOVO MÉTODO ###
    // Pega os detalhes da última inseminação (data e nome do touro)
    public function getLastInseminationDetails($id_vaca) {
        $query = "SELECT
                    i.data_inseminacao,
                    t.nome as nome_touro
                  FROM
                    " . $this->table_name . " i
                  LEFT JOIN touros t ON i.id_touro = t.id
                  WHERE i.id_vaca = :id_vaca AND i.ativo = 1
                  ORDER BY i.data_inseminacao DESC 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vaca', $id_vaca, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
	
	
public function getLastInseminationInfo($vaca_id) {
    // Query que busca o ID e o Nome do touro da última inseminação da vaca
    $query = "SELECT 
                i.touro_id, 
                t.nome as touro_nome 
              FROM 
                inseminacoes i
              JOIN 
                touros t ON i.touro_id = t.id
              WHERE 
                i.vaca_id = :vaca_id 
              ORDER BY 
                i.data_inseminacao DESC 
              LIMIT 1";
    
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':vaca_id', $vaca_id, PDO::PARAM_INT);
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}



 public function read($searchQuery = '') { 
        $filters = [];
        $queryParams = [];

        $query = "SELECT
                    i.id, i.tipo, i.id_vaca, i.id_inseminador, i.id_touro, i.data_inseminacao,
                    i.observacoes, i.status_inseminacao, i.ativo,
                    i.created_at, i.updated_at,
                    g.brinco as brinco_vaca_display, g.nome as nome_vaca_display, g.grupo as grupo_vaca_display,
                    ins.nome as nome_inseminador_display,
                    t.nome as nome_touro_display
                  FROM
                    " . $this->table_name . " i
                  LEFT JOIN gado g ON i.id_vaca = g.id
                  LEFT JOIN inseminadores ins ON i.id_inseminador = ins.id
                  LEFT JOIN touros t ON i.id_touro = t.id";

        $filters[] = "g.ativo = 1";

        if (!empty($searchQuery)) {
            $filters[] = "(g.brinco LIKE :search_query OR g.nome LIKE :search_query OR t.nome LIKE :search_query OR ins.nome LIKE :search_query OR g.grupo LIKE :search_query OR i.status_inseminacao LIKE :search_query OR i.tipo LIKE :search_query)";
            $queryParams[':search_query'] = '%' . $searchQuery . '%'; 
        }

        if (!empty($filters)) {
            $query .= " WHERE " . implode(" AND ", $filters);
        }

        $query .= " ORDER BY i.data_inseminacao DESC, i.id DESC";

        $stmt = $this->conn->prepare($query);
        foreach ($queryParams as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO
                    " . $this->table_name . "
                  SET
                    tipo=:tipo, id_vaca=:id_vaca, id_inseminador=:id_inseminador, id_touro=:id_touro,
                    data_inseminacao=:data_inseminacao,
                    observacoes=:observacoes, status_inseminacao=:status_inseminacao,
                    ativo=:ativo";

        $stmt = $this->conn->prepare($query);

        $this->tipo=htmlspecialchars(strip_tags($this->tipo));
        $this->id_vaca=htmlspecialchars(strip_tags($this->id_vaca));
        $this->id_inseminador=htmlspecialchars(strip_tags($this->id_inseminador));
        $this->id_touro=htmlspecialchars(strip_tags($this->id_touro));
        $this->data_inseminacao=htmlspecialchars(strip_tags($this->data_inseminacao));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $this->status_inseminacao=htmlspecialchars(strip_tags($this->status_inseminacao));
        $this->ativo=htmlspecialchars(strip_tags($this->ativo));

        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":id_vaca", $this->id_vaca);
        $stmt->bindParam(":id_inseminador", $this->id_inseminador);
        $stmt->bindParam(":id_touro", $this->id_touro);
        $stmt->bindParam(":data_inseminacao", $this->data_inseminacao);
        $stmt->bindParam(":observacoes", $this->observacoes);
        $stmt->bindParam(":status_inseminacao", $this->status_inseminacao);
        $stmt->bindParam(":ativo", $this->ativo);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function readOne() {
        $query = "SELECT
                    i.id, i.tipo, i.id_vaca, i.id_inseminador, i.id_touro, i.data_inseminacao,
                    i.observacoes, i.status_inseminacao, i.ativo,
                    i.created_at, i.updated_at,
                    g.brinco as brinco_vaca_display, g.nome as nome_vaca_display,
                    ins.nome as nome_inseminador_display,
                    t.nome as nome_touro_display
                  FROM
                    " . $this->table_name . " i
                  LEFT JOIN gado g ON i.id_vaca = g.id
                  LEFT JOIN inseminadores ins ON i.id_inseminador = ins.id
                  LEFT JOIN touros t ON i.id_touro = t.id
                  WHERE
                    i.id = ?
                  LIMIT
                    0,1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->tipo = $row['tipo'];
            $this->id_vaca = $row['id_vaca'];
            $this->id_inseminador = $row['id_inseminador'];
            $this->id_touro = $row['id_touro'];
            $this->data_inseminacao = $row['data_inseminacao'];
            $this->observacoes = $row['observacoes'];
            $this->status_inseminacao = $row['status_inseminacao'];
            $this->ativo = $row['ativo'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->brinco_vaca_display = $row['brinco_vaca_display'];
            $this->nome_vaca_display = $row['nome_vaca_display'];
            $this->nome_inseminador_display = $row['nome_inseminador_display'];
            $this->nome_touro_display = $row['nome_touro_display'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE
                    " . $this->table_name . "
                  SET
                    tipo=:tipo, id_vaca=:id_vaca, id_inseminador=:id_inseminador, id_touro=:id_touro,
                    data_inseminacao=:data_inseminacao,
                    observacoes=:observacoes, status_inseminacao=:status_inseminacao,
                    updated_at=CURRENT_TIMESTAMP
                  WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->tipo=htmlspecialchars(strip_tags($this->tipo));
        $this->id_vaca=htmlspecialchars(strip_tags($this->id_vaca));
        $this->id_inseminador=htmlspecialchars(strip_tags($this->id_inseminador));
        $this->id_touro=htmlspecialchars(strip_tags($this->id_touro));
        $this->data_inseminacao=htmlspecialchars(strip_tags($this->data_inseminacao));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $this->status_inseminacao=htmlspecialchars(strip_tags($this->status_inseminacao));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":id_vaca", $this->id_vaca);
        $stmt->bindParam(":id_inseminador", $this->id_inseminador);
        $stmt->bindParam(":id_touro", $this->id_touro);
        $stmt->bindParam(":data_inseminacao", $this->data_inseminacao);
        $stmt->bindParam(":observacoes", $this->observacoes);
        $stmt->bindParam(":status_inseminacao", $this->status_inseminacao);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // Método para buscar a última data de inseminação para um ID de vaca específico
    public function getUltimaInseminacao($id_vaca) {
        $query = "SELECT MAX(data_inseminacao) as data_inseminacao
                  FROM " . $this->table_name . "
                  WHERE id_vaca = :id_vaca AND ativo = 1 AND tipo IN ('IATF', 'Cio')"; // CORREÇÃO AQUI: tipo IN ('IATF', 'Cio')

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vaca', $id_vaca);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row && !empty($row['data_inseminacao']) ? $row : null;
    }
	
	public function confirmarUltimaInseminacao($id_vaca) {
        // Encontra o ID da última inseminação ativa (IATF ou Cio) para a vaca
        $query_select = "SELECT id FROM " . $this->table_name . " 
                         WHERE id_vaca = :id_vaca AND ativo = 1 AND tipo IN ('IATF', 'Cio')
                         ORDER BY data_inseminacao DESC LIMIT 1";
        
        $stmt_select = $this->conn->prepare($query_select);
        $stmt_select->bindParam(':id_vaca', $id_vaca, PDO::PARAM_INT);
        $stmt_select->execute();
        $row = $stmt_select->fetch(PDO::FETCH_ASSOC);

        if ($row && !empty($row['id'])) {
            $id_inseminacao_para_confirmar = $row['id'];
            
            // Atualiza o status dessa inseminação para 'Confirmada (Prenha)'
            $query_update = "UPDATE " . $this->table_name . " 
                             SET status_inseminacao = 'Confirmada (Prenha)' 
                             WHERE id = :id_inseminacao";
                             
            $stmt_update = $this->conn->prepare($query_update);
            $stmt_update->bindParam(':id_inseminacao', $id_inseminacao_para_confirmar, PDO::PARAM_INT);
            
            return $stmt_update->execute();
        }
        
        // Se não encontrou inseminação para confirmar, retorna falso
        return false;
    }
	// ### INÍCIO DA NOVA FUNÇÃO PARA GRÁFICOS ###
    /**
     * Retorna a contagem de inseminações e gestações confirmadas por mês.
     * @param array $filtros Filtros a serem aplicados.
     * @return array
     */
/**
     * Retorna a contagem de inseminações e gestações confirmadas por mês.
     * @param array $filtros Filtros a serem aplicados.
     * @return array
     */
    public function getEficienciaMensal($filtros = []) {
        // ### INÍCIO DA MODIFICAÇÃO ###
        // A consulta agora inclui uma cláusula WHERE para filtrar os últimos 6 meses.
        $query = "
            SELECT
                DATE_FORMAT(data_inseminacao, '%Y-%m') as mes,
                COUNT(id) as total_inseminacoes,
                SUM(CASE WHEN status_inseminacao = 'Confirmada (Prenha)' THEN 1 ELSE 0 END) as total_confirmadas
            FROM " . $this->table_name . "
            WHERE 
                ativo = 1 AND
                data_inseminacao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            -- Lógica de filtros globais pode ser adicionada aqui no futuro
            GROUP BY mes
            ORDER BY mes ASC;
        ";
        // ### FIM DA MODIFICAÇÃO ###
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // ### FIM DA NOVA FUNÇÃO ###
	
	/**
     * Retorna um ranking de vacas com mais inseminações que não resultaram em prenhez confirmada.
     * @param array $filtros Filtros a serem aplicados.
     * @return array
     */
   /**
     * Retorna um ranking de vacas com mais inseminações que não resultaram em prenhez confirmada.
     * @param array $filtros Filtros a serem aplicados.
     * @return array
     */
/**
     * Retorna um ranking de vacas com mais inseminações que não resultaram em prenhez confirmada.
     * @param array $filtros Filtros a serem aplicados.
     * @return array
     */
    public function getFalhasInseminacaoPorVaca($filtros = []) {
        $query = "
            SELECT 
                g.brinco as label,
                COUNT(i.id) as value
            FROM inseminacoes i
            JOIN gado g ON i.id_vaca = g.id
            LEFT JOIN (
                SELECT id_vaca, MAX(data_parto) as ultimo_parto_data
                FROM partos
                GROUP BY id_vaca
            ) as ult_parto ON g.id = ult_parto.id_vaca
            WHERE 
                i.status_inseminacao IN ('Falha', 'Aguardando Diagnostico')
                AND g.status != 'Prenha'
                AND g.ativo = 1
                AND (ult_parto.ultimo_parto_data IS NULL OR i.data_inseminacao > ult_parto.ultimo_parto_data)
            GROUP BY g.id, g.brinco
            HAVING value > 1
            ORDER BY value DESC
            LIMIT 10;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>