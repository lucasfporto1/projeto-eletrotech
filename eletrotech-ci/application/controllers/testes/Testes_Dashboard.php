<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testes_Dashboard extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('unit_test');
        $this->load->model('DashboardModel');
    }

    public function index() {
        $this->unit->use_strict(TRUE);

        $this->testar_contar_totais();
        $this->testar_os_por_status();
        $this->testar_movimentacao_por_mes();
        $this->testar_os_por_eletricista();

        echo "<div style='font-family: Arial; padding: 20px;'>";
        echo "<h2>Relatório de Testes: DashboardModel</h2>";
        echo $this->unit->report();
        echo "</div>";
    }

    /**
     * Helper para criar um eletricista de teste válido
     * e evitar repetição / erros de FK (foreign key) nos testes.
     */
    private function criarEletricistaTeste($nome = 'Eletricista Teste') {
        $this->db->insert('tabela_eletricistas', [
            'nome' => $nome,
            'data_demissao' => null,
            'cpf' => '000' . rand(100000000, 999999999)
        ]);
        return $this->db->insert_id();
    }

    private function testar_contar_totais() {
        $this->db->trans_start();

        // 1. Insere o Eletricista e captura o ID gerado
        $idEletricista = $this->criarEletricistaTeste('Eletricista Teste Totais');

        // 2. Insere produto
        $this->db->insert('tabela_produtos', ['nome_produto' => 'Prod Teste']);

        // 3. Insere a OS vinculando ao Eletricista criado
        $this->db->insert('tabela_ordens_servico', [
            'status' => 'aberta',
            'eletricista_os' => $idEletricista
        ]);

        // 4. Insere a Meta vinculando ao Eletricista criado
        $this->db->insert('tabela_metas', [
            'vlr_meta' => 100.50,
            'eletricista_meta' => $idEletricista,
            'mes_meta' => date('Y-m')
        ]);

        $totais = $this->DashboardModel->contarTotais();

        // Verifica estrutura
        $this->unit->run(isset($totais['eletricistas']), TRUE, 'ContarTotais: Deve retornar chave "eletricistas"');
        $this->unit->run($totais['metas'] >= 100.50, TRUE, 'ContarTotais: A soma das metas deve ser calculada corretamente');

        $this->db->trans_rollback();
    }

    /**
     * getOsPorStatus() agrega a tabela inteira, então comparar com número
     * absoluto só funcionaria num banco vazio. Medimos a variação (delta)
     * causada pelas OS que este teste cria.
     */
    private function testar_os_por_status() {
        $this->db->trans_start();

        $antes = $this->DashboardModel->getOsPorStatus();

        // Cria um eletricista válido para satisfazer a FK obrigatória de tabela_ordens_servico
        $idEletricista = $this->criarEletricistaTeste('Eletricista Teste Status');

        $this->db->insert('tabela_ordens_servico', [
            'status' => 'aberta',
            'eletricista_os' => $idEletricista
        ]);
        $this->db->insert('tabela_ordens_servico', [
            'status' => 'aberta',
            'eletricista_os' => $idEletricista
        ]);
        $this->db->insert('tabela_ordens_servico', [
            'status' => 'fechada',
            'eletricista_os' => $idEletricista
        ]);

        $depois = $this->DashboardModel->getOsPorStatus();

        $this->unit->run($depois['aberta'] - $antes['aberta'], 2, 'Status: Deve contar exatamente 2 OS abertas a mais');
        $this->unit->run($depois['fechada'] - $antes['fechada'], 1, 'Status: Deve contar exatamente 1 OS fechada a mais');

        $this->db->trans_rollback();
    }

    /**
     * Helper: extrai a linha de um mês específico do retorno de
     * getMovimentacaoPorMes(), já com os totais convertidos para float.
     */
    private function totaisDoMes(array $movs, $mes) {
        foreach ($movs as $m) {
            if ($m['mes'] === $mes) {
                return ['entrada' => (float) $m['entrada'], 'saida' => (float) $m['saida']];
            }
        }
        return ['entrada' => 0.0, 'saida' => 0.0];
    }

    /**
     * Assim como getOsPorStatus(), getMovimentacaoPorMes() soma tudo o que já
     * existe no banco. Comparamos o delta, não o valor absoluto.
     */
    private function testar_movimentacao_por_mes() {
        $this->db->trans_start();
        $mes_atual = date('Y-m');

        $antes = $this->totaisDoMes($this->DashboardModel->getMovimentacaoPorMes(), $mes_atual);

        // Cria um produto válido para satisfazer a FK obrigatória de tabela_movimentacoes
        $this->db->insert('tabela_produtos', ['nome_produto' => 'Produto Teste Movimentação']);
        $idProduto = $this->db->insert_id();

        // Insere 1 Entrada de R$ 50
        $this->db->insert('tabela_movimentacoes', [
            'tipo' => 'entrada', 'quantidade' => 1, 'valor_unitario' => 50,
            'data_mov' => date('Y-m-d'), 'id_produto' => $idProduto
        ]);
        // Insere 1 Saída de R$ 20
        $this->db->insert('tabela_movimentacoes', [
            'tipo' => 'saida', 'quantidade' => 1, 'valor_unitario' => 20,
            'data_mov' => date('Y-m-d'), 'id_produto' => $idProduto
        ]);

        $movs = $this->DashboardModel->getMovimentacaoPorMes();

        // O mês atual tem que aparecer no relatório
        $meses = array_column($movs, 'mes');
        $this->unit->run(in_array($mes_atual, $meses, TRUE), TRUE, 'Movimentação: Mês atual encontrado no relatório');

        $depois = $this->totaisDoMes($movs, $mes_atual);

        $this->unit->run($depois['entrada'] - $antes['entrada'], 50.0, 'Movimentação: Soma de entradas calculada corretamente');
        $this->unit->run($depois['saida'] - $antes['saida'], 20.0, 'Movimentação: Soma de saídas calculada corretamente');

        $this->db->trans_rollback();
    }

    private function testar_os_por_eletricista() {
        $this->db->trans_start();

        // Nome único: o agrupamento é por nome e um homônimo já cadastrado
        // no banco somaria as OS dele às nossas.
        $nome = 'Eletricista A ' . uniqid();
        $id = $this->criarEletricistaTeste($nome);

        $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $id]);
        $this->db->insert('tabela_ordens_servico', ['eletricista_os' => $id]);

        $resultado = $this->DashboardModel->getOsPorEletricista();

        // Busca o nosso eletricista no array retornado
        $encontrado = false;
        foreach ($resultado as $r) {
            if ($r['eletricista'] === $nome) {
                $this->unit->run((int)$r['total'], 2, 'OS por Eletricista: O total de OS para o eletricista deve ser 2');
                $encontrado = true;
            }
        }
        $this->unit->run($encontrado, TRUE, 'OS por Eletricista: Eletricista encontrado no agrupamento');

        $this->db->trans_rollback();
    }
}