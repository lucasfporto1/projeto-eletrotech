<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testes_Checklist extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('unit_test');
        $this->load->model('ChecklistModel');
    }

    public function index() {
        $this->unit->use_strict(TRUE);

        $this->testar_crud_checklist();
        $this->testar_normalizacao();
        $this->testar_fluxo_bloqueio_autorizacao();

        echo "<div style='font-family: Arial; padding: 20px;'>";
        echo "<h2>Relatório de Testes: ChecklistModel</h2>";
        echo $this->unit->report();
        echo "</div>";
    }

    private function testar_crud_checklist() {
        $this->db->trans_start();

        $dados = ['titulo' => 'Checklist Teste', 'tipo' => 'inicio', 'selecionado' => 0];
        $perguntas = [
            ['texto' => 'Pergunta 1', 'tipo_resposta' => 'radio'],
            'Pergunta 2' // Testando o formato string simples também
        ];

        $id = $this->ChecklistModel->insert_checklist($dados, $perguntas);
        
        $this->unit->run($id > 0, TRUE, 'CRUD: Deve inserir checklist com sucesso');

        $lista_perguntas = $this->ChecklistModel->get_perguntas($id);
        $this->unit->run(count($lista_perguntas), 2, 'CRUD: Deve carregar exatamente 2 perguntas');

        $this->db->trans_rollback();
    }

    private function testar_normalizacao() {
        $input = "Água, Ção, Ênfase";
        $esperado = "agua, cao, enfase";
        
        $resultado = $this->ChecklistModel->normalizar_resposta($input);
        
        $this->unit->run($resultado, $esperado, 'Utils: Normalizar deve remover acentos e converter para minusculo');
    }

    private function testar_fluxo_bloqueio_autorizacao() {
        $this->db->trans_start();

        // 1. Setup: Eletricista e OS
        $this->db->insert('tabela_eletricistas', [
            'nome' => 'Eletricista Teste', 
            'cpf' => '000' . rand(100000000, 999999999)
        ]);
        $idE = $this->db->insert_id();
        
        $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $idE, 'status' => 'aberta']);
        $idOs = $this->db->insert_id();

        // 2. Simula bloqueio (checklist de início falhou)
        $this->ChecklistModel->registrar_bloqueio($idOs, 'inicio');
        
        $status = $this->ChecklistModel->get_status_by_os_tipo($idOs, 'inicio');
        $this->unit->run((int)$status['bloqueado'], 1, 'Fluxo: OS deve estar bloqueada após registrar_bloqueio');

        // 3. Simula autorização (admin liberando)
        $autorizado = $this->ChecklistModel->finalizar_checklist($idOs, 'inicio', 'Tudo ok', 'autorizar');
        $this->unit->run($autorizado, TRUE, 'Fluxo: A liberação deve retornar true');

        // 4. Verifica se o status mudou
        $status_atualizado = $this->ChecklistModel->get_status_by_os_tipo($idOs, 'inicio');
        $this->unit->run((int)$status_atualizado['bloqueado'], 0, 'Fluxo: Bloqueado deve ser 0 após autorização');

        $this->db->trans_rollback();
    }
}