<?php
class UsuarioAdotante
{
    private $conn;
    private $table_name = "usuario_adotante"; // Nome da tabela

    // Propriedades do adotante (campos da tabela)
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
        // Query para buscar o usuário pelo email
        $query = "SELECT nome_completo, senha FROM " . $this->table_name . " WHERE email = ? LIMIT 1";

        // Prepara a query
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }

        // Limpa os dados recebidos
        $email = htmlspecialchars(strip_tags($email));

        // Liga o parâmetro à query
        $stmt->bind_param('s', $email);

        // Executa a query
        $stmt->execute();

        // Obtém o resultado
        $result = $stmt->get_result();

        // Verifica se encontrou algum usuário com o email fornecido
        if ($result->num_rows > 0) {

            $row = $result->fetch_assoc();
            $this->nome_completo = $row['nome_completo'];
            $hashed_password = $row['senha'];

            // Liga as colunas do resultado aos atributos
            $stmt->bind_result($this->nome_completo, $hashed_password);

            // Verifica se a senha informada corresponde à senha criptografada armazenada
            if (password_verify($senha, $hashed_password)) {
                // Se a senha estiver correta, inicia uma sessão e armazena as informações do usuário
                session_start();
                $_SESSION['nome_completo'] = $this->nome_completo;
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
