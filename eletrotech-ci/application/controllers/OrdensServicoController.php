<?php
defined('BASEPATH') or exit('No direct script access allowed');

class OrdensServicoController extends Auth_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->exigirPermissao('ordemServico');
        $this->load->model('OrdemservicoModel');
        $this->load->model('ChecklistModel');
    }

    public function index()
    {
        $data['titulo'] = 'Ordens de Serviço - EletroTech';

        // Eletricista só enxerga as próprias OS, mas abre e fecha as dela.
        if (!$this->ehAdmin && $this->eletricistaId) {
            $eletricistaId = $this->eletricistaId;
            $total = $this->OrdemservicoModel->contar_por_eletricista($eletricistaId);
            $data  = array_merge($data, $this->paginar($total, site_url('ordemServico')));

            $data['ordensServico'] = $this->OrdemservicoModel->get_all_by_eletricista($eletricistaId, $data['por_pagina'], $data['offset']);
            $data['eletricistasAtivos'] = [];
            $data['produtosDisponiveis'] = $this->OrdemservicoModel->get_produtos_disponiveis();
            $data['checklistInicio'] = $this->ChecklistModel->get_selected_by_type('inicio');
            $data['checklistFim'] = $this->ChecklistModel->get_selected_by_type('fim');
            $data['podeSolicitar'] = false;

            $this->load->view('telas/OrdemServicoView', $data);
            return;
        }

        $total = $this->OrdemservicoModel->contar();
        $data  = array_merge($data, $this->paginar($total, site_url('ordemServico')));

        $data['ordensServico'] = $this->OrdemservicoModel->get_all($data['por_pagina'], $data['offset']);
        $data['eletricistasAtivos'] = $this->OrdemservicoModel->get_eletricistas_ativos();
        $data['produtosDisponiveis'] = $this->OrdemservicoModel->get_produtos_disponiveis();
        $data['checklistInicio'] = $this->ChecklistModel->get_selected_by_type('inicio');
        $data['checklistFim'] = $this->ChecklistModel->get_selected_by_type('fim');
        $data['podeSolicitar'] = $this->ehAdmin;

        $this->load->view('telas/OrdemServicoView', $data);
    }

    /**
     * Passo 1 do fluxo: o admin solicita o serviço e atribui um eletricista.
     * A OS nasce como "solicitada" — sem materiais e sem baixa de estoque.
     */
    public function solicitar()
    {
        $this->exigirAdmin();

        $this->form_validation->set_rules('eletricista_os', 'Eletricista', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('erro', validation_errors());
            redirect('ordemServico');
            return;
        }

        $eletricistaId = $this->input->post('eletricista_os', TRUE);
        $dataOs = (string) $this->input->post('data_os', TRUE);

        if ($dataOs !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataOs)) {
            $this->session->set_flashdata('erro', 'A data da operação deve ter o formato YYYY-MM-DD ou ficar em branco.');
            redirect('ordemServico');
            return;
        }

        $idOs = $this->OrdemservicoModel->solicitar_os($eletricistaId, $dataOs ?: null);

        if ($idOs) {
            $this->session->set_flashdata(
                'sucesso',
                'OS #' . str_pad($idOs, 5, '0', STR_PAD_LEFT) . ' solicitada! O eletricista já pode abri-la.'
            );
        } else {
            $this->session->set_flashdata('erro', 'Erro ao solicitar a OS. Tente novamente.');
        }

        redirect('ordemServico');
    }

    /**
     * Passo 2 do fluxo: o eletricista responsável (ou o admin) abre a OS
     * solicitada, informando os materiais e respondendo o checklist de início.
     * É aqui que o estoque é baixado.
     */
    public function abrir()
    {
        $idOs = (int) $this->input->post('id_os', TRUE);
        $ordem = $this->OrdemservicoModel->get_by_id($idOs);

        if (empty($ordem)) {
            $this->session->set_flashdata('erro', 'Ordem de serviço não encontrada.');
            redirect('ordemServico');
            return;
        }

        if (!$this->ehAdmin && $this->eletricistaId && (int) $ordem['eletricista_os'] !== $this->eletricistaId) {
            $this->session->set_flashdata('erro', 'Você só pode abrir ordens atribuídas ao seu perfil.');
            redirect('ordemServico');
            return;
        }

        if ($ordem['status'] !== 'solicitada') {
            $this->session->set_flashdata('erro', 'Somente ordens com status "solicitada" podem ser abertas.');
            redirect('ordemServico');
            return;
        }

        $this->form_validation->set_rules('id_os', 'OS', 'required|numeric');
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

        $idsProdutos = $this->input->post('id_produto', TRUE) ?? [];
        $quantidades = $this->input->post('qtd_utilizada', TRUE) ?? [];
        $respostas = $this->input->post('checklist_resposta', TRUE) ?: [];

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

        if ($this->OrdemservicoModel->abrir_os($idOs, $produtos, $checklistRespostas)) {
            $mensagem = 'OS #' . str_pad($idOs, 5, '0', STR_PAD_LEFT) . ' aberta e estoque atualizado!';

            $foto = $this->uploadFoto('foto_abertura');
            if ($foto['status'] === 'ok') {
                $this->OrdemservicoModel->add_comentario($idOs, 'Foto registrada na abertura da OS.', $foto['nome']);
            } elseif ($foto['status'] === 'erro') {
                $mensagem .= ' (Não foi possível anexar a foto de abertura: ' . $foto['erro'] . ')';
            }

            $this->session->set_flashdata('sucesso', $mensagem);
        } else {
            $this->session->set_flashdata('erro', 'Erro ao abrir a OS. Verifique se há estoque suficiente para os materiais selecionados e tente novamente.');
        }

        redirect('ordemServico');
    }

    public function fechar()
    {
        if (!$this->ehAdmin && $this->eletricistaId) {
            $idOs = (int) $this->input->post('id_os', TRUE);
            $ordem = $this->OrdemservicoModel->get_by_id($idOs);

            if (empty($ordem) || (int) $ordem['eletricista_os'] !== $this->eletricistaId) {
                $this->session->set_flashdata('erro', 'Você só pode fechar ordens atribuídas ao seu perfil.');
                redirect('ordemServico');
                return;
            }
        }

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
            $mensagem = 'OS #' . str_pad($idOs, 5, '0', STR_PAD_LEFT) . ' fechada com sucesso.';

            $foto = $this->uploadFoto('foto_fechamento');
            if ($foto['status'] === 'ok') {
                $this->OrdemservicoModel->add_comentario($idOs, 'Foto registrada no fechamento da OS.', $foto['nome']);
            } elseif ($foto['status'] === 'erro') {
                $mensagem .= ' (Não foi possível anexar a foto de fechamento: ' . $foto['erro'] . ')';
            }

            $this->session->set_flashdata('sucesso', $mensagem);
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

        $ordem = $this->OrdemservicoModel->get_by_id((int) $idOs);

        if (empty($ordem)) {
            echo '<p class="text-center text-danger">Ordem de serviço não encontrada.</p>';
            return;
        }

        if (!$this->ehAdmin && $this->eletricistaId && (int) $ordem['eletricista_os'] !== $this->eletricistaId) {
            echo '<p class="text-center text-danger">Você não tem acesso a esta ordem.</p>';
            return;
        }

        $materiais = $this->OrdemservicoModel->get_materiais_by_os($idOs);
        $respostas = $this->OrdemservicoModel->get_checklist_respostas_by_os($idOs);
        $comentarios = $this->OrdemservicoModel->get_comentarios_by_os($idOs);

        $this->load->view('telas/Detalhes', [
            'idOs' => (int) $idOs,
            'ordem' => $ordem,
            'materiais' => $materiais,
            'respostas' => $respostas,
            'comentarios' => $comentarios,
        ]);
    }

    public function adicionarComentario()
    {
        $this->output->set_content_type('application/json');

        $idOs = (int) $this->input->post('id_os', TRUE);
        $comentario = trim((string) $this->input->post('comentario', TRUE));

        if ($idOs <= 0) {
            return $this->output->set_status_header(400)
                ->set_output(json_encode(['sucesso' => false, 'mensagem' => 'OS inválida.']));
        }

        if (!$this->ehAdmin && $this->eletricistaId) {
            $ordem = $this->OrdemservicoModel->get_by_id($idOs);

            if (empty($ordem) || (int) $ordem['eletricista_os'] !== $this->eletricistaId) {
                return $this->output->set_status_header(403)
                    ->set_output(json_encode(['sucesso' => false, 'mensagem' => 'Você não tem acesso a esta ordem.']));
            }
        }

        $temFoto = isset($_FILES['foto']) && isset($_FILES['foto']['error']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE;

        if ($comentario === '' && !$temFoto) {
            return $this->output->set_status_header(422)
                ->set_output(json_encode(['sucesso' => false, 'mensagem' => 'Escreva um comentário ou anexe uma foto.']));
        }

        $fotoNome = null;

        if ($temFoto) {
            $foto = $this->uploadFoto('foto');
            if ($foto['status'] === 'erro') {
                return $this->output->set_status_header(422)
                    ->set_output(json_encode(['sucesso' => false, 'mensagem' => $foto['erro']]));
            }
            $fotoNome = $foto['nome'];
        }

        if (!$this->OrdemservicoModel->add_comentario($idOs, $comentario, $fotoNome)) {
            if ($fotoNome) {
                @unlink(FCPATH . 'assets/uploads/os/' . $fotoNome);
                @unlink(FCPATH . 'assets/uploads/os/' . preg_replace('/(\.[^.]+)$/', '_thumb$1', $fotoNome));
            }
            return $this->output->set_status_header(500)
                ->set_output(json_encode(['sucesso' => false, 'mensagem' => 'Não foi possível salvar o comentário.']));
        }

        return $this->output->set_output(json_encode(['sucesso' => true]));
    }

    /**
     * Faz o upload de uma foto para assets/uploads/os/.
     *
     * @return array{status: 'ok'|'erro'|'vazio', nome: string|null, erro: string|null}
     */
    private function uploadFoto($campo = 'foto')
    {
        if (!isset($_FILES[$campo]) || !isset($_FILES[$campo]['error']) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
            return ['status' => 'vazio', 'nome' => null, 'erro' => null];
        }

        $diretorio = FCPATH . 'assets/uploads/os/';
        if (!is_dir($diretorio)) {
            @mkdir($diretorio, 0755, true);
        }

        $this->upload->initialize([
            'upload_path' => $diretorio,
            'allowed_types' => 'jpg|jpeg|png|gif|webp',
            'max_size' => 8192,
            'encrypt_name' => true,
        ]);

        if (!$this->upload->do_upload($campo)) {
            return ['status' => 'erro', 'nome' => null, 'erro' => strip_tags($this->upload->display_errors('', ''))];
        }

        $dados = $this->upload->data();

        // Gera uma miniatura para exibição leve no histórico. Se falhar (ex.: GD
        // indisponível para o formato), seguimos com a foto original — a listagem
        // faz fallback para o arquivo cheio quando a thumb não existe.
        $this->gerarThumb($dados['full_path']);

        return ['status' => 'ok', 'nome' => $dados['file_name'], 'erro' => null];
    }

    /**
     * Cria uma miniatura (sufixo _thumb) ao lado da imagem original.
     * Retorna true em sucesso; não lança erro se a GD não suportar o formato.
     */
    private function gerarThumb($caminhoOrigem)
    {
        $this->load->library('image_lib');
        $this->image_lib->clear();

        $this->image_lib->initialize([
            'image_library'  => 'gd2',
            'source_image'   => $caminhoOrigem,
            'create_thumb'   => true,
            'thumb_marker'   => '_thumb',
            'maintain_ratio' => true,
            'width'          => 600,
            'height'         => 600,
        ]);

        $ok = $this->image_lib->resize();
        $this->image_lib->clear();

        return $ok;
    }
}
