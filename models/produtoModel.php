<?php
require_once '../lib-php/libUtils.php';

class ProdutoModel
{
    private $db;

    public function __construct()
    {
        $this->db = ConexaoBanco::connect();
    }

    public function cadastrarProduto($nome, $preco, $qtd)
    {
        $sql = "INSERT INTO tabela_produtos (nome_produto, vlr_unitario, qtd_estoque) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sdi", $nome, $preco, $qtd);

        return $stmt->execute();
    }

    public function atualizarProduto($id, $nomeProduto, $vlrUnitario)
    {
        $sql = "UPDATE tabela_produtos SET nome_produto = ?, vlr_unitario = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sdi", $nomeProduto, $vlrUnitario, $id);

        return $stmt->execute();
    }
}
