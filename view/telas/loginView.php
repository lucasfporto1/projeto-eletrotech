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
  <title>Login - EletroTech</title>

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

    #body-login .container-login .login-inputs a#naoTemConta {
      color: #282828;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s ease;
    }

    #body-login .container-login .login-inputs a#naoTemConta:hover {
      color: #FBD814;
      transition: color 0.3s ease;
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

    <h1>Login</h1>

    <form action="../../controllers/processaLogin.php" method="POST" class="login-inputs">
      <label for="login-nome">Nome de Usuário</label>
      <input type="text" id="login-nome" name="nome" placeholder="Seu nome" required />

      <label for="login-senha">Senha</label>
      <div class="ml-input-wrapper">
        <input type="password" id="login-senha" name="senha" class="ml-input-field" placeholder="Sua senha" required />
        <i class="fa-regular fa-eye ml-input-icon"></i>
      </div>

      <div class="container-button">
        <button type="submit" class="ml-button">Fazer login</button>
      </div>

      <div style="margin-top: 15px; justify-content: center; display: flex;">
        <a id="naoTemConta" href="cadastroView.php">Não tem conta? Cadastre-se</a>
      </div>
    </form>
  </div>

  <script src="../../node_modules/js-lib/myLibrary.js"></script>
  <script>
    MyLib.initPasswordToggle({
      wrapperSelector: ".ml-input-wrapper"
    });
  </script>
</body>

</html>