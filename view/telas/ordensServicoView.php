<?php
require_once '../../lib-php/libUtils.php';
checkSession('loginView.php');

$db = ConexaoBanco::connect();

$queryEletricistas = "SELECT id, nome FROM tabela_eletricistas WHERE data_demissao IS NULL ORDER BY nome";
$resultadoEletricistas = $db->query($queryEletricistas);

$queryProdutos = "SELECT id, nome_produto, qtd_estoque FROM tabela_produtos WHERE qtd_estoque > 0 ORDER BY nome_produto";
$resultadoProdutos = $db->query($queryProdutos);

$queryOS = "SELECT os.id, os.data_os, e.nome as nome_eletricista 
            FROM tabela_ordens_servico os 
            JOIN tabela_eletricistas e ON os.eletricista_os = e.id 
            ORDER BY os.id DESC";
$resultadoOS = $db->query($queryOS);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <title>Ordens de Serviço - EletroTech</title>
    <style>
        body {
            background-color: #3c3b3b;
            color: white;
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

        #acoes_id {
            background-color: #3c3b3b;
            margin-top: 3rem;
            margin-bottom: 2rem;
            gap: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #acoes_id>button,
        #acoes_id a button {
            background-color: #ebca1e;
            color: #282828;
            border-radius: 30px;
            padding: 10px 20px;
            border: none;
            font-weight: bold;
            transition: 0.3s;
        }

        #acoes_id>button:hover,
        #acoes_id a button:hover {
            background-color: #ffffff;
            color: #282828;
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

        .modal-content.eletrotech-modal {
            background-color: #282828;
            color: white;
            border: 1px solid #FBD814;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        }

        .modal-content.eletrotech-modal .modal-header {
            border-bottom: 1px solid rgba(251, 216, 20, 0.3);
        }

        .modal-content.eletrotech-modal .modal-title {
            color: #FBD814;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.1rem;
        }

        .btn-close-white {
            filter: invert(1) grayscale(100%) brightness(200%);
        }

        form.eletrotech-form {
            display: flex;
            flex-direction: column;
        }

        form.eletrotech-form label {
            color: #ccc;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        form.eletrotech-form input,
        form.eletrotech-form select {
            border: none;
            border-bottom: 1px solid #777;
            background: transparent;
            color: white;
            padding: 8px 0;
            width: 100%;
            outline: none;
            margin-bottom: 20px;
            font-size: 14px;
            transition: border-bottom 0.3s;
        }

        form.eletrotech-form input:focus,
        form.eletrotech-form select:focus {
            border-bottom: 2px solid #FBD814;
        }

        form.eletrotech-form option {
            background-color: #282828;
            color: white;
        }

        form.eletrotech-form .btn-submit {
            background-color: #ebca1e;
            color: #282828;
            border: none;
            border-radius: 30px;
            padding: 12px 20px;
            font-weight: bold;
            text-transform: uppercase;
            transition: 0.3s;
            margin-top: 10px;
        }

        form.eletrotech-form .btn-submit:hover {
            background-color: #ffffff;
        }

        .linha-produto {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            margin-bottom: 10px;
        }

        .linha-produto select {
            flex-grow: 1;
            margin-bottom: 0;
        }

        .linha-produto input {
            width: 100px;
            margin-bottom: 0;
        }

        .btn-remover-linha {
            color: #dc3545;
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            margin-bottom: 5px;
            transition: 0.2s;
        }

        .btn-remover-linha:hover {
            color: #ff4c5d;
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
            <a class="navbar-brand d-flex align-items-center" href="menuView.php">
                <img src="../../assets/logo-eletrotech.png" alt="Logo Eletrotech">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0 align-items-center">
                    <li class="nav-item"><a class="nav-link" href="menuView.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="usuariosView.php">Usuários</a></li>
                    <li class="nav-item"><a class="nav-link" href="eletricistasView.php">Eletricistas</a></li>
                    <li class="nav-item"><a class="nav-link" href="produtosView.php">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="metasView.php">Metas</a></li>
                    <li class="nav-item"><a class="nav-link active" href="ordensServicoView.php">Ordens de Serviço</a></li>
                    <li class="nav-item ms-3"><a class="nav-link text-danger fw-bold" style="background-color: transparent;" href="../../controllers/processaLogout.php">Sair</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <?php
        if (isset($_SESSION['erro'])) {
            echo '<div class="alert alert-danger text-center">' . $_SESSION['erro'] . '</div>';
            unset($_SESSION['erro']);
        }
        if (isset($_SESSION['sucesso'])) {
            echo '<div class="alert alert-success text-center">' . $_SESSION['sucesso'] . '</div>';
            unset($_SESSION['sucesso']);
        }
        ?>
    </div>

    <div id="acoes_id">
        <button data-bs-toggle="modal" data-bs-target="#modalNovaOS">
            <i class="fa-solid fa-plus"></i> Registrar Nova OS
        </button>
    </div>

    <div class="container mt-2">
        <table class="table table-dark table-hover table-bordered custom-table text-center">
            <thead>
                <tr>
                    <th scope="col" style="width: 15%;">ID OS</th>
                    <th scope="col" style="width: 20%;">Data da Operação</th>
                    <th scope="col" style="width: 45%;">Eletricista Responsável</th>
                    <th scope="col" style="width: 20%;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultadoOS && $resultadoOS->num_rows > 0): ?>
                    <?php while ($os = $resultadoOS->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= str_pad($os['id'], 5, "0", STR_PAD_LEFT) ?></td>
                            <td><?= date('d/m/Y', strtotime($os['data_os'])) ?></td>
                            <td><?= htmlspecialchars($os['nome_eletricista']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" title="Ver Detalhes"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalDetalhesOS"
                                    onclick="carregarDetalhesOS(<?= $os['id'] ?>)">
                                    <i class="fa-solid fa-eye"></i> Histórico
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="empty-state">Nenhuma Ordem de Serviço registrada no momento.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modalNovaOS" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content eletrotech-modal">
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Ordem de Serviço</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="../../controllers/processaOrdensServico.php" method="POST" class="eletrotech-form" id="formOS">

                        <div class="row">
                            <div class="col-md-6">
                                <label for="eletricista_os">Eletricista Responsável</label>
                                <select name="eletricista_os" id="eletricista_os" required>
                                    <option value="" disabled selected hidden>Selecione (Apenas Ativos)</option>
                                    <?php while ($eletricista = $resultadoEletricistas->fetch_assoc()): ?>
                                        <option value="<?= $eletricista['id'] ?>"><?= htmlspecialchars($eletricista['nome']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="data_os">Data da Operação</label>
                                <input type="date" name="data_os" id="data_os" required max="<?= date('Y-m-d') ?>">
                            </div>
                        </div>

                        <hr style="border-color: rgba(251, 216, 20, 0.3); margin: 20px 0;">
                        <h6 style="color: #FBD814; text-transform: uppercase; font-size: 14px; font-weight: bold; margin-bottom: 15px;">Materiais Utilizados na Operação</h6>

                        <div id="lista-materiais">
                            <div class="linha-produto">
                                <div>
                                    <label>Produto</label>
                                    <select name="id_produto[]" required>
                                        <option value="" disabled selected hidden>Selecione o material...</option>
                                        <?php
                                        $opcoesProdutos = "";
                                        if ($resultadoProdutos && $resultadoProdutos->num_rows > 0) {
                                            while ($prod = $resultadoProdutos->fetch_assoc()) {
                                                $nomeLimpo = htmlspecialchars($prod['nome_produto'], ENT_QUOTES, 'UTF-8');
                                                $opcoesProdutos .= "<option value='{$prod['id']}'>{$nomeLimpo} (Estoque: {$prod['qtd_estoque']})</option>";
                                            }
                                        } else {
                                            $opcoesProdutos = "<option value='' disabled>Nenhum material com estoque disponível</option>";
                                        }
                                        echo $opcoesProdutos;
                                        ?>
                                    </select>
                                </div>
                                <div>
                                    <label>Quantidade</label>
                                    <input type="number" name="qtd_utilizada[]" min="1" placeholder="Ex: 5" required>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-warning mt-2 mb-3" onclick="adicionarMaterial()" style="border-radius: 20px; font-weight: bold;">
                            <i class="fa-solid fa-plus"></i> Adicionar Mais um Produto
                        </button>

                        <button type="submit" class="btn-submit">Registrar OS e Baixar Estoque</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalDetalhesOS" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content eletrotech-modal">
                <div class="modal-header">
                    <h5 class="modal-title">Materiais Utilizados</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div id="conteudo-detalhes">
                        <p class="text-center">Carregando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include_once '../../view/components/chatbot.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const opcoesHtml = `<?= $opcoesProdutos ?>`;

        function adicionarMaterial() {
            const container = document.getElementById('lista-materiais');
            const novaLinha = document.createElement('div');
            novaLinha.className = 'linha-produto';

            novaLinha.innerHTML = `
                <div>
                    <select name="id_produto[]" required>
                        <option value="" disabled selected hidden>Selecione o material...</option>
                        ${opcoesHtml}
                    </select>
                </div>
                <div>
                    <input type="number" name="qtd_utilizada[]" min="1" placeholder="Ex: 5" required>
                </div>
                <button type="button" class="btn-remover-linha" onclick="removerMaterial(this)" title="Remover Material">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            `;

            container.appendChild(novaLinha);
        }

        function removerMaterial(botao) {
            botao.parentElement.remove();
        }

        function carregarDetalhesOS(idOs) {
            const container = document.getElementById('conteudo-detalhes');
            container.innerHTML = '<p class="text-center">Carregando...</p>';

            fetch('../../controllers/getMateriaisOS.php?id_os=' + idOs)
                .then(response => response.text())
                .then(html => {
                    container.innerHTML = html;
                });
        }
    </script>
</body>

</html>