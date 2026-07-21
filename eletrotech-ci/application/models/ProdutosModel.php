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
        $ok = $this->db->insert($this->table, $data);

        if ($ok && (int) ($data['qtd_estoque'] ?? 0) > 0) {
            $this->registrarMovimentacao(
                $this->db->insert_id(),
                'entrada',
                (int) $data['qtd_estoque'],
                $data['vlr_unitario'] ?? 0,
                'Estoque inicial (cadastro)'
            );
        }

        return $ok;
    }

    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }

    public function zerarEstoque($id)
    {
        $produto = $this->get_by_id($id);

        if (!$produto) {
            return false;
        }

        $qtdAtual = (int) $produto['qtd_estoque'];
        $ok = $this->db->update($this->table, ['qtd_estoque' => 0], ['id' => $id]);

        if ($ok && $qtdAtual > 0) {
            $this->registrarMovimentacao(
                $id,
                'saida',
                $qtdAtual,
                $produto['vlr_unitario'] ?? 0,
                'Baixa manual de estoque'
            );
        }

        return $ok;
    }

    public function aumentarQtdEstoque($id, $quantidade)
    {
        $this->db->set('qtd_estoque', 'qtd_estoque + ' . (int)$quantidade, FALSE);
        $this->db->where('id', $id);
        $ok = $this->db->update($this->table);

        if ($ok) {
            $produto = $this->get_by_id($id);
            $this->registrarMovimentacao(
                $id,
                'entrada',
                (int) $quantidade,
                $produto['vlr_unitario'] ?? 0,
                'Reposição de estoque'
            );
        }

        return $ok;
    }

    /**
     * Grava uma movimentação no ledger (tabela_movimentacoes),
     * que alimenta a tela de Baixas.
     */
    private function registrarMovimentacao($idProduto, $tipo, $qtd, $valorUnitario, $origem, $idOs = null)
    {
        $this->db->insert('tabela_movimentacoes', array(
            'id_produto'     => (int) $idProduto,
            'tipo'           => $tipo,
            'quantidade'     => (int) $qtd,
            'valor_unitario' => $valorUnitario,
            'data_mov'       => date('Y-m-d'),
            'origem'         => $origem,
            'id_os'          => $idOs,
        ));
    }
}