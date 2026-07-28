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

        $this->testar_insercao_e_perguntas();
        $this->testar_selecao_padrao();
        $this->testar_listagem_e_delete();

        echo "<div style='font-family: Arial; padding: 20px;'>";
        echo "<h2>Relatório de Testes: ChecklistModel</h2>";
        echo $this->unit->report();
        echo "</div>";
    }

    /**
     * ATENÇÃO: tabela_checklist.tipo é um ENUM('inicio','fim'). Qualquer outro
     * valor é gravado como string vazia (o banco roda sem STRICT_TRANS_TABLES,
     * porque database.php usa 'stricton' => FALSE) e as buscas por tipo param
     * de encontrar o registro. Use só 'inicio' ou 'fim' nos testes.
     */
    private function testar_insercao_e_perguntas() {
        $this->db->trans_start();

        $dados = ['titulo' => 'Checklist Instalação', 'tipo' => 'inicio', 'selecionado' => 0];
        $perguntas = [
            ['texto' => 'O disjuntor está desligado?', 'tipo_resposta' => 'radio'],
            ['texto' => 'Fiação isolada?', 'tipo_resposta' => 'text']
        ];

        // 1. Testa Inserção
        $idChecklist = $this->ChecklistModel->insert_checklist($dados, $perguntas);
        $this->unit->run($idChecklist > 0, TRUE, 'Insert: Deve criar checklist e retornar ID');

        // 2. Verifica se as perguntas foram salvas
        $perguntas_salvas = $this->ChecklistModel->get_perguntas($idChecklist);
        $this->unit->run(count($perguntas_salvas), 2, 'Perguntas: Deve ter salvado exatamente 2 perguntas');
        $this->unit->run($perguntas_salvas[0]['texto_pergunta'], 'O disjuntor está desligado?', 'Perguntas: O texto da pergunta 1 está correto');

        $this->db->trans_rollback();
    }

    private function testar_selecao_padrao() {
        $this->db->trans_start();

        // Cria dois checklists do mesmo tipo
        $id1 = $this->ChecklistModel->insert_checklist(['titulo' => 'C1', 'tipo' => 'fim'], ['P1']);
        $id2 = $this->ChecklistModel->insert_checklist(['titulo' => 'C2', 'tipo' => 'fim'], ['P2']);

        // Define C2 como padrão
        $this->ChecklistModel->select_default($id2, 'fim');

        // Busca o selecionado
        $selecionado = $this->ChecklistModel->get_selected_by_type('fim');

        // Cast obrigatório: row_array() devolve tudo como string e o
        // use_strict(TRUE) compara também o tipo.
        $this->unit->run((int)$selecionado['id'], (int)$id2, 'Seleção: O checklist selecionado deve ser o ID 2');
        
        // Verifica se C1 foi desmarcado
        $c1 = $this->db->get_where('tabela_checklist', ['id' => $id1])->row_array();
        $this->unit->run((int)$c1['selecionado'], 0, 'Seleção: O checklist anterior deve estar desmarcado (0)');

        $this->db->trans_rollback();
    }

    private function testar_listagem_e_delete() {
        $this->db->trans_start();

        // Insere checklist
        $id = $this->ChecklistModel->insert_checklist(['titulo' => 'Para Deletar', 'tipo' => 'inicio'], ['Pergunta']);
        
        // Testa get_all (verifica se retorna pelo menos o que criamos)
        $lista = $this->ChecklistModel->get_all();
        $encontrado = false;
        foreach($lista as $item) { if($item['id'] == $id) $encontrado = true; }
        $this->unit->run($encontrado, TRUE, 'Listagem: O checklist deve aparecer em get_all()');

        // Testa Delete
        $this->ChecklistModel->delete_checklist($id);
        $check = $this->db->get_where('tabela_checklist', ['id' => $id])->row_array();
        $this->unit->run(empty($check), TRUE, 'Delete: O checklist deve ser removido do banco');

        $this->db->trans_rollback();
    }
}