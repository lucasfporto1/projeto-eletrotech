<?php
require_once '../lib-php/libUtils.php';
$db = ConexaoBanco::connect();

if (isset($_GET['id_os'])) {
    $id_os = (int)$_GET['id_os'];
    $sql = "SELECT p.nome_produto, m.qtd_utilizada 
            FROM tabela_os_materiais m
            JOIN tabela_produtos p ON m.id_produto = p.id
            WHERE m.id_os = ?";
    $stmt = $db->prepare($sql);
    $stmt->bind_param("i", $id_os);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<ul class="list-group list-group-flush" style="background: transparent;">';
    while ($row = $result->fetch_assoc()) {
        echo '<li class="list-group-item" style="background: transparent; color: white; border-color: #555;">';
        echo htmlspecialchars($row['nome_produto']) . ' - <strong>' . $row['qtd_utilizada'] . ' un</strong>';
        echo '</li>';
    }
    echo '</ul>';
}
