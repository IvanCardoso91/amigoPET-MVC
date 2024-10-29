<?php
ob_start();
session_start();
require_once __DIR__ . '/../models/Animal.php';
require_once __DIR__ . '/../../config/database.php';

class AnimalController
{
    private $db;
    private $animal;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->animal = new animal($this->db);
    }

    public function mostrarPagina()
    {
        $id_ong = $_SESSION['id_ong'];

        // Buscar animais cadastrados pela ONG
        $animais = $this->animal->buscarAnimaisPorOng($id_ong);

        if ($animais === false) {
            echo "Não foi encontrado nenhum animal";
            exit();
        }

        $_SESSION['animais'] = $animais;

        // Incluir a view para exibir os animais
        include '../views/info-ong.php';
    }


    public function cadastrarAnimal()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Obtém os dados do formulário
            $this->animal->id_ong = $_POST['id_ong'];
            $this->animal->id_tipo = $_POST['id_tipo'];
            $this->animal->raca = $_POST['raca'];
            $this->animal->peso = $_POST['peso'];
            $this->animal->idade = $_POST['idade'];
            $this->animal->porte = $_POST['porte'];
            $this->animal->sexo = $_POST['sexo'];
            $this->animal->descricao = $_POST['descricao'];

            // Chama o método para cadastrar o animal na model
            if ($this->animal->cadastrar()) {
                echo "Animal cadastrado com sucesso!";
                $this->mostrarPagina();
            } else {
                echo "Erro ao cadastrar o animal.";
            }
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new AnimalController();
    switch ($_GET['action']) {
        case 'cadastrar_animal':
            $controller->cadastrarAnimal();
            break;
        default:
            echo "Ação não reconhecida.";
            break;
    }
}
