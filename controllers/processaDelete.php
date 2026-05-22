<?php
session_start();
require_once '../lib-php/libUtils.php';
checkSession('../view/telas/loginView.php');

if (isset($_GET['tabela']) && isset($_GET['id']) && isset($_GET['origem'])) {
    $tabela = preg_replace('/[^a-zA-Z0-9_]/', '', $_GET['tabela']);
    $id = (int) $_GET['id'];
    $origem = $_GET['origem'];

    $db = ConexaoBanco::connect();

    try {
        if ($tabela === 'tabela_produtos') {
            $sql = "UPDATE tabela_produtos SET qtd_estoque = 0 WHERE id = ?";
            $mensagem_sucesso = "Estoque do produto zerado com sucesso!";
        } elseif ($tabela === 'tabela_eletricistas') {
            $sql = "UPDATE tabela_eletricistas SET data_demissao = CURRENT_DATE WHERE id = ?";
            $mensagem_sucesso = "Eletricista demitido com sucesso!";
        } else {
            $sql = "DELETE FROM {$tabela} WHERE id = ?";
            $mensagem_sucesso = "Registo excluído com sucesso!";
        }

        $stmt = $db->prepare($sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $_SESSION['sucesso'] = $mensagem_sucesso;
        } else {
            $_SESSION['erro'] = "Erro ao processar exclusão.";
        }
    } catch (Exception $e) {
        $_SESSION['erro'] = "Não foi possível excluir. O registo pode estar vinculado a outras informações no sistema.";
    }

    header("Location: ../view/telas/" . $origem);
    exit;
} else {
    header("Location: ../view/telas/menuView.php");
    exit;
}
