<?php
function checkSession($loginPage)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header("Location: $loginPage");
        exit;
    }

    $db = ConexaoBanco::connect();
    $id = $_SESSION['user_id'];

    $sql = "SELECT id FROM tabela_usuarios WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        session_destroy();
        header("Location: $loginPage");
        exit;
    }
}
