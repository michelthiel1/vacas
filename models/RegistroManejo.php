<?php
class RegistroManejo {
    private $conn;
    private $table_name = "registros_manejos";

    public $id;
    public $id_manejo;
    public $id_gado;
    public $aplicado_rebanho;
    public $data_aplicacao;
    public $observacoes;
    public $id_evento_calendario;
    public $tipo_manejo;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET id_manejo=:id_manejo, id_gado=:id_gado, aplicado_rebanho=:aplicado_rebanho, data_aplicacao=:data_aplicacao, observacoes=:observacoes";
        $stmt = $this->conn->prepare($query);
        $this->id_manejo=htmlspecialchars(strip_tags($this->id_manejo));
        $this->id_gado=$this->id_gado === null ? null : htmlspecialchars(strip_tags($this->id_gado));
        $this->aplicado_rebanho=htmlspecialchars(strip_tags($this->aplicado_rebanho));
        $this->data_aplicacao=htmlspecialchars(strip_tags($this->data_aplicacao));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $stmt->bindParam(":id_manejo", $this->id_manejo);
        $stmt->bindParam(":id_gado", $this->id_gado);
        $stmt->bindParam(":aplicado_rebanho", $this->aplicado_rebanho);
        $stmt->bindParam(":data_aplicacao", $this->data_aplicacao);
        $stmt->bindParam(":observacoes", $this->observacoes);
        return $stmt->execute();
    }

 public function read($filters = []) {
        $query = "SELECT 
                    r.id,
                    r.data_aplicacao,
                    r.aplicado_rebanho,
                    m.nome as nome_manejo,
                    m.tipo as tipo_manejo,
                    g.brinco as brinco_gado
                  FROM " . $this->table_name . " r
                  LEFT JOIN manejos m ON r.id_manejo = m.id
                  LEFT JOIN gado g ON r.id_gado = g.id";
        
        $where_clauses = [];
        $params = [];

        $where_clauses[] = "(g.ativo = 1 OR r.aplicado_rebanho = 1)";

        if (!empty($filters['search_query'])) {
            $where_clauses[] = "(g.brinco LIKE :search_query OR m.tipo LIKE :search_query OR m.nome LIKE :search_query)";
            $params[':search_query'] = '%' . $filters['search_query'] . '%';
        }
        
        if (!empty($filters['data_inicio'])) {
            $where_clauses[] = "r.data_aplicacao >= :data_inicio";
            $params[':data_inicio'] = $filters['data_inicio'];
        }
        if (!empty($filters['data_fim'])) {
            $where_clauses[] = "r.data_aplicacao <= :data_fim";
            $params[':data_fim'] = $filters['data_fim'];
        }
        if (!empty($filters['tipos_manejo'])) {
            $tipo_placeholders = [];
            foreach ($filters['tipos_manejo'] as $key => $tipo) {
                $placeholder = ":tipo".$key;
                $tipo_placeholders[] = $placeholder;
                $params[$placeholder] = $tipo;
            }
            $where_clauses[] = "m.tipo IN (" . implode(',', $tipo_placeholders) . ")";
        }
        if (!empty($filters['id_manejo'])) {
            $where_clauses[] = "r.id_manejo = :id_manejo";
            $params[':id_manejo'] = $filters['id_manejo'];
        }

        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        $query .= " ORDER BY r.data_aplicacao DESC, r.id DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        
        $stmt->execute();
        return $stmt;
    }
    
    public function readOne() {
        $query = "SELECT r.id, r.id_manejo, r.id_gado, r.aplicado_rebanho, r.data_aplicacao, r.observacoes, r.id_evento_calendario, m.tipo as tipo_manejo
                  FROM " . $this->table_name . " r
                  LEFT JOIN manejos m ON r.id_manejo = m.id
                  WHERE r.id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id = $row['id'];
            $this->id_manejo = $row['id_manejo'];
            $this->id_gado = $row['id_gado'];
            $this->aplicado_rebanho = $row['aplicado_rebanho'];
            $this->data_aplicacao = $row['data_aplicacao'];
            $this->observacoes = $row['observacoes'];
            $this->id_evento_calendario = $row['id_evento_calendario'];
            $this->tipo_manejo = $row['tipo_manejo'];
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET id_manejo = :id_manejo, id_gado = :id_gado, aplicado_rebanho = :aplicado_rebanho, data_aplicacao = :data_aplicacao, observacoes = :observacoes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $this->id=htmlspecialchars(strip_tags($this->id));
        $this->id_manejo=htmlspecialchars(strip_tags($this->id_manejo));
        $this->id_gado=$this->id_gado === null ? null : htmlspecialchars(strip_tags($this->id_gado));
        $this->aplicado_rebanho=htmlspecialchars(strip_tags($this->aplicado_rebanho));
        $this->data_aplicacao=htmlspecialchars(strip_tags($this->data_aplicacao));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':id_manejo', $this->id_manejo);
        $stmt->bindParam(':id_gado', $this->id_gado);
        $stmt->bindParam(':aplicado_rebanho', $this->aplicado_rebanho);
        $stmt->bindParam(':data_aplicacao', $this->data_aplicacao);
        $stmt->bindParam(':observacoes', $this->observacoes);
        return $stmt->execute();
    }
    
    public function updateEventId($id, $event_id) {
        $query = "UPDATE " . $this->table_name . " SET id_evento_calendario = :event_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
}
?>