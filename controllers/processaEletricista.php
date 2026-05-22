<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/EletricistaModel.php';
checkSession('../view/telas/loginView.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $camposFaltando = Validador::checkRequired($_POST, ['cpf', 'nome', 'data_contratacao']);
    if (!empty($camposFaltando)) {
        $_SESSION['erro'] = "Por favor, preencha todos os campos obrigatórios.";
        header("Location: ../view/telas/eletricistasView.php");
        exit;
    }

    $cpf = trim($_POST['cpf']);
    $nome = trim($_POST['nome']);
    $data_contratacao = $_POST['data_contratacao'];

    if (Verificador::exists('tabela_eletricistas', 'cpf', $cpf)) {
        $_SESSION['erro'] = "Já existe um eletricista registado com este CPF.";
        header("Location: ../view/telas/eletricistasView.php");
        exit;
    }

    $eletricistaModel = new EletricistaModel();

    if ($eletricistaModel->cadastrarEletricista($cpf, $nome, $data_contratacao)) {
        $_SESSION['sucesso'] = "Eletricista registado com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao registar o eletricista na base de dados.";
    }

    header("Location: ../view/telas/eletricistasView.php");
    exit;
} else {
    header("Location: ../view/telas/eletricistasView.php");
    exit;
}
