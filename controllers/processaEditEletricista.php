<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/EletricistaModel.php';
checkSession('../view/telas/loginView.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['id']) || empty($_POST['nome'])) {
        $_SESSION['erro'] = "Dados incompletos para edição.";
        header("Location: ../view/telas/eletricistasView.php");
        exit;
    }

    $id = (int) $_POST['id'];
    $nome = trim($_POST['nome']);

    $eletricistaModel = new EletricistaModel();

    if ($eletricistaModel->atualizarEletricista($id, $nome)) {
        $_SESSION['sucesso'] = "Eletricista atualizado com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao atualizar os dados do eletricista.";
    }

    header("Location: ../view/telas/eletricistasView.php");
    exit;
} else {
    header("Location: ../view/telas/eletricistasView.php");
    exit;
}
