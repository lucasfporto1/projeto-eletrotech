<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testes_Produtos extends CI_Controller {

    public function __construct() {
        parent::__construct();
        // Carrega a biblioteca nativa
        $this->load->library('unit_test');
        // Carrega a model
        $this->load->model('ProdutosModel');
    }

    public function index() {
        // Habilita formatação estrita
        $this->unit->use_strict(TRUE);

        // Roda os testes
        $this->testar_insercao_e_movimentacao_inicial();
        $this->testar_update();
        $this->testar_zerar_estoque();
        $this->testar_aumentar_estoque();

        // Exibe o relatório
        echo "<div style='font-family: Arial; padding: 20px;'>";
        echo "<h2>Relatório de Testes: ProdutosModel</h2>";
        echo $this->unit->report();
        echo "</div>";
    }

    private function testar_insercao_e_movimentacao_inicial() {
        $this->db->trans_start(); 

        // Cenário: Criar um produto com estoque inicial > 0
        $dados = array(
            'nome_produto' => 'Produto Teste 1', // Alterado de 'descricao' para 'nome_produto'
            'vlr_unitario' => 50.00,
            'qtd_estoque'  => 10
        );
        
        $sucesso = $this->ProdutosModel->insert($dados);
        $id = $this->db->insert_id();

        // ... resto da função permanece igual
    }

    private function testar_update() {
        $this->db->trans_start();

        // Insere um produto para atualizar depois
        $this->ProdutosModel->insert(['vlr_unitario' => 10.00, 'qtd_estoque' => 0]);
        $id = $this->db->insert_id();

        // Atualiza o valor
        $this->ProdutosModel->update($id, ['vlr_unitario' => 15.50]);

        // Busca e verifica se o valor mudou
        $produto = $this->ProdutosModel->get_by_id($id);
        
        // Convertendo para float para evitar falsos negativos por causa do tipo de dado do banco (string decimal vs float)
        $this->unit->run((float)$produto['vlr_unitario'], 15.50, 'Update: Deve atualizar o valor unitário corretamente');

        $this->db->trans_rollback();
    }

    private function testar_zerar_estoque() {
        $this->db->trans_start();

        // Cria produto com estoque 5
        $this->ProdutosModel->insert(['vlr_unitario' => 20.00, 'qtd_estoque' => 5]);
        $id = $this->db->insert_id();

        // Zera o estoque
        $this->ProdutosModel->zerarEstoque($id);

        // 1. Verifica se a tabela principal zerou
        $produto = $this->ProdutosModel->get_by_id($id);
        $this->unit->run((int)$produto['qtd_estoque'], 0, 'ZerarEstoque: A quantidade no cadastro do produto deve ir para 0');

        // 2. Verifica se a movimentação de SAÍDA foi gerada
        // Order by id DESC para pegar a última movimentação (a saída) já que a entrada inicial gera um registro também.
        $mov_saida = $this->db->order_by('id', 'DESC')->get_where('tabela_movimentacoes', ['id_produto' => $id])->row_array();
        $this->unit->run($mov_saida['tipo'], 'saida', 'ZerarEstoque: Deve gerar uma movimentação do tipo "saida" no ledger');
        $this->unit->run((int)$mov_saida['quantidade'], 5, 'ZerarEstoque: A quantidade da movimentação de saída deve ser igual ao estoque que foi zerado (5)');

        $this->db->trans_rollback();
    }

    private function testar_aumentar_estoque() {
        $this->db->trans_start();

        // Cria produto com estoque 2
        $this->ProdutosModel->insert(['vlr_unitario' => 30.00, 'qtd_estoque' => 2]);
        $id = $this->db->insert_id();

        // Aumenta o estoque em +3
        $this->ProdutosModel->aumentarQtdEstoque($id, 3);

        // 1. Verifica se a matemática deu certo (2 + 3 = 5)
        $produto = $this->ProdutosModel->get_by_id($id);
        $this->unit->run((int)$produto['qtd_estoque'], 5, 'AumentarEstoque: O saldo total do produto deve ser a soma exata (2 + 3 = 5)');

        // 2. Verifica a movimentação de ENTRADA
        $mov_entrada = $this->db->order_by('id', 'DESC')->get_where('tabela_movimentacoes', ['id_produto' => $id])->row_array();
        $this->unit->run($mov_entrada['tipo'], 'entrada', 'AumentarEstoque: Deve gerar uma movimentação do tipo "entrada"');
        $this->unit->run((int)$mov_entrada['quantidade'], 3, 'AumentarEstoque: A quantidade do registro deve refletir o valor adicionado (3)');

        $this->db->trans_rollback();
    }
}
