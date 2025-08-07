<?php
require_once __DIR__ . '/Estoque.php';

class LancamentoFinanceiro {
    private $conn;
    private $table_name = "financeiro_lancamentos";
    private $estoqueModel;
    public $id;
    public $descricao;
    public $valor_total;
    public $tipo;
    public $id_contato;
    public $id_categoria;
    public $observacoes;
    public $nome_contato;
    public $nome_categoria;

    public function __construct($db) {
        $this->conn = $db;
        $this->estoqueModel = new Estoque($db);
    }

    public function read($filters = []) {
        $query = "SELECT
                    l.id, l.descricao, l.valor_total, l.tipo,
                    c.nome as nome_contato,
                    child_cat.nome as nome_categoria,
                    parent_cat.nome as nome_categoria_pai,
                    (SELECT COUNT(*) FROM financeiro_parcelas WHERE id_lancamento = l.id) as total_parcelas,
                    (SELECT COUNT(*) FROM financeiro_parcelas WHERE id_lancamento = l.id AND status = 'Pago') as parcelas_pagas,
                    (SELECT COUNT(*) FROM financeiro_lancamento_itens WHERE id_lancamento = l.id) as item_count
                  FROM " . $this->table_name . " l
                  LEFT JOIN financeiro_contatos c ON l.id_contato = c.id
                  LEFT JOIN financeiro_categorias child_cat ON l.id_categoria = child_cat.id
                  LEFT JOIN financeiro_categorias parent_cat ON child_cat.parent_id = parent_cat.id";

        if (!empty($filters['search_query'])) {
            $query .= " WHERE l.descricao LIKE :search_query OR child_cat.nome LIKE :search_query OR parent_cat.nome LIKE :search_query OR c.nome LIKE :search_query";
        }
        
        $query .= " ORDER BY l.id DESC";
        
        $stmt = $this->conn->prepare($query);

        if (!empty($filters['search_query'])) {
            $searchTerm = '%' . $filters['search_query'] . '%';
            $stmt->bindParam(':search_query', $searchTerm);
        }

        $stmt->execute();
        return $stmt;
    }
    
    public function readOne() {
        $query = "SELECT l.*, c.nome as nome_contato, cat.nome as nome_categoria FROM " . $this->table_name . " l
                  LEFT JOIN financeiro_contatos c ON l.id_contato = c.id
                  LEFT JOIN financeiro_categorias cat ON l.id_categoria = cat.id
                  WHERE l.id = ? LIMIT 1";
                  
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row) {
            foreach ($row as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
            return true;
        }
        return false;
    }

