<?php
class ParcelaFinanceira {
    private $conn;
    private $table_name = "financeiro_parcelas";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function readByLancamentoId($id_lancamento) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_lancamento = :id_lancamento ORDER BY numero_parcela ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_lancamento', $id_lancamento);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function marcarComoPaga($id_parcela, $data_pagamento, $forma_pagamento, $valor_pago) {
        // ATENÇÃO: Adicione a coluna 'valor_pago' (ex: DECIMAL(10,2)) à sua tabela 'financeiro_parcelas'.
        $query = "UPDATE " . $this->table_name . " 
                  SET status = 'Pago', data_pagamento = :data_pagamento, forma_pagamento = :forma_pagamento, valor_pago = :valor_pago
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':data_pagamento', $data_pagamento);
        $stmt->bindParam(':forma_pagamento', $forma_pagamento);
        $stmt->bindParam(':valor_pago', $valor_pago);
        $stmt->bindParam(':id', $id_parcela);

        return $stmt->execute();
    }
}
?>
