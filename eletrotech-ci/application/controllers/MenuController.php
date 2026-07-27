<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MenuController extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->exigirPermissao('menu');
    }

    public function index()
    {
        $this->load->model('DashboardModel', 'dashboard');

        $mesFiltro = $this->input->get('mes', TRUE);

        $dados = array(
            'totais'             => $this->dashboard->contarTotais(),
            'usuario'            => $this->session->userdata('usuario'),
            'mesFiltro'          => $mesFiltro ?? '',
            'graficoEletricista' => $this->dashboard->getOsPorEletricista(),
            'graficoMes'         => $this->dashboard->getOsPorMes($mesFiltro),
            'graficoStatus'      => $this->dashboard->getOsPorStatus(),
            'graficoMovimentacao' => $this->dashboard->getMovimentacaoPorMes(),
        );

        $this->load->view('telas/MenuView', $dados);
    }

}
