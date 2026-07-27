<?php
defined('BASEPATH') or exit('No direct script access allowed');

if (!function_exists('permissoes_disponiveis')) {
    // 'usuarios' fica de fora: gerenciar acesso é exclusivo de admin.
    function permissoes_disponiveis()
    {
        return array(
            'menu'         => 'Dashboard',
            'ordemServico' => 'Ordens de Serviço',
            'checklist'    => 'Checklist',
            'produtos'     => 'Produtos',
            'baixas'       => 'Baixas',
            'metas'        => 'Metas',
            'eletricistas' => 'Eletricistas',
        );
    }
}

if (!function_exists('rota_inicial')) {
    function rota_inicial($ehAdmin, $permissoes)
    {
        if ($ehAdmin) {
            return 'home';
        }

        foreach (array_keys(permissoes_disponiveis()) as $chave) {
            if (in_array($chave, (array) $permissoes, true)) {
                return $chave;
            }
        }

        return null;
    }
}
