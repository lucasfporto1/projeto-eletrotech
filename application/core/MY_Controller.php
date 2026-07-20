<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CI_Loader $load
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Session $session
 * @property CI_DB_query_builder $db
 * @property CI_Form_validation $form_validation
 * @property UsuarioModel $usuarios
 * @property DashboardModel $dashboard
 */
class MY_Controller extends CI_Controller
{
}

// Controllers de páginas que exigem login estendem esta classe (antigo checkSession)
class Auth_Controller extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $userId = $this->session->userdata('user_id');

        if (!$userId) {
            redirect('auth');
        }

        $this->load->model('UsuarioModel', 'usuarios');

        if (!$this->usuarios->buscarUsuarioPorId($userId)) {
            $this->session->sess_destroy();
            redirect('auth');
        }
    }
}
