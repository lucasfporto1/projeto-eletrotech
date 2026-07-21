<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrdemServicoModel extends CI_Model
{
    protected $table = 'tabela_ordens_servico';
    protected $tabelaPivot = 'tabela_os_materiais';
    protected $tableRespostas = 'tabela_os_checklist_respostas';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all()
    {
        $query = $this->db->select('os.id, os.data_os, os.data_fechamento, os.status, e.nome as nome_eletricista')
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

    public function get_checklist_respostas_by_os($idOs)
    {
        $query = $this->db->select('r.id_pergunta, p.texto_pergunta, r.resposta, r.motivo_nao')
            ->from($this->tableRespostas . ' r')
            ->join('tabela_checklist_perguntas p', 'r.id_pergunta = p.id', 'inner')
            ->where('r.id_os', $idOs)
            ->order_by('p.ordem', 'ASC')
            ->get();

        if ($query === false) {
            return [];
        }

        return $query->result_array();
    }

    public function registrar_os($eletricistaId, $dataOs, array $produtos, array $respostas = [])
    {
        $this->db->trans_start();

        $this->db->insert($this->table, [
            'eletricista_os' => $eletricistaId,
            'data_os' => $dataOs,
            'status' => 'aberta',
        ]);
        $idOs = $this->db->insert_id();

        $insufficientStock = false;
        foreach ($produtos as $item) {
            $produtoId = (int) $item['id'];
            $qtd = (int) $item['qtd'];

            $produto = $this->db->select('qtd_estoque')
                ->where('id', $produtoId)
                ->get('tabela_produtos')
                ->row_array();

            if (!$produto || $produto['qtd_estoque'] < $qtd) {
                $insufficientStock = true;
                break;
            }

            $this->db->insert($this->tabelaPivot, [
                'id_os' => $idOs,
                'id_produto' => $produtoId,
                'qtd_utilizada' => $qtd,
            ]);

            $this->db->set('qtd_estoque', 'qtd_estoque - ' . $qtd, FALSE)
                ->where('id', $produtoId)
                ->update('tabela_produtos');
        }

        if ($insufficientStock) {
            $this->db->trans_rollback();
            $this->db->trans_complete();
            return false;
        }

        if ($this->db->trans_status() !== FALSE && !empty($respostas)) {
            foreach ($respostas as $perguntaId => $resposta) {
                $this->db->insert($this->tableRespostas, [
                    'id_os' => $idOs,
                    'id_pergunta' => (int) $perguntaId,
                    'resposta' => $resposta,
                ]);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        }

        return $idOs;
    }

    public function fechar_os($idOs, array $respostas)
    {
        $ordem = $this->db->where('id', $idOs)->where('status', 'aberta')->get($this->table)->row_array();

        if (empty($ordem)) {
            return false;
        }

        $this->db->trans_start();

        $this->db->where('id', $idOs)->update($this->table, [
            'status' => 'fechada',
            'data_fechamento' => date('Y-m-d'),
        ]);

        foreach ($respostas as $perguntaId => $respostaData) {
            $this->db->insert($this->tableRespostas, [
                'id_os' => $idOs,
                'id_pergunta' => (int) $perguntaId,
                'resposta' => $respostaData['resposta'],
                'motivo_nao' => isset($respostaData['motivo_nao']) ? $respostaData['motivo_nao'] : null,
            ]);
        }

        $this->db->trans_complete();

        return $this->db->trans_status() !== FALSE;
    }
}
