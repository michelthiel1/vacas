<?php
class Pesagem {
    private $conn;
    private $table_name = "pesagens";

    public $id;
    public $id_gado;
    public $peso;
    public $data_pesagem;
    public $observacoes;

    // Propriedades para JOIN
    public $brinco_gado;

    public function __construct($db) {
        $this->conn = $db;
    }

public function read($searchQuery = '') {
        $query = "SELECT p.id, p.id_gado, p.peso, p.data_pesagem, g.brinco as brinco_gado
                  FROM " . $this->table_name . " p
                  LEFT JOIN gado g ON p.id_gado = g.id
                  WHERE g.ativo = 1";

        if (!empty($searchQuery)) {
            $query .= " AND g.brinco LIKE :search_query";
        }
        
        $query .= " ORDER BY p.data_pesagem DESC";
        
        $stmt = $this->conn->prepare($query);

        if (!empty($searchQuery)) {
            $searchTerm = "%" . $searchQuery . "%";
            $stmt->bindParam(':search_query', $searchTerm);
        }

        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->id_gado = $row['id_gado'];
            $this->peso = $row['peso'];
            $this->data_pesagem = $row['data_pesagem'];
            $this->observacoes = $row['observacoes'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET id_gado=:id_gado, peso=:peso, data_pesagem=:data_pesagem, observacoes=:observacoes";
        $stmt = $this->conn->prepare($query);

        $this->id_gado = htmlspecialchars(strip_tags($this->id_gado));
        $this->peso = htmlspecialchars(strip_tags($this->peso));
        $this->data_pesagem = htmlspecialchars(strip_tags($this->data_pesagem));
        $this->observacoes = htmlspecialchars(strip_tags($this->observacoes));

        $stmt->bindParam(":id_gado", $this->id_gado);
        $stmt->bindParam(":peso", $this->peso);
        $stmt->bindParam(":data_pesagem", $this->data_pesagem);
        $stmt->bindParam(":observacoes", $this->observacoes);

        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET id_gado=:id_gado, peso=:peso, data_pesagem=:data_pesagem, observacoes=:observacoes WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->id_gado = htmlspecialchars(strip_tags($this->id_gado));
        $this->peso = htmlspecialchars(strip_tags($this->peso));
        $this->data_pesagem = htmlspecialchars(strip_tags($this->data_pesagem));
        $this->observacoes = htmlspecialchars(strip_tags($this->observacoes));
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_gado", $this->id_gado);
        $stmt->bindParam(":peso", $this->peso);
        $stmt->bindParam(":data_pesagem", $this->data_pesagem);
        $stmt->bindParam(":observacoes", $this->observacoes);

        return $stmt->execute();
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $this->id = htmlspecialchars(strip_tags($this->id));
        $stmt->bindParam(1, $this->id);
        return $stmt->execute();
    }
	
	public function readByGadoId($id_gado) {
        // Query para buscar o histórico de peso de um animal e calcular a idade em meses em cada pesagem
        $query = "SELECT 
                    p.peso,
                    TIMESTAMPDIFF(MONTH, g.nascimento, p.data_pesagem) AS idade_em_meses
                  FROM 
                    " . $this->table_name . " p
                  JOIN 
                    gado g ON p.id_gado = g.id
                  WHERE 
                    p.id_gado = :id_gado
                  ORDER BY 
                    p.data_pesagem ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id_gado", $id_gado, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
	 // ### NOVO MÉTODO ###
    public function getLastByGadoId($id_gado) {
        $query = "SELECT data_pesagem, peso 
                  FROM " . $this->table_name . " 
                  WHERE id_gado = :id_gado 
                  ORDER BY data_pesagem DESC 
                  LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_gado', $id_gado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>