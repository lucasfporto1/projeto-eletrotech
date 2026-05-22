<?php
require_once '../lib-php/libUtils.php';

class EletricistaModel
{
    private $db;

    public function __construct()
    {
        $this->db = ConexaoBanco::connect();
    }

    public function atualizarEletricista($id, $nome)
    {
        $sql = "UPDATE tabela_eletricistas SET nome = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $nome, $id);

        return $stmt->execute();
    }

    public function reativarEletricista($id)
    {
        $sql = "UPDATE tabela_eletricistas SET data_demissao = NULL WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }
    public function cadastrarEletricista($cpf, $nome, $data_contratacao)
    {
        $sql = "INSERT INTO tabela_eletricistas (cpf, nome, data_contratacao) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sss", $cpf, $nome, $data_contratacao);

        return $stmt->execute();
    }
}
