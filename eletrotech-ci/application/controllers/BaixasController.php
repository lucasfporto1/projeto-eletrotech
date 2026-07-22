<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BaixasController extends Auth_Controller
{
    const POR_PAGINA = 10;

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
            'por_pagina'    => self::POR_PAGINA,
            'paginacao'     => '',
        );

        if ($consultou) {
            $offset = (int) $this->input->get('per_page');
            $total  = $this->BaixasModel->contar($filtros);

            $data['movimentacoes'] = $this->BaixasModel->consultar($filtros, self::POR_PAGINA, $offset);
            $data['totais']        = $this->BaixasModel->totais($filtros);
            $data['total_rows']    = $total;
            $data['offset']        = $offset;
            $data['paginacao']     = $this->montar_paginacao($total);
        }

        $this->load->view('telas/BaixasView', $data);
    }


    private function montar_paginacao($total)
    {
        $config = array(
            'base_url'            => site_url('baixas'),
            'total_rows'          => $total,
            'per_page'            => self::POR_PAGINA,
            'page_query_string'   => TRUE,
            'query_string_segment' => 'per_page',
            'reuse_query_string'  => TRUE,
            'num_links'           => 2,
            'first_link'          => 'Primeira',
            'last_link'           => 'Última',
            'prev_link'           => '&laquo;',
            'next_link'           => '&raquo;',
            'full_tag_open'       => '<ul class="pagination justify-content-center mb-0">',
            'full_tag_close'      => '</ul>',
            'first_tag_open'      => '<li class="page-item">',
            'first_tag_close'     => '</li>',
            'last_tag_open'       => '<li class="page-item">',
            'last_tag_close'      => '</li>',
            'prev_tag_open'       => '<li class="page-item">',
            'prev_tag_close'      => '</li>',
            'next_tag_open'       => '<li class="page-item">',
            'next_tag_close'      => '</li>',
            'num_tag_open'        => '<li class="page-item">',
            'num_tag_close'       => '</li>',
            'cur_tag_open'        => '<li class="page-item active"><span class="page-link">',
            'cur_tag_close'       => '</span></li>',
            'attributes'          => array('class' => 'page-link'),
        );

        $this->pagination->initialize($config);

        return $this->pagination->create_links();
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
