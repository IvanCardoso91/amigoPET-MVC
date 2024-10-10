<?php
class UsuarioOng
{
    private $conn;
    private $table_name = "usuario_ong"; // Nome da tabela

    // Propriedades da ONG (campos da tabela)
    public $nome_fantasia;
    public $email;
    public $telefone;
    public $cnpj;
    public $senha;

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
    public function login($cnpj, $email, $senha)
    {
        // Query para buscar o usuário pelo cnpj e email
        $query = "SELECT nome_fantasia, email, senha FROM " . $this->table_name . " WHERE cnpj = ? AND email = ? LIMIT 1";

        // Prepara a query
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }

        // Limpa os dados recebidos
        $cnpj = htmlspecialchars(strip_tags($cnpj));
        $email = htmlspecialchars(strip_tags($email));

        // Liga os parâmetros à query
        $stmt->bind_param('ss', $cnpj, $email);

        // Executa a query
        $stmt->execute();

        // Obtém o resultado
        $result = $stmt->get_result();

        // Verifica se encontrou algum usuário com o cnpj e email fornecidos
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
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
                $_SESSION['email'] = $email;
                return true;
            } else {
                // Senha incorreta
                return false;
            }
        } else {
            // Usuário não encontrado
            return false;
        }
    }
}
