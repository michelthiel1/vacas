<?php

class Parto {
    private $conn;
    private $table_name = "partos";

    public $id;
    public $id_vaca;
    public $id_touro;
    public $sexo_cria;
    public $data_parto;
    public $observacoes;
    public $ativo;
    public $created_at;
    public $updated_at;

    // Propriedades para exibição via JOIN
    public $brinco_vaca_display;
    public $nome_vaca_display;
    public $nome_touro_display;

    public function __construct($db) {
        $this->conn = $db;
    }

 public function read($searchQuery = '') {
        $filters = [];
        $queryParams = [];

        $query = "SELECT
                    p.id, p.id_vaca, p.id_touro, p.sexo_cria, p.data_parto, p.observacoes, p.ativo,
                    p.created_at, p.updated_at,
                    g.brinco as brinco_vaca_display, g.nome as nome_vaca_display,
                    t.nome as nome_touro_display
                  FROM
                    " . $this->table_name . " p
                  LEFT JOIN gado g ON p.id_vaca = g.id
                  LEFT JOIN touros t ON p.id_touro = t.id";

        $filters[] = "g.ativo = 1"; 
        
        if (!empty($searchQuery)) {
            $filters[] = "(g.brinco LIKE :search_query OR g.nome LIKE :search_query OR t.nome LIKE :search_query OR p.sexo_cria LIKE :search_query)";
            $queryParams[':search_query'] = '%' . $searchQuery . '%';
        }

        if (!empty($filters)) {
            $query .= " WHERE " . implode(" AND ", $filters);
        }

        $query .= " ORDER BY p.data_parto DESC, p.id DESC";

        $stmt = $this->conn->prepare($query);
        foreach ($queryParams as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();
        return $stmt;
    }
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    id_vaca=:id_vaca, id_touro=:id_touro, sexo_cria=:sexo_cria,
                    data_parto=:data_parto, observacoes=:observacoes, ativo=:ativo,
                    created_at=CURRENT_TIMESTAMP, updated_at=CURRENT_TIMESTAMP";
        
        $stmt = $this->conn->prepare($query);

        $this->id_vaca=htmlspecialchars(strip_tags($this->id_vaca));
        $this->id_touro=htmlspecialchars(strip_tags($this->id_touro)); // Pode ser null
        $this->sexo_cria=htmlspecialchars(strip_tags($this->sexo_cria));
        $this->data_parto=htmlspecialchars(strip_tags($this->data_parto));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $this->ativo=1; // Sempre ativo por padrão no cadastro

        $stmt->bindParam(":id_vaca", $this->id_vaca);
        $stmt->bindParam(":id_touro", $this->id_touro);
        $stmt->bindParam(":sexo_cria", $this->sexo_cria);
        $stmt->bindParam(":data_parto", $this->data_parto);
        $stmt->bindParam(":observacoes", $this->observacoes);
        $stmt->bindParam(":ativo", $this->ativo);

        if($stmt->execute()){
            $this->id = $this->conn->lastInsertId(); // Pega o ID do parto recém-criado
            return true;
        }
        return false;
    }

    public function readOne() {
        $query = "SELECT
                    p.id, p.id_vaca, p.id_touro, p.sexo_cria, p.data_parto, p.observacoes, p.ativo,
                    p.created_at, p.updated_at,
                    g.brinco as brinco_vaca_display, g.nome as nome_vaca_display,
                    t.nome as nome_touro_display
                  FROM
                    " . $this->table_name . " p
                  LEFT JOIN gado g ON p.id_vaca = g.id
                  LEFT JOIN touros t ON p.id_touro = t.id
                  WHERE p.id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id_vaca = $row['id_vaca'];
            $this->id_touro = $row['id_touro'];
            $this->sexo_cria = $row['sexo_cria'];
            $this->data_parto = $row['data_parto'];
            $this->observacoes = $row['observacoes'];
            $this->ativo = $row['ativo'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->brinco_vaca_display = $row['brinco_vaca_display'];
            $this->nome_vaca_display = $row['nome_vaca_display'];
            $this->nome_touro_display = $row['nome_touro_display'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET
                    id_vaca=:id_vaca, id_touro=:id_touro, sexo_cria=:sexo_cria,
                    data_parto=:data_parto, observacoes=:observacoes, updated_at=CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->id_vaca=htmlspecialchars(strip_tags($this->id_vaca));
        $this->id_touro=htmlspecialchars(strip_tags($this->id_touro));
        $this->sexo_cria=htmlspecialchars(strip_tags($this->sexo_cria));
        $this->data_parto=htmlspecialchars(strip_tags($this->data_parto));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":id_vaca", $this->id_vaca);
        $stmt->bindParam(":id_touro", $this->id_touro);
        $stmt->bindParam(":sexo_cria", $this->sexo_cria);
        $stmt->bindParam(":data_parto", $this->data_parto);
        $stmt->bindParam(":observacoes", $this->observacoes);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }
    
    // Método para buscar o último parto de uma vaca específica
    public function getUltimoParto($id_vaca) {
        $query = "SELECT data_parto
                  FROM " . $this->table_name . "
                  WHERE id_vaca = :id_vaca AND ativo = 1
                  ORDER BY data_parto DESC LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vaca', $id_vaca, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $row : null;
    }
    
    // ### NOVO MÉTODO ###
    // Conta o número total de partos de uma vaca
    public function countByVacaId($id_vaca) {
        $query = "SELECT COUNT(id) as total FROM " . $this->table_name . " WHERE id_vaca = :id_vaca AND ativo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_vaca', $id_vaca, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? (int)$row['total'] : 0;
    }
	// ### INÍCIO DA NOVA FUNÇÃO PARA GRÁFICOS ###
    /**
     * Retorna a contagem de animais por ordem de parto (lactação).
     * @param array $filtros Filtros a serem aplicados.
     * @return array
     */
    public function getContagemPorOrdemDeParto($filtros = []) {
        $query = "
            SELECT 
                ordem_parto, 
                COUNT(*) as total 
            FROM (
                SELECT 
                    p.id_vaca, 
                    COUNT(p.id) as ordem_parto
                FROM partos p
                JOIN gado g ON p.id_vaca = g.id
                WHERE g.ativo = 1
                -- Futuramente, os filtros serão aplicados aqui dentro
                GROUP BY p.id_vaca
            ) as subquery
            GROUP BY ordem_parto
            ORDER BY ordem_parto ASC
        ";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $resultadoFormatado = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultadoFormatado[] = [
                'label' => $row['ordem_parto'] . 'º Parto',
                'value' => (int)$row['total']
            ];
        }
        return $resultadoFormatado;
    }
    // ### FIM DA NOVA FUNÇÃO ###
	/**
     * Pega todos os partos históricos, agrupados por mês.
     * ESSENCIAL PARA O GRÁFICO DE LACTAÇÃO.
     * @return array Um array associativo (ex: ['2025-06' => 5, '2025-07' => 3]).
     */
    public function getContagemDePartosPorMes() {
        $query = "SELECT DATE_FORMAT(data_parto, '%Y-%m') as mes, COUNT(id) as total
                  FROM " . $this->table_name . "
                  WHERE ativo = 1
                  GROUP BY mes";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
?>
