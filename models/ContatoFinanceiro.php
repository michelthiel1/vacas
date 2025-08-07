<?php
class ContatoFinanceiro {
    private $conn;
    private $table_name = "financeiro_contatos";

    public $id;
    public $nome;
    public $tipo;
    public $telefone;
    public $cpf_cnpj;

    public function __construct($db) {
        $this->conn = $db;
    }

    // ### MÉTODO READ ATUALIZADO PARA ACEITAR PESQUISA ###
    public function read($searchQuery = '') {
        $query = "SELECT id, nome, tipo, telefone, cpf_cnpj FROM " . $this->table_name;
        
        if (!empty($searchQuery)) {
            // Adiciona a cláusula WHERE para filtrar por qualquer um dos campos principais
            $query .= " WHERE nome LIKE :searchQuery 
                        OR tipo LIKE :searchQuery 
                        OR telefone LIKE :searchQuery 
                        OR cpf_cnpj LIKE :searchQuery";
        }
        
        $query .= " ORDER BY nome ASC";
        
        $stmt = $this->conn->prepare($query);

        if (!empty($searchQuery)) {
            // Vincula o parâmetro de pesquisa de forma segura
            $searchTerm = "%" . $searchQuery . "%";
            $stmt->bindParam(":searchQuery", $searchTerm);
        }

        $stmt->execute();
        return $stmt;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET nome=:nome, tipo=:tipo, telefone=:telefone, cpf_cnpj=:cpf_cnpj";
        $stmt = $this->conn->prepare($query);

        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->cpf_cnpj = htmlspecialchars(strip_tags($this->cpf_cnpj));

        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":telefone", $this->telefone);
        $stmt->bindParam(":cpf_cnpj", $this->cpf_cnpj);

        return $stmt->execute();
    }
    
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET nome=:nome, tipo=:tipo, telefone=:telefone, cpf_cnpj=:cpf_cnpj WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->cpf_cnpj = htmlspecialchars(strip_tags($this->cpf_cnpj));

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':nome', $this->nome);
        $stmt->bindParam(':tipo', $this->tipo);
        $stmt->bindParam(':telefone', $this->telefone);
        $stmt->bindParam(':cpf_cnpj', $this->cpf_cnpj);

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
