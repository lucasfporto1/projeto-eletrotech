<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testes_OrdemServico extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('unit_test');
        $this->load->model('OrdemServicoModel');
    }

    public function index() {
        $this->unit->use_strict(TRUE);

        $this->testar_registrar_os_com_sucesso();
        $this->testar_registrar_os_estoque_insuficiente();
        $this->testar_fechar_os();
        $this->testar_add_comentario();
        $this->testar_contadores_dashboard();
        $this->testar_ultimas_os_do_eletricista();

        echo "<div style='font-family: Arial; padding: 20px;'>";
        echo "<h2>Relatório de Testes: OrdemServicoModel</h2>";
        echo $this->unit->report();
        echo "</div>";
    }

    /**
     * Helper para gerar um CPF único por teste, evitando colisão
     * com a constraint UNIQUE de tabela_eletricistas.cpf
     */
    private function gerar_cpf_teste($prefixo) {
        return $prefixo . rand(100000000, 999999999);
    }

    private function testar_registrar_os_com_sucesso() {
        $this->db->trans_start();
        try {
            // 1. PREPARAÇÃO: Criamos dados falsos apenas para o teste
            $this->db->insert('tabela_eletricistas', [
                'nome' => 'Eletricista Teste OS',
                'cpf'  => $this->gerar_cpf_teste('001')
            ]);
            $eletricistaId = $this->db->insert_id();

            $this->db->insert('tabela_produtos', [
                'nome_produto' => 'Cabo 10mm Teste',
                'qtd_estoque'  => 50,
                'vlr_unitario' => 10.00
            ]);
            $produtoId = $this->db->insert_id();

            // 2. AÇÃO: Executamos o método registrar_os consumindo 5 unidades do produto
            $produtos_usados = [['id' => $produtoId, 'qtd' => 5]];
            $idOs = $this->OrdemServicoModel->registrar_os($eletricistaId, date('Y-m-d'), $produtos_usados);

            // 3. VERIFICAÇÕES
            $this->unit->run($idOs > 0, TRUE, 'Registrar OS: Deve criar a OS e retornar um ID válido');

            // Verifica se baixou o estoque (50 - 5 = 45)
            $produto_atualizado = $this->db->where('id', $produtoId)->get('tabela_produtos')->row_array();
            $this->unit->run((int)$produto_atualizado['qtd_estoque'], 45, 'Registrar OS: Deve deduzir o estoque do produto corretamente');

            // Verifica se registrou a movimentação financeira/estoque (tabela_movimentacoes)
            $movimentacao = $this->db->where('id_os', $idOs)->get('tabela_movimentacoes')->row_array();
            $this->unit->run($movimentacao['tipo'] ?? '', 'saida', 'Registrar OS: Deve gerar uma movimentação de "saída" vinculada à OS');
        } finally {
            $this->db->trans_rollback();
        }
    }

    private function testar_registrar_os_estoque_insuficiente() {
        $this->db->trans_start();
        try {
            // Cria Eletricista e um Produto com apenas 2 no estoque
            $this->db->insert('tabela_eletricistas', [
                'nome' => 'Eletricista Teste 2',
                'cpf'  => $this->gerar_cpf_teste('002')
            ]);
            $eletricistaId = $this->db->insert_id();

            $this->db->insert('tabela_produtos', ['nome_produto' => 'Fita Isolante', 'qtd_estoque' => 2]);
            $produtoId = $this->db->insert_id();

            // Tenta usar 5 unidades (mais do que tem no estoque)
            $produtos_usados = [['id' => $produtoId, 'qtd' => 5]];
            $resultado = $this->OrdemServicoModel->registrar_os($eletricistaId, date('Y-m-d'), $produtos_usados);

            // Como o estoque é insuficiente, o método deve retornar FALSE e abortar tudo
            $this->unit->run($resultado, FALSE, 'Estoque Insuficiente: Deve retornar FALSE e impedir a criação da OS');
        } finally {
            $this->db->trans_rollback();
        }
    }

    private function testar_fechar_os() {
        $this->db->trans_start();
        try {
            // Cria um eletricista próprio para este teste (evita depender de ID fixo = 1)
            $this->db->insert('tabela_eletricistas', [
                'nome' => 'Eletricista Fechar OS',
                'cpf'  => $this->gerar_cpf_teste('003')
            ]);
            $eletricistaId = $this->db->insert_id();

            // Cria uma OS manual com status "aberta"
            $this->db->insert('tabela_ordens_servico', [
                'eletricista_os' => $eletricistaId,
                'status'         => 'aberta',
                'data_os'        => date('Y-m-d')
            ]);
            $idOs = $this->db->insert_id();

            // Busca perguntas de checklist JÁ EXISTENTES no banco, em vez de criar novas.
            // tabela_checklist_perguntas exige um id_checklist válido (FK obrigatória),
            // então criar perguntas fake aqui exigiria também criar um checklist fake,
            // o que não é responsabilidade deste teste. Perguntas de checklist são
            // dados de configuração do sistema e devem já existir previamente.
            $perguntas_existentes = $this->db->limit(2)->get('tabela_checklist_perguntas')->result_array();

            if (count($perguntas_existentes) < 2) {
                $this->unit->run(
                    FALSE,
                    TRUE,
                    'Fechar OS: Teste ignorado - é necessário ter ao menos 2 perguntas cadastradas em tabela_checklist_perguntas para rodar este teste'
                );
                return;
            }

            $idPergunta1 = $perguntas_existentes[0]['id'];
            $idPergunta2 = $perguntas_existentes[1]['id'];

            // Simula respostas de um checklist, usando IDs de pergunta reais
            $respostas = [
                $idPergunta1 => ['resposta' => 'Sim'],
                $idPergunta2 => ['resposta' => 'Não', 'motivo_nao' => 'Material danificado']
            ];

            // Executa o fechamento
            $sucesso = $this->OrdemServicoModel->fechar_os($idOs, $respostas);

            // Verifica se deu certo
            $this->unit->run($sucesso, TRUE, 'Fechar OS: O método deve retornar TRUE ao fechar com sucesso');

            // Busca a OS para ver se o status mudou
            $os_atualizada = $this->OrdemServicoModel->get_by_id($idOs);
            $this->unit->run($os_atualizada['status'], 'fechada', 'Fechar OS: O status no banco deve ser alterado para "fechada"');
        } finally {
            $this->db->trans_rollback();
        }
    }

    private function testar_add_comentario() {
        $this->db->trans_start();
        try {
            // 1. Testa erro: tentar adicionar comentário numa OS que não existe
            $falhou = $this->OrdemServicoModel->add_comentario(999999, 'Comentário inválido');
            $this->unit->run($falhou, FALSE, 'Comentários: Deve retornar FALSE ao tentar comentar numa OS inexistente');

            // 2. Testa sucesso: cria um eletricista e uma OS real e comenta
            $this->db->insert('tabela_eletricistas', [
                'nome' => 'Eletricista Comentario',
                'cpf'  => $this->gerar_cpf_teste('004')
            ]);
            $eletricistaId = $this->db->insert_id();

            $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $eletricistaId, 'status' => 'aberta']);
            $idOs = $this->db->insert_id();

            $sucesso = $this->OrdemServicoModel->add_comentario($idOs, 'Fio trocado com sucesso');
            $this->unit->run($sucesso, TRUE, 'Comentários: Deve retornar TRUE ao inserir comentário numa OS válida');
        } finally {
            $this->db->trans_rollback();
        }
    }

    private function testar_contadores_dashboard() {
        $this->db->trans_start();
        try {
            $this->db->insert('tabela_eletricistas', [
                'nome' => 'João Dashboard',
                'cpf'  => $this->gerar_cpf_teste('005')
            ]);
            $id_eletricista = $this->db->insert_id();

            // Adiciona 2 OS abertas e 1 fechada para o João
            $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $id_eletricista, 'status' => 'aberta']);
            $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $id_eletricista, 'status' => 'aberta']);
            $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $id_eletricista, 'status' => 'fechada']);

            // Testa as funções matemáticas da model
            $totalAbertas  = $this->OrdemServicoModel->totalAbertasPorEletricista($id_eletricista);
            $totalFechadas = $this->OrdemServicoModel->totalFechadasPorEletricista($id_eletricista);
            $totalGeral    = $this->OrdemServicoModel->totalPorEletricista($id_eletricista);

            $this->unit->run($totalAbertas, 2, 'Dashboard: Deve contar corretamente as OS abertas do eletricista (2)');
            $this->unit->run($totalFechadas, 1, 'Dashboard: Deve contar corretamente as OS fechadas do eletricista (1)');
            $this->unit->run($totalGeral, 3, 'Dashboard: O total de OS do eletricista deve ser a soma exata (3)');
        } finally {
            $this->db->trans_rollback();
        }
    }

    private function testar_ultimas_os_do_eletricista() {
        $this->db->trans_start();
        try {
            $this->db->insert('tabela_eletricistas', [
                'nome' => 'Maria Dashboard',
                'cpf'  => $this->gerar_cpf_teste('006')
            ]);
            $id_eletricista = $this->db->insert_id();

            $this->db->insert('tabela_eletricistas', [
                'nome' => 'Pedro Dashboard',
                'cpf'  => $this->gerar_cpf_teste('007')
            ]);
            $id_outro_eletricista = $this->db->insert_id();

            $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $id_eletricista, 'status' => 'aberta']);
            $id_os = $this->db->insert_id();

            $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $id_outro_eletricista, 'status' => 'fechada']);

            $ultimas = $this->OrdemServicoModel->ultimasOSs($id_eletricista, 5);
            $this->unit->run(count($ultimas), 1, 'Dashboard: Deve retornar apenas as OSs do eletricista filtrado');
            $this->unit->run((int) ($ultimas[0]['id'] ?? 0), (int) $id_os, 'Dashboard: Deve trazer a OS correta para o eletricista filtrado');
        } finally {
            $this->db->trans_rollback();
        }
    }
}