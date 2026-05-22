<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/ProdutoModel.php';
checkSession('../view/telas/loginView.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['id']) || empty($_POST['nome_produto']) || empty($_POST['vlr_unitario'])) {
        $_SESSION['erro'] = "Preencha todos os campos para editar o produto.";
        header("Location: ../view/telas/produtosView.php");
        exit;
    }

    $id = (int) $_POST['id'];
    $nomeProduto = trim($_POST['nome_produto']);
    $vlrUnitario = (float) $_POST['vlr_unitario'];

    $produtoModel = new ProdutoModel();

    if ($produtoModel->atualizarProduto($id, $nomeProduto, $vlrUnitario)) {
        $_SESSION['sucesso'] = "Produto atualizado com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao atualizar o produto.";
    }

    header("Location: ../view/telas/produtosView.php");
    exit;
} else {
    header("Location: ../view/telas/produtosView.php");
    exit;
}
