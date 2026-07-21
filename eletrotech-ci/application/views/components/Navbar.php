<?php

/** @var string $ativo */ ?>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="<?= site_url('home') ?>">
            <img src="<?= base_url('assets/logo-eletrotech.png') ?>" alt="Logo Eletrotech">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                <li class="nav-item">
                    <a class="nav-link <?= $ativo === 'menu' ? 'active' : '' ?>" href="<?= site_url('menu') ?>">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $ativo === 'usuarios' ? 'active' : '' ?>" href="<?= site_url('usuarios') ?>">Usuários</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $ativo === 'eletricistas' ? 'active' : '' ?>" href="<?= site_url('eletricistas') ?>">Eletricistas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $ativo === 'produtos' ? 'active' : '' ?>" href="<?= site_url('produtos') ?>">Produtos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $ativo === 'metas' ? 'active' : '' ?>" href="<?= site_url('metas') ?>">Metas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $ativo === 'ordemServico' ? 'active' : '' ?>" href="<?= site_url('ordemServico') ?>">Ordens de Serviço</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $ativo === 'baixas' ? 'active' : '' ?>" href="<?= site_url('baixas') ?>">Baixas</a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link text-danger fw-bold" style="background-color: transparent;" href="<?= site_url('auth/sair') ?>">Sair</a>
                </li>
            </ul>
        </div>
    </div>
</nav>