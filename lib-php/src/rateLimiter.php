<?php

/**
 * Impede abusos de requisições limitando tentativas por tempo.
 * Retorna TRUE se o usuário estiver bloqueado, FALSE se estiver liberado.
 */
function isRateLimited(string $acao, int $limiteTentativas = 5, int $bloqueioSegundos = 60): bool
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    $chaveTentativas = "rl_{$acao}_tentativas";
    $chaveBloqueio = "rl_{$acao}_bloqueio_ate";

    if (isset($_SESSION[$chaveBloqueio])) {
        if (time() < $_SESSION[$chaveBloqueio]) {
            return true;
        } else {

            unset($_SESSION[$chaveBloqueio]);
            unset($_SESSION[$chaveTentativas]);
        }
    }

    $_SESSION[$chaveTentativas] = ($_SESSION[$chaveTentativas] ?? 0) + 1;

    if ($_SESSION[$chaveTentativas] > $limiteTentativas) {
        $_SESSION[$chaveBloqueio] = time() + $bloqueioSegundos;
        return true;
    }

    return false;
}