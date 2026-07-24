<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <title><?= $titulo ?></title>
    <style>
        body {
            background-color: #3c3b3b;
            color: white;
            padding-bottom: 50px;
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

        .boas-vindas {
            text-align: center;
            margin-top: 2.5rem;
            margin-bottom: 2rem;
        }

        .boas-vindas h1 {
            font-weight: 700;
            font-size: 1.8rem;
        }

        .boas-vindas h1 span {
            color: #FBD814;
        }

        .boas-vindas p {
            color: #a0a0a0;
            font-size: 15px;
        }

        .card-resumo {
            background-color: #282828;
            border: 1px solid rgba(251, 216, 20, 0.25);
            border-radius: 14px;
            padding: 22px;
            height: 100%;
            transition: 0.3s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        }

        .card-resumo:hover {
            border-color: #FBD814;
            transform: translateY(-3px);
        }

        .card-resumo .icone {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 14px;
        }

        .card-resumo .icone.amarelo {
            background-color: rgba(251, 216, 20, 0.15);
            color: #FBD814;
        }

        .card-resumo .icone.verde {
            background-color: rgba(25, 135, 84, 0.15);
            color: #43c98a;
        }

        .card-resumo .icone.cinza {
            background-color: rgba(160, 160, 160, 0.15);
            color: #a0a0a0;
        }

        .card-resumo .valor {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .card-resumo .rotulo {
            color: #ccc;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }

        #acoes_dashboard {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 2.5rem 0;
            flex-wrap: wrap;
        }

        #acoes_dashboard button {
            background-color: #ebca1e;
            color: #282828;
            border-radius: 30px;
            padding: 12px 24px;
            border: none;
            font-weight: bold;
            transition: 0.3s;
        }

        #acoes_dashboard button:hover {
            background-color: #ffffff;
            color: #282828;
        }

        #acoes_dashboard button.secundario {
            background-color: transparent;
            color: #ffffff;
            border: 1px solid #FBD814;
        }

        #acoes_dashboard button.secundario:hover {
            background-color: rgba(251, 216, 20, 0.15);
            color: #ffffff;
        }

        h2.titulo-secao {
            color: #FBD814;
            font-size: 1.1rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 2.5rem 0 1rem;
        }

        table.table.custom-table,
        table.table.custom-table th,
        table.table.custom-table td {
            border-color: #FBD814;
            vertical-align: middle;
        }

        table.table.custom-table thead th {
            color: #FBD814;
            font-size: 1.1rem;
        }

        table.table.custom-table td.empty-state {
            text-align: center;
            color: #a0a0a0;
            padding: 2rem;
            font-style: italic;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .img-dash {
            max-height: 80px;
            width: auto;
            object-fit: contain;
        }

        .alert-success {
            background-color: transparent !important;
            color: #198754 !important;
            border: 1px solid #198754 !important;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-out;
            font-weight: 500;
        }

        .alert-danger {
            background-color: transparent !important;
            color: #dc3545 !important;
            border: 1px solid #dc3545 !important;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-out;
            font-weight: 500;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-custom">
        <div class="container-fluid px-4">
                    <img src="<?= base_url('assets/logo-eletrotech.png') ?>" alt="Logo Eletrotech" class="img-dash">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                        <li class="nav-item">
                            <a class="nav-link <?= $ativo === 'ordemServico' ? 'active' : '' ?>" href="<?= site_url('ordemServico') ?>">Minhas Ordens</a>
                        </li>
                    <li class="nav-item ms-3">
                        <a class="nav-link text-danger fw-bold" style="background-color: transparent;" href="<?= site_url('auth/sair') ?>">Sair</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-3">
        <?php if ($this->session->flashdata('erro')): ?>
            <div class="alert alert-danger text-center"><?= $this->session->flashdata('erro') ?></div>
        <?php endif; ?>
        <?php if ($this->session->flashdata('sucesso')): ?>
            <div class="alert alert-success text-center"><?= $this->session->flashdata('sucesso') ?></div>
        <?php endif; ?>
    </div>

    <div class="container">
        <div class="boas-vindas">
            <h1>Olá, <span><?= htmlspecialchars($this->session->userdata('usuario')) ?></span></h1>
            <p>Aqui está um resumo das suas Ordens de Serviço.</p>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card-resumo">
                    <div class="icone amarelo"><i class="fa-solid fa-lock-open"></i></div>
                    <div class="valor"><?= (int) ($totalAbertas ?? 0) ?></div>
                    <div class="rotulo">OSs Abertas</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-resumo">
                    <div class="icone verde"><i class="fa-solid fa-lock"></i></div>
                    <div class="valor"><?= (int) ($totalFechadas ?? 0) ?></div>
                    <div class="rotulo">OSs Fechadas</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-resumo">
                    <div class="icone cinza"><i class="fa-solid fa-clipboard-list"></i></div>
                    <div class="valor"><?= (int) ($totalGeral ?? 0) ?></div>
                    <div class="rotulo">Total de OSs</div>
                </div>
            </div>
        </div>

        <div id="acoes_dashboard">
            <a href="<?= site_url('ordemServico') ?>"><button><i class="fa-solid fa-list"></i> Ver Minhas OSs</button></a>
            <a href="<?= site_url('ordemServico') ?>"><button class="secundario"><i class="fa-solid fa-plus"></i> Registrar Nova OS</button></a>
        </div>

        <h2 class="titulo-secao">Últimas Ordens de Serviço</h2>

        <table class="table table-dark table-hover table-bordered custom-table text-center">
            <thead>
                <tr>
                    <th scope="col" style="width: 20%;">ID OS</th>
                    <th scope="col" style="width: 30%;">Data da Operação</th>
                    <th scope="col" style="width: 30%;">Data de Fechamento</th>
                    <th scope="col" style="width: 20%;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ultimasOS)): ?>
                    <?php foreach ($ultimasOS as $os): ?>
                        <tr>
                            <td>#<?= str_pad($os['id'], 5, "0", STR_PAD_LEFT) ?></td>
                            <td><?= !empty($os['data_os']) ? date('d/m/Y', strtotime($os['data_os'])) : '-' ?></td>
                            <td><?= !empty($os['data_fechamento']) ? date('d/m/Y', strtotime($os['data_fechamento'])) : '-' ?></td>
                            <td>
                                <?php if ($os['status'] === 'aberta'): ?>
                                    <span class="badge bg-success">Aberta</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Fechada</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-state">Nenhuma Ordem de Serviço registrada ainda.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php $this->load->view('components/Chatbot'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>