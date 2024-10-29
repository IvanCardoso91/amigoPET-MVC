<?php
ob_start();
session_start();
// views/info-usuario.php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'adotante') {
  header("Location: index.php?error=nao_autenticado");
  exit();
}

$nome_completo = htmlspecialchars($dados_usuario['nome_completo']);
$email = htmlspecialchars($dados_usuario['email']);
$telefone = htmlspecialchars($dados_usuario['telefone']);
$cpf = htmlspecialchars($dados_usuario['cpf']);
$data_nascimento = htmlspecialchars($dados_usuario['data_nascimento']);

$mensagem_sucesso = '';
$mensagem_erro = '';

if (isset($_GET['success'])) {
  if ($_GET['success'] == 1) {
    $mensagem_sucesso = "Dados atualizados com sucesso!";
  } elseif ($_GET['success'] == 'senha') {
    $mensagem_sucesso = "Senha atualizada com sucesso!";
  }
}

if (isset($_GET['error'])) {
  if ($_GET['error'] == 1) {
    $mensagem_erro = "Erro ao atualizar os dados.";
  } elseif ($_GET['error'] == 'senha') {
    $mensagem_erro = "Erro ao atualizar a senha. Verifique se a senha atual está correta.";
  }
}
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
    <a class="button-list" href="../html/listagem.php">Veja os animais</a>
    <!-- Primeiro Bloco -->
    <div class="block">
      <h2>Informações do Usuário</h2>
      <?php if ($mensagem_sucesso): ?>
        <div class="success-message"><?php echo $mensagem_sucesso; ?></div>
      <?php endif; ?>
      <?php if ($mensagem_erro): ?>
        <div class="error-message"><?php echo $mensagem_erro; ?></div>
      <?php endif; ?>
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
        <button onclick="showTab(1)">ONG 1 - 1 GATO</button>
        <button onclick="showTab(2)">ONG 2 - 1 CACHORRO</button>
      </div>
      <div id="tab1" class="tab-content">
        <p>ONG: ONG 1</p>
        <p>Animal: 1 GATO</p>
        <p>Mensagem: Recebemos sua mensagem.</p>
        <input type="text" placeholder="Responder..." />
      </div>
      <div id="tab2" class="tab-content">
        <p>ONG: ONG 2</p>
        <p>Animal: 1 CACHORRO</p>
        <p>
          Mensagem: Estamos disponibilizando todas as informações
          requisitadas.
        </p>
        <input type="text" placeholder="Responder..." />
      </div>
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