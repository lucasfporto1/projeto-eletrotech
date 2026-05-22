<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/UsuarioModel.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $camposFaltando = Validador::checkRequired($_POST, ['nome', 'senha']);
    if (!empty($camposFaltando)) {
        $_SESSION['erro'] = "Por favor, preencha todos os campos.";
        header("Location: ../view/telas/cadastroView.php");
        exit;
    }

    $nomeUsuario = trim($_POST['nome']);
    $senha = $_POST['senha'];

    $usuarioModel = new UsuarioModel();

    if ($usuarioModel->usuarioExiste($nomeUsuario)) {
        $_SESSION['erro'] = "Este nome de utilizador já está em uso. Escolha outro.";
        header("Location: ../view/telas/cadastroView.php");
        exit;
    }

    if ($usuarioModel->criarUsuario($nomeUsuario, $senha)) {
        $_SESSION['sucesso'] = "Conta criada com sucesso! Faça login.";
        header("Location: ../view/telas/loginView.php");
    } else {
        $_SESSION['erro'] = "Erro ao registar na base de dados.";
        header("Location: ../view/telas/cadastroView.php");
    }
    exit;
} else {
    header("Location: ../view/telas/loginView.php");
    exit;
}
