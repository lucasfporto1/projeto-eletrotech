<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/metaModel.php';
checkSession('../view/telas/loginView.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $camposFaltando = Validador::checkRequired($_POST, ['eletricista_meta', 'mes_meta', 'vlr_meta']);
    if (!empty($camposFaltando)) {
        $_SESSION['erro'] = "Preencha todos os campos!";
        header("Location: ../view/telas/metasView.php");
        exit;
    }

    $eletricista = (int)$_POST['eletricista_meta'];
    $mes = (trim($_POST['mes_meta']));
    $vlr = (float) str_replace(',', '.', $_POST['vlr_meta']);

    $metaModel = new MetaModel();

    if ($metaModel->cadastrarMeta($eletricista, $mes, $vlr)) {
        $_SESSION['sucesso'] = "Meta cadastrada com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro ao cadastrar meta.";
    }

    header("Location: ../view/telas/metasView.php");
    exit;
}
