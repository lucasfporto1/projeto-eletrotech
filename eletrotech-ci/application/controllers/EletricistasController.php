<?php
defined('BASEPATH') or exit('No direct script access allowed');

class EletricistasController extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('EletricistasModel');
    }

    public function index()
    {
        $data['titulo'] = 'Eletricistas - EletroTech';

        $total = $this->EletricistasModel->contar();
        $data  = array_merge($data, $this->paginar($total, site_url('eletricistas')));

        $data['eletricistas'] = $this->EletricistasModel->get_all($data['por_pagina'], $data['offset']);

        $this->load->view('telas/EletricistasView', $data);
    }

    public function cadastrar()
    {
        $this->form_validation->set_rules('cpf', 'CPF', 'required|exact_length[11]|numeric');
        $this->form_validation->set_rules('nome', 'Nome', 'required|min_length[3]');
        $this->form_validation->set_rules('data_contratacao', 'Data de Contratação', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('eletricistas');
            return;
        }

        $cpf = $this->input->post('cpf', TRUE);

        if ($this->EletricistasModel->get_by_cpf($cpf)) {
            $this->session->set_flashdata('erro', 'Já existe um eletricista cadastrado com este CPF.');
            redirect('eletricistas');
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

        redirect('eletricistas');
    }

    public function editar()
    {
        $id   = $this->input->post('id', TRUE);
        $nome = $this->input->post('nome', TRUE);

        $this->form_validation->set_rules('id', 'ID', 'required|numeric');
        $this->form_validation->set_rules('nome', 'Nome', 'required|min_length[3]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('eletricistas');
            return;
        }

        if ($this->EletricistasModel->update($id, ['nome' => $nome])) {
            $this->session->set_flashdata('sucesso', 'Dados atualizados com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao atualizar eletricista.');
        }

        redirect('eletricistas');
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

        redirect('eletricistas');
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

        redirect('eletricistas');
    }

    public function historico_os($id = null)
    {
        if (empty($id) || !is_numeric($id)) {
            echo '<p class="text-center text-danger">Eletricista inválido.</p>';
            return;
        }

        $historico = $this->EletricistasModel->get_os_by_eletricista((int) $id);

        if (empty($historico)) {
            echo '<p class="text-center">Nenhuma OS registrada para este eletricista.</p>';
            return;
        }

        echo '<div class="table-responsive">';
        echo '<table class="table table-dark table-bordered custom-table text-center">';
        echo '<thead><tr><th>ID</th><th>Data da OS</th><th>Status</th><th>Data de Fechamento</th></tr></thead>';
        echo '<tbody>';

        foreach ($historico as $os) {
            $dataOs = !empty($os['data_os']) ? date('d/m/Y', strtotime($os['data_os'])) : '-';
            $dataFechamento = !empty($os['data_fechamento']) ? date('d/m/Y', strtotime($os['data_fechamento'])) : '-';
            $status = ucfirst($os['status'] ?? '');

            echo '<tr>';
            echo '<td>#' . str_pad((int) $os['id'], 5, '0', STR_PAD_LEFT) . '</td>';
            echo '<td>' . htmlspecialchars($dataOs) . '</td>';
            echo '<td>' . htmlspecialchars($status) . '</td>';
            echo '<td>' . htmlspecialchars($dataFechamento) . '</td>';
            echo '</tr>';
        }

        echo '</tbody></table></div>';
    }
}
