<?php
require_once '../lib-php/libUtils.php';

class OrdensServicoModel
{
    private $db;

    public function __construct()
    {
        $this->db = ConexaoBanco::connect();
    }

    public function criarOrdensServico($eletricista_os, $data_os, $materiais)
    {
        $this->db->begin_transaction();

        try {
            $sqlOS = "INSERT INTO tabela_ordens_servico (eletricista_os, data_os) VALUES (?, ?)";
            $stmtOS = $this->db->prepare($sqlOS);
            $stmtOS->bind_param("is", $eletricista_os, $data_os);
            $stmtOS->execute();
            $id_os = $this->db->insert_id;

            $sqlMaterial = "INSERT INTO tabela_os_materiais (id_os, id_produto, qtd_utilizada) VALUES (?, ?, ?)";
            $stmtMaterial = $this->db->prepare($sqlMaterial);

            $sqlEstoque = "UPDATE tabela_produtos SET qtd_estoque = qtd_estoque - ? WHERE id = ?";
            $stmtEstoque = $this->db->prepare($sqlEstoque);

            foreach ($materiais as $material) {
                $id_produto = $material['id_produto'];
                $qtd_utilizada = $material['qtd_utilizada'];

                $stmtMaterial->bind_param("iii", $id_os, $id_produto, $qtd_utilizada);
                $stmtMaterial->execute();

                $stmtEstoque->bind_param("ii", $qtd_utilizada, $id_produto);
                $stmtEstoque->execute();
            }

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    public function verificarEstoque($id_produto, $qtd_solicitada)
    {
        $sql = "SELECT qtd_estoque, nome_produto FROM tabela_produtos WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id_produto);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($linha = $resultado->fetch_assoc()) {
            if ($linha['qtd_estoque'] < $qtd_solicitada) {
                return $linha['nome_produto'];
            }
            return true;
        }
        return false;
    }
}
