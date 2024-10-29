<?php
class Animal
{
    private $conn;
    private $table_name = "animal"; // Nome da tabela

    // Propriedades do animal (campos da tabela)
    public $id_animal; // ID do animal (auto incremento)
    public $id_ong;    // ID da ONG (chave estrangeira)
    public $id_tipo;   // ID do tipo de animal (chave estrangeira)
    public $raca;      // Raça do animal
    public $peso;      // Peso do animal
    public $idade;     // Idade do animal
    public $porte;     // Porte do animal
    public $sexo;      // Sexo do animal
    public $descricao; // Descrição do animal

    // Construtor para conectar ao banco de dados
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Método para cadastrar um novo animal
    public function cadastrar()
    {
        // Query de inserção
        $query = "INSERT INTO " . $this->table_name . " 
                  (id_ong, id_tipo, raca, peso, idade, porte, sexo, descricao) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        // Prepara a query
        $stmt = $this->conn->prepare($query);

        // Verifica se a preparação da query foi bem-sucedida
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error; // Usando mysqli_error()
            return false;
        }

        // Limpa os dados para evitar problemas de segurança
        $this->id_ong = htmlspecialchars(strip_tags($this->id_ong));
        $this->id_tipo = htmlspecialchars(strip_tags($this->id_tipo));
        $this->raca = htmlspecialchars(strip_tags($this->raca));
        $this->peso = htmlspecialchars(strip_tags($this->peso));
        $this->idade = htmlspecialchars(strip_tags($this->idade));
        $this->porte = htmlspecialchars(strip_tags($this->porte));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));

        // Liga os parâmetros aos valores vindos do formulário
        $stmt->bind_param('iisissss', $this->id_ong, $this->id_tipo, $this->raca, $this->peso, $this->idade, $this->porte, $this->sexo, $this->descricao);

        // Executa a query
        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao executar a query: " . $stmt->error; // Usando mysqli_stmt_error()
            return false;
        }
    }

    public function buscarAnimaisPorOng($id_ong)
    {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id_ong = " . $id_ong;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        // Retorna os dados dos animais como um array
        $animais = [];
        while ($row = $result->fetch_assoc()) {
            $animais[] = $row;
        }

        return $animais;
    }
}
