<?php
ob_start();
session_start();
require_once __DIR__ . '/../models/UsuarioAdotante.php';
require_once __DIR__ . '/../../config/database.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    public function mostrarPagina($id_usuario)
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'adotante') {
            header("Location: ../html/index.php?error=nao_autenticado");
            exit();
        }

        $id_usuario = $_SESSION['id_usuario'];

        $dados_usuario = $this->usuarioAdotante->getUsuarioById($id_usuario);

        $_SESSION['nome_completo'] = $dados_usuario['nome_completo'];
        $_SESSION['email'] = $dados_usuario['email'];
        $_SESSION['telefone'] = $dados_usuario['telefone'];
        $_SESSION['cpf'] = $dados_usuario['cpf'];
        $_SESSION['data_nascimento'] = $dados_usuario['data_nascimento'];

        if ($dados_usuario === false) {
            echo "Erro ao buscar os dados do usuário.";
            exit();
        }

        header("Location: ../views/info-usuario.php?id=" . $id_usuario);
        return $dados_usuario;
        exit();
    }

    public function cadastrar()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $cpf = preg_replace('/\D/', '', $_POST['cpf']);
            $telefone = preg_replace('/\D/', '', $_POST['telefone']);

            $this->usuarioAdotante->nome_completo = $_POST['nome_completo'];
            $this->usuarioAdotante->email = $_POST['email'];
            $this->usuarioAdotante->telefone = $telefone;
            $this->usuarioAdotante->senha = $_POST['senha'];
            $this->usuarioAdotante->cpf = $cpf;
            $this->usuarioAdotante->data_nascimento = $_POST['data_nascimento'];

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
                $this->mostrarPagina($_SESSION['id_usuario']);
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
                $this->mostrarPagina($_SESSION['id_usuario']);
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
                $this->mostrarPagina($_SESSION['id_usuario']);
            } else {
                echo "não foi possivel alterar os dados";
            }
        }
    }

    private function gerarSenhaAleatoria($tamanho = 8)
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, $tamanho);
    }

    public function recuperarSenha()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];

            $usuario_adotante = $this->usuarioAdotante->getUsuarioByEmail($email);

            if ($usuario_adotante) {
                $nova_senha = $this->gerarSenhaAleatoria();

                if ($this->usuarioAdotante->atualizarSenhaGeradaRandomicamente($usuario_adotante['id_usuario'], $usuario_adotante['senha'], $nova_senha)) {
                    if ($this->enviarEmailRecuperacao($usuario_adotante['email'], $nova_senha)) {
                        echo "Nova senha enviada ao e-mail informado.";
                    } else {
                        echo "Erro ao enviar o e-mail de recuperação.";
                    }
                } else {
                    echo "Erro ao atualizar a senha no banco de dados.";
                }
            } else {
                echo "CNPJ não encontrado.";
            }
        }
    }

    private function enviarEmailRecuperacao($email, $nova_senha)
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = '';
            $mail->SMTPAuth = true;
            $mail->Username = '';
            $mail->Password = '';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('recuperarsenha@amigopet-dev.com.br', 'Amigopet');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de Senha';
            $mail->Body    = "Sua nova senha de acesso para o e-mail {$email} é: <strong>{$nova_senha}</strong>";

            $mail->send();
            return true;
        } catch (Exception $e) {
            echo "Erro ao enviar o e-mail: {$mail->ErrorInfo}";
            return false;
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
            $controller->mostrarPagina($_SESSION['id_usuario']);
            break;
        case 'recuperar_senha':
            $controller->recuperarSenha();
            break;
        default:
            echo "Ação não reconhecida.";
            break;
    }
}
