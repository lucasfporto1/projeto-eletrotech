<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ProdutosModel extends CI_Model
{
    protected $table = 'tabela_produtos';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $query = $this->db->order_by('id', 'DESC')->get($this->table);

        if ($query === false) {
            return [];
        }

        return $query->result_array();
    }

    public function get_by_id($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row_array();
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

    public function zerarEstoque($id)
    {
        return $this->db->update($this->table, ['qtd_estoque' => 0], ['id' => $id]);
    }
}