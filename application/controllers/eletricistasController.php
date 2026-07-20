<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EletricistasController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
		$this->load->model('EletricistasModel');
        $this->load->library(['session', 'form_validation']);
        $this->load->helper(['url', 'form']);
		/*
        if (!$this->session->userdata('logado')) {
            redirect('auth', 'refresh');
        }*/
		
       
    }

    public function index()
    {
        $data['titulo'] = 'Eletricistas - EletroTech';
        $data['eletricistas'] = $this->EletricistasModel->get_all();

        $this->load->view('telas/eletricistasView', $data);
    }

    public function cadastrar()
    {
        $this->form_validation->set_rules('cpf', 'CPF', 'required|exact_length[11]|numeric');
        $this->form_validation->set_rules('nome', 'Nome', 'required|min_length[3]');
        $this->form_validation->set_rules('data_contratacao', 'Data de Contratação', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('eletricistasController');
            return;
        }

        $cpf = $this->input->post('cpf', TRUE);

        if ($this->EletricistasModel->get_by_cpf($cpf)) {
            $this->session->set_flashdata('erro', 'Já existe um eletricista cadastrado com este CPF.');
            redirect('eletricistasController');
            return;
        }

        $data = [
            'cpf' => $cpf,
            'nome' => $this->input->post('nome', TRUE),
            'data_contratacao' => $this->input->post('data_contratacao', TRUE),
            'data_demissao' => null,
        ];

        if ($this->EletricistasModel->insert($data)) {
            $this->session->set_flashdata('sucesso', 'Eletricista cadastrado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao cadastrar eletricista.');
        }

        redirect('eletricistasController');
    }

    public function editar()
    {
        $id   = $this->input->post('id', TRUE);
        $nome = $this->input->post('nome', TRUE);

        $this->form_validation->set_rules('id', 'ID', 'required|numeric');
        $this->form_validation->set_rules('nome', 'Nome', 'required|min_length[3]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('eletricistasController');
            return;
        }

        if ($this->EletricistasModel->update($id, ['nome' => $nome])) {
            $this->session->set_flashdata('sucesso', 'Dados atualizados com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao atualizar eletricista.');
        }

        redirect('eletricistasController');
    }
    public function demitir($id = null)
    {
        if (empty($id) || !is_numeric($id)) {
            show_404();
            return;
        }

        if ($this->EletricistasModel->demitir($id)) {
            $this->session->set_flashdata('sucesso', 'Eletricista demitido com sucesso.');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao demitir eletricista.');
        }

        redirect('eletricistasController');
    }

    public function reativar($id = null)
    {
        if (empty($id) || !is_numeric($id)) {
            show_404();
            return;
        }

        if ($this->EletricistasModel->reativar($id)) {
            $this->session->set_flashdata('sucesso', 'Eletricista readmitido com sucesso.');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao readmitir eletricista.');
        }

        redirect('eletricistasController');
    }
}