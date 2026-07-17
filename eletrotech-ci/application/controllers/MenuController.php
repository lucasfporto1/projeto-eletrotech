<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MenuController extends Auth_Controller
{
    public function index()
    {
        $this->load->model('DashboardModel', 'dashboard');

        $dados = array(
            'totais'  => $this->dashboard->contarTotais(),
            'usuario' => $this->session->userdata('usuario'),
        );

        $this->load->view('telas/MenuView', $dados);
    }
}
