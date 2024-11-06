<?php
class UsuarioOng
{
    private $conn;
    private $table_name = "usuario_ong"; // Nome da tabela

    // Propriedades da ONG (campos da tabela)
    public $id;
    public $nome_fantasia;
    public $email;
    public $telefone;
    public $cnpj;
    public $senha;
    public $data_cadastro;

    // Construtor (nada mais é que antes de começar qualquer código ele "constroi" a base, nesse caso ele esta se conectando ao banco que ja existe para entao começar a fazer o que precisa ser feito)
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Método para cadastrar uma nova ONG
    public function cadastrar()
    {
        // Query de inserção - aqui eu fiz uma query de adicionar ou gravar itens na tabela, mas na model pode ser criado querys de BUSCA ou até mesmo de CRIAR novas tabelas
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome_fantasia, email, telefone, cnpj, senha) 
                  VALUES (?, ?, ?, ?, ?)";

        // Prepara a query
        $stmt = $this->conn->prepare($query);

        // Verifica se a preparação da query foi bem-sucedida
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error; // Usando mysqli_error()
            return false;
        }

        // Limpa os dados para evitar problemas de segurança
        $this->nome_fantasia = htmlspecialchars(strip_tags($this->nome_fantasia));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->cnpj = htmlspecialchars(strip_tags($this->cnpj));
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT); // Criptografa a senha

        // Liga os parâmetros aos valores vindos do formulário
        $stmt->bind_param('sssss', $this->nome_fantasia, $this->email, $this->telefone, $this->cnpj, $this->senha);

        // Executa a query
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao executar a query: " . $stmt->error; // Usando mysqli_stmt_error()
            return false;
        }
    }
    public function login($cnpj, $senha)
    {
        // Query para buscar o usuário pelo cnpj e email
        $query = "SELECT nome_fantasia, email, senha, telefone, cnpj, data_cadastro, id_ong FROM " . $this->table_name . " WHERE cnpj = ? LIMIT 1";

        // Prepara a query
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }

        // Limpa os dados recebidos
        $cnpj = htmlspecialchars(strip_tags($cnpj));

        // Liga os parâmetros à query
        $stmt->bind_param('s', $cnpj);

        // Executa a query
        $stmt->execute();

        // Obtém o resultado
        $result = $stmt->get_result();


        // Verifica se encontrou algum usuário com o cnpj e email fornecidos
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $this->id = $row['id_ong'];
            $this->nome_fantasia = $row['nome_fantasia'];
            $hashed_password = $row['senha'];


            // Verifica se a senha informada corresponde à senha criptografada armazenada
            if (password_verify($senha, $hashed_password)) {

                // Se a senha estiver correta, inicia uma sessão e armazena as informações do usuário
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_type'] = 'ong';
                $_SESSION['nome_fantasia'] = $this->nome_fantasia;
                $_SESSION['id_ong'] = $this->id;
                $_SESSION['email'] = $this->email;
                $_SESSION['telefone'] = $this->telefone;
                $_SESSION['cnpj'] = $this->cnpj;
                $_SESSION['data_cadastro'] = $this->data_cadastro;

                return true;
            } else {
                // Senha incorreta
                echo "senha incorreta";
                return false;
            }
        } else {
            // Usuário não encontrado
            echo "usuario nao encontrado";
            return false;
        }
    }

    public function getOngById($id)
    {
        $query = "SELECT nome_fantasia, email, telefone, cnpj, data_cadastro FROM " . $this->table_name . " WHERE id_ong = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return false;
    }

    public function getOngByCNPJ($cnpj)
    {
        // Defina a consulta SQL para selecionar os dados da ONG pelo CNPJ
        $query = "SELECT id_ong, nome_fantasia, email, telefone, cnpj, data_cadastro FROM " . $this->table_name . " WHERE cnpj = ? LIMIT 1";

        // Prepare a consulta
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }

        // Vincule o parâmetro CNPJ (assumindo que seja uma string)
        $stmt->bind_param('s', $cnpj); // Use 's' para strings
        $stmt->execute();

        // Obtenha o resultado
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc(); // Retorna os dados da ONG
        }

        return false; // Retorna false se não encontrar
    }

    public function atualizarSenha($id, $senha_atual, $nova_senha)
    {
        // Primeiro, verificar se a senha atual está correta
        $query = "SELECT senha FROM " . $this->table_name . " WHERE id_ong = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            echo "Usuário não encontrado.";
            return false;
        }
        $row = $result->fetch_assoc();
        $hashed_password = $row['senha'];

        if (!password_verify($senha_atual, $hashed_password)) {
            echo "Senha atual incorreta.";
            return false;
        }

        // Atualizar a senha
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->table_name . " SET senha = ? WHERE id_ong = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }
        $stmt->bind_param('si', $nova_senha_hash, $id);
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao executar a query: " . $stmt->error;
            return false;
        }
    }

    public function atualizarDados($id, $nome_fantasia, $email, $telefone)
    {
        $query = "UPDATE " . $this->table_name . " SET nome_fantasia = ?, email = ?, telefone = ? WHERE id_ong = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }
        // Limpa os dados
        $nome_fantasia = htmlspecialchars(strip_tags($nome_fantasia));
        $email = htmlspecialchars(strip_tags($email));
        $telefone = htmlspecialchars(strip_tags($telefone));

        $stmt->bind_param('sssi', $nome_fantasia, $email, $telefone, $id);
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao executar a query: " . $stmt->error;
            return false;
        }
    }
}
