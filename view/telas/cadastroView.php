<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Lora:wght@400;600&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />

  <link rel="stylesheet" href="../../node_modules/ml-ui/css/main.css" />
  <link rel="stylesheet" href="../../node_modules/ml-ui/css/login.css" />
  <title>Criar Conta - EletroTech</title>
  <style>
    #body-login .container-login .login-inputs .ml-input-wrapper {
      position: relative;
      width: 100%;
      margin-bottom: 20px;
    }

    #body-login .container-login .login-inputs .ml-input-wrapper .ml-input-field {
      width: 100%;
      border: none;
      border-bottom: 1px solid #ccc;
      border-radius: 0;
      padding: 8px 30px 8px 0;
      background: transparent;
      outline: none;
      box-shadow: none;
      font-family: inherit;
      font-size: 16px;
      color: inherit;
      transition: border-bottom 0.3s ease;
    }

    #body-login .container-login .login-inputs .ml-input-wrapper .ml-input-field:focus {
      border-bottom: 2px solid #FBD814;
    }

    #body-login .container-login .login-inputs .ml-input-wrapper .ml-input-icon {
      position: absolute;
      right: 0;
      bottom: 10px;
      cursor: pointer;
      color: #777;
      transition: color 0.3s ease;
    }

    #body-login .container-login .login-inputs .ml-input-wrapper .ml-input-icon:hover {
      color: #FBD814;
    }
  </style>
</head>

<body id="body-login">
  <header>
    <img src="../../assets/logo-eletrotech.png" alt="logo-eletrotech" />
  </header>

  <div class="container-login">
    <?php
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (isset($_SESSION['erro'])) {
      echo '<div class="alert alert-danger" style="color: #ff4c5d; text-align: center; margin-bottom: 15px; font-weight: bold;">' . $_SESSION['erro'] . '</div>';
      unset($_SESSION['erro']);
    }
    if (isset($_SESSION['sucesso'])) {
      echo '<div class="alert alert-success" style="color: #198754; text-align: center; margin-bottom: 15px; font-weight: bold;">' . $_SESSION['sucesso'] . '</div>';
      unset($_SESSION['sucesso']);
    }
    ?>
    <h1>Criar Conta</h1>

    <form action="../../controllers/processaCadastro.php" method="POST" class="login-inputs" id="form-cadastro">

      <label for="cadastro-nome">Nome de usuário</label>
      <input type="text" id="cadastro-nome" name="nome" placeholder="Seu nome" required />

      <label for="cadastro-senha">Senha</label>
      <div class="ml-input-wrapper">
        <input type="password" id="cadastro-senha" name="senha" class="ml-input-field" placeholder="Sua senha" required />
        <i class="fa-regular fa-eye ml-input-icon"></i>
      </div>

      <label for="cadastro-confirmar-senha" style="margin-top: 10px;">Confirmar Senha</label>
      <div class="ml-input-wrapper">
        <input type="password" id="cadastro-confirmar-senha" name="confirmar_senha" class="ml-input-field" placeholder="Repita sua senha" required />
        <i class="fa-regular fa-eye ml-input-icon"></i>
      </div>

      <div id="erro-senha" style="color: #ff4c5d; font-size: 13px; text-align: center; margin-top: 5px; display: none;">
        As senhas não coincidem.
      </div>

      <div class="container-button" style="margin-top: 20px;">
        <button type="submit" class="ml-button">Cadastrar</button>
      </div>

      <div style="margin-top: 15px; justify-content: center; display: flex;">
        <a id="jaTemConta" href="loginView.php">Já tem conta? Fazer Login</a>
      </div>

    </form>
  </div>

  <script src="../../node_modules/js-lib/myLibrary.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      MyLib.initPasswordToggle({
        wrapperSelector: ".ml-input-wrapper"
      });

      const form = document.getElementById('form-cadastro');
      const senha = document.getElementById('cadastro-senha');
      const confirmarSenha = document.getElementById('cadastro-confirmar-senha');
      const mensagemErro = document.getElementById('erro-senha');

      form.addEventListener('submit', function(event) {
        if (senha.value !== confirmarSenha.value) {
          event.preventDefault();
          mensagemErro.style.display = 'block';
          confirmarSenha.focus();
        } else {
          mensagemErro.style.display = 'none';
        }
      });
    });
  </script>
</body>

</html>