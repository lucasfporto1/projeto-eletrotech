<?php
function carregarEnv($caminho)
{
    if (!file_exists($caminho)) {
        return false;
    }

    $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($linhas as $linha) {
        if (strpos(trim($linha), '#') === 0) {
            continue;
        }

        if (strpos($linha, '=') !== false) {
            list($nome, $valor) = explode('=', $linha, 2);
            $nome = trim($nome);
            $valor = trim($valor);
            $valor = trim($valor, '"\''); // Remove aspas

            if (!array_key_exists($nome, $_SERVER) && !array_key_exists($nome, $_ENV)) {
                putenv(sprintf('%s=%s', $nome, $valor));
                $_ENV[$nome] = $valor;
                $_SERVER[$nome] = $valor;
            }
        }
    }
    return true;
}
