<?php
class UsuarioAdotante
{
    private $conn;
    private $table_name = "usuario_adotante"; // Nome da tabela

    // Propriedades do adotante (campos da tabela)
    public $id;
    public $nome_completo;
    public $email;
    public $telefone;
    public $cpf;
    public $senha;
    public $data_nascimento;
    public $status_cadastro;

    // Construtor (nada mais é que antes de começar qualquer código ele "constroi" a base, nesse caso ele esta se conectando ao banco que ja existe para entao começar a fazer o que precisa ser feito)
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Método para cadastrar um novo adotante
    public function cadastrar()
    {
        // Query de inserção - aqui eu fiz uma query de adicionar ou gravar itens na tabela, mas na model pode ser criado querys de BUSCA ou até mesmo de CRIAR novas tabelas
        $query = "INSERT INTO " . $this->table_name . " 
                  (nome_completo, email, telefone, cpf, senha, data_nascimento, status_cadastro) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";

        // Prepara a query
        $stmt = $this->conn->prepare($query);

        // Verifica se a preparação da query foi bem-sucedida
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error; // Usando mysqli_error()
            return false;
        }

        // Limpa os dados para evitar problemas de segurança
        $this->nome_completo = htmlspecialchars(strip_tags($this->nome_completo));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->telefone = htmlspecialchars(strip_tags($this->telefone));
        $this->cpf = htmlspecialchars(strip_tags($this->cpf));
        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT); // Criptografa a senha
        $this->data_nascimento = htmlspecialchars(strip_tags($this->data_nascimento));
        $this->status_cadastro = 1; // Define o status_cadastro como ativo (1)

        // Liga os parâmetros aos valores vindos do formulário
        $stmt->bind_param('ssssssi', $this->nome_completo, $this->email, $this->telefone, $this->cpf, $this->senha, $this->data_nascimento, $this->status_cadastro);

        // Executa a query
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao executar a query: " . $stmt->error; // Usando mysqli_stmt_error()
            return false;
        }
    }
    public function login($email, $senha)
    {
        $query = "SELECT nome_completo, senha, id_usuario FROM " . $this->table_name . " WHERE email = ? LIMIT 1";

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return ['status' => false, 'message' => "Erro interno no sistema. Por favor, tente novamente mais tarde." . $this->conn->error];
        }

        $email = htmlspecialchars(strip_tags($email));

        $stmt->bind_param('s', $email);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();
            $this->id = $row['id_usuario'];
            $this->nome_completo = $row['nome_completo'];
            $hashed_password = $row['senha'];

            $stmt->bind_result($this->nome_completo, $hashed_password, $this->id);

            if (password_verify($senha, $hashed_password)) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_type'] = 'adotante';
                $_SESSION['nome_completo'] = $this->nome_completo;
                $_SESSION['email'] = $email;
                $_SESSION['id_usuario'] = $this->id;
                return ['status' => true];
            } else {
                // Senha incorreta
                return ['status' => false, 'message' => "Senha incorreta. Por favor, tente novamente."];
            }
        } else {
            // Usuário não encontrado
            return ['status' => false, 'message' => "Usuário não encontrado. Verifique o e-mail digitado."];
        }
    }

    public function getUsuarioById($id)
    {
        $query = "SELECT nome_completo, email, telefone, cpf, data_nascimento FROM " . $this->table_name . " WHERE id_usuario = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();

            return $row;
        }
        return false;
    }

    public function getUsuarioByEmail($email)
    {
        $query = "SELECT nome_completo, telefone, cpf, data_nascimento, id_usuario, senha, email FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();

            return $row;
        }
        return false;
    }

    public function atualizarSenha($id, $senha_atual, $nova_senha)
    {
        // Primeiro, verificar se a senha atual está correta
        $query = "SELECT senha FROM " . $this->table_name . " WHERE id_usuario = ? LIMIT 1";
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
        $query = "UPDATE " . $this->table_name . " SET senha = ? WHERE id_usuario = ?";
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

    public function atualizarDados($id, $nome_completo, $email, $telefone)
    {
        $query = "UPDATE " . $this->table_name . " SET nome_completo = ?, email = ?, telefone = ? WHERE id_usuario = ?";
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }
        // Limpa os dados
        $nome_completo = htmlspecialchars(strip_tags($nome_completo));
        $email = htmlspecialchars(strip_tags($email));
        $telefone = htmlspecialchars(strip_tags($telefone));

        $stmt->bind_param('sssi', $nome_completo, $email, $telefone, $id);
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao executar a query: " . $stmt->error;
            return false;
        }
    }

    public function atualizarSenhaGeradaRandomicamente($id, $senha_atual, $nova_senha)
    {
        // Primeiro, verificar se a senha atual está correta
        $query = "SELECT senha FROM " . $this->table_name . " WHERE id_usuario = ? LIMIT 1";
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


        if ($senha_atual !== $hashed_password) {
            echo "Senha atual incorreta.";
            return false;
        }

        // Atualizar a senha
        $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
        $query = "UPDATE " . $this->table_name . " SET senha = ? WHERE id_usuario = ?";
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
}
