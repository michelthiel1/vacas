<?php
class EnvioLeite {
    private $conn;
    private $table_name = "envios_leite";

    public $id;
    public $data_envio;
    public $litros_enviados;
    public $numero_vacas;
    public $observacoes;
    public $ativo;
	public $leite_bezerros; // <-- Adicionar esta linha

    public function __construct($db) {
        $this->conn = $db;
    }

    // Ler todos os envios
    public function read() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE ativo = 1 ORDER BY data_envio DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Criar um novo envio
    public function create() {
       $query = "INSERT INTO " . $this->table_name . " SET data_envio=:data_envio, litros_enviados=:litros_enviados, numero_vacas=:numero_vacas, leite_bezerros=:leite_bezerros, observacoes=:observacoes";
         $stmt = $this->conn->prepare($query);

        // Limpa os dados
        $this->data_envio = htmlspecialchars(strip_tags($this->data_envio));
        $this->litros_enviados = htmlspecialchars(strip_tags($this->litros_enviados));
        $this->numero_vacas = htmlspecialchars(strip_tags($this->numero_vacas));
        $this->observacoes = htmlspecialchars(strip_tags($this->observacoes));
		  $this->leite_bezerros = htmlspecialchars(strip_tags($this->leite_bezerros)); // <-- Adicionar esta linha
      
	  

        // Associa os parâmetros
        $stmt->bindParam(":data_envio", $this->data_envio);
        $stmt->bindParam(":litros_enviados", $this->litros_enviados);
        $stmt->bindParam(":numero_vacas", $this->numero_vacas);
        $stmt->bindParam(":observacoes", $this->observacoes);
		$stmt->bindParam(":leite_bezerros", $this->leite_bezerros); // <-- Adicionar esta linha
      

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Ler um único envio (para edição)
    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->data_envio = $row['data_envio'];
            $this->litros_enviados = $row['litros_enviados'];
            $this->numero_vacas = $row['numero_vacas'];
            $this->observacoes = $row['observacoes'];
			 $this->leite_bezerros = $row['leite_bezerros']; // <-- Adicionar esta linha
           
        }
    }

    // Atualizar um envio
    public function update() {
        $query = "UPDATE " . $this->table_name . " SET data_envio = :data_envio, litros_enviados = :litros_enviados, numero_vacas = :numero_vacas, leite_bezerros = :leite_bezerros, observacoes = :observacoes WHERE id = :id";
       $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->data_envio = htmlspecialchars(strip_tags($this->data_envio));
        $this->litros_enviados = htmlspecialchars(strip_tags($this->litros_enviados));
        $this->numero_vacas = htmlspecialchars(strip_tags($this->numero_vacas));
        $this->observacoes = htmlspecialchars(strip_tags($this->observacoes));
		 $this->leite_bezerros = htmlspecialchars(strip_tags($this->leite_bezerros)); // <-- Adicionar esta linha
  

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':data_envio', $this->data_envio);
        $stmt->bindParam(':litros_enviados', $this->litros_enviados);
        $stmt->bindParam(':numero_vacas', $this->numero_vacas);
        $stmt->bindParam(':observacoes', $this->observacoes);
		 $stmt->bindParam(':leite_bezerros', $this->leite_bezerros); // <-- Adicionar esta linha
     

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Deletar um envio (soft delete)
    public function delete() {
        $query = "UPDATE " . $this->table_name . " SET ativo = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
	
	// Método para ler envios por um mês e ano específicos
    public function readByMonthYear($year, $month) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE YEAR(data_envio) = :year AND MONTH(data_envio) = :month AND ativo = 1 ORDER BY data_envio ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        return $stmt;
    }
}
?>