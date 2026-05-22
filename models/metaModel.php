<?php
require_once '../lib-php/libUtils.php';

class MetaModel
{
    private $db;

    public function __construct()
    {
        $this->db = ConexaoBanco::connect();
    }

    public function cadastrarMeta($eletricista, $mes, $vlr)
    {
        $sql = "INSERT INTO tabela_metas (eletricista_meta, mes_meta, vlr_meta) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("isd", $eletricista, $mes, $vlr);

        return $stmt->execute();
    }

    public function atualizarMeta($id, $vlr_meta)
    {
        $sql = "UPDATE tabela_metas SET vlr_meta = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("di", $vlr_meta, $id);

        return $stmt->execute();
    }
}
