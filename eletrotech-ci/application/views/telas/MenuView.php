<?php
/** @var array $totais */
/** @var string $usuario */
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <title>Menu Principal - EletroTech</title>
    <style>
        body {
            background-color: #3c3b3b;
            color: white;
            font-family: 'DM Sans', sans-serif;
        }

        nav.navbar.navbar-custom {
            background-color: #282828;
            padding-top: 15px;
            padding-bottom: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
        }

        nav.navbar.navbar-custom .navbar-brand img {
            max-height: 80px;
            width: auto;
            object-fit: contain;
        }

        nav.navbar.navbar-custom ul.navbar-nav .nav-link {
            color: #ffffff;
            font-weight: 500;
            font-size: 16px;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        nav.navbar.navbar-custom ul.navbar-nav .nav-link:hover,
        nav.navbar.navbar-custom ul.navbar-nav .nav-link.active {
            color: #282828;
            background-color: #FBD814;
            border-radius: 6px;
        }

        nav.navbar.navbar-custom .navbar-toggler {
            border-color: #FBD814;
            padding: 8px;
        }

        nav.navbar.navbar-custom .navbar-toggler-icon {
            background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3e%3cpath stroke='%23FBD814' stroke-width='2.5' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .dashboard-header {
            margin-top: 3rem;
            margin-bottom: 2.5rem;
            border-left: 5px solid #FBD814;
            padding-left: 15px;
        }

        .dashboard-header h1 {
            font-weight: 700;
            font-size: 28px;
            margin: 0;
        }

        .dashboard-header p {
            color: #a0a0a0;
            margin: 5px 0 0 0;
            font-size: 16px;
        }

        .dashboard-card {
            background-color: #1f1f1f;
            border-radius: 12px;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 4px solid #FBD814;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.4);
            background-color: #242424;
        }

        .dashboard-card .icon-metric {
            font-size: 40px;
            color: #FBD814;
            margin-bottom: 15px;
        }

        .dashboard-card .metric-value {
            font-size: 36px;
            font-weight: 700;
            color: white;
            line-height: 1;
            margin-bottom: 5px;
        }

        .dashboard-card .metric-label {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ccc;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <?php $this->load->view('components/Navbar', array('ativo' => 'menu')); ?>

    <div class="container">
        <div class="dashboard-header">
            <h1>Olá, <?= htmlspecialchars($usuario ?? 'Usuário') ?>!</h1>
            <p>Bem-vindo(a) ao painel de controle da EletroTech Soluções Elétricas.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="dashboard-card">
                    <i class="fa-solid fa-users icon-metric"></i>
                    <div class="metric-value"><?= $totais['eletricistas'] ?></div>
                    <div class="metric-label">Eletricistas Ativos</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="dashboard-card">
                    <i class="fa-solid fa-box-open icon-metric"></i>
                    <div class="metric-value"><?= $totais['produtos'] ?></div>
                    <div class="metric-label">Produtos Cadastrados</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="dashboard-card">
                    <i class="fa-solid fa-clipboard-list icon-metric"></i>
                    <div class="metric-value"><?= $totais['os'] ?></div>
                    <div class="metric-label">OS Realizadas</div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="dashboard-card">
                    <i class="fa-solid fa-chart-line icon-metric"></i>
                    <div class="metric-value">R$ <?= number_format($totais['metas'], 2, ',', '.') ?></div>
                    <div class="metric-label">Metas Atingidas</div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body" id="toast-message"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showToast(mensagem, tipo) {
            const toastElement = document.getElementById('liveToast');
            const toastBody = document.getElementById('toast-message');

            toastElement.className = 'toast align-items-center text-white border-0 ' + (tipo === 'erro' ? 'bg-danger' : 'bg-success');
            toastBody.textContent = mensagem;

            const toast = new bootstrap.Toast(toastElement);
            toast.show();
        }
    </script>
    <?php if ($sucesso = $this->session->flashdata('sucesso')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                showToast("<?= $sucesso ?>", 'sucesso');
            });
        </script>
    <?php endif; ?>

    <?php if ($erro = $this->session->flashdata('erro')): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                showToast("<?= $erro ?>", 'erro');
            });
        </script>
    <?php endif; ?>

    <?php $this->load->view('components/Chatbot'); ?>
</body>

</html>
