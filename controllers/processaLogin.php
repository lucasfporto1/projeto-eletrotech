<?php
session_start();

require_once '../lib-php/libUtils.php';
require_once '../models/UsuarioModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isRateLimited('login', 5, 60)) {
        $_SESSION['erro'] = "Muitas tentativas falhadas. Tente novamente mais tarde.";
        header("Location: ../view/telas/loginView.php");
        exit;
    }

    $camposFaltando = Validador::checkRequired($_POST, ['nome', 'senha']);
    if (!empty($camposFaltando)) {
        $_SESSION['erro'] = "Por favor, preencha todos os campos.";
        header("Location: ../view/telas/loginView.php");
        exit;
    }

    $nomeUsuario = trim($_POST['nome']);
    $senha = $_POST['senha'];

    $usuarioModel = new UsuarioModel();
    $utilizador = $usuarioModel->buscarUsuarioPorNome($nomeUsuario);

    if ($utilizador && verifyHash($senha, $utilizador['senha'])) {

        $_SESSION['user_id'] = $utilizador['id'];
        $_SESSION['usuario'] = $nomeUsuario;

        header("Location: ../view/telas/menuView.php");
        exit;
    } else {
        $_SESSION['erro'] = "Credenciais inválidas.";
        header("Location: ../view/telas/loginView.php");
        exit;
    }
} else {
    header("Location: ../view/telas/loginView.php");
    exit;
}
