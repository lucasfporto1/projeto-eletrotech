<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrdensServicoController extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('OrdemservicoModel');
    }

    public function index()
    {
        $data['titulo'] = 'Ordens de Serviço - EletroTech';
        $data['ordensServico'] = $this->OrdemservicoModel->get_all();
        $data['eletricistasAtivos'] = $this->OrdemservicoModel->get_eletricistas_ativos();
        $data['produtosDisponiveis'] = $this->OrdemservicoModel->get_produtos_disponiveis();

        $this->load->view('telas/OrdemServicoView', $data);
    }

    public function cadastrar()
    {
        $this->form_validation->set_rules('eletricista_os', 'Eletricista', 'required|numeric');
        $this->form_validation->set_rules('data_os', 'Data da Operação', 'required');
        $this->form_validation->set_rules('id_produto[]', 'Produto', 'required');
        $this->form_validation->set_rules('qtd_utilizada[]', 'Quantidade', 'required|integer|greater_than[0]');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('ordemServico');
            return;
        }

        $eletricistaId = $this->input->post('eletricista_os', TRUE);
        $dataOs        = $this->input->post('data_os', TRUE);
        $idsProdutos   = $this->input->post('id_produto', TRUE) ?? [];
        $quantidades   = $this->input->post('qtd_utilizada', TRUE) ?? [];

        $produtos = [];
        foreach ($idsProdutos as $i => $idProduto) {
            $produtos[] = [
                'id'  => $idProduto,
                'qtd' => $quantidades[$i] ?? 0,
            ];
        }

        $idOs = $this->OrdemservicoModel->registrar_os($eletricistaId, $dataOs, $produtos);

        if ($idOs) {
            $this->session->set_flashdata('sucesso', 'OS #' . str_pad($idOs, 5, '0', STR_PAD_LEFT) . ' registrada e estoque atualizado!');
        } else {
            $this->session->set_flashdata('erro', 'Erro ao registrar OS. Verifique se há estoque suficiente para os materiais selecionados.');
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

        $this->load->view('telas/Detalhes', ['materiais' => $materiais]);
    }
}