    public function getItens() {
        $query = "SELECT 
                    i.*, 
                    e.Produto as nome_produto 
                  FROM 
                    financeiro_lancamento_itens i
                  JOIN 
                    estoque e ON i.id_produto_estoque = e.Id
                  WHERE 
                    i.id_lancamento = :id_lancamento";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_lancamento', $this->id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($num_parcelas, $data_vencimento_inicial, $forma_pagamento, $itens_compra = [], $id_produto_simples = null) {
        $this->conn->beginTransaction();
        try {
            if (!empty($id_produto_simples) && empty($itens_compra)) {
                $stmt_get_cat = $this->conn->prepare("SELECT id_categoria_financeira FROM estoque WHERE Id = :id_produto");
                $stmt_get_cat->execute([':id_produto' => $id_produto_simples]);
                $this->id_categoria = ($stmt_get_cat->fetch(PDO::FETCH_ASSOC))['id_categoria_financeira'] ?? null;
            }

            $query_lancamento = "INSERT INTO " . $this->table_name . " SET descricao=:descricao, valor_total=:valor_total, tipo=:tipo, id_contato=:id_contato, id_categoria=:id_categoria, observacoes=:observacoes";
            $stmt_lancamento = $this->conn->prepare($query_lancamento);
            
            $this->descricao = htmlspecialchars(strip_tags($this->descricao));
            $this->valor_total = str_replace(',', '.', $this->valor_total);
            $this->tipo = htmlspecialchars(strip_tags($this->tipo));
            $this->id_contato = empty($this->id_contato) ? null : htmlspecialchars(strip_tags($this->id_contato));
            $this->id_categoria = empty($this->id_categoria) ? null : htmlspecialchars(strip_tags($this->id_categoria));
            $this->observacoes = htmlspecialchars(strip_tags($this->observacoes));

            $stmt_lancamento->bindParam(":descricao", $this->descricao);
            $stmt_lancamento->bindParam(":valor_total", $this->valor_total);
            $stmt_lancamento->bindParam(":tipo", $this->tipo);
            $stmt_lancamento->bindParam(":id_contato", $this->id_contato);
            $stmt_lancamento->bindParam(":id_categoria", $this->id_categoria);
            $stmt_lancamento->bindParam(":observacoes", $this->observacoes);

            if (!$stmt_lancamento->execute()) throw new Exception("Erro ao salvar o lançamento principal.");
            $this->id = $this->conn->lastInsertId();

            if (!empty($itens_compra)) {
                $query_item = "INSERT INTO financeiro_lancamento_itens (id_lancamento, id_produto_estoque, quantidade, valor_unitario) VALUES (:id_lancamento, :id_produto, :qtd, :valor)";
                $stmt_item = $this->conn->prepare($query_item);
                
                foreach ($itens_compra as $item) {
                    $stmt_item->execute([
                        ':id_lancamento' => $this->id,
                        ':id_produto' => $item['produto_id'],
                        ':qtd' => str_replace(',', '.', $item['quantidade']),
                        ':valor' => str_replace(',', '.', $item['valor_unitario'])
                    ]);
                }
            }
            
            if ($num_parcelas > 0 && (float)$this->valor_total > 0) {
                $valor_parcela = round((float)$this->valor_total / $num_parcelas, 2);
                $query_parcela = "INSERT INTO financeiro_parcelas (id_lancamento, numero_parcela, valor_parcela, data_vencimento, status, forma_pagamento) VALUES (:id_lancamento, :numero_parcela, :valor_parcela, :data_vencimento, :status, :forma_pagamento)";
                $stmt_parcela = $this->conn->prepare($query_parcela);
                $data_vencimento = new DateTime($data_vencimento_inicial);

                for ($i = 1; $i <= $num_parcelas; $i++) {
                    $status_parcela = 'Aberto';
                    if ($forma_pagamento === 'Desconto no Leite') {
                        $status_parcela = 'Pago';
                    }

                    $stmt_parcela->execute([
                        ':id_lancamento' => $this->id,
                        ':numero_parcela' => $i,
                        ':valor_parcela' => $valor_parcela,
                        ':data_vencimento' => $data_vencimento->format('Y-m-d'),
                        ':status' => $status_parcela,
                        ':forma_pagamento' => $forma_pagamento
                    ]);
                    $data_vencimento->modify('+1 month');
                }
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Erro em LancamentoFinanceiro->create(): " . $e->getMessage());
            return false;
        }
    }

    public function update($num_parcelas, $data_vencimento_inicial, $forma_pagamento, $itens_compra = []) {
        $this->conn->beginTransaction();

        try {
            $query_update_lancamento = "UPDATE " . $this->table_name . " SET 
                                            descricao=:descricao, valor_total=:valor_total, 
                                            tipo=:tipo, id_contato=:id_contato, id_categoria=:id_categoria, observacoes=:observacoes
                                        WHERE id=:id";
            
            $stmt_update_lancamento = $this->conn->prepare($query_update_lancamento);
            
            $this->id = htmlspecialchars(strip_tags($this->id));
            $this->descricao = htmlspecialchars(strip_tags($this->descricao));
            $this->valor_total = str_replace(',', '.', $this->valor_total);
            $this->tipo = htmlspecialchars(strip_tags($this->tipo));
            $this->id_contato = empty($this->id_contato) ? null : htmlspecialchars(strip_tags($this->id_contato));
            $this->id_categoria = empty($this->id_categoria) ? null : htmlspecialchars(strip_tags($this->id_categoria));
            $this->observacoes = htmlspecialchars(strip_tags($this->observacoes));

            $stmt_update_lancamento->bindParam(":descricao", $this->descricao);
            $stmt_update_lancamento->bindParam(":valor_total", $this->valor_total);
            $stmt_update_lancamento->bindParam(":tipo", $this->tipo);
            $stmt_update_lancamento->bindParam(":id_contato", $this->id_contato);
            $stmt_update_lancamento->bindParam(":id_categoria", $this->id_categoria);
            $stmt_update_lancamento->bindParam(":observacoes", $this->observacoes);
            $stmt_update_lancamento->bindParam(":id", $this->id);
            if (!$stmt_update_lancamento->execute()) throw new Exception("Erro ao atualizar o lançamento principal.");

            $stmt_delete_parcelas = $this->conn->prepare("DELETE FROM financeiro_parcelas WHERE id_lancamento = :id");
            $stmt_delete_parcelas->execute([':id' => $this->id]);
            
            $stmt_delete_itens = $this->conn->prepare("DELETE FROM financeiro_lancamento_itens WHERE id_lancamento = :id");
            $stmt_delete_itens->execute([':id' => $this->id]);

            if (!empty($itens_compra)) {
                $query_item = "INSERT INTO financeiro_lancamento_itens (id_lancamento, id_produto_estoque, quantidade, valor_unitario) VALUES (:id_lancamento, :id_produto, :qtd, :valor)";
                $stmt_item = $this->conn->prepare($query_item);
                
                foreach ($itens_compra as $item) {
                    $stmt_item->execute([
                        ':id_lancamento' => $this->id,
                        ':id_produto' => $item['produto_id'],
                        ':qtd' => str_replace(',', '.', $item['quantidade']),
                        ':valor' => str_replace(',', '.', $item['valor_unitario'])
                    ]);
                }
            }
            
            if ($num_parcelas > 0 && (float)$this->valor_total > 0) {
                $valor_parcela = round((float)$this->valor_total / $num_parcelas, 2);
                $query_parcela = "INSERT INTO financeiro_parcelas (id_lancamento, numero_parcela, valor_parcela, data_vencimento, status, forma_pagamento) VALUES (:id_lancamento, :numero_parcela, :valor_parcela, :data_vencimento, :status, :forma_pagamento)";
                $stmt_parcela = $this->conn->prepare($query_parcela);
                $data_vencimento = new DateTime($data_vencimento_inicial);

                for ($i = 1; $i <= $num_parcelas; $i++) {
                    $status_parcela = 'Aberto';
                    if ($forma_pagamento === 'Desconto no Leite') {
                        $status_parcela = 'Pago';
                    }

                    $stmt_parcela->execute([
                        ':id_lancamento' => $this->id,
                        ':numero_parcela' => $i,
                        ':valor_parcela' => $valor_parcela,
                        ':data_vencimento' => $data_vencimento->format('Y-m-d'),
                        ':status' => $status_parcela,
                        ':forma_pagamento' => $forma_pagamento
                    ]);
                    $data_vencimento->modify('+1 month');
                }
            }

            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Erro em LancamentoFinanceiro->update(): " . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        $this->conn->beginTransaction();
        try {
            $stmt_itens = $this->conn->prepare("DELETE FROM financeiro_lancamento_itens WHERE id_lancamento = :id");
            $stmt_itens->execute([':id' => $id]);
            $stmt_parcelas = $this->conn->prepare("DELETE FROM financeiro_parcelas WHERE id_lancamento = :id");
            $stmt_parcelas->execute([':id' => $id]);
            $stmt_lancamento = $this->conn->prepare("DELETE FROM " . $this->table_name . " WHERE id = :id");
            $stmt_lancamento->execute([':id' => $id]);
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Erro em LancamentoFinanceiro->delete(): " . $e->getMessage());
            return false;
        }
    }
}