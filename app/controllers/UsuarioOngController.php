<?php
require_once __DIR__ . '/../models/UsuarioOng.php';
require_once __DIR__ . '/../../config/database.php';

class UsuarioOngController
{
    public function cadastrar()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //aqui ele se conecta ao banco de dados - todos arquivos precisaram disso
            $database = new Database();
            $db = $database->getConnection();

            // aqui ele pega os dados enviados pelo HTML e se baseando no MODELO da tabela criada ele insere os dados de acordo com a tabela
            $usuarioOng = new UsuarioOng($db);
            $usuarioOng->nome_fantasia = $_POST['nome_fantasia'];
            $usuarioOng->email = $_POST['email'];
            $usuarioOng->telefone = $_POST['telefone'];
            $usuarioOng->cnpj = $_POST['cnpj'];
            $usuarioOng->senha = $_POST['senha'];

            // aqui ele verifica se deu tudo certo em cadastrar, se sim ele redirecionara o usuario a uma pagina de sucesso / se nao exibirá uma informação de erro (pode ser melhorado ambos os fluxos)
            if ($usuarioOng->cadastrar()) {
                echo "Cadastro realizado com sucesso!";
                require __DIR__ . '../../views/sucesso-cadastro-usuario.html';
            } else {
                echo "Erro ao cadastrar!";
            }
        }
    }

    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $database = new Database();
            $db = $database->getConnection();

            $usuarioOng = new UsuarioOng($db);

            // Captura os dados do formulário de login
            $cnpj = $_POST['cnpj'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            // Verifica se o login foi bem-sucedido
            if ($usuarioOng->login($cnpj, $email, $senha)) {
                echo "Login realizado com sucesso!";
                // Redirecionar para a página inicial ou painel

                exit();
            } else {
                echo "Email ou senha incorretos!";
                // Exibir mensagem de erro ou redirecionar para a página de login novamente

                exit();
            }
        }
    }
}
