<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaixasController extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('BaixasModel');
    }

    public function index()
    {
        $consultou = $this->input->get('consultar') !== null;

        $filtros = array(
            'tipo'        => $this->input->get('tipo', true) ?: '',
            'id_produto'  => $this->input->get('id_produto', true) ?: '',
            'data_inicio' => $this->input->get('data_inicio', true) ?: '',
            'data_fim'    => $this->input->get('data_fim', true) ?: '',
        );

        $data = array(
            'titulo'        => 'Baixas / Movimentações - EletroTech',
            'produtos'      => $this->BaixasModel->produtos_lista(),
            'consultou'     => $consultou,
            'filtros'       => $filtros,
            'movimentacoes' => array(),
            'totais'        => array('total_entrada' => 0, 'total_saida' => 0),
            'total_rows'    => 0,
            'offset'        => 0,
            'por_pagina'    => self::POR_PAGINA_PADRAO,
            'paginacao'     => '',
        );

        if ($consultou) {
            $total = $this->BaixasModel->contar($filtros);
            $data  = array_merge($data, $this->paginar($total, site_url('baixas')));

            $data['movimentacoes'] = $this->BaixasModel->consultar($filtros, $data['por_pagina'], $data['offset']);
            $data['totais']        = $this->BaixasModel->totais($filtros);
        }

        $this->load->view('telas/BaixasView', $data);
    }

    public function detalhes($id = null)
    {
        if (empty($id) || !is_numeric($id)) {
            show_404();
            return;
        }

        $movimentacao = $this->BaixasModel->detalhe($id);

        if (empty($movimentacao)) {
            show_404();
            return;
        }

        $data = array(
            'titulo'       => 'Detalhes da Baixa - EletroTech',
            'movimentacao' => $movimentacao,
            'materiais'    => array(),
            'respostas'    => array(),
        );

        if (!empty($movimentacao['id_os'])) {
            $this->load->model('OrdemservicoModel');
            $data['materiais'] = $this->OrdemservicoModel->get_materiais_by_os($movimentacao['id_os']);
            $data['respostas'] = $this->OrdemservicoModel->get_checklist_respostas_by_os($movimentacao['id_os']);
        }

        $this->load->view('telas/BaixaDetalheView', $data);
    }
}
