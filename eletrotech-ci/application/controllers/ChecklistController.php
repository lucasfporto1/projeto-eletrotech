<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ChecklistController extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ChecklistModel');
    }

    public function index()
    {
        $data['titulo'] = 'Checklist - EletroTech';
        $data['checklists'] = $this->ChecklistModel->get_all();
        $data['selectedInicio'] = $this->ChecklistModel->get_selected_by_type('inicio');
        $data['selectedFim'] = $this->ChecklistModel->get_selected_by_type('fim');

        $this->load->view('telas/ChecklistView', $data);
    }

    public function cadastrar()
    {
        $this->form_validation->set_rules('titulo', 'Título', 'required|min_length[3]');
        $this->form_validation->set_rules('tipo', 'Tipo', 'required|in_list[inicio,fim]');

        $perguntas = $this->input->post('pergunta', TRUE) ?: [];
        $perguntasValidas = array_values(array_filter(array_map('trim', $perguntas)));

        if ($this->form_validation->run() === FALSE || empty($perguntasValidas)) {
            $this->session->set_flashdata('erro', 'Preencha o título e pelo menos uma pergunta válida.');
            redirect('checklist');
            return;
        }

        $data = [
            'titulo' => $this->input->post('titulo', TRUE),
            'tipo' => $this->input->post('tipo', TRUE),
            'selecionado' => 0,
        ];

        $idChecklist = $this->ChecklistModel->insert_checklist($data, $perguntasValidas);

        if ($idChecklist) {
            $selected = $this->ChecklistModel->get_selected_by_type($data['tipo']);
            if (empty($selected)) {
                $this->ChecklistModel->select_default($idChecklist, $data['tipo']);
            }
            $this->session->set_flashdata('sucesso', 'Checklist cadastrado com sucesso!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao cadastrar checklist.');
        }

        redirect('checklist');
    }

    public function selecionar()
    {
        $idChecklist = $this->input->post('id_checklist', TRUE);
        $tipo = $this->input->post('tipo', TRUE);

        if (empty($idChecklist) || !is_numeric($idChecklist) || !in_array($tipo, ['inicio', 'fim'])) {
            $this->session->set_flashdata('erro', 'Checklist inválido.');
            redirect('checklist');
            return;
        }

        if ($this->ChecklistModel->select_default((int) $idChecklist, $tipo)) {
            $this->session->set_flashdata('sucesso', 'Checklist padrão selecionado com sucesso.');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao definir checklist como padrão.');
        }

        redirect('checklist');
    }

    public function perguntas($id = null)
    {
        if (empty($id) || !is_numeric($id)) {
            echo '<p class="text-center text-danger">Checklist inválido.</p>';
            return;
        }

        $perguntas = $this->ChecklistModel->get_perguntas((int) $id);

        if (empty($perguntas)) {
            echo '<p class="text-center">Nenhuma pergunta cadastrada para este checklist.</p>';
            return;
        }

        echo '<div><h5 class="text-center mb-3">Perguntas do Checklist</h5><ul class="list-group">';
        foreach ($perguntas as $p) {
            echo '<li class="list-group-item bg-dark text-white border-secondary">' . htmlspecialchars($p['texto_pergunta']) . '</li>';
        }
        echo '</ul></div>';
    }
}
