<?php
class ProducaoLeite {
    private $conn;
    private $table_name = "producao_leite";

    public $id;
    public $id_gado;
    public $data_producao;
    public $ordenha_1;
    public $ordenha_2;
    public $ordenha_3;
    public $producao_total;
    public $observacoes;

    // Propriedade para JOIN
    public $brinco_gado;

    public function __construct($db) {
        $this->conn = $db;
    }

    private function calcularTotal() {
        $this->producao_total = (float)$this->ordenha_1 + (float)$this->ordenha_2 + (float)$this->ordenha_3;
    }

    public function read($searchQuery = '') {
        $query = "SELECT p.id, p.data_producao, p.producao_total, g.brinco as brinco_gado
                  FROM " . $this->table_name . " p
                  LEFT JOIN gado g ON p.id_gado = g.id";

        if (!empty($searchQuery)) {
            $query .= " WHERE g.brinco LIKE :search_query";
        }
        
        $query .= " ORDER BY p.data_producao DESC, p.id DESC";
        
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
            $this->data_producao = $row['data_producao'];
            $this->ordenha_1 = $row['ordenha_1'];
            $this->ordenha_2 = $row['ordenha_2'];
            $this->ordenha_3 = $row['ordenha_3'];
            $this->producao_total = $row['producao_total'];
            $this->observacoes = $row['observacoes'];
            return true;
        }
        return false;
    }

    public function create() {
        $this->calcularTotal(); // Calcula o total antes de inserir

        $query = "INSERT INTO " . $this->table_name . " SET id_gado=:id_gado, data_producao=:data_producao, ordenha_1=:ordenha_1, ordenha_2=:ordenha_2, ordenha_3=:ordenha_3, producao_total=:producao_total, observacoes=:observacoes";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->id_gado = htmlspecialchars(strip_tags($this->id_gado));
        $this->data_producao = htmlspecialchars(strip_tags($this->data_producao));
        $this->ordenha_1 = htmlspecialchars(strip_tags($this->ordenha_1));
        $this->ordenha_2 = htmlspecialchars(strip_tags($this->ordenha_2));
        $this->ordenha_3 = htmlspecialchars(strip_tags($this->ordenha_3));
        $this->observacoes = htmlspecialchars(strip_tags($this->observacoes));

        $stmt->bindParam(":id_gado", $this->id_gado);
        $stmt->bindParam(":data_producao", $this->data_producao);
        $stmt->bindParam(":ordenha_1", $this->ordenha_1);
        $stmt->bindParam(":ordenha_2", $this->ordenha_2);
        $stmt->bindParam(":ordenha_3", $this->ordenha_3);
        $stmt->bindParam(":producao_total", $this->producao_total);
        $stmt->bindParam(":observacoes", $this->observacoes);

        return $stmt->execute();
    }

    public function update() {
        $this->calcularTotal(); // Calcula o total antes de atualizar

        $query = "UPDATE " . $this->table_name . " SET id_gado=:id_gado, data_producao=:data_producao, ordenha_1=:ordenha_1, ordenha_2=:ordenha_2, ordenha_3=:ordenha_3, producao_total=:producao_total, observacoes=:observacoes WHERE id=:id";
        $stmt = $this->conn->prepare($query);

        // Sanitize
        $this->id = htmlspecialchars(strip_tags($this->id));
        $this->id_gado = htmlspecialchars(strip_tags($this->id_gado));
        $this->data_producao = htmlspecialchars(strip_tags($this->data_producao));
        $this->ordenha_1 = htmlspecialchars(strip_tags($this->ordenha_1));
        $this->ordenha_2 = htmlspecialchars(strip_tags($this->ordenha_2));
        $this->ordenha_3 = htmlspecialchars(strip_tags($this->ordenha_3));
        $this->observacoes = htmlspecialchars(strip_tags($this->observacoes));
        
        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":id_gado", $this->id_gado);
        $stmt->bindParam(":data_producao", $this->data_producao);
        $stmt->bindParam(":ordenha_1", $this->ordenha_1);
        $stmt->bindParam(":ordenha_2", $this->ordenha_2);
        $stmt->bindParam(":ordenha_3", $this->ordenha_3);
        $stmt->bindParam(":producao_total", $this->producao_total);
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
	  // ### NOVO MÉTODO ###
    public function getLastTwoByGadoId($id_gado) {
        $query = "SELECT data_producao, producao_total 
                  FROM " . $this->table_name . " 
                  WHERE id_gado = :id_gado 
                  ORDER BY data_producao DESC 
                  LIMIT 2";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_gado', $id_gado, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
	// ### INÍCIO DAS NOVAS FUNÇÕES PARA GRÁFICOS ###

    /**
     * Calcula a produção média de leite por faixa de Dias em Lactação (DEL).
     * @param array $filtros Filtros a serem aplicados.
     * @return array
     */
    public function getMediaProducaoPorFaixaDEL($filtros = []) {
        $query = "
            SELECT 
                CASE
                    WHEN DATEDIFF(p.data_producao, lp.data_parto) < 100 THEN '< 100 dias'
                    WHEN DATEDIFF(p.data_producao, lp.data_parto) BETWEEN 100 AND 200 THEN '100-200 dias'
                    ELSE '> 200 dias'
                END as faixa_del,
                AVG(p.producao_total) as media_producao
            FROM producao_leite p
            JOIN gado g ON p.id_gado = g.id
            JOIN (
                SELECT id_vaca, MAX(data_parto) as data_parto
                FROM partos
                GROUP BY id_vaca
            ) as lp ON p.id_gado = lp.id_vaca
            WHERE g.ativo = 1 AND g.status = 'Lactante'
            GROUP BY faixa_del
            ORDER BY faixa_del;
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $resultadoFormatado = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $resultadoFormatado[] = [
                'label' => $row['faixa_del'],
                'value' => round($row['media_producao'], 2)
            ];
        }
        return $resultadoFormatado;
    }

    /**
     * Calcula a produção média de leite por ordem de parto.
     * @param array $filtros Filtros a serem aplicados.
     * @return array
     */
    public function getMediaProducaoPorOrdemDeParto($filtros = []) {
        $query = "
            SELECT 
                partos_cont.ordem_parto,
                AVG(p.producao_total) as media_producao
            FROM producao_leite p
            JOIN (
                SELECT id_vaca, COUNT(id) as ordem_parto
                FROM partos
                GROUP BY id_vaca
            ) as partos_cont ON p.id_gado = partos_cont.id_vaca
            GROUP BY partos_cont.ordem_parto
            ORDER BY partos_cont.ordem_parto;
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        $resultadoFormatado = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $resultadoFormatado[] = [
                'label' => $row['ordem_parto'] . 'º Parto',
                'value' => round($row['media_producao'], 2)
            ];
        }
        return $resultadoFormatado;
    }
    // ### FIM DAS NOVAS FUNÇÕES ###
}
?>