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

        if ($this->usuarioOng->login($cnpj, $senha)) {
            $_SESSION['user_type'] = 'ong';
            $this->mostrarPagina($cnpj);
        } else {
            echo "Erro ao fazer login.";
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
                echo "não foi possível alterar a senha";
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
            $mail->Body    = "Sua nova senha de acesso à ONG é: <strong>{$nova_senha}</strong>";

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
