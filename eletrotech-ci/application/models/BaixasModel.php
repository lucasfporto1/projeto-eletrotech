<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaixasModel extends CI_Model
{

    private function aplicar_filtros($filtros)
    {
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
    }

    public function consultar($filtros, $limite = null, $offset = 0)
    {
        $this->db
            ->select('m.id, m.tipo, m.quantidade, m.valor_unitario,
                      (m.quantidade * m.valor_unitario) AS valor_total,
                      m.data_mov, m.origem, p.nome_produto')
            ->from('tabela_movimentacoes m')
            ->join('tabela_produtos p', 'm.id_produto = p.id', 'inner')
            ->order_by('m.data_mov', 'DESC')
            ->order_by('m.id', 'DESC');

        $this->aplicar_filtros($filtros);

        if ($limite !== null) {
            $this->db->limit($limite, $offset);
        }

        $query = $this->db->get();
        return $query === false ? array() : $query->result_array();
    }

    public function contar($filtros)
    {
        $this->db->from('tabela_movimentacoes m');
        $this->aplicar_filtros($filtros);

        return $this->db->count_all_results();
    }


    public function totais($filtros)
    {
        $this->db
            ->select("IFNULL(SUM(CASE WHEN m.tipo = 'entrada' THEN m.quantidade * m.valor_unitario END), 0) AS total_entrada,
                      IFNULL(SUM(CASE WHEN m.tipo = 'saida'   THEN m.quantidade * m.valor_unitario END), 0) AS total_saida")
            ->from('tabela_movimentacoes m');

        $this->aplicar_filtros($filtros);

        $query = $this->db->get();

        if ($query === false) {
            return array('total_entrada' => 0, 'total_saida' => 0);
        }

        return $query->row_array();
    }


    public function consultar_por_ids(array $ids)
    {
        if (empty($ids)) {
            return array();
        }

        $this->db
            ->select('m.id, m.tipo, m.quantidade, m.valor_unitario,
                      (m.quantidade * m.valor_unitario) AS valor_total,
                      m.data_mov, m.origem, p.nome_produto,
                      CASE WHEN m.tipo = "entrada" THEN (m.quantidade * m.valor_unitario) ELSE 0 END AS valor_entrada,
                      CASE WHEN m.tipo = "saida" THEN (m.quantidade * m.valor_unitario) ELSE 0 END AS valor_saida')
            ->from('tabela_movimentacoes m')
            ->join('tabela_produtos p', 'm.id_produto = p.id', 'inner')
            ->where_in('m.id', array_map('intval', $ids))
            ->order_by('m.data_mov', 'DESC')
            ->order_by('m.id', 'DESC');

        $query = $this->db->get();

        return $query === false ? array() : $query->result_array();
    }

    public function detalhe($id)
    {
        $query = $this->db
            ->select('m.id, m.tipo, m.quantidade, m.valor_unitario,
                      (m.quantidade * m.valor_unitario) AS valor_total,
                      m.data_mov, m.origem, m.id_os,
                      p.nome_produto, p.vlr_unitario AS vlr_atual, p.qtd_estoque,
                      os.data_os, os.status, os.data_fechamento,
                      e.nome AS nome_eletricista')
            ->from('tabela_movimentacoes m')
            ->join('tabela_produtos p', 'm.id_produto = p.id', 'inner')
            ->join('tabela_ordens_servico os', 'm.id_os = os.id', 'left')
            ->join('tabela_eletricistas e', 'os.eletricista_os = e.id', 'left')
            ->where('m.id', (int) $id)
            ->get();

        if ($query === false) {
            return null;
        }

        return $query->row_array();
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
