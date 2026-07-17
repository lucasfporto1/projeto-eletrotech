<?php /** @var string $ativo */ ?>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid px-4">
        <a class="navbar-brand d-flex align-items-center" href="<?= site_url('menu') ?>">
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
                    <a class="nav-link <?= $ativo === 'menu' ? 'active' : '' ?>" href="<?= site_url('menu') ?>">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= $ativo === 'usuarios' ? 'active' : '' ?>" href="<?= site_url('usuarios') ?>">Usuários</a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link text-danger fw-bold" style="background-color: transparent;" href="<?= site_url('auth/sair') ?>">Sair</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
