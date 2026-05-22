<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/usuarioModel.php';
checkSession('../view/telas/loginView.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (empty($_POST['id']) || empty($_POST['usuario'])) {
        $_SESSION['erro'] = "O nome de utilizador não pode estar vazio.";
        header("Location: ../view/telas/usuariosView.php");
        exit;
    }

    $id = (int) $_POST['id'];
    $nomeUsuario = trim($_POST['usuario']);

    $usuarioModel = new UsuarioModel();

    try {
        if ($usuarioModel->atualizarUsuario($id, $nomeUsuario)) {
            $_SESSION['sucesso'] = "Utilizador atualizado com sucesso!";
        } else {
            $_SESSION['erro'] = "Erro ao atualizar o utilizador.";
        }
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            $_SESSION['erro'] = "Erro: Já existe um utilizador com o nome '{$nomeUsuario}'. Escolha outro.";
        } else {
            $_SESSION['erro'] = "Erro no banco de dados: " . $e->getMessage();
        }
    }

    header("Location: ../view/telas/usuariosView.php");
    exit;
} else {
    header("Location: ../view/telas/usuariosView.php");
    exit;
}
