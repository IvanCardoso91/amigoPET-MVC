<?php
ob_start();
session_start();
// views/info-usuario.php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'adotante') {
  header("Location: index.php?error=nao_autenticado");
  exit();
}

$nome_completo = htmlspecialchars($_SESSION['nome_completo']);
$email = htmlspecialchars($_SESSION['email']);
$telefone = htmlspecialchars($_SESSION['telefone']);
$cpf = htmlspecialchars($_SESSION['cpf']);
$data_nascimento = htmlspecialchars($_SESSION['data_nascimento']);

$mensagens = $_SESSION['todas_mensagens'];


?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Info Usuário - Amigopet</title>
  <link rel="stylesheet" href="../views/style/style-info-usuario.css" />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Jomolhari&display=swap");
  </style>
</head>

<body>
  <header>
    <a href="../../index.php">
      <img src="../../app/views/assets/logo-menor.svg" id="logo" alt="logo amigopet" />
    </a>
  </header>
  <div class="container">
    <a class="button-list" href="./listagem.php">Veja os animais</a>
    <!-- Primeiro Bloco -->
    <div class="block">
      <h2>Informações do Usuário</h2>
      <div class="form-group">
        <div>
          <label>E-mail do Usuário:</label>
          <span><?php echo $email; ?></span>
        </div>
        <div>
          <label>Número do Contato:</label>
          <span><?php echo $telefone; ?></span>
        </div>
        <div>
          <label>Nome Completo:</label>
          <span><?php echo $nome_completo; ?></span>
        </div>
        <div>
          <label>CPF:</label>
          <span><?php echo $cpf; ?></span>
        </div>
      </div>
      <div class="buttons">
        <button class="button-blue" onclick="openPasswordModal()">
          Redefinir sua Senha
        </button>
        <button class="button-yellow" onclick="openEditModal()">
          Deseja editar seus dados?
        </button>
      </div>
    </div>

    <div class="block chat">
      <h2>Contato com ONG's</h2>
      <div class="tabs">
        <?php foreach ($mensagens as $mensagem): ?>
          <button onclick="showTab(<?php echo $mensagem['id_ong']; ?>)">
            <?php echo htmlspecialchars($mensagem['nome_ong']); ?> -
            <?php echo htmlspecialchars($mensagem['nome_animal']); ?>
          </button>
        <?php endforeach; ?>
      </div>

      <?php foreach ($mensagens as $mensagem): ?>
        <div id="tab<?php echo $mensagem['id_ong']; ?>" class="tab-content">
          <p>ONG: <?php echo htmlspecialchars($mensagem['nome_ong']); ?></p>
          <p>Animal: <?php echo htmlspecialchars($mensagem['nome_animal']); ?></p>
          <p><strong><?php echo $mensagem['enviado_por'] == 'adotante' ? 'Você' : 'ONG'; ?>:</strong>
            <?php echo htmlspecialchars($mensagem['mensagem']); ?></p>
          <p><small>Enviado em: <?php echo $mensagem['data_envio']; ?></small></p>
        </div>
      <?php endforeach; ?>

      <!-- Formulário para enviar nova mensagem -->
      <form method="POST" action="UsuarioAdotanteController.php">
        <input type="hidden" name="action" value="enviar_mensagem">
        <input type="hidden" name="id_ong" value="<!-- ID da ONG -->">
        <input type="hidden" name="id_animal" value="<!-- ID do Animal -->">
        <input type="text" name="mensagem" placeholder="Digite sua mensagem">
        <button type="submit">Enviar</button>
      </form>
    </div>


  </div>

  <!-- Modals -->
  <div id="passwordModal" class="modal">
    <div class="modal-content">
      <button class="close" onclick="closePasswordModal()">×</button>
      <h3>Redefinir Senha</h3>
      <form action="../../app/controllers/UsuarioAdotanteController.php?action=atualizar_senha_usuario"
        method="POST">
        <label for="current-password">Senha Atual:</label>
        <input type="password" id="current-password" name="current-password" required />
        <label for="new-password">Nova Senha:</label>
        <input type="password" id="new-password" name="new-password" required />
        <div class="buttons">
          <button type="submit" class="button-blue">Confirmar</button>
        </div>
      </form>
    </div>
  </div>

  <div id="editModal" class="modal">
    <div class="modal-content">
      <button class="close" onclick="closeEditModal()">×</button>
      <h3>Editar Informações do Usuário</h3>
      <form action="../../app/controllers/UsuarioAdotanteController.php?action=atualizar_dados_usuario"
        method="POST">
        <label for="edit-email">E-mail do Usuário:</label>
        <input type="email" id="edit-email" name="email" value="<?php echo $email; ?>" required />
        <label for="edit-contact">Número para Contato:</label>
        <input type="text" id="edit-contact" name="contact" value="<?php echo $telefone; ?>" required />
        <label for="edit-name">Nome Completo:</label>
        <input type="text" id="edit-name" name="nome" value="<?php echo $nome_completo; ?>" required />
        <div class="buttons">
          <button type="submit" class="button-blue">Confirmar</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function openPasswordModal() {
      document.getElementById("passwordModal").style.display = "flex";
    }

    function closePasswordModal() {
      document.getElementById("passwordModal").style.display = "none";
    }

    function openEditModal() {
      document.getElementById("editModal").style.display = "flex";
    }

    function closeEditModal() {
      document.getElementById("editModal").style.display = "none";
    }

    function showTab(tabIndex) {
      document.querySelectorAll(".tab-content").forEach((tab) => {
        tab.classList.remove("active");
      });
      document.getElementById(`tab${tabIndex}`).classList.add("active");
    }

    // Default to showing the first tab
    showTab(1);
  </script>
</body>

</html>