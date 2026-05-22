<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/ordensServicoModel.php';
checkSession('../view/telas/loginView.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['eletricista_os']) || empty($_POST['data_os']) || empty($_POST['id_produto']) || empty($_POST['qtd_utilizada'])) {
        $_SESSION['erro'] = "Preencha todos os campos obrigatórios.";
        header("Location: ../view/telas/ordensServicoView.php");
        exit;
    }

    $eletricista_os = (int) $_POST['eletricista_os'];
    $data_os = $_POST['data_os'];

    $ids_produtos = $_POST['id_produto'];
    $qtds_utilizadas = $_POST['qtd_utilizada'];

    $osModel = new OrdensServicoModel();
    $materiais_para_salvar = [];

    for ($i = 0; $i < count($ids_produtos); $i++) {
        $id_produto = (int) $ids_produtos[$i];
        $qtd_solicitada = (int) $qtds_utilizadas[$i];

        if ($id_produto > 0 && $qtd_solicitada > 0) {
            $verificacao = $osModel->verificarEstoque($id_produto, $qtd_solicitada);

            if ($verificacao !== true) {
                $_SESSION['erro'] = "Estoque insuficiente para o produto: " . $verificacao;
                header("Location: ../view/telas/ordensServicoView.php");
                exit;
            }

            $materiais_para_salvar[] = [
                'id_produto' => $id_produto,
                'qtd_utilizada' => $qtd_solicitada
            ];
        }
    }

    if (empty($materiais_para_salvar)) {
        $_SESSION['erro'] = "Nenhum material válido foi adicionado à OS.";
        header("Location: ../view/telas/ordensServicoView.php");
        exit;
    }

    try {
        if ($osModel->criarOrdensServico($eletricista_os, $data_os, $materiais_para_salvar)) {
            $_SESSION['sucesso'] = "Ordem de Serviço criada e estoque atualizado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao criar Ordem de Serviço.";
        }
    } catch (Exception $e) {
        $_SESSION['erro'] = "Erro crítico ao processar a OS: " . $e->getMessage();
    }

    header("Location: ../view/telas/ordensServicoView.php");
    exit;
} else {
    header("Location: ../view/telas/ordensServicoView.php");
    exit;
}
