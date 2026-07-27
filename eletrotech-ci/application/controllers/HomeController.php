<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HomeController extends Auth_Controller
{
    public function index()
    {
        if (!$this->ehAdmin) {
            redirect(rota_inicial($this->ehAdmin, $this->permissoes) ?? 'auth/sair');
            return;
        }

        $this->load->view('telas/HomeView', array(
            'usuario' => $this->session->userdata('usuario'),
        ));
    }
}
