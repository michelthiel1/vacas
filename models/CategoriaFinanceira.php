<?php
class CategoriaFinanceira {
    private $conn;
    private $table_name = "financeiro_categorias";

    public $id;
    public $nome;
    public $tipo; // PAGAR ou RECEBER
    public $parent_id;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($nome, $tipo, $parent_id) {
        $query = "INSERT INTO " . $this->table_name . " (nome, tipo, parent_id) VALUES (:nome, :tipo, :parent_id)";
        $stmt = $this->conn->prepare($query);

        $nome = htmlspecialchars(strip_tags($nome));
        $tipo = htmlspecialchars(strip_tags($tipo));
        $parent_id = $parent_id ?: null;

        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':parent_id', $parent_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

public function read($searchQuery = '') {
        $query = "SELECT id, nome, tipo, parent_id FROM " . $this->table_name;
        
        $params = [];
        if (!empty($searchQuery)) {
            // A pesquisa agora filtra por nome ou tipo
            $query .= " WHERE nome LIKE :searchQuery OR tipo LIKE :searchQuery";
            $params[':searchQuery'] = '%' . $searchQuery . '%';
        }
        
        // A ordenação por nome é importante para a exibição
        $query .= " ORDER BY nome ASC";
        
        $stmt = $this->conn->prepare($query);
        
        if (!empty($params)) {
            $stmt->bindParam(':searchQuery', $params[':searchQuery']);
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne($id) {
        $query = "SELECT id, nome, tipo, parent_id FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $this->id = $row['id'];
            $this->nome = $row['nome'];
            $this->tipo = $row['tipo'];
            $this->parent_id = $row['parent_id'];
            return true;
        }
        return false;
    }

    public function update($id, $nome, $tipo, $parent_id) {
        $query = "UPDATE " . $this->table_name . " SET nome = :nome, tipo = :tipo, parent_id = :parent_id WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $id = htmlspecialchars(strip_tags($id));
        $nome = htmlspecialchars(strip_tags($nome));
        $tipo = htmlspecialchars(strip_tags($tipo));
        $parent_id = $parent_id ?: null;

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nome', $nome);
        $stmt->bindParam(':tipo', $tipo);
        $stmt->bindParam(':parent_id', $parent_id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            // Captura erro de chave estrangeira, se a categoria estiver em uso
            return false;
        }
        return false;
    }
    
    public function hasChildren($id) {
        $query = "SELECT COUNT(*) FROM " . $this->table_name . " WHERE parent_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
}