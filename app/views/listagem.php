<?php
ob_start();
session_start();

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
  <link rel="stylesheet" href="./style/style-listagem.css" />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Jomolhari&display=swap");
  </style>
</head>

<body>
  <header>
    <div class="logo">
      <a href="../../index.php">
        <img src="./assets/logo-menor.svg" id="logo-amigopet" alt="logo amigopet" /></a>
    </div>
  </header>
  <main>
    <div id="popup-info" class="popup">
      <div class="popup-content">
        <span class="close-popup-btn" id="close-popup-info-btn">&times;</span>
        <h2>Informações do Animal</h2>
        <div id="animal-info">
          <!-- Aqui só é adicionado informação que for incluida pelo JS, nao mexer nessa DIV -->
        </div>
      </div>
    </div>
    <div class="filters">
      <select id="filtro">
        <option value="all">Todos</option>
        <option value="cachorro">Cachorro</option>
        <option value="gato">Gato</option>
      </select>
    </div>

    <div class="container">
      <?php foreach ($todosAnimais as $animal): ?>
        <div class="card">
          <img src="<?= $animal['imagem']; ?>" alt="Imagem de <?= $animal['raca']; ?>">
          <div class="info">
            <h2><?= $animal['raca']; ?></h2>
            <p>Tamanho: <?= $animal['porte']; ?></p>
            <p>Peso: <?= $animal['peso']; ?>kg</p>
            <p>Idade: <?= $animal['idade']; ?> anos</p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </main>

  <script>
    // function openPopup(popupId) {
    //     const popup = document.getElementById(popupId);
    //     popup.style.display = "block";
    // }

    // function closePopup(popupId) {
    //     const popup = document.getElementById(popupId);
    //     popup.style.display = "none";
    // }

    // document.getElementById("filtro").addEventListener("change", function() {
    //     const filtro = this.value.toLowerCase().trim();
    //     const cards = document.querySelectorAll(".card");

    //     cards.forEach((card) => {
    //         const tipoElement = card.querySelector(".info h2");
    //         if (!tipoElement) return;

    //         const tipoCompleto = tipoElement.textContent.toLowerCase().trim();
    //         const tipo = tipoCompleto.split(" ")[0];

    //         if (filtro === "all" || tipo === filtro) {
    //             card.style.display = "block";
    //         } else {
    //             card.style.display = "none";
    //         }
    //     });
    // });

    // document.querySelectorAll(".card").forEach((card) => {
    //     card.addEventListener("click", function() {
    //         const animalName = this.querySelector(".info h2").textContent;
    //         const animalInfo = getAnimalInfo(animalName);
    //         const popupInfo = document.getElementById("popup-info");
    //         const animalInfoElement = document.getElementById("animal-info");

    //         animalInfoElement.innerHTML = animalInfo;

    //         openPopup("popup-info");
    //     });
    // });

    // function getAnimalInfo(animalName) {
    //     if (animalName.toLowerCase().includes("gato")) {
    //         return "<p>Informações adicionais sobre gatos...</p><br><a class='adote-menu' href='../html/info-usuario.html'>Adote</a>";
    //     } else if (animalName.toLowerCase().includes("cachorro")) {
    //         return "<p>Informações adicionais sobre cachorros...</p><br><a class='adote-menu' href='../html/info-usuario.html'>Adote</a>";
    //     } else {
    //         return "<p>Informações adicionais sobre outros animais...</p><br><a class='adote-menu' href='../html/info-usuario.html'>Adote</a>";
    //     }
    // }
    // document
    //     .getElementById("close-popup-info-btn")
    //     .addEventListener("click", function() {
    //         closePopup("popup-info");
    //     });
  </script>
</body>

</html>