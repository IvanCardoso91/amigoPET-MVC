<?php
ob_start();
session_start();
require_once __DIR__ . '/../controllers/AnimalController.php';
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

    public function mostrarPagina($cnpj)
    {

        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ong') {
            header("Location: ../html/index.php?error=nao_autenticado");
            exit();
        }

        $ong_data = $this->usuarioOng->getOngByCNPJ($cnpj);

        $_SESSION['id_ong'] = $ong_data['id_ong'];
        $_SESSION['nome_fantasia'] = $ong_data['nome_fantasia'];
        $_SESSION['email'] = $ong_data['email'];
        $_SESSION['telefone'] = $ong_data['telefone'];
        $_SESSION['cnpj'] = $ong_data['cnpj'];
        $_SESSION['data_cadastro'] = $ong_data['data_cadastro'];


        $id_ong = $_SESSION['id_ong'];

        header("Location: ../views/info-ong.php?id=" . $id_ong);
        exit(); // Adicione exit após header
    }

    public function cadastrar()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $this->usuarioOng->nome_fantasia = $_POST['nome_fantasia'];
            $this->usuarioOng->email = $_POST['email'];
            $this->usuarioOng->telefone = $_POST['telefone'];
            $this->usuarioOng->cnpj = $_POST['cnpj'];
            $this->usuarioOng->senha = $_POST['senha'];

            // aqui ele verifica se deu tudo certo em cadastrar, se sim ele redirecionara o usuario a uma pagina de sucesso / se nao exibirá uma informação de erro (pode ser melhorado ambos os fluxos)
            if ($this->usuarioOng->cadastrar()) {
                echo "Cadastro realizado com sucesso!";
                require __DIR__ . '../../views/sucesso-cadastro-ong.html';
            } else {
                echo "Erro ao cadastrar!";
            }
        }
    }

    public function login()
    {

        $cnpj = $_POST['cnpj'];
        $senha = $_POST['senha'];

        if ($this->usuarioOng->login($cnpj, $senha)) {
            $_SESSION['user_type'] = 'ong';
            $animalController = new AnimalController();
            $animalController->mostrarPagina();
            $this->mostrarPagina($cnpj);
            header("Location: ../views/info-ong.php?id=" . $ong_data['id_ong']);
            exit();
        } else {
            echo "ERRO2";
            exit();
        }
    }

    public function atualizarSenha()
    {
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
                $this->mostrarPagina($_SESSION['cnpj']);
            } else {
                echo "não foi possivel alterar a senha";
            }
        }
    }

    public function atualizarDadosUsuario()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ong') {
            header("Location: ../html/index.php?error=nao_autenticado");
            exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $id_ong = $_SESSION['id_ong'];
            $nome_fantasia = $_POST['nome'];
            $email = $_POST['email'];
            $telefone = $_POST['contact'];

            if ($this->usuarioOng->atualizarDados($id_ong, $nome_fantasia, $email, $telefone)) {
                $_SESSION['nome_fantasia'] = $nome_fantasia;
                $_SESSION['email'] = $email;
                echo "Dados alterados com sucesso";
                $this->mostrarPagina($_SESSION['cnpj']);
            } else {
                echo "não foi possivel alterar os dados";
            }
        }
    }
}

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
            $controller->atualizarDadosUsuario();
            break;
        default:
            echo "Ação não reconhecida.";
            break;
    }
}
