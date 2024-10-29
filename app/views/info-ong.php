<?php
ob_start();
session_start();
// views/info-usuario.php
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ong') {
    header("Location: index.php?error=nao_autenticado");
    exit();
}

$id_ong = $_GET['id_ong'] ?? null;
$nome_fantasia = htmlspecialchars($_SESSION['nome_fantasia'] ?? null);
$email = htmlspecialchars($_SESSION['email'] ?? null);
$telefone = htmlspecialchars($_SESSION['telefone'] ?? null);
$cnpj = htmlspecialchars($_SESSION['cnpj'] ?? null);
$data_cadastro = htmlspecialchars($_SESSION['data_cadastro'] ?? null);
$animais = $_SESSION['animais'] ?? null;

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
    <title>Info Ong - Amigopet</title>
    <link rel="stylesheet" href="../views/style/style-info-ong.css" />
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
            <h2>Informações da Ong</h2>
            <?php if ($mensagem_sucesso): ?>
            <div class="success-message"><?php echo $mensagem_sucesso; ?></div>
            <?php endif; ?>
            <?php if ($mensagem_erro): ?>
            <div class="error-message"><?php echo $mensagem_erro; ?></div>
            <?php endif; ?>
            <div class="form-group">
                <div>
                    <label>CNPJ:</label>
                    <span><?php echo $cnpj; ?></span>
                </div>
                <div>
                    <label>E-mail da Ong:</label>
                    <span><?php echo $email; ?></span>
                </div>
                <div>
                    <label>Número de Contato:</label>
                    <span><?php echo $telefone; ?></span>
                </div>
                <div>
                    <label>Nome Fantasia:</label>
                    <span><?php echo $nome_fantasia; ?></span>
                </div>
                <div>
                    <label>Data do cadastro:</label>
                    <span><?php echo $data_cadastro; ?></span>
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
            <h2>Cadastrar Animal</h2>
            <form action="../../app/controllers/AnimalController.php?action=cadastrar_animal" method="POST"
                enctype="multipart/form-data" class="form-animal">
                <input type="hidden" name="id_ong" value="<?php echo $_SESSION['id_ong']; ?>" />

                <label for="id_tipo">Tipo de Animal:</label>
                <select name="id_tipo" id="id_tipo" required>
                    <option value="1">Cachorro</option>
                    <option value="2">Gato</option>
                </select>

                <label for="raca">Raça:</label>
                <input type="text" id="raca" name="raca" placeholder="Raça do animal" required />

                <label for="peso">Peso:</label>
                <input type="number" id="peso" name="peso" placeholder="Peso em kg" step="0.01" required />

                <label for="idade">Idade:</label>
                <input type="number" id="idade" name="idade" placeholder="Idade em anos" required />

                <label for="porte">Porte:</label>
                <select name="porte" id="porte" required>
                    <option value="pequeno">Pequeno</option>
                    <option value="medio">Médio</option>
                    <option value="grande">Grande</option>
                </select>

                <label for="sexo">Sexo:</label>
                <select name="sexo" id="sexo" required>
                    <option value="1">Macho</option>
                    <option value="0">Fêmea</option>
                </select>

                <label for="imagem">Imagem do Animal:</label>
                <input type="file" name="imagem" accept="image/*" required>

                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" placeholder="Descrição do animal" rows="4"></textarea>

                <button type="submit">Cadastrar Animal</button>
            </form>
        </div>
        <div class="block animais-cadastrados">
            <h2>Animais Cadastrados</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Raça</th>
                        <th>Peso</th>
                        <th>Idade</th>
                        <th>Porte</th>
                        <th>Sexo</th>
                        <th>Descrição</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($animais)): ?>
                    <?php foreach ($animais as $animal): ?>
                    <tr>
                        <td><?php echo $animal['id_animal']; ?></td>
                        <td><?php echo $animal['id_tipo'] == 1 ? 'Cachorro' : 'Gato'; ?></td>
                        <td><?php echo $animal['raca']; ?></td>
                        <td><?php echo $animal['peso']; ?></td>
                        <td><?php echo $animal['idade']; ?></td>
                        <td><?php echo ucfirst($animal['porte']); ?></td>
                        <td><?php echo $animal['sexo'] == 1 ? 'Macho' : 'Fêmea'; ?></td>
                        <td><?php echo $animal['descricao']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="8">Nenhum animal cadastrado.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modals -->
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
            <h3>Editar Informações do Usuário</h3>
            <form action="../../app/controllers/UsuarioOngController.php?action=atualizar_dados_ong" method="POST">
                <label for="edit-email">E-mail do Usuário:</label>
                <input type="email" id="edit-email" name="email" value="<?php echo $email; ?>" required />
                <label for="edit-contact">Número para Contato:</label>
                <input type="text" id="edit-contact" name="contact" value="<?php echo $telefone; ?>" required />
                <label for="edit-name">Nome Fantasia:</label>
                <input type="text" id="edit-name" name="nome" value="<?php echo $nome_fantasia; ?>" required />
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