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

    public function mostrarTodosAnimais()
    {
        $todosAnimais = $this->animal->buscarTodosAnimais();

        if ($todosAnimais === false) {
            echo "Não foi encontrado nenhum animal";
            exit();
        }

        $_SESSION['todos_animais'] = $todosAnimais;

        // Incluir a view para exibir os animais
        header("Location: ../views/listagem.php");
        include '../views/listagem.php';
        exit();
    }


    public function cadastrarAnimal()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (isset($_FILES['imagem'])) {
                $imagem = $_FILES['imagem'];
                $extensao = strtolower(pathinfo($imagem['name'], PATHINFO_EXTENSION));
                $novoNome = uniqid() . ($extensao === 'jfif' ? '.jpg' : '.' . $extensao);
                $caminhoDestino = '../views/assets/' . basename($novoNome);

                // Movendo a imagem
                if (move_uploaded_file($imagem['tmp_name'], $caminhoDestino)) {
                    // Salvar o caminho da imagem no banco de dados
                    $this->animal->imagem = 'assets/' . basename($novoNome); // Ajuste conforme seu caminho
                } else {
                    echo "Erro ao carregar a imagem.";
                    return;
                }
            }
            $this->animal->id_ong = $_POST['id_ong'];
            $this->animal->id_tipo = $_POST['id_tipo'];
            $this->animal->raca = $_POST['raca'];
            $this->animal->peso = $_POST['peso'];
            $this->animal->idade = $_POST['idade'];
            $this->animal->porte = $_POST['porte'];
            $this->animal->sexo = $_POST['sexo'];
            $this->animal->descricao = $_POST['descricao'];

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
        case 'exibir_todos_animais':
            $controller->mostrarTodosAnimais();
            break;
        default:
            echo "Ação não reconhecida.";
            break;
    }
}
