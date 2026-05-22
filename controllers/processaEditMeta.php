<?php
session_start();
require_once '../lib-php/libUtils.php';
require_once '../models/metaModel.php';
checkSession('../view/telas/loginView.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['id']) || trim($_POST['id']) === '') {
        $_SESSION['erro'] = "Erro: ID da meta não foi enviado.";
        header("Location: ../view/telas/metasView.php");
        exit;
    }
    if (!isset($_POST['vlr_meta']) || trim($_POST['vlr_meta']) === '') {
        $_SESSION['erro'] = "Erro: O valor da meta não foi preenchido.";
        header("Location: ../view/telas/metasView.php");
        exit;
    }

    $id = (int) $_POST['id'];
    $vlr_meta = (float) $_POST['vlr_meta'];

    $metaModel = new MetaModel();

    if ($metaModel->atualizarMeta($id, $vlr_meta)) {
        $_SESSION['sucesso'] = "Valor da meta atualizado com sucesso!";
    } else {
        $_SESSION['erro'] = "Erro no banco de dados ao atualizar a meta.";
    }

    header("Location: ../view/telas/metasView.php");
    exit;
} else {
    header("Location: ../view/telas/metasView.php");
    exit;
}
