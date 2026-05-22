<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/eletricistaModel.php';
checkSession('../view/telas/loginView.php');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $origem = $_GET['origem'] ?? 'eletricistasView.php';

    $eletricistaModel = new EletricistaModel();

    if ($eletricistaModel->reativarEletricista($id)) {
        $_SESSION['sucesso'] = "Eletricista readmitido com sucesso! Ele já pode receber novas Ordens de Serviço.";
    } else {
        $_SESSION['erro'] = "Erro ao reativar eletricista.";
    }

    header("Location: ../view/telas/" . $origem);
    exit;
} else {
    header("Location: ../view/telas/menuView.php");
    exit;
}
