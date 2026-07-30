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

        $this->testar_fluxo_completo_os();
        $this->testar_bloqueio_estoque_insuficiente();
        $this->testar_contadores_por_eletricista();

        echo "<div style='font-family: Arial; padding: 20px;'>";
        echo "<h2>Relatório de Testes: OrdemServicoModel</h2>";
        echo $this->unit->report();
        echo "</div>";
    }

    private function testar_fluxo_completo_os() {
        $this->db->trans_start();

        // 1. Setup: Eletricista e Produto
        $this->db->insert('tabela_eletricistas', [
            'nome' => 'Eletricista Teste OS', 
            'cpf' => '000' . rand(100000000, 999999999)
        ]);
        $idEletricista = $this->db->insert_id();

        $this->db->insert('tabela_produtos', ['nome_produto' => 'Cabo Teste', 'qtd_estoque' => 10, 'vlr_unitario' => 5.0]);
        $idProd = $this->db->insert_id();

        // 2. Solicitar
        $idOs = $this->OrdemServicoModel->solicitar_os($idEletricista, date('Y-m-d'));
        $this->unit->run($idOs > 0, TRUE, 'Ciclo: Deve solicitar a OS com sucesso');

        // 3. Abrir (Baixa de Estoque)
        $produtos = [['id' => $idProd, 'qtd' => 2]];
        $abriu = $this->OrdemServicoModel->abrir_os($idOs, $produtos);
        $this->unit->run($abriu, TRUE, 'Ciclo: Deve abrir a OS e baixar estoque');

        // Verifica estoque baixado (10 - 2 = 8)
        $prod = $this->db->get_where('tabela_produtos', ['id' => $idProd])->row_array();
        $this->unit->run((int)$prod['qtd_estoque'], 8, 'Estoque: Estoque deve ser 8 após abertura');

        // 4. Fechar
        $fechou = $this->OrdemServicoModel->fechar_os($idOs, []);
        $this->unit->run($fechou, TRUE, 'Ciclo: Deve fechar a OS com sucesso');

        $this->db->trans_rollback();
    }

    private function testar_bloqueio_estoque_insuficiente() {
        $this->db->trans_start();

        $this->db->insert('tabela_eletricistas', ['nome' => 'Eletricista Teste 2', 'cpf' => '000' . rand(100000000, 999999999)]);
        $idEletricista = $this->db->insert_id();

        $this->db->insert('tabela_produtos', ['nome_produto' => 'Produto Raro', 'qtd_estoque' => 1, 'vlr_unitario' => 10]);
        $idProd = $this->db->insert_id();

        $idOs = $this->OrdemServicoModel->solicitar_os($idEletricista, date('Y-m-d'));

        // Tenta gastar 5 (tem apenas 1)
        $produtos = [['id' => $idProd, 'qtd' => 5]];
        $abriu = $this->OrdemServicoModel->abrir_os($idOs, $produtos);

        $this->unit->run($abriu, FALSE, 'Validação: Não deve permitir abrir OS com estoque insuficiente');
        
        $this->db->trans_rollback();
    }

    private function testar_contadores_por_eletricista() {
        $this->db->trans_start();

        $this->db->insert('tabela_eletricistas', ['nome' => 'Eletricista Contador', 'cpf' => '000' . rand(100000000, 999999999)]);
        $id = $this->db->insert_id();

        // Insere 2 solicitadas
        $this->OrdemServicoModel->solicitar_os($id, date('Y-m-d'));
        $this->OrdemServicoModel->solicitar_os($id, date('Y-m-d'));

        $total = $this->OrdemServicoModel->totalSolicitadasPorEletricista($id);
        $this->unit->run($total, 2, 'Contador: Deve contar exatamente 2 OS solicitadas');

        $this->db->trans_rollback();
    }
}