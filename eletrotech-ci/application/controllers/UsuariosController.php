<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UsuariosController extends Auth_Controller
{
    public function index()
    {
        $dados = array(
            'usuarios' => $this->usuarios->listarUsuarios(),
        );

        $this->load->view('telas/UsuariosView', $dados);
    }

    public function editar()
    {
        if ($this->input->method() !== 'post') {
            redirect('usuarios');
        }

        $id          = (int) $this->input->post('id');
        $nomeUsuario = trim((string) $this->input->post('usuario'));

        if (!$id || $nomeUsuario === '') {
            $this->session->set_flashdata('erro', 'O nome de utilizador não pode estar vazio.');
            redirect('usuarios');
        }

        if ($this->usuarios->usuarioExiste($nomeUsuario, $id)) {
            $this->session->set_flashdata('erro', "Erro: Já existe um utilizador com o nome '{$nomeUsuario}'. Escolha outro.");
            redirect('usuarios');
        }

        if ($this->usuarios->atualizarUsuario($id, $nomeUsuario)) {
            $this->session->set_flashdata('sucesso', 'Utilizador atualizado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao atualizar o utilizador.');
        }

        redirect('usuarios');
    }

    public function excluir($id = null)
    {
        $id = (int) $id;

        if (!$id) {
            redirect('usuarios');
        }

        if ($this->usuarios->excluirUsuario($id)) {
            $this->session->set_flashdata('sucesso', 'Registo excluído com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Não foi possível excluir. O registo pode estar vinculado a outras informações no sistema.');
        }

        redirect('usuarios');
    }
}
