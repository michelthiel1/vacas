<?php

class Evento {
    private $conn;
    private $table_name = "eventos";

    public $id;
    public $titulo;
    public $data_evento;
    public $descricao;
    public $tipo_evento;
    public $id_vaca;
    public $id_registro_manejo; // Vínculo com o registro de manejo
    public $ativo;
    public $created_at;
    public $updated_at;

    // Propriedades para exibição via JOIN
    public $brinco_vaca_display;
    public $nome_vaca_display;

    public function __construct($db) {
        $this->conn = $db;
    }
	
	
	// Adicione este novo método dentro da classe Evento
    public function deactivate() {
        $query = "UPDATE " . $this->table_name . " SET ativo = 0, updated_at = CURRENT_TIMESTAMP WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(':id', $this->id);
        
        if($stmt->execute()){
            return true;
        }
        return false;
    }

public function read($searchQuery = '', $mes = null, $ano = null, $showCompleted = false) {
        $filters = [];
        $queryParams = [];

        // Adicionado LEFT JOIN para as tabelas de manejo
        $query = "SELECT
                    e.id, e.titulo, e.data_evento, e.descricao, e.tipo_evento, e.id_vaca, e.ativo,
                    e.created_at, e.updated_at,
                    g.brinco as brinco_vaca_display, g.nome as nome_vaca_display
                  FROM
                    " . $this->table_name . " e
                  LEFT JOIN gado g ON e.id_vaca = g.id
                  LEFT JOIN registros_manejos rm ON e.id_registro_manejo = rm.id
                  LEFT JOIN manejos m ON rm.id_manejo = m.id";
        
        if (!$showCompleted) {
            $filters[] = "e.ativo = 1";
        }

        if ($mes !== null && $ano !== null) {
            $filters[] = "MONTH(e.data_evento) = :mes AND YEAR(e.data_evento) = :ano";
            $queryParams[':mes'] = $mes;
            $queryParams[':ano'] = $ano;
        }

        if (!empty($searchQuery)) {
            // Condição de busca expandida para incluir nome e tipo do manejo
            $filters[] = "(e.titulo LIKE :search_query 
                          OR g.brinco LIKE :search_query 
                          OR m.nome LIKE :search_query 
                          OR m.tipo LIKE :search_query)";
            $queryParams[':search_query'] = '%' . $searchQuery . '%';
        }

        if (!empty($filters)) {
            $query .= " WHERE " . implode(" AND ", $filters);
        }

        // Adicionado GROUP BY para evitar duplicatas por causa dos JOINs
        $query .= " GROUP BY e.id ORDER BY e.data_evento ASC, e.id ASC";

        $stmt = $this->conn->prepare($query);
        foreach ($queryParams as $param => $value) {
            $stmt->bindValue($param, $value);
        }
        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    titulo=:titulo, data_evento=:data_evento, descricao=:descricao, tipo_evento=:tipo_evento,
                    id_vaca=:id_vaca, id_registro_manejo=:id_registro_manejo, ativo=:ativo, created_at=CURRENT_TIMESTAMP, updated_at=CURRENT_TIMESTAMP";
        
        $stmt = $this->conn->prepare($query);

        $this->titulo=htmlspecialchars(strip_tags($this->titulo));
        $this->data_evento=htmlspecialchars(strip_tags($this->data_evento));
        $this->descricao=htmlspecialchars(strip_tags($this->descricao));
        $this->tipo_evento=htmlspecialchars(strip_tags($this->tipo_evento));
        $this->id_vaca=($this->id_vaca === null || $this->id_vaca === '') ? null : htmlspecialchars(strip_tags($this->id_vaca));
        $this->id_registro_manejo=($this->id_registro_manejo === null) ? null : htmlspecialchars(strip_tags($this->id_registro_manejo));
        $this->ativo=1;

        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":data_evento", $this->data_evento);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":tipo_evento", $this->tipo_evento);
        $stmt->bindParam(":id_vaca", $this->id_vaca, PDO::PARAM_INT);
        $stmt->bindParam(":id_registro_manejo", $this->id_registro_manejo, PDO::PARAM_INT);
        $stmt->bindParam(":ativo", $this->ativo);

        return $stmt->execute();
    }

