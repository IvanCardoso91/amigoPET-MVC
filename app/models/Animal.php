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
    public $imagem; // imagem do animal
    public $status_adocao;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function cadastrar()
    {
        $query = "INSERT INTO " . $this->table_name . " 
          (id_ong, id_tipo, raca, peso, idade, porte, sexo, descricao, imagem, status_adocao) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }

        $this->id_ong = htmlspecialchars(strip_tags($this->id_ong));
        $this->id_tipo = htmlspecialchars(strip_tags($this->id_tipo));
        $this->raca = htmlspecialchars(strip_tags($this->raca));
        $this->peso = htmlspecialchars(strip_tags($this->peso));
        $this->idade = htmlspecialchars(strip_tags($this->idade));
        $this->porte = htmlspecialchars(strip_tags($this->porte));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->imagem = htmlspecialchars(strip_tags($this->imagem));
        $statusAdocao = 1;

        $stmt->bind_param(
            'iisisssssi',
            $this->id_ong,
            $this->id_tipo,
            $this->raca,
            $this->peso,
            $this->idade,
            $this->porte,
            $this->sexo,
            $this->descricao,
            $this->imagem,
            $statusAdocao
        );

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao executar a query: " . $stmt->error;
            return false;
        }
    }

    public function buscarAnimaisPorOng($id_ong)
    {
        $query = "SELECT 
                animal.*,
                status_adocao.descricao AS status_adocao,
                usuario_adotante.nome_completo AS nome_adotante,
                usuario_adotante.data_nascimento,
                usuario_adotante.email,
                usuario_adotante.telefone
              FROM 
                animal
              LEFT JOIN 
                status_adocao 
                ON animal.status_adocao = status_adocao.id
              LEFT JOIN 
                usuario_adotante 
                ON animal.id_usuario = usuario_adotante.id_usuario
              WHERE 
                animal.id_ong = ?";

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }

        $stmt->bind_param('i', $id_ong);
        $stmt->execute();
        $result = $stmt->get_result();

        $animais = [];
        while ($row = $result->fetch_assoc()) {
            $animais[] = $row;
        }

        return $animais;
    }

    public function buscarTodosAnimais()
    {
        $query = "SELECT animal.*, usuario_ong.nome_fantasia 
              FROM " . $this->table_name . "
              JOIN 
              usuario_ong 
              ON 
              animal.id_ong = usuario_ong.id_ong";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();

        $todosAnimais = [];
        while ($row = $result->fetch_assoc()) {
            $todosAnimais[] = $row;
        }
        return $todosAnimais;
    }

    public function editarAnimal()
    {
        $query = "UPDATE " . $this->table_name . " 
              SET raca = ?, peso = ?, idade = ?, porte = ?, descricao = ?, imagem = ?, status_adocao = ?
              WHERE id_animal = ?";

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }

        $this->raca = htmlspecialchars(strip_tags($this->raca));
        $this->peso = htmlspecialchars(strip_tags($this->peso));
        $this->idade = htmlspecialchars(strip_tags($this->idade));
        $this->porte = htmlspecialchars(strip_tags($this->porte));
        $this->descricao = htmlspecialchars(strip_tags($this->descricao));
        $this->imagem = htmlspecialchars(strip_tags($this->imagem));
        $this->status_adocao = htmlspecialchars(strip_tags($this->status_adocao));
        $this->id_animal = htmlspecialchars(strip_tags($this->id_animal));

        $stmt->bind_param(
            'ssisssii',
            $this->raca,
            $this->peso,
            $this->idade,
            $this->porte,
            $this->descricao,
            $this->imagem,
            $this->status_adocao,
            $this->id_animal
        );

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao executar a query: " . $stmt->error;
            return false;
        }
    }

    public function deletarAnimal($id_animal)
    {
        $query = "DELETE FROM " . $this->table_name . " WHERE id_animal = ?";
        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            echo "Erro na preparação da query: " . $this->conn->error;
            return false;
        }

        $stmt->bind_param('i', $id_animal);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Erro ao executar a query: " . $stmt->error;
            return false;
        }
    }
}
