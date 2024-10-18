<?php
// views/info-ong.php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ong') {
  header("Location: index.php?error=nao_autenticado");
  exit();
}
// $id_ong = $_SESSION['id_ong'];
$nome_fantasia = htmlspecialchars($dados_ong['nome_fantasia']);
$email = htmlspecialchars($dados_ong['email']);
$telefone = htmlspecialchars($dados_ong['telefone']);
$cnpj = htmlspecialchars($dados_ong['cnpj']);

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
    <title>Info ONG - Amigopet</title>
    <link rel="stylesheet" href="../views/style/style-info-ong.css" />
    <style>
        @import url("https://fonts.googleapis.com/css2?family=Jomolhari&display=swap");
    </style>
</head>
<body>
    <header>
        <a href="../html/index.php">
            <img src="../assets/logo-menor.svg" id="logo" alt="logo amigopet" />
        </a>
    </header>
    <div class="container">
        <div class="block">
            <h2>Informações da ONG</h2>
            <?php if ($mensagem_sucesso): ?>
            <div class="success-message"><?php echo $mensagem_sucesso; ?></div>
            <?php endif; ?>
            <?php if ($mensagem_erro): ?>
            <div class="error-message"><?php echo $mensagem_erro; ?></div>
            <?php endif; ?>
            <div class="form-group">
                <div>
                    <label>E-mail da Empresa:</label>
                    <span><?php echo $email; ?></span>
                </div>
                <div>
                    <label>Número do Contato:</label>
                    <span><?php echo $telefone; ?></span>
                </div>
                <div>
                    <label>Nome Fantasia:</label>
                    <span><?php echo $nome_fantasia; ?></span>
                </div>
                <div>
                    <label>CNPJ:</label>
                    <span><?php echo $cnpj; ?></span>
                </div>
            </div>
            <div class="buttons">
                <button class="button-blue" onclick="openPasswordModal()">Redefinir sua Senha</button>
                <button class="button-yellow" onclick="openEditModal()">Deseja editar seus dados?</button>
            </div>
        </div>
        <div class="block animal-list">
            <h2>Animais Cadastrados</h2>
            <ul id="animal-list">
                <?php foreach ($animais as $animal): ?>
                <li>
                    <a href="../html/listagem.php"><?php echo htmlspecialchars($animal['tipo']) . ' - ' . htmlspecialchars($animal['nome']); ?></a>
                    <button class="delete-button" onclick="confirmDelete('<?php echo htmlspecialchars($animal['nome']); ?>')">X</button>
                </li>
                <?php endforeach; ?>
            </ul>
            <button class="button-green" onclick="openAddAnimalModal()">Adicionar Animal</button>
        </div>
    </div>

    <!-- Modais para redefinir senha e editar informações -->
    <div id="passwordModal" class="modal">
        <div class="modal-content">
            <button class="close" onclick="closePasswordModal()">×</button>
            <h3>Redefinir Senha</h3>
            <form action="../../app/controllers/UsuarioOngController.php?action=atualizar_senha_ong" method="POST">
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
            <h3>Editar Informações da ONG</h3>
            <form action="../../app/controllers/UsuarioOngController.php?action=atualizar_dados_ong" method="POST">
                <label for="edit-email">E-mail da Empresa:</label>
                <input type="email" id="edit-email" name="email" value="<?php echo $email; ?>" required />
                <label for="edit-telefone">Número do telefone:</label>
                <input type="text" id="edit-telefone" name="telefone" value="<?php echo $telefone; ?>" required />
                <label for="edit-name">Nome Fantasia:</label>
                <input type="text" id="edit-name" name="nome_fantasia" value="<?php echo $nome_fantasia; ?>" required />
                <div class="buttons">
                    <button type="submit" class="button-blue">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
      let animalToDelete = "";

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

      function confirmDelete(animalName) {
        animalToDelete = animalName;
        // Chamar a função para confirmação de exclusão
        // Ex: mostrar modal
      }

      // Função para excluir animal
      function deleteAnimal() {
        // Implementar a lógica de exclusão
      }
    </script>
</body>
</html>
