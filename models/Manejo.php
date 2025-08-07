<?php
class Manejo {
    private $conn;
    private $table_name = "manejos";

    public $id;
    public $nome;
    public $tipo;
    public $ativo;
	public $recorrencia_meses;
	public $recorrencia_dias;
    public $evento_dias_1, $evento_titulo_1;
    public $evento_dias_2, $evento_titulo_2;
    public $evento_dias_3, $evento_titulo_3;
    public $evento_dias_4, $evento_titulo_4;
    public $evento_dias_5, $evento_titulo_5;
    public $evento_dias_6, $evento_titulo_6;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($filters = []) {
        // Query base
        $query = "SELECT id, nome, tipo, ativo FROM " . $this->table_name;
        
        $where_clauses = [];
        $params = [];

        // Filtro da Barra de Pesquisa (Nome ou Tipo)
        if (!empty($filters['search_query'])) {
            $where_clauses[] = "(nome LIKE :search_query OR tipo LIKE :search_query)";
            $params[':search_query'] = '%' . $filters['search_query'] . '%';
        }

        // Filtro por Tipos (do modal)
        if (!empty($filters['tipos'])) {
            $tipo_placeholders = [];
            foreach ($filters['tipos'] as $key => $tipo) {
                $placeholder = ":tipo".$key;
                $tipo_placeholders[] = $placeholder;
                $params[$placeholder] = $tipo;
            }
            $where_clauses[] = "tipo IN (" . implode(',', $tipo_placeholders) . ")";
        }
        
        // Adiciona a cláusula WHERE se houver filtros
        if (!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        $query .= " ORDER BY tipo, nome ASC";
        
        $stmt = $this->conn->prepare($query);
        
        // Binds dos parâmetros
        foreach ($params as $key => &$val) {
            $stmt->bindParam($key, $val);
        }
        
        $stmt->execute();
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
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

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
                    nome=:nome, tipo=:tipo, ativo=:ativo,recorrencia_meses=:recorrencia_meses,recorrencia_dias=:recorrencia_dias,
                    evento_dias_1=:evento_dias_1, evento_titulo_1=:evento_titulo_1,
                    evento_dias_2=:evento_dias_2, evento_titulo_2=:evento_titulo_2,
                    evento_dias_3=:evento_dias_3, evento_titulo_3=:evento_titulo_3,
                    evento_dias_4=:evento_dias_4, evento_titulo_4=:evento_titulo_4,
                    evento_dias_5=:evento_dias_5, evento_titulo_5=:evento_titulo_5,
                    evento_dias_6=:evento_dias_6, evento_titulo_6=:evento_titulo_6";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->ativo = htmlspecialchars(strip_tags($this->ativo));
		$this->recorrencia_meses = ($this->recorrencia_meses === '' || $this->recorrencia_meses === null) ? null : htmlspecialchars(strip_tags($this->recorrencia_meses));
		$this->recorrencia_dias = ($this->recorrencia_dias === '' || $this->recorrencia_dias === null) ? null : htmlspecialchars(strip_tags($this->recorrencia_dias));
        $this->evento_dias_1 = ($this->evento_dias_1 === '' || $this->evento_dias_1 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_1));
        $this->evento_titulo_1 = ($this->evento_titulo_1 === '' || $this->evento_titulo_1 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_1));
        $this->evento_dias_2 = ($this->evento_dias_2 === '' || $this->evento_dias_2 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_2));
        $this->evento_titulo_2 = ($this->evento_titulo_2 === '' || $this->evento_titulo_2 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_2));
        $this->evento_dias_3 = ($this->evento_dias_3 === '' || $this->evento_dias_3 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_3));
        $this->evento_titulo_3 = ($this->evento_titulo_3 === '' || $this->evento_titulo_3 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_3));
        $this->evento_dias_4 = ($this->evento_dias_4 === '' || $this->evento_dias_4 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_4));
        $this->evento_titulo_4 = ($this->evento_titulo_4 === '' || $this->evento_titulo_4 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_4));
        $this->evento_dias_5 = ($this->evento_dias_5 === '' || $this->evento_dias_5 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_5));
        $this->evento_titulo_5 = ($this->evento_titulo_5 === '' || $this->evento_titulo_5 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_5));
        $this->evento_dias_6 = ($this->evento_dias_6 === '' || $this->evento_dias_6 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_6));
        $this->evento_titulo_6 = ($this->evento_titulo_6 === '' || $this->evento_titulo_6 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_6));

        // Bind
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":ativo", $this->ativo);
		 $stmt->bindParam(":recorrencia_meses", $this->recorrencia_meses);
		 $stmt->bindParam(":recorrencia_dias", $this->recorrencia_dias);
        $stmt->bindParam(":evento_dias_1", $this->evento_dias_1);
        $stmt->bindParam(":evento_titulo_1", $this->evento_titulo_1);
        $stmt->bindParam(":evento_dias_2", $this->evento_dias_2);
        $stmt->bindParam(":evento_titulo_2", $this->evento_titulo_2);
        $stmt->bindParam(":evento_dias_3", $this->evento_dias_3);
        $stmt->bindParam(":evento_titulo_3", $this->evento_titulo_3);
        $stmt->bindParam(":evento_dias_4", $this->evento_dias_4);
        $stmt->bindParam(":evento_titulo_4", $this->evento_titulo_4);
        $stmt->bindParam(":evento_dias_5", $this->evento_dias_5);
        $stmt->bindParam(":evento_titulo_5", $this->evento_titulo_5);
        $stmt->bindParam(":evento_dias_6", $this->evento_dias_6);
        $stmt->bindParam(":evento_titulo_6", $this->evento_titulo_6);
        
        return $stmt->execute();
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET 
                    nome=:nome, tipo=:tipo, ativo=:ativo,recorrencia_meses=:recorrencia_meses,recorrencia_dias=:recorrencia_dias,
                    evento_dias_1=:evento_dias_1, evento_titulo_1=:evento_titulo_1,
                    evento_dias_2=:evento_dias_2, evento_titulo_2=:evento_titulo_2,
                    evento_dias_3=:evento_dias_3, evento_titulo_3=:evento_titulo_3,
                    evento_dias_4=:evento_dias_4, evento_titulo_4=:evento_titulo_4,
                    evento_dias_5=:evento_dias_5, evento_titulo_5=:evento_titulo_5,
                    evento_dias_6=:evento_dias_6, evento_titulo_6=:evento_titulo_6
                  WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->nome = htmlspecialchars(strip_tags($this->nome));
        $this->tipo = htmlspecialchars(strip_tags($this->tipo));
        $this->ativo = htmlspecialchars(strip_tags($this->ativo));
		$this->recorrencia_meses = ($this->recorrencia_meses === '' || $this->recorrencia_meses === null) ? null : htmlspecialchars(strip_tags($this->recorrencia_meses));
    $this->recorrencia_dias = ($this->recorrencia_dias === '' || $this->recorrencia_dias === null) ? null : htmlspecialchars(strip_tags($this->recorrencia_dias));
        $this->evento_dias_1 = ($this->evento_dias_1 === '' || $this->evento_dias_1 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_1));
        $this->evento_titulo_1 = ($this->evento_titulo_1 === '' || $this->evento_titulo_1 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_1));
        $this->evento_dias_2 = ($this->evento_dias_2 === '' || $this->evento_dias_2 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_2));
        $this->evento_titulo_2 = ($this->evento_titulo_2 === '' || $this->evento_titulo_2 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_2));
        $this->evento_dias_3 = ($this->evento_dias_3 === '' || $this->evento_dias_3 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_3));
        $this->evento_titulo_3 = ($this->evento_titulo_3 === '' || $this->evento_titulo_3 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_3));
        $this->evento_dias_4 = ($this->evento_dias_4 === '' || $this->evento_dias_4 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_4));
        $this->evento_titulo_4 = ($this->evento_titulo_4 === '' || $this->evento_titulo_4 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_4));
        $this->evento_dias_5 = ($this->evento_dias_5 === '' || $this->evento_dias_5 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_5));
        $this->evento_titulo_5 = ($this->evento_titulo_5 === '' || $this->evento_titulo_5 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_5));
        $this->evento_dias_6 = ($this->evento_dias_6 === '' || $this->evento_dias_6 === null) ? null : htmlspecialchars(strip_tags($this->evento_dias_6));
        $this->evento_titulo_6 = ($this->evento_titulo_6 === '' || $this->evento_titulo_6 === null) ? null : htmlspecialchars(strip_tags($this->evento_titulo_6));

        // Bind
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":nome", $this->nome);
        $stmt->bindParam(":tipo", $this->tipo);
        $stmt->bindParam(":ativo", $this->ativo);
		$stmt->bindParam(":recorrencia_meses", $this->recorrencia_meses);
		$stmt->bindParam(":recorrencia_dias", $this->recorrencia_dias);
        $stmt->bindParam(":evento_dias_1", $this->evento_dias_1);
        $stmt->bindParam(":evento_titulo_1", $this->evento_titulo_1);
        $stmt->bindParam(":evento_dias_2", $this->evento_dias_2);
        $stmt->bindParam(":evento_titulo_2", $this->evento_titulo_2);
        $stmt->bindParam(":evento_dias_3", $this->evento_dias_3);
        $stmt->bindParam(":evento_titulo_3", $this->evento_titulo_3);
        $stmt->bindParam(":evento_dias_4", $this->evento_dias_4);
        $stmt->bindParam(":evento_titulo_4", $this->evento_titulo_4);
        $stmt->bindParam(":evento_dias_5", $this->evento_dias_5);
        $stmt->bindParam(":evento_titulo_5", $this->evento_titulo_5);
        $stmt->bindParam(":evento_dias_6", $this->evento_dias_6);
        $stmt->bindParam(":evento_titulo_6", $this->evento_titulo_6);
        
        return $stmt->execute();
    }
    
    public function readByType($tipo) {
        $query = "SELECT id, nome FROM " . $this->table_name . " WHERE tipo = :tipo AND ativo = 1 ORDER BY nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":tipo", $tipo);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
	// models/Manejo.php
    
    // **NOVO MÉTODO**
    public function readAll() {
        $query = "SELECT id, nome, tipo FROM " . $this->table_name . " WHERE ativo = 1 ORDER BY tipo, nome ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	
}
?>