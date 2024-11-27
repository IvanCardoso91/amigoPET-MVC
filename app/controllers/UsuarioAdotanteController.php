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
            header("Location: ../../views/erro-autenticacao.html");
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
                header("Location: ../views/sucesso-cadastro-usuario.html");
            } else {
                echo "Erro ao cadastrar!";
            }
        }
    }

    public function login()
    {

        $email = $_POST['email'];
        $senha = $_POST['senha'];

        $loginResult = $this->usuarioAdotante->login($email, $senha);

        if ($loginResult['status']) {
            $_SESSION['user_type'] = 'adotante';
            $this->mostrarPagina($_SESSION['id_usuario']);
        } else {
            // Salva a mensagem de erro na sessão
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['login_error'] = $loginResult['message'];
            header("Location: ../views/login-usuario.php");
            exit();
        }
    }

    public function atualizarSenha()
    {
        session_start();
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'adotante') {
            header("Location: ../../views/erro-autenticacao.html");
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
            header("Location: ../../views/erro-autenticacao.html");
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
                        header("Location: ../views/info-email.html");
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
        $config = include __DIR__ . '../../../config/config.php';
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $config['smtp']['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['smtp']['username'];
            $mail->Password = $config['smtp']['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $config['smtp']['port'];

            $mail->setFrom($config['smtp']['from_email'], $config['smtp']['from_name']);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Recuperação de Senha';
            $mail->Body = "
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #FFF9EB;
                            margin: 0;
                            padding: 0;
                        }
                        .email-container {
                            max-width: 600px;
                            margin: 20px auto;
                            background-color: #A0704C;
                            border-radius: 5px;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                            padding: 20px;
                            color: #FFF9EB;
                        }
                        .header {
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            margin-bottom: 20px;
                        }
                        .header img {
                            height: 80px;
                            width: auto;
                        }
                        .title {
                            text-align: center;
                            color: #EFDCAE;
                            font-size: 24px;
                            margin-bottom: 10px;
                        }
                        .body {
                            text-align: center;
                            font-size: 18px;
                            line-height: 1.6;
                        }
                        .body strong {
                            color: rgba(0, 0, 0, 0.5);
                            background-color: #EFDCAE;
                            padding: 5px 10px;
                            border-radius: 5px;
                        }
                        .footer {
                            text-align: center;
                            margin-top: 20px;
                            font-size: 14px;
                            color: #EFDCAE;
                        }
                        .footer a {
                            color: #FFF9EB;
                            text-decoration: none;
                        }
                        .footer a:hover {
                            text-decoration: underline;
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='header'>
                            <img src='https://via.placeholder.com/150x50?text=LOGO' alt='Amigopet Logo'>
                        </div>
                        <div class='title'>Recuperação de Senha</div>
                        <div class='body'>
                            <p>Olá,</p>
                            <p>Sua nova senha para acessar a ONG é:</p>
                            <p><strong>{$nova_senha}</strong></p>
                            <p>Recomendamos que você altere essa senha ao fazer login.</p>
                            <p>Obrigado por fazer parte da nossa comunidade!</p>
                        </div>
                        <div class='footer'>
                            <p>Amigopet - ONG para proteção animal</p>
                            <p><a href='#'>www.amigopet-dev.com.br</a></p>
                            <p>Este é um e-mail automático. Por favor, não responda.</p>
                        </div>
                    </div>
                </body>
                </html>
            ";

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
