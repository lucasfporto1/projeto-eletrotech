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

if (!function_exists('permissoes_padrao_eletricista')) {
    // Acessos que toda conta de eletricista recebe ao ser criada junto com o
    // cadastro. 'menu' fica de fora: aquele dashboard é gerencial e mostra os
    // números de todos os eletricistas.
    function permissoes_padrao_eletricista()
    {
        return array('ordemServico');
    }
}

if (!function_exists('primeira_tela_liberada')) {
    // Primeira tela que o usuário pode abrir, ou NULL se ele não tiver nenhuma.
    // É o destino do botão da tela de boas-vindas.
    function primeira_tela_liberada($ehAdmin, $permissoes)
    {
        if ($ehAdmin) {
            return 'menu';
        }

        foreach (array_keys(permissoes_disponiveis()) as $chave) {
            if (in_array($chave, (array) $permissoes, true)) {
                return $chave;
            }
        }

        return null;
    }
}

if (!function_exists('rota_inicial')) {
    // Para onde mandar o usuário depois do login: todos passam pelas boas-vindas.
    // NULL significa "sem nenhum acesso liberado" e faz o login ser recusado.
    function rota_inicial($ehAdmin, $permissoes)
    {
        return primeira_tela_liberada($ehAdmin, $permissoes) === null ? null : 'home';
    }
}
