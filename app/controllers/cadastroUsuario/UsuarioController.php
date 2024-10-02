<?php

require_once '../../models/UsuarioAdotante.php';

class UsuarioController
{
    private $usuario;

    public function __construct($db)
    {
        $this->usuario = new Usuario($db);
    }

    public function cadastrarUsuario()
    {
        echo "chegou aqui";
        echo "<pre>";
        print_r($_POST); // Exibe os dados enviados via POST
        echo "</pre>";

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $nome_completo = $_POST['name'];
            $email = $_POST['email'];
            $senha = $_POST['password'];
            $confirmar_senha = $_POST['password-confirm'];
            $telefone = $_POST['phone'];
            $cpf = $_POST['cpf'];

            if ($senha !== $confirmar_senha) {
                echo "As senhas não coincidem.";
                return;
            }

            $senha_criptografada = password_hash($senha, PASSWORD_DEFAULT);

            if ($this->usuario->cadastrar($nome_completo, $email, $senha_criptografada, $telefone, $cpf)) {
                header("Location: /amigopet-mvc/app/views/sucesso-cadastro-usuario.html");
            } else {
                echo "Erro ao cadastrar o usuário.";
            }
        }
    }
}
