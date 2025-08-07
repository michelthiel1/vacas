<?php

class Touro {
    private $conn;
    private $table_name = "touros";
public $doses_estoque;
    public $id;
    public $nome;
    public $raca;
    public $observacoes;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($filters = []) {
       // Na função read(), altere a linha da query para:
$query = "SELECT id, nome, raca, observacoes, ativo, doses_estoque, created_at, updated_at
          FROM " . $this->table_name;
        
        $where_clauses = [];
        $params = [];

        if (!empty($filters['search_query'])) {
            $where_clauses[] = "(nome LIKE :search_query OR raca LIKE :search_query)";
            $params[':search_query'] = '%' . $filters['search_query'] . '%';
        }
        
        // Adiciona a cláusula WHERE se houver filtros
        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }

        $query .= " ORDER BY nome";

        $stmt = $this->conn->prepare($query);

        // Binds
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }

        $stmt->execute();
        return $stmt;
    }
    
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    nome=:nome, raca=:raca, observacoes=:observacoes, ativo=:ativo, created_at=CURRENT_TIMESTAMP, updated_at=CURRENT_TIMESTAMP";
        
        $stmt = $this->conn->prepare($query);

        $this->nome=htmlspecialchars(strip_tags($this->nome));
        $this->raca=htmlspecialchars(strip_tags($this->raca));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $this->ativo=htmlspecialchars(strip_tags($this->ativo));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":raca", $this->raca);
        $stmt->bindParam(":observacoes", $this->observacoes);
        $stmt->bindParam(":ativo", $this->ativo);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    public function readOne() {
        $query = "SELECT
                    id, nome, raca, observacoes, ativo, created_at, updated_at
                  FROM
                    " . $this->table_name . "
                  WHERE
                    id = ?
                  LIMIT
                    0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row['id'];
        $this->nome = $row['nome'];
        $this->raca = $row['raca'];
        $this->observacoes = $row['observacoes'];
        $this->ativo = $row['ativo'];
        $this->created_at = $row['created_at'];
        $this->updated_at = $row['updated_at'];
        return true;
    }

    public function update() {
        $query = "UPDATE
                    " . $this->table_name . "
                  SET
                    nome=:nome, raca=:raca, observacoes=:observacoes, ativo=:ativo, updated_at=CURRENT_TIMESTAMP
                  WHERE
                    id = :id";

        $stmt = $this->conn->prepare($query);

        $this->nome=htmlspecialchars(strip_tags($this->nome));
        $this->raca=htmlspecialchars(strip_tags($this->raca));
        $this->observacoes=htmlspecialchars(strip_tags($this->observacoes));
        $this->ativo=htmlspecialchars(strip_tags($this->ativo));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":raca", $this->raca);
        $stmt->bindParam(":observacoes", $this->observacoes);
        $stmt->bindParam(":ativo", $this->ativo);
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

    public function readAllNames() {
        $query = "SELECT id, nome FROM " . $this->table_name . " WHERE ativo = 1 ORDER BY nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }
	
	/**
 * Ajusta a quantidade de doses de sêmen para um touro específico.
 * @param int $id_touro O ID do touro.
 * @param int $quantidade A quantidade para adicionar (positiva) ou subtrair (negativa).
 * @return bool True se a operação for bem-sucedida.
 */
public function ajustarDosesEstoque($id_touro, $quantidade) {
    $query = "UPDATE " . $this->table_name . " 
              SET doses_estoque = doses_estoque + :quantidade 
              WHERE id = :id_touro";

    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':quantidade', $quantidade, PDO::PARAM_INT);
    $stmt->bindParam(':id_touro', $id_touro, PDO::PARAM_INT);

    return $stmt->execute();
}
}
?>