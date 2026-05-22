<?php
function createHash(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyHash(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}