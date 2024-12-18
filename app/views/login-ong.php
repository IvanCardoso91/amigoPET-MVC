<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>login ONG - Amigopet</title>
    <link rel="stylesheet" href="./style/style-login-ong.css" />
    <style>
    @import url("https://fonts.googleapis.com/css2?family=Jomolhari&display=swap");
    </style>
</head>

<body>
    <header>
        <a class="logo" href="../../index.php">
            <img src="./assets/logo.svg" id="logo" alt="logo amigopet" />
        </a>
    </header>
    <div class="frase">
        <h4>Empresas precisam ter um cadastro em nosso sistema</h4>
    </div>
    <div class="form-container">
        <form action="../controllers/UsuarioOngController.php?action=login_ong" method="POST" class="form">
            <h1 class="login-titulo">Login Empresa</h1>

            <?php
            session_start();
            if (isset($_SESSION['login_error'])) {
                echo '<div class="error-message">' . htmlspecialchars($_SESSION['login_error']) . '</div>';
                unset($_SESSION['login_error']);
            }
            ?>

            <div class="div-input">
                <input type="text" id="cnpj" name="cnpj" required placeholder="CNPJ" maxlength="18" />
            </div>
            <div class="div-input">
                <input type="password" id="senha" name="senha" required placeholder="Senha" />
            </div>
            <div class="div-input">
                <input type="submit" id="submit-button" value="Confirmar" />
            </div>
            <div class="links-container">
                <a href="./esqueceu-senha-ong.html" class="links">Esqueceu sua senha?</a>
                <a href="./cadastro-ong.html" class="links">Não sou cadastrado</a>
            </div>
        </form>
    </div>
    <script>
    document.getElementById("cnpj").addEventListener("input", function(e) {
        let value = e.target.value.replace(/\D/g, "");

        if (value.length > 14) {
            value = value.slice(0, 14);
        }

        value = value.replace(/^(\d{2})(\d)/, "$1.$2");
        value = value.replace(/(\d{3})(\d)/, "$1.$2");
        value = value.replace(/(\d{3})(\d)/, "$1/$2");
        value = value.replace(/(\d{4})(\d{2})$/, "$1-$2");

        e.target.value = value;
    });
    </script>
</body>

</html>