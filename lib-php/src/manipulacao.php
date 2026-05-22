<?php
function formatToBrl(float $value): string
{
    return 'R$ ' . number_format($value, 2, ',', '.');
}

function brlToFloat(string $value): float
{
    $value = str_replace(['R$', ' ', '.'], '', $value);
    $value = str_replace(',', '.', $value);

    return (float) $value;
}