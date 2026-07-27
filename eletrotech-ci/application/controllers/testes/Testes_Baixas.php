<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testes_Baixas extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('unit_test');
        $this->load->model('BaixasModel');
    }

    public function index() {
        $this->unit->use_strict(TRUE);

        $this->testar_filtros_e_consulta();
        $this->testar_calculo_totais();
        $this->testar_detalhe_com_joins();

        echo "<div style='font-family: Arial; padding: 20px;'>";
        echo "<h2>Relatório de Testes: BaixasModel</h2>";
        echo $this->unit->report();
        echo "</div>";
    }

    private function testar_filtros_e_consulta() {
        $this->db->trans_start();

        // 1. Cria um produto e uma movimentação
        $this->db->insert('tabela_produtos', ['nome_produto' => 'Prod Teste Baixa']);
        $idProd = $this->db->insert_id();

        $this->db->insert('tabela_movimentacoes', [
            'id_produto' => $idProd,
            'tipo' => 'entrada',
            'quantidade' => 10,
            'valor_unitario' => 5.00,
            'data_mov' => '2026-07-27',
            'origem' => 'Teste'
        ]);

        // 2. Testa filtro por tipo
        $filtros = ['tipo' => 'entrada', 'id_produto' => $idProd];
        $lista = $this->BaixasModel->consultar($filtros);
        
        $this->unit->run(count($lista), 1, 'Consultar: Deve encontrar a movimentação com os filtros aplicados');
        $this->unit->run((float)$lista[0]['valor_total'], 50.0, 'Consultar: Cálculo do valor total (qtd * vlr) deve ser 50.0');

        $this->db->trans_rollback();
    }

    private function testar_calculo_totais() {
        $this->db->trans_start();

        $this->db->insert('tabela_produtos', ['nome_produto' => 'Prod T']);
        $idProd = $this->db->insert_id();

        // Insere Entrada (10 * 10 = 100)
        $this->db->insert('tabela_movimentacoes', ['id_produto' => $idProd, 'tipo' => 'entrada', 'quantidade' => 10, 'valor_unitario' => 10, 'data_mov' => date('Y-m-d')]);
        // Insere Saída (5 * 4 = 20)
        $this->db->insert('tabela_movimentacoes', ['id_produto' => $idProd, 'tipo' => 'saida', 'quantidade' => 5, 'valor_unitario' => 4, 'data_mov' => date('Y-m-d')]);

        $totais = $this->BaixasModel->totais([]);

        $this->unit->run((float)$totais['total_entrada'], 100.0, 'Totais: Soma de entradas deve ser 100.0');
        $this->unit->run((float)$totais['total_saida'], 20.0, 'Totais: Soma de saídas deve ser 20.0');

        $this->db->trans_rollback();
    }

    private function testar_detalhe_com_joins() {
        $this->db->trans_start();

        // 1. Cria cenário completo: Produto -> OS -> Eletricista -> Movimentação
        // Usando CPF aleatório para evitar conflito de duplicidade no banco
        $this->db->insert('tabela_eletricistas', [
            'nome' => 'João Teste', 
            'cpf' => '123' . rand(100000000, 999999999) 
        ]);
        $idEletricista = $this->db->insert_id();

        $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $idEletricista, 'status' => 'fechada']);
        $idOs = $this->db->insert_id();

        $this->db->insert('tabela_produtos', ['nome_produto' => 'Cabo Teste', 'vlr_unitario' => 10, 'qtd_estoque' => 10]);
        $idProd = $this->db->insert_id();

        $this->db->insert('tabela_movimentacoes', [
            'id_produto' => $idProd, 
            'id_os' => $idOs, 
            'tipo' => 'saida', 
            'quantidade' => 1, 
            'valor_unitario' => 10,
            'data_mov' => date('Y-m-d')
        ]);
        $idMov = $this->db->insert_id();

        // 2. Testa se o detalhe busca tudo (Joins)
        $detalhe = $this->BaixasModel->detalhe($idMov);

        $this->unit->run($detalhe['nome_produto'], 'Cabo Teste', 'Detalhe: Deve carregar o nome do produto via Join');
        $this->unit->run($detalhe['nome_eletricista'], 'João Teste', 'Detalhe: Deve carregar o nome do eletricista da OS via Join');

        $this->db->trans_rollback();
    }
}