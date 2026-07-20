<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EletricistasModel extends CI_Model
{
    protected $table = 'tabela_eletricistas';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $mesAtual = date('Y-m');
        $sql = "SELECT e.*,
                    (SELECT COUNT(*) FROM tabela_ordens_servico WHERE eletricista_os = e.id) as total_os,
                    (SELECT vlr_meta FROM tabela_metas
                        WHERE eletricista_meta = e.id
                        AND DATE_FORMAT(mes_meta, '%Y-%m') = ?
                        LIMIT 1) as meta_atual
                FROM tabela_eletricistas e
                ORDER BY e.id DESC";

        $query = $this->db->query($sql, [$mesAtual]);

        if ($query === false) {
            return [];
        }

        return $query->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
    }

    public function get_by_cpf($cpf)
    {
        return $this->db->get_where($this->table, ['cpf' => $cpf])->row_array();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function demitir($id)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, [
            'data_demissao' => date('Y-m-d')
        ]);
    }

    public function reativar($id)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, [
            'data_demissao' => null
        ]);
    }
}