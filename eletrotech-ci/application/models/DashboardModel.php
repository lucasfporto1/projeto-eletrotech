<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DashboardModel extends CI_Model
{
    public function contarTotais()
    {
        $totais = array(
            'eletricistas' => 0,
            'produtos'     => 0,
            'os'           => 0,
            'metas'        => 0.00,
        );

        $totais['eletricistas'] = $this->db
            ->where('data_demissao', null)
            ->count_all_results('tabela_eletricistas');

        $totais['produtos'] = $this->db->count_all('tabela_produtos');
        $totais['os']       = $this->db->count_all('tabela_ordens_servico');

        $linha = $this->db
            ->select_sum('vlr_meta', 'total')
            ->get('tabela_metas')
            ->row();

        $totais['metas'] = $linha->total ?? 0;

        return $totais;
    }

    public function getOsPorEletricista()
    {
        $query = $this->db
            ->select('e.nome AS eletricista, COUNT(os.id) AS total')
            ->from('tabela_ordens_servico os')
            ->join('tabela_eletricistas e', 'e.id = os.eletricista_os', 'left')
            ->group_by('e.id, e.nome')
            ->order_by('total', 'DESC')
            ->get();

        if ($query === false || $query->num_rows() === 0) {
            return [];
        }

        return $query->result_array();
    }

    public function getMovimentacaoPorMes()
    {
        $query = $this->db
            ->select("DATE_FORMAT(data_mov, '%Y-%m') AS mes,
                      SUM(CASE WHEN tipo = 'entrada' THEN quantidade * valor_unitario ELSE 0 END) AS entrada,
                      SUM(CASE WHEN tipo = 'saida'   THEN quantidade * valor_unitario ELSE 0 END) AS saida", false)
            ->from('tabela_movimentacoes')
            ->group_by("DATE_FORMAT(data_mov, '%Y-%m')")
            ->order_by('mes', 'ASC')
            ->get();

        if ($query === false || $query->num_rows() === 0) {
            return [];
        }

        return $query->result_array();
    }

    public function getOsPorStatus()
    {
        $query = $this->db
            ->select('status, COUNT(id) AS total')
            ->from('tabela_ordens_servico')
            ->group_by('status')
            ->get();

        $resultado = array('aberta' => 0, 'fechada' => 0);

        if ($query === false) {
            return $resultado;
        }

        foreach ($query->result_array() as $linha) {
            $resultado[$linha['status']] = (int) $linha['total'];
        }

        return $resultado;
    }

    public function getOsPorMes($mes = null)
    {
        $this->db
            ->select("DATE_FORMAT(data_os, '%Y-%m') AS mes, COUNT(id) AS total", false)
            ->from('tabela_ordens_servico')
            ->group_by("DATE_FORMAT(data_os, '%Y-%m')")
            ->order_by('mes', 'ASC');

        if (!empty($mes)) {
            $this->db->where("DATE_FORMAT(data_os, '%Y-%m') =", $mes);
        }

        $query = $this->db->get();

        if ($query === false || $query->num_rows() === 0) {
            return [];
        }

        return $query->result_array();
    }
}
