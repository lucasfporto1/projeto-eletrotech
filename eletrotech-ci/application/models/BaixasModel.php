<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaixasModel extends CI_Model
{
    public function consultar($filtros)
    {
        $this->db
            ->select('m.id, m.tipo, m.quantidade, m.valor_unitario,
                      (m.quantidade * m.valor_unitario) AS valor_total,
                      m.data_mov, m.origem, p.nome_produto')
            ->from('tabela_movimentacoes m')
            ->join('tabela_produtos p', 'm.id_produto = p.id', 'inner')
            ->order_by('m.data_mov', 'DESC')
            ->order_by('m.id', 'DESC');

        if (!empty($filtros['tipo']) && in_array($filtros['tipo'], array('entrada', 'saida'))) {
            $this->db->where('m.tipo', $filtros['tipo']);
        }
        if (!empty($filtros['id_produto'])) {
            $this->db->where('m.id_produto', (int) $filtros['id_produto']);
        }
        if (!empty($filtros['data_inicio'])) {
            $this->db->where('m.data_mov >=', $filtros['data_inicio']);
        }
        if (!empty($filtros['data_fim'])) {
            $this->db->where('m.data_mov <=', $filtros['data_fim']);
        }

        $query = $this->db->get();
        return $query === false ? array() : $query->result_array();
    }

    public function produtos_lista()
    {
        $query = $this->db
            ->select('id, nome_produto')
            ->order_by('nome_produto', 'ASC')
            ->get('tabela_produtos');

        return $query === false ? array() : $query->result_array();
    }
}