public function readOne() {
        // A query foi corrigida para incluir 'e.id_registro_manejo'
        $query = "SELECT
                    e.id, e.titulo, e.data_evento, e.descricao, e.tipo_evento, e.id_vaca, e.ativo,
                    e.id_registro_manejo, -- << ESTA LINHA FOI ADICIONADA À QUERY
                    e.created_at, e.updated_at,
                    g.brinco as brinco_vaca_display, g.nome as nome_vaca_display
                  FROM
                    " . $this->table_name . " e
                  LEFT JOIN gado g ON e.id_vaca = g.id
                  WHERE e.id = ? LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->titulo = $row['titulo'];
            $this->data_evento = $row['data_evento'];
            $this->descricao = $row['descricao'];
            $this->tipo_evento = $row['tipo_evento'];
            $this->id_vaca = $row['id_vaca'];
            $this->ativo = $row['ativo'];
            $this->id_registro_manejo = $row['id_registro_manejo']; // Agora esta linha funcionará corretamente
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            $this->brinco_vaca_display = $row['brinco_vaca_display'];
            $this->nome_vaca_display = $row['nome_vaca_display'];
            return true;
        }
        return false;
    }
    public function update() {
        $query = "UPDATE " . $this->table_name . "
                  SET
                    titulo=:titulo, data_evento=:data_evento, descricao=:descricao, tipo_evento=:tipo_evento,
                    id_vaca=:id_vaca, ativo=:ativo, updated_at=CURRENT_TIMESTAMP
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->titulo=htmlspecialchars(strip_tags($this->titulo));
        $this->data_evento=htmlspecialchars(strip_tags($this->data_evento));
        $this->descricao=htmlspecialchars(strip_tags($this->descricao));
        $this->tipo_evento=htmlspecialchars(strip_tags($this->tipo_evento));
        $this->id_vaca=($this->id_vaca === null || $this->id_vaca === '') ? null : htmlspecialchars(strip_tags($this->id_vaca));
        $this->ativo=htmlspecialchars(strip_tags($this->ativo));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":data_evento", $this->data_evento);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":tipo_evento", $this->tipo_evento);
        $stmt->bindParam(":id_vaca", $this->id_vaca, PDO::PARAM_INT);
        $stmt->bindParam(":ativo", $this->ativo);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);

        return $stmt->execute();
    }

    public function getEventDaysInMonth($mes, $ano) {
        $query = "SELECT DISTINCT DAY(data_evento) as dia 
                  FROM " . $this->table_name . " 
                  WHERE MONTH(data_evento) = :mes AND YEAR(data_evento) = :ano AND ativo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mes', $mes, PDO::PARAM_INT);
        $stmt->bindParam(':ano', $ano, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN, 0); 
    }

    public function getEventosEmAtraso() {
        $query = "SELECT COUNT(id) as total_atraso 
                  FROM " . $this->table_name . " 
                  WHERE data_evento < CURDATE() AND ativo = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total_atraso'] ?? 0;
    }

    public function updateByIdRegistroManejo() {
        $query = "UPDATE " . $this->table_name . " SET
                    titulo=:titulo, data_evento=:data_evento, descricao=:descricao, tipo_evento=:tipo_evento,
                    id_vaca=:id_vaca, updated_at=CURRENT_TIMESTAMP
                  WHERE id_registro_manejo = :id_registro_manejo";
        
        $stmt = $this->conn->prepare($query);

        $this->titulo=htmlspecialchars(strip_tags($this->titulo));
        $this->data_evento=htmlspecialchars(strip_tags($this->data_evento));
        $this->descricao=htmlspecialchars(strip_tags($this->descricao));
        $this->tipo_evento=htmlspecialchars(strip_tags($this->tipo_evento));
        $this->id_vaca=($this->id_vaca === null) ? null : htmlspecialchars(strip_tags($this->id_vaca));
        $this->id_registro_manejo=htmlspecialchars(strip_tags($this->id_registro_manejo));

        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":data_evento", $this->data_evento);
        $stmt->bindParam(":descricao", $this->descricao);
        $stmt->bindParam(":tipo_evento", $this->tipo_evento);
        $stmt->bindParam(":id_vaca", $this->id_vaca);
        $stmt->bindParam(":id_registro_manejo", $this->id_registro_manejo);

        return $stmt->execute();
    }

    public function deleteByIdRegistroManejo($id_registro_manejo) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_registro_manejo = ?";
        $stmt = $this->conn->prepare($query);
        $id_registro_manejo=htmlspecialchars(strip_tags($id_registro_manejo));
        $stmt->bindParam(1, $id_registro_manejo);
        return $stmt->execute();
    }
}
?>