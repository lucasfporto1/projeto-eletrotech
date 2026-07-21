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
            'movimentacoes' => $consultou ? $this->BaixasModel->consultar($filtros) : array(),
        );

        $this->load->view('telas/BaixasView', $data);
    }
}
