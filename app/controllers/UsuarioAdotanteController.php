<?php
require_once __DIR__ . '/../models/UsuarioAdotante.php';
require_once __DIR__ . '/../../config/database.php';

class UsuarioAdotanteController
{
    public function cadastrar()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            //aqui ele se conecta ao banco de dados - todos arquivos precisaram disso
            $database = new Database();
            $db = $database->getConnection();

            // aqui ele pega os dados enviados pelo HTML e se baseando no MODELO da tabela criada ele insere os dados de acordo com a tabela
            $usuarioAdotante = new UsuarioAdotante($db);
            $usuarioAdotante->nome_completo = $_POST['nome_completo'];
            $usuarioAdotante->email = $_POST['email'];
            $usuarioAdotante->telefone = $_POST['telefone'];
            $usuarioAdotante->senha = $_POST['senha'];
            $usuarioAdotante->cpf = $_POST['cpf'];
            $usuarioAdotante->data_nascimento = $_POST['data_nascimento'];

            // aqui ele verifica se deu tudo certo em cadastrar, se sim ele redirecionara o usuario a uma pagina de sucesso / se nao exibirá uma informação de erro (pode ser melhorado ambos os fluxos)
            if ($usuarioAdotante->cadastrar()) {
                echo "Cadastro realizado com sucesso!";
                require __DIR__ . '../../views/sucesso-cadastro-usuario.html';
            } else {
                echo "Erro ao cadastrar!";
            }
        }
    }
}
