<?php

ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../controllers/AnimalController.php';
require_once __DIR__ . '/../models/UsuarioOng.php';
require_once __DIR__ . '/../../config/database.php';
require '../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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
            header("Location: ../../views/erro-autenticacao.html");
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
        exit();
    }

    public function cadastrar()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $cpf = preg_replace('/\D/', '', $_POST['cnpj']);
            $telefone = preg_replace('/\D/', '', $_POST['telefone']);

            $this->usuarioOng->nome_fantasia = $_POST['nome_fantasia'];
            $this->usuarioOng->email = $_POST['email'];
            $this->usuarioOng->telefone = $telefone;
            $this->usuarioOng->cnpj = $cpf;
            $this->usuarioOng->senha = $_POST['senha'];

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
        $cpf = preg_replace('/\D/', '', $_POST['cnpj']);
        $cnpj = $cpf;
        $senha = $_POST['senha'];

        $loginResult = $this->usuarioOng->login($cnpj, $senha);

        if ($loginResult['status']) {
            $_SESSION['user_type'] = 'ong';
            $this->mostrarPagina($cnpj);
        } else {
            // Salva a mensagem de erro na sessão
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['login_error'] = $loginResult['message'];
            header("Location: ../views/login-ong.php");
            exit();
        }
    }

    public function atualizarSenha()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ong') {
            header("Location: ../../views/erro-autenticacao.html");
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
                echo "não foi possível alterar a senha";
            }
        }
    }

    public function atualizarDadosUsuario()
    {
        if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'ong') {
            header("Location: ../../views/erro-autenticacao.html");
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
                echo "não foi possível alterar os dados";
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
            $cnpj = preg_replace('/\D/', '', $_POST['cnpj']);

            $ong = $this->usuarioOng->getOngByCNPJ($cnpj);

            if ($ong) {
                $nova_senha = $this->gerarSenhaAleatoria();

                if ($this->usuarioOng->atualizarSenhaGeradaRandomicamente($ong['id_ong'], $ong['senha'], $nova_senha)) {
                    if ($this->enviarEmailRecuperacao($ong['email'], $nova_senha)) {
                        echo "Nova senha enviada ao e-mail vinculado ao CNPJ informado.";
                        header("Location: ../views/info-email-ong.html");
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
        case 'mostrar_pagina':
            $controller->mostrarPagina($_SESSION['cnpj']);
            break;
        case 'recuperar_senha':
            $controller->recuperarSenha();
            break;
        default:
            echo "Ação não reconhecida.";
            break;
    }
}
