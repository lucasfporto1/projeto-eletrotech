<?php
require_once '../../lib-php/libUtils.php';
checkSession('loginView.php');

$db = ConexaoBanco::connect();

$mesAtual = date('Y-m');

$query = "SELECT e.*, 
       (SELECT COUNT(*) FROM tabela_ordens_servico WHERE eletricista_os = e.id) as total_os,
       (SELECT vlr_meta FROM tabela_metas 
        WHERE eletricista_meta = e.id 
        AND mes_meta = DATE_FORMAT(NOW(), '%Y-%m') 
        LIMIT 1) as meta_atual
       FROM tabela_eletricistas e
       ORDER BY e.id DESC";

$resultado = $db->query($query);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <title>Eletricistas - EletroTech</title>
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
            gap: 30px;
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

        form.eletrotech-form input {
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

        form.eletrotech-form input:focus {
            border-bottom: 2px solid #FBD814;
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
                    <li class="nav-item"><a class="nav-link active" href="eletricistasView.php">Eletricistas</a></li>
                    <li class="nav-item"><a class="nav-link" href="produtosView.php">Produtos</a></li>
                    <li class="nav-item"><a class="nav-link" href="metasView.php">Metas</a></li>
                    <li class="nav-item"><a class="nav-link" href="ordensServicoView.php">Ordens de Serviço</a></li>
                    <li class="nav-item ms-3"><a class="nav-link text-danger fw-bold" href="../../controllers/processaLogout.php">Sair</a></li>
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
        <button data-bs-toggle="modal" data-bs-target="#modalNovoEletricista">
            <i class="fa-solid fa-plus"></i> Novo Eletricista
        </button>
    </div>

    <div class="container mt-2">
        <table class="table table-dark table-hover table-bordered custom-table text-center">
            <thead>
                <tr>
                    <th scope="col">CPF</th>
                    <th scope="col">Nome</th>
                    <th scope="col">Contratação</th>
                    <th scope="col">Demissão</th>
                    <th scope="col">Status</th>
                    <th scope="col">OS Realizadas</th>
                    <th scope="col">Meta Atual</th>
                    <th scope="col">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($resultado && $resultado->num_rows > 0): ?>
                    <?php while ($eletricista = $resultado->fetch_assoc()):
                        $ativo = is_null($eletricista['data_demissao']);
                        $statusText = $ativo ? 'Ativo' : 'Demitido';
                        $badgeClass = $ativo ? 'bg-success' : 'bg-secondary';

                        $dataContratacao = date('d/m/Y', strtotime($eletricista['data_contratacao']));
                        $dataDemissao = !$ativo ? date('d/m/Y', strtotime($eletricista['data_demissao'])) : '-';
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($eletricista['cpf']) ?></td>
                            <td><?= htmlspecialchars($eletricista['nome']) ?></td>
                            <td><?= $dataContratacao ?></td>
                            <td><?= $dataDemissao ?></td>
                            <td><span class="badge <?= $badgeClass ?>"><?= $statusText ?></span></td>
                            <td><?= $eletricista['total_os'] ?></td>
                            <td><?= ($eletricista['meta_atual'] > 0) ? 'R$ ' . number_format($eletricista['meta_atual'], 2, ',', '.') : 'R$ 0,00' ?></td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditarEletricista"
                                    data-id="<?= $eletricista['id'] ?>"
                                    data-nome="<?= htmlspecialchars($eletricista['nome']) ?>"
                                    onclick="preencherModalEditar(this)">
                                    <i class="fa-solid fa-pen"></i>
                                </button>

                                <?php if ($ativo): ?>
                                    <a href="../../controllers/processaDelete.php?tabela=tabela_eletricistas&id=<?= $eletricista['id'] ?>&origem=eletricistasView.php"
                                        class="btn btn-sm btn-outline-danger" onclick="return confirm('Demitir este funcionário?');" title="Demitir">
                                        <i class="fa-solid fa-user-minus"></i>
                                    </a>
                                <?php else: ?>
                                    <a href="../../controllers/processaReativarEletricista.php?id=<?= $eletricista['id'] ?>&origem=eletricistasView.php"
                                        class="btn btn-sm btn-outline-success" onclick="return confirm('Deseja readmitir este eletricista?');" title="Reativar">
                                        <i class="fa-solid fa-user-plus"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="empty-state">Nenhum eletricista cadastrado no momento.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="modalNovoEletricista" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content eletrotech-modal">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Eletricista</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="../../controllers/processaEletricista.php" method="POST" class="eletrotech-form">

                        <label>CPF</label>
                        <input type="text" id="cpf" name="cpf" placeholder="Apenas números" minlength="11" maxlength="11" required>
                        <small id="cpf-feedback" style="font-weight: bold; margin-top: 5px; display: block;"></small>

                        <label for="nome_eletricista">Nome Completo</label>
                        <input type="text" name="nome" id="nome_eletricista" placeholder="Ex: João Silva" required />

                        <label for="data_contratacao">Data de Contratação</label>
                        <input type="date" name="data_contratacao" id="data_contratacao" required />

                        <button type="submit" class="btn-submit mt-3">Salvar Eletricista</button>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalEditarEletricista" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content eletrotech-modal">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Eletricista</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <form action="../../controllers/processaEditEletricista.php" method="POST" class="eletrotech-form">

                        <input type="hidden" name="id" id="edit_id">

                        <label for="edit_nome">Nome Completo</label>
                        <input type="text" name="nome" id="edit_nome" required>

                        <button type="submit" class="btn-submit mt-4">Gravar Alterações</button>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php include_once '../../view/components/chatbot.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const preencherModalEditar = (botao) => {
            const id = botao.getAttribute('data-id');
            const nome = botao.getAttribute('data-nome');

            document.getElementById('edit_id').value = id;
            document.getElementById('edit_nome').value = nome;
        };
    </script>
    <script src="../../node_modules/js-lib/myLibrary.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const cpfInput = document.getElementById("cpf");
            const cpfFeedback = document.getElementById("cpf-feedback");

            if (cpfInput && cpfFeedback) {
                cpfInput.addEventListener("blur", (e) => {
                    const cpf = e.target.value;

                    if (cpf.length > 0) {
                        const isValid = MyLib.isValidCPF(cpf);

                        if (isValid) {
                            cpfFeedback.textContent = "CPF Válido";
                            cpfFeedback.style.color = "#28a745";
                        } else {
                            cpfFeedback.textContent = "CPF Inválido";
                            cpfFeedback.style.color = "#dc3545";
                        }
                    } else {
                        cpfFeedback.textContent = "";
                    }
                });
            }
        });
    </script>
</body>

</html>