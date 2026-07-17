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
}
