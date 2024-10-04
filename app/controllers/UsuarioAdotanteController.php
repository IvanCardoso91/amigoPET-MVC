<?php
require_once __DIR__ . '/../models/UsuarioAdotante.php';
require_once __DIR__ . '/../../config/database.php';

class UsuarioAdotanteController
{
    public function cadastrar()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $database = new Database();
            $db = $database->getConnection();

            $usuarioAdotante = new UsuarioAdotante($db);
            $usuarioAdotante->nome_completo = $_POST['nome_completo'];
            $usuarioAdotante->email = $_POST['email'];
            $usuarioAdotante->telefone = $_POST['telefone'];
            $usuarioAdotante->senha = $_POST['senha'];
            $usuarioAdotante->cpf = $_POST['cpf'];

            if ($usuarioAdotante->cadastrar()) {
                echo "Cadastro realizado com sucesso!";
                require __DIR__ . '../../views/sucesso-cadastro-usuario.html';
            } else {
                echo "Erro ao cadastrar!";
            }
        }
    }
}
