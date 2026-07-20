<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AuthController extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UsuarioModel', 'usuarios');
        $this->load->library('form_validation');
    }

    public function index()
    {
        if ($this->session->userdata('user_id')) {
            redirect('home');
        }

        $this->load->view('telas/LoginView');
    }

    public function entrar()
    {
        if ($this->input->method() !== 'post') {
            redirect('auth');
        }

        if ($this->estaBloqueado('login', 5, 60)) {
            $this->session->set_flashdata('erro', 'Muitas tentativas falhadas. Tente novamente mais tarde.');
            redirect('auth');
        }

        $this->form_validation->set_rules('nome', 'Nome de Usuário', 'trim|required');
        $this->form_validation->set_rules('senha', 'Senha', 'required');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('erro', 'Por favor, preencha todos os campos.');
            redirect('auth');
        }

        $nomeUsuario = $this->input->post('nome');
        $senha       = $this->input->post('senha');

        $usuario = $this->usuarios->buscarUsuarioPorNome($nomeUsuario);

        if ($usuario && password_verify($senha, $usuario->senha)) {
            $this->session->set_userdata(array(
                'user_id' => $usuario->id,
                'usuario' => $nomeUsuario,
            ));
            redirect('home');
        }

        $this->session->set_flashdata('erro', 'Credenciais inválidas.');
        redirect('auth');
    }

    public function cadastro()
    {
        $this->load->view('telas/CadastroView');
    }

    public function registrar()
    {
        if ($this->input->method() !== 'post') {
            redirect('auth');
        }

        $this->form_validation->set_rules('nome', 'Nome de Usuário', 'trim|required');
        $this->form_validation->set_rules('senha', 'Senha', 'required|min_length[8]|callback_senha_forte');

        $this->form_validation->set_message('required', 'Por favor, preencha todos os campos.');
        $this->form_validation->set_message('min_length', 'A senha deve ter no mínimo {param} caracteres.');

        if ($this->form_validation->run() === false) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('auth/cadastro');
        }

        $nomeUsuario = $this->input->post('nome');

        if ($this->usuarios->usuarioExiste($nomeUsuario)) {
            $this->session->set_flashdata('erro', 'Este nome de utilizador já está em uso. Escolha outro.');
            redirect('auth/cadastro');
        }

        if ($this->usuarios->criarUsuario($nomeUsuario, $this->input->post('senha'))) {
            $this->session->set_flashdata('sucesso', 'Conta criada com sucesso! Faça login.');
            redirect('auth');
        }

        $this->session->set_flashdata('erro', 'Erro ao registar na base de dados.');
        redirect('auth/cadastro');
    }

    public function sair()
    {
        $this->session->sess_destroy();
        redirect('auth');
    }


    public function senha_forte($senha)
    {
        if (!preg_match('/[A-Z]/', $senha)) {
            $this->form_validation->set_message('senha_forte', 'A senha deve conter ao menos uma letra maiúscula.');
            return false;
        }

        if (!preg_match('/[^a-zA-Z0-9]/', $senha)) {
            $this->form_validation->set_message('senha_forte', 'A senha deve conter ao menos um caractere especial.');
            return false;
        }

        return true;
    }

    private function estaBloqueado($acao, $limiteTentativas = 5, $bloqueioSegundos = 60)
    {
        $chaveTentativas = "rl_{$acao}_tentativas";
        $chaveBloqueio   = "rl_{$acao}_bloqueio_ate";

        $bloqueadoAte = $this->session->userdata($chaveBloqueio);

        if ($bloqueadoAte) {
            if (time() < $bloqueadoAte) {
                return true;
            }
            $this->session->unset_userdata(array($chaveBloqueio, $chaveTentativas));
        }

        $tentativas = (int) $this->session->userdata($chaveTentativas) + 1;
        $this->session->set_userdata($chaveTentativas, $tentativas);

        if ($tentativas > $limiteTentativas) {
            $this->session->set_userdata($chaveBloqueio, time() + $bloqueioSegundos);
            return true;
        }

        return false;
    }
}
