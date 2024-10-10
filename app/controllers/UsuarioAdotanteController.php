<?php
require_once __DIR__ . '/../models/UsuarioAdotante.php';
require_once __DIR__ . '/../../config/database.php';

class UsuarioAdotanteController
{
    private $db;
    private $usuarioAdotante;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioAdotante = new UsuarioAdotante($this->db);
    }

    public function mostrarPagina()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'adotante') {
            header("Location: ../html/index.php?error=nao_autenticado");
            exit();
        }

        $id_usuario = $_SESSION['id_usuario'];
        $dados_usuario = $this->usuarioAdotante->getUsuarioById($id_usuario);

        if ($dados_usuario === false) {
            echo "Erro ao buscar os dados do usuário.";
            exit();
        }

        include __DIR__ . '../../views/info-usuario.php';
    }

    public function cadastrar()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $this->usuarioAdotante->nome_completo = $_POST['nome_completo'];
            $this->usuarioAdotante->email = $_POST['email'];
            $this->usuarioAdotante->telefone = $_POST['telefone'];
            $this->usuarioAdotante->senha = $_POST['senha'];
            $this->usuarioAdotante->cpf = $_POST['cpf'];
            $this->usuarioAdotante->data_nascimento = $_POST['data_nascimento'];

            // aqui ele verifica se deu tudo certo em cadastrar, se sim ele redirecionara o usuario a uma pagina de sucesso / se nao exibirá uma informação de erro (pode ser melhorado ambos os fluxos)
            if ($this->usuarioAdotante->cadastrar()) {
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

            // Captura os dados do formulário de login
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            // Verifica se o login foi bem-sucedido
            if ($this->usuarioAdotante->login($email, $senha)) {
                echo "Login realizado com sucesso!";
                // Redirecionar para a página inicial ou painel
                $this->mostrarPagina();
                exit();
            } else {
                echo "Email ou senha incorretos!";
                // Exibir mensagem de erro ou redirecionar para a página de login novamente

                exit();
            }
        }
    }

    public function atualizarSenha()
    {
        session_start();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'adotante') {
            header("Location: ../html/index.php?error=nao_autenticado");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_usuario = $_SESSION['id_usuario'];
            $senha_atual = $_POST['current-password'];
            $nova_senha = $_POST['new-password'];

            if ($this->usuarioAdotante->atualizarSenha($id_usuario, $senha_atual, $nova_senha)) {
                echo "Senha alterada com sucesso";
                $this->mostrarPagina();
            } else {
                echo "não foi possivel alterar a senha";
            }
        }
    }

    public function atualizarDadosUsuario()
    {
        session_start();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'adotante') {
            header("Location: ../html/index.php?error=nao_autenticado");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_usuario = $_SESSION['id_usuario'];
            $nome_completo = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['contact'];

            if ($this->usuarioAdotante->atualizarDados($id_usuario, $nome_completo, $email, $telefone)) {
                $_SESSION['nome_completo'] = $nome_completo;
                $_SESSION['email'] = $email;
                echo "Dados alterados com sucesso";
                $this->mostrarPagina();
            } else {
                echo "não foi possivel alterar os dados";
            }
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new UsuarioAdotanteController();
    switch ($_GET['action']) {
        case 'cadastrar_usuario':
            $controller->cadastrar();
            break;
        case 'login_usuario':
            $controller->login();
            break;
        case 'atualizar_senha_usuario':
            $controller->atualizarSenha();
            break;
        case 'atualizar_dados_usuario':
            $controller->atualizarDadosUsuario();
            break;
        case 'mostrar_pagina':
            $controller->mostrarPagina();
            break;
        default:
            echo "Ação não reconhecida.";
            break;
    }
}
