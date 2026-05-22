<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/produtoModel.php';
checkSession('../view/telas/loginView.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $camposFaltando = Validador::checkRequired($_POST, ['nome_produto', 'vlr_unitario', 'qtd_estoque']);
    if (!empty($camposFaltando)) {
        $_SESSION['erro'] = "Preencha todos os campos!";
        header("Location: ../view/telas/produtosView.php");
        exit;
    }

    $nome = trim($_POST['nome_produto']);
    $preco = (float) str_replace(',', '.', $_POST['vlr_unitario']);
    $qtd = (int) $_POST['qtd_estoque'];

    $produtoModel = new ProdutoModel();

    if ($produtoModel->cadastrarProduto($nome, $preco, $qtd)) {
        $_SESSION['sucesso'] = "Produto cadastrado com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao cadastrar produto.";
    }

    header("Location: ../view/telas/produtosView.php");
    exit;
}
