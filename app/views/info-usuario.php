<?php
ob_start();
session_start();
// views/info-usuario.php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'adotante') {
  header("Location: ../../app/views/erro-autenticacao.html");
  exit();
}

$nome_completo = htmlspecialchars($_SESSION['nome_completo']);
$email = htmlspecialchars($_SESSION['email']);
$telefone = htmlspecialchars($_SESSION['telefone']);
$cpf = htmlspecialchars($_SESSION['cpf']);
$data_nascimento = htmlspecialchars($_SESSION['data_nascimento']);

function formatarCpf($cpf)
{
  // Remove qualquer caractere que não seja número
  $cpf = preg_replace('/\D/', '', $cpf);

  // Verifica se o CPF tem 11 dígitos e aplica a máscara
  if (strlen($cpf) === 11) {
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
  }
  // Retorna o CPF sem máscara caso tenha tamanho diferente
  return $cpf;
}
function formatarTelefone($telefone)
{
  $telefone = preg_replace('/\D/', '', $telefone);
  if (strlen($telefone) === 10) {
    return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
  } elseif (strlen($telefone) === 11) {
    return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
  }
  return $telefone;
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Info Usuário - Amigopet</title>
  <link rel="stylesheet" href="../views/style/style-info-usuario.css?v=1.0" />
  <style>
    @import url("https://fonts.googleapis.com/css2?family=Jomolhari&display=swap");
  </style>
</head>

<body>
  <header>
    <a href="../../index.php">
      <img src="../../app/views/assets/logo.svg" id="logo" alt="logo amigopet" />
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
          <label>Telefone:</label>
          <span><?php echo formatarTelefone($telefone); ?></span>
        </div>
        <div>
          <label>Nome Completo:</label>
          <span><?php echo $nome_completo; ?></span>
        </div>
        <div>
          <label>CPF:</label>
          <span><?php echo formatarCpf($cpf); ?></span>
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