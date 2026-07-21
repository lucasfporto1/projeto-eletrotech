<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrdemServicoModel extends CI_Model
{
    protected $table = 'tabela_ordens_servico';
    protected $tabelaPivot = 'tabela_os_materiais';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $query = $this->db->select('os.id, os.data_os, e.nome as nome_eletricista')
            ->from('tabela_ordens_servico os')
            ->join('tabela_eletricistas e', 'os.eletricista_os = e.id', 'inner')
            ->order_by('os.id', 'DESC')
            ->get();

        if ($query === false) {
            return [];
        }

        return $query->result_array();
    }

    public function get_eletricistas_ativos()
    {
        $query = $this->db->select('id, nome')
            ->where('data_demissao', null)
            ->order_by('nome', 'ASC')
            ->get('tabela_eletricistas');

        if ($query === false) {
            return [];
        }

        return $query->result_array();
    }

    public function get_produtos_disponiveis()
    {
        $query = $this->db->select('id, nome_produto, qtd_estoque')
            ->where('qtd_estoque >', 0)
            ->order_by('nome_produto', 'ASC')
            ->get('tabela_produtos');

        if ($query === false) {
            return [];
        }

        return $query->result_array();
    }

    public function get_materiais_by_os($idOs)
    {
        $query = $this->db->select('p.nome_produto, mp.qtd_utilizada')
            ->from($this->tabelaPivot . ' mp')
            ->join('tabela_produtos p', 'mp.id_produto = p.id', 'inner')
            ->where('mp.id_os', $idOs)
            ->get();

        if ($query === false) {
            return [];
        }

        return $query->result_array();
    }

    /**
     * Registra a OS, os materiais utilizados e dá baixa no estoque,
     *
     * @param int    $eletricistaId
     * @param string $dataOs
     * @param array  $produtos  ex: [['id' => 3, 'qtd' => 5], ['id' => 7, 'qtd' => 2]]
     * @return int|false  ID da nova OS, ou false em caso de falha/estoque insuficiente
     */
    public function registrar_os($eletricistaId, $dataOs, array $produtos)
    {
        $this->db->trans_start();

        $this->db->insert($this->table, [
            'eletricista_os' => $eletricistaId,
            'data_os' => $dataOs,
        ]);
        $idOs = $this->db->insert_id();

        foreach ($produtos as $item) {
            $produtoId = (int) $item['id'];
            $qtd       = (int) $item['qtd'];

            $produto = $this->db->select('qtd_estoque, vlr_unitario')
                ->where('id', $produtoId)
                ->get('tabela_produtos')
                ->row_array();

            if (!$produto || $produto['qtd_estoque'] < $qtd) {
                $this->db->trans_status(FALSE);
                break;
            }

            $this->db->insert($this->tabelaPivot, [
                'id_os'         => $idOs,
                'id_produto'    => $produtoId,
                'qtd_utilizada' => $qtd,
            ]);

            $this->db->set('qtd_estoque', 'qtd_estoque - ' . $qtd, FALSE)
                ->where('id', $produtoId)
                ->update('tabela_produtos');


            $this->db->insert('tabela_movimentacoes', [
                'id_produto'     => $produtoId,
                'tipo'           => 'saida',
                'quantidade'     => $qtd,
                'valor_unitario' => $produto['vlr_unitario'],
                'data_mov'       => $dataOs,
                'origem'         => 'OS #' . str_pad($idOs, 5, '0', STR_PAD_LEFT),
                'id_os'          => $idOs,
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $idOs;
    }
}
