<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrdensServicoController extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('OrdemservicoModel');
        $this->load->model('ChecklistModel');
    }

    public function index()
    {
        $data['titulo'] = 'Ordens de Serviço - EletroTech';
        $data['ordensServico'] = $this->OrdemservicoModel->get_all();
        $data['eletricistasAtivos'] = $this->OrdemservicoModel->get_eletricistas_ativos();
        $data['produtosDisponiveis'] = $this->OrdemservicoModel->get_produtos_disponiveis();
        $data['checklistInicio'] = $this->ChecklistModel->get_selected_by_type('inicio');
        $data['checklistFim'] = $this->ChecklistModel->get_selected_by_type('fim');

        $this->load->view('telas/OrdemServicoView', $data);
    }

    public function cadastrar()
    {
        $this->form_validation->set_rules('eletricista_os', 'Eletricista', 'required|numeric');
        $this->form_validation->set_rules('id_produto[]', 'Produto', 'required');
        $this->form_validation->set_rules('qtd_utilizada[]', 'Quantidade', 'required|integer|greater_than[0]');

        $checklistInicio = $this->ChecklistModel->get_selected_by_type('inicio');

        if (empty($checklistInicio) || empty($checklistInicio['perguntas'])) {
            $this->session->set_flashdata('erro', 'Nenhum checklist de início está selecionado. Configure um checklist na tela de Checklist antes de abrir uma OS.');
            redirect('ordemServico');
            return;
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('ordemServico');
            return;
        }

        $eletricistaId = $this->input->post('eletricista_os', TRUE);
        $dataOs = $this->input->post('data_os', TRUE);
        $idsProdutos = $this->input->post('id_produto', TRUE) ?? [];
        $quantidades = $this->input->post('qtd_utilizada', TRUE) ?? [];
        $respostas = $this->input->post('checklist_resposta', TRUE) ?: [];

        if ($dataOs !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataOs)) {
            $this->session->set_flashdata('erro', 'A data da operação deve ter o formato YYYY-MM-DD ou ficar em branco.');
            redirect('ordemServico');
            return;
        }

        $produtoIds = array_map('intval', $idsProdutos);
        if (count($produtoIds) !== count(array_unique($produtoIds))) {
            $this->session->set_flashdata('erro', 'Não é permitido adicionar o mesmo produto mais de uma vez na mesma OS.');
            redirect('ordemServico');
            return;
        }

        $checklistRespostas = [];
        foreach ($checklistInicio['perguntas'] as $pergunta) {
            $valor = strtolower(trim($respostas[$pergunta['id']] ?? ''));
            if ($valor === '') {
                $this->session->set_flashdata('erro', 'Responda todas as perguntas do checklist de início antes de abrir a OS.');
                redirect('ordemServico');
                return;
            }
            if (!in_array($valor, ['sim', 'nao'], true)) {
                $this->session->set_flashdata('erro', 'As respostas do checklist de início devem ser apenas Sim ou Não.');
                redirect('ordemServico');
                return;
            }
            if ($valor === 'nao') {
                $this->session->set_flashdata('erro', 'Não é possível abrir a OS quando uma resposta do checklist de início for não.');
                redirect('ordemServico');
                return;
            }
            $checklistRespostas[$pergunta['id']] = $valor;
        }

        $produtos = [];
        foreach ($idsProdutos as $i => $idProduto) {
            $produtos[] = [
                'id' => $idProduto,
                'qtd' => $quantidades[$i] ?? 0,
            ];
        }

        $idOs = $this->OrdemservicoModel->registrar_os($eletricistaId, $dataOs ?: null, $produtos, $checklistRespostas);

        if ($idOs) {
            $this->session->set_flashdata('sucesso', 'OS #' . str_pad($idOs, 5, '0', STR_PAD_LEFT) . ' registrada e estoque atualizado!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao registrar OS. Verifique se há estoque suficiente para os materiais selecionados e tente novamente.');
        }

        redirect('ordemServico');
    }

    public function fechar()
    {
        $this->form_validation->set_rules('id_os', 'OS', 'required|numeric');

        $checklistFim = $this->ChecklistModel->get_selected_by_type('fim');

        if (empty($checklistFim) || empty($checklistFim['perguntas'])) {
            $this->session->set_flashdata('erro', 'Nenhum checklist de fim está selecionado. Configure um checklist na tela de Checklist antes de fechar uma OS.');
            redirect('ordemServico');
            return;
        }

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('ordemServico');
            return;
        }

        $idOs = (int) $this->input->post('id_os', TRUE);
        $respostas = $this->input->post('checklist_resposta', TRUE) ?: [];
        $motivos = $this->input->post('motivo_nao', TRUE) ?: [];

        $fechamentoRespostas = [];
        foreach ($checklistFim['perguntas'] as $pergunta) {
            $valor = strtolower(trim($respostas[$pergunta['id']] ?? ''));
            if ($valor === '') {
                $this->session->set_flashdata('erro', 'Responda todas as perguntas do checklist de fim antes de fechar a OS.');
                redirect('ordemServico');
                return;
            }
            if (!in_array($valor, ['sim', 'nao'], true)) {
                $this->session->set_flashdata('erro', 'As respostas do checklist de fim devem ser apenas Sim ou Não.');
                redirect('ordemServico');
                return;
            }

            $motivo = null;
            if ($valor === 'nao') {
                $motivo = trim($motivos[$pergunta['id']] ?? '');
                if ($motivo === '') {
                    $this->session->set_flashdata('erro', 'Explique o motivo do não para todas as perguntas negativas antes de fechar a OS.');
                    redirect('ordemServico');
                    return;
                }
            }

            $fechamentoRespostas[$pergunta['id']] = [
                'resposta' => $valor,
                'motivo_nao' => $motivo,
            ];
        }

        if ($this->OrdemservicoModel->fechar_os($idOs, $fechamentoRespostas)) {
            $this->session->set_flashdata('sucesso', 'OS #' . str_pad($idOs, 5, '0', STR_PAD_LEFT) . ' fechada com sucesso.');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao fechar a OS. Verifique se a OS ainda está aberta e tente novamente.');
        }

        redirect('ordemServico');
    }

    public function detalhes($idOs = null)
    {
        if (empty($idOs) || !is_numeric($idOs)) {
            echo '<p class="text-center text-danger">OS inválida.</p>';
            return;
        }

        $materiais = $this->OrdemservicoModel->get_materiais_by_os($idOs);
        $respostas = $this->OrdemservicoModel->get_checklist_respostas_by_os($idOs);

        $this->load->view('telas/Detalhes', [
            'materiais' => $materiais,
            'respostas' => $respostas,
        ]);
    }
}

