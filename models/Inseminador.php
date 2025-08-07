<?php

class Inseminador {
    private $conn;
    private $table_name = "inseminadores";

    public $id;
    public $nome;
    public $ativo;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read() {
        // Garantir que o ID e o nome sejam selecionados
        $query = "SELECT id, nome FROM " . $this->table_name . " WHERE ativo = 1 ORDER BY nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Método para criar um novo Inseminador
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    nome=:nome, ativo=:ativo, created_at=CURRENT_TIMESTAMP, updated_at=CURRENT_TIMESTAMP";
        
        $stmt = $this->conn->prepare($query);

        $this->nome=htmlspecialchars(strip_tags($this->nome));
        $this->ativo=htmlspecialchars(strip_tags($this->ativo));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":ativo", $this->ativo);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // Método para ler um único Inseminador
    public function readOne() {
        $query = "SELECT
                    id, nome, ativo, created_at, updated_at
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

        if ($row) {
            $this->nome = $row['nome'];
            $this->ativo = $row['ativo'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // Método para atualizar um Inseminador
    public function update() {
        $query = "UPDATE
                    " . $this->table_name . "
                  SET
                    nome=:nome, ativo=:ativo, updated_at=CURRENT_TIMESTAMP
                  WHERE
                    id = :id";
        
        $stmt = $this->conn->prepare($query);

        $this->nome=htmlspecialchars(strip_tags($this->nome));
        $this->ativo=htmlspecialchars(strip_tags($this->ativo));
        $this->id=htmlspecialchars(strip_tags($this->id));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":ativo", $this->ativo);
        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()){
            return true;
        }
        return false;
    }

    // Método para deletar um Inseminador
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
}
?>