<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HomeController extends Auth_Controller
{
    public function index()
    {
        $this->load->view('telas/HomeView', array(
            'usuario' => $this->session->userdata('usuario'),
        ));
    }
}
