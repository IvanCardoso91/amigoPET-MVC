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

        <div class="container">
            <?php foreach ($todosAnimais as $animal): ?>
                <div class="card" onclick="abrirModalAdocao(<?= $animal['id_animal']; ?>)">
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

    <div id="adocaoModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" style="cursor: pointer; float: right;">&times;</span>
            <h3>Iniciar Processo de Adoção</h3>
            <p>Você deseja adotar este animal?</p>
            <form id="adocaoForm" action="../controllers/AnimalController.php?action=iniciar_processo_adocao"
                method="POST">
                <input type="hidden" id="id_animal" name="id_animal" value="">
                <button type="submit">Iniciar Processo de Adoção</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById("adocaoModal");
        const closeModal = document.getElementById("closeModal");
        const idAnimalInput = document.getElementById("id_animal");

        function abrirModalAdocao(idAnimal) {
            idAnimalInput.value = idAnimal;
            modal.style.display = "flex";
        }

        closeModal.addEventListener("click", () => {
            modal.style.display = "none";
        });

        window.addEventListener("click", (event) => {
            if (event.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>
</body>

</html>