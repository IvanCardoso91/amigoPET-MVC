<?php
ob_start();
session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./app/views/style/style-index.css" />
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Jomolhari&display=swap");
    </style>
    <title>Amigopet</title>
</head>

<body>
    <div class="header">
        <div class="logo">
            <img src="./app/views/assets/logo.svg" alt="logo amigopet" />
        </div>
        <div class="options">
            <a href="#servicos" id="servicos-options">Serviços</a>
            <a href="#sobre-nos" id="sobre-options">Sobre nós</a>
            <a href="#footer" id="contato-options">Contato</a>


            <?php if (isset($_SESSION['user_type'])): ?>
                <div class="user-info">
                    <?php if ($_SESSION['user_type'] === 'adotante'): ?>
                        <a href="./app/controllers/UsuarioAdotanteController.php?action=mostrar_pagina">
                            <span>
                                Olá,
                                <?php echo htmlspecialchars($_SESSION['nome_completo']); ?>!
                            </span>
                        </a>
                    <?php elseif ($_SESSION['user_type'] === 'ong'): ?>
                        <a href="./app/controllers/UsuarioOngController.php?action=mostrar_pagina">
                            <span>
                                Olá,
                                <?php echo htmlspecialchars($_SESSION['nome_fantasia']); ?>!
                            </span>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="logout-btn">Logout</a>
                </div>

            <?php else: ?>
                <div class="login-header">
                    <a id="open-popup-login-btn">Entrar</a>
                </div>
                <div class="cadastro-header">
                    <a id="open-popup-sign-btn">Cadastre-se</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Background overlay for popups -->
    <div id="popup-bg" class="popup-bg"></div>

    <div id="popup-login" class="popup">
        <div class="popup-content">
            <span class="close-popup-btn" id="close-popup-login-btn">&times;</span>
            <h2>ATENÇÃO</h2>
            <p>Selecione o login desejado abaixo</p>
            <a href="./app/views/login-usuario.php" class="close-btn">Login adotante</a>
            <a href="./app/views/login-ong.php" class="close-btn">Login ONG</a>
        </div>
    </div>

    <div id="popup-sign" class="popup">
        <div class="popup-content">
            <span class="close-popup-btn" id="close-popup-sign-btn">&times;</span>
            <h2>ATENÇÃO</h2>
            <p>Selecione o cadastro desejado abaixo</p>
            <a href="./app/views/cadastro-usuario.html" class="close-btn">Cadastro adotante</a>
            <a href="./app/views/cadastro-ong.html" class="close-btn">Cadastro ONG</a>
        </div>
    </div>

    <div class="menu">
        <div class="conteudo-menu">
            <h1 id="adote-ja">Adote já o seu novo <br />companheiro!</h1>
            <?php if ($_SESSION): ?>
                <?php if ($_SESSION['user_type'] === 'adotante'): ?>
                    <div class="adote-menu">
                        <a href="./app/views/listagem.php">Adote</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <div class="img-menu"><img src="./app/views/assets/img.menu.png" alt="" /></div>
    </div>

    <section id="servicos" class="servicos">
        <div class="conteudo">
            <img src="./app/views/assets/img.servicos.png" class="img-servicos" id="cachorro-servicos" />
            <div class="titulo-servicos">
                <h2>NOSSOS <br />SERVIÇOS</h2>
            </div>
        </div>
        <div class="texto-servicos">
            <p>
                Conectamos você a animais adoráveis em busca de um lar em Curitiba e
                região. Facilitamos a adoção responsável, oferecendo uma plataforma
                para ONGs cadastrarem e divulgarem animais disponíveis. Simplificamos
                o processo de adoção, tornando mais fácil encontrar seu novo melhor
                amigo. Envolva-se com nossa comunidade dedicada e apaixonada,
                comprometida em criar um mundo onde todos os animais tenham um lar
                amoroso. Ao se juntar a nós, você não apenas encontra um novo
                companheiro peludo, mas também faz parte de uma rede que promove o
                bem-estar animal e a conexão entre seres humanos e animais. Junte-se a
                nós e faça a diferença na vida de um amigo peludo hoje!
            </p>
        </div>
    </section>

    <section id="sobre-nos" class="sobre-nos">
        <div class="conteudo-sobre">
            <h2>Sobre Nós</h2>
            <p>
                Nós somos um grupo de estudantes do primeiro período do curso de
                Análise e Desenvolvimento de Sistemas, e estamos muito empolgados em
                apresentar o Projeto AMIGOPET, nosso projeto integrador que visa
                auxiliar na doação de animais de estimação. O Projeto AMIGOPET foi
                concebido com o objetivo de facilitar o processo de adoção responsável
                de cães e gatos, proporcionando uma plataforma intuitiva e amigável
                para os potenciais adotantes encontrarem seu novo companheiro peludo.
            </p>
            <p>
                Além disso, nosso projeto tem como missão divulgar e apoiar ONGs e
                protetores independentes que trabalham incansavelmente para garantir o
                bem-estar dos animais. Acreditamos que a tecnologia pode ser uma
                aliada poderosa na promoção do cuidado com os animais, e estamos
                comprometidos em usar nossas habilidades em programação para fazer a
                diferença na vida dos nossos amigos de quatro patas.
            </p>
        </div>
    </section>

    <section id="footer" class="footer">
        <div class="logo-footer">
            <div class="contato">
                <h2>Contato</h2>
                <p>EMAIL:amigopet@gmail.com</p>
                <p>TELEFONE:+55 (00)9999-9999</p>
            </div>
            <div class="direitos">Amigopet. Alguns direitos reservados</div>
        </div>
    </section>

    <script>
        function openPopup(popupId) {
            const popup = document.getElementById(popupId);
            const popupBg = document.getElementById("popup-bg");
            popup.classList.add("show");
            popupBg.classList.add("show");
        }

        function closePopups() {
            const popups = document.querySelectorAll(".popup");
            const popupBg = document.getElementById("popup-bg");
            popups.forEach((popup) => {
                popup.classList.remove("show");
            });
            popupBg.classList.remove("show");
        }

        document
            .getElementById("open-popup-login-btn")
            .addEventListener("click", function() {
                openPopup("popup-login");
            });

        document
            .getElementById("open-popup-sign-btn")
            .addEventListener("click", function() {
                openPopup("popup-sign");
            });

        window.addEventListener("click", function(event) {
            const popups = document.querySelectorAll(".popup");
            const popupBg = document.getElementById("popup-bg");
            if (event.target === popupBg) {
                closePopups();
            }
        });

        document
            .getElementById("close-popup-login-btn")
            .addEventListener("click", closePopups);
        document
            .getElementById("close-popup-sign-btn")
            .addEventListener("click", closePopups);
    </script>
</body>

</html>