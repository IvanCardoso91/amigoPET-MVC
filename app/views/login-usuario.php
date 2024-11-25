<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>login usuario - Amigopet</title>
  <link rel="stylesheet" href="./style/style-login-usuario.css" />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Jomolhari&display=swap");
  </style>
</head>

<body>
  <header>
    <a class="logo" href="../../index.php">
      <img src="./assets/logo.svg" alt="logo amigopet" />
    </a>
  </header>
  <div class="frase">
    <h4>Para adotar um animalzinho, você precisa ter um cadastro</h4>
  </div>
  <div class="form-container">
    <form action="../controllers/UsuarioAdotanteController.php?action=login_usuario" method="POST" class="form">
      <h1 class="login-titulo">Login Adotante</h1>

      <?php
      session_start();
      if (isset($_SESSION['login_error'])) {
        echo '<div class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
        unset($_SESSION['login_error']);
      }
      ?>

      <div class="div-input">
        <input type="email" id="email" name="email" required placeholder="Email" />
      </div>
      <div class="div-input">
        <input type="password" id="senha" name="senha" required placeholder="Senha" />
      </div>
      <div class="div-input">
        <input type="submit" id="submit-button" value="Confirmar" />
      </div>
      <div class="links-container">
        <a href="./esqueceu-senha.html" class="links">Esqueceu sua senha?</a>
        <a href="./cadastro-usuario.html" class="links">Não sou cadastrado</a>
      </div>
    </form>
  </div>
</body>

</html>