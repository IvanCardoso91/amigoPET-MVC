<?php
ob_start();
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'adotante') {
    header("Location: ../../app/views/erro-autenticacao.html");
    exit();
}

if (!isset($_SESSION['todos_animais'])) {
    header("Location: ../controllers/AnimalController.php?action=exibir_todos_animais");
    exit();
}

$todosAnimais = $_SESSION['todos_animais'] ?? null;
$mensagem_sucesso = '';
$mensagem_erro = '';

if (isset($_GET['mensagem']) && $_GET['mensagem'] === 'sucesso') {
    $mensagem_sucesso = "Mensagem enviada com sucesso!";
}

if (isset($_GET['mensagem']) && $_GET['mensagem'] === 'erro') {
    $mensagem_erro = "Mensagem não foi enviada.";
}
?>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pets - Amigopet</title>
    <link rel="stylesheet" href="./style/style-listagem.css?v=1.0" />
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Jomolhari&display=swap");

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            width: 300px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .modal-content h3 {
            text-align: center;
        }

        .modal-content form {
            display: flex;
            flex-direction: column;
        }

        .modal-content input {
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <header>
        <div class="logo">
            <a href="../../index.php">
                <img src="./assets/logo.svg" id="logo-amigopet" alt="logo amigopet" /></a>
        </div>
    </header>
    <main>

        <?php if ($mensagem_sucesso): ?>
            <div class="success-message"><?php echo $mensagem_sucesso; ?></div>
        <?php endif; ?>
        <?php if ($mensagem_erro): ?>
            <div class="error-message"><?php echo $mensagem_erro; ?></div>
        <?php endif; ?>

        <div class="container">
            <?php foreach ($todosAnimais as $animal): ?>
                <div class="card" data-id-animal="<?= $animal['id_animal']; ?>" data-id-ong="<?= $animal['id_ong']; ?>">
                    <img src="<?= $animal['imagem']; ?>" alt="Imagem de <?= $animal['raca']; ?>">
                    <div class="info">
                        <h2><?= $animal['raca']; ?></h2>
                        <p>Tamanho: <?= $animal['porte']; ?></p>
                        <p>Peso: <?= $animal['peso']; ?>kg</p>
                        <p>Idade: <?= $animal['idade']; ?> anos</p>
                        <p>ONG: <span class="nomeOng"><?= $animal['nome_fantasia']; ?></span></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </main>

    <script>
        const modal = document.getElementById("mensagemModal");
        const closeModal = document.getElementById("closeModal");

        // Função para abrir a modal
        function abrirModal() {
            modal.style.display = "flex"; // Isso faz a modal aparecer centralizada
        }

        // Função para fechar a modal
        closeModal.addEventListener("click", () => {
            modal.style.display = "none";
        });
    </script>
</body>

</html>