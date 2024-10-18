<?php
require_once __DIR__ . '/../models/UsuarioOng.php';
require_once __DIR__ . '/../../config/database.php';

class UsuarioOngController
{
    private $db;
    private $usuarioOng;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->usuarioOng = new UsuarioOng($this->db);
    }

    public function mostrarPagina()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ong') {
            header("Location: ../html/index.php?error=nao_autenticado");
            exit();
        }

        $id_ong = $_SESSION['id_ong'];
        $dados_ong = $this->usuarioOng->getUserById($id_ong);

        if ($dados_ong === false) {
            echo "Erro ao buscar os dados da ONG.";
            exit();
        }

        include __DIR__ . '../../views/info-ong.php';
    }
    public function cadastrar()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $this->usuarioOng->nome_fantasia = $_POST['nome_fantasia'];
            $this->usuarioOng->email = $_POST['email'];
            $this->usuarioOng->telefone = $_POST['telefone'];
            $this->usuarioOng->cnpj = $_POST['cnpj'];
            $this->usuarioOng->senha = $_POST['senha'];

            // Verifica se deu tudo certo em cadastrar, se sim redireciona o usuário a uma página de sucesso
            if ($this->usuarioOng->cadastrar()) {
                // Redireciona para a página de sucesso
                header("Location: http://localhost/php/amigoPET-MVC/app/views/sucesso-cadastro.html?user-type=ong");
                exit(); // Para garantir que o script pare de executar
            } else {
                echo "Erro ao cadastrar!";
            }
        }
    }

    public function login()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Captura os dados do formulário de login
            $cnpj = $_POST['cnpj'];
            $email = $_POST['email'];
            $senha = $_POST['senha'];

            // Verifica se o login foi bem-sucedido
            if ($this->usuarioOng->login($cnpj, $email, $senha)) {
                echo "Login realizado com sucesso!";
                // Armazena as informações na sessão
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['user_type'] = 'ong';          // Define o tipo de usuário
                $_SESSION['id_ong'] = $this->usuarioOng->id; // Armazena o ID da ONG
                $_SESSION['nome_fantasia'] = $this->usuarioOng->nome_fantasia; // Armazena o nome fantasia
                $_SESSION['email'] = $email; // Armazena o email
                // Redirecionar para a página inicial ou painel
                $this->mostrarPagina(); // Redireciona para a página após o login
                exit();
            } else {
                echo "CNPJ, Email ou senha incorretos!";
                exit();
            }
        }
    }


    public function atualizarSenha()
    {
        session_start();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ong') {
            header("Location: ../html/index.php?error=nao_autenticado");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_ong = $_SESSION['id_ong'];
            $senha_atual = $_POST['current-password'];
            $nova_senha = $_POST['new-password'];

            if ($this->usuarioOng->atualizarSenha($id_ong, $senha_atual, $nova_senha)) {
                echo "Senha alterada com sucesso";
                $this->mostrarPagina();
            } else {
                echo "não foi possivel alterar a senha";
            }
        }
    }


    public function atualizarDadosOng()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ong') {
            header("Location: ../html/index.php?error=nao_autenticado");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_ong = $_SESSION['id_ong'];
            $nome_fantasia = $_POST['nome_fantasia'];
            $email = $_POST['email'];
            $telefone = $_POST['telefone'];

            if ($this->usuarioOng->atualizarDados($id_ong, $nome_fantasia, $email, $telefone)) {
                $_SESSION['nome_fantasia'] = $nome_fantasia;
                $_SESSION['email'] = $email;
                echo "Dados alterados com sucesso!";
                $this->mostrarPagina();
            } else {
                echo "Não foi possível alterar os dados.";
            }
        }
    }
}

// Verifica qual ação será executada
if (isset($_GET['action'])) {
    $controller = new UsuarioOngController();
    switch ($_GET['action']) {
        case 'cadastrar_ong':
            $controller->cadastrar();
            break;
        case 'login_ong':
            $controller->login();
            break;
        case 'atualizar_senha_ong':
            $controller->atualizarSenha();
            break;
        case 'atualizar_dados_ong':
            $controller->atualizarDadosOng();
            break;
        case 'mostrar_pagina_ong':
            $controller->mostrarPagina();
            break;
        default:
            echo "Ação não reconhecida.";
            break;
    }
}
