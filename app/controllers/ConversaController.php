<?php
ob_start();
require_once __DIR__ . '/../models/Conversa.php';
require_once __DIR__ . '/../../config/database.php';

class ConversaController
{
    private $db;
    private $conversa;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->conversa = new Conversa($this->db);
    }

    public function enviaMensagem()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_SESSION['id_usuario'];
            $id_animal = $_POST['id_animal'];
            $id_ong = $_POST['id_ong'];
            $mensagem = $_POST['mensagem'];

            if ($this->conversa->enviarMensagem($id_usuario, $id_animal, $id_ong, $mensagem)) {
                header("Location: ../views/listagem.php?mensagem=sucesso");
            } else {
                header("Location: ../views/listagem.php?mensagem=erro");
            }
        }
    }

    public function enviaMensagemAdotante()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_usuario = $_SESSION['id_usuario'];
            $id_animal = $_POST['id_animal'];
            $id_ong = $_POST['id_ong'];
            $mensagem = $_POST['mensagem'];

            if ($this->conversa->enviarMensagemAdotante($id_usuario, $id_animal, $id_ong, $mensagem)) {
                header("Location: ../views/info-usuario.php?mensagem=sucesso");
            } else {
                header("Location: ../views/info-usuario.php?mensagem=erro");
            }
        }
    }

    public function enviaMensagemOng()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_ong = $_POST['id_ong'];
            $id_usuario = $_POST['id_adotante'];
            $id_animal = $_POST['id_animal'];
            $mensagem = $_POST['mensagem'];

            if ($this->conversa->enviarMensagemOng($id_usuario, $id_animal, $id_ong, $mensagem)) {
                header("Location: ../views/info-ong.php?mensagem=sucesso");
            } else {
                header("Location: ../views/info-ong.php?mensagem=erro");
            }
        }
    }

    public function mostrarTodasMensagens($tipoUsuario)
    {
        $todasMensagens = $this->conversa->exibirTodasMensagens($tipoUsuario);

        if ($todasMensagens === false) {
            echo "Não foi encontrado nenhuma mensagem";
            exit();
        }

        $_SESSION['todas_mensagens'] = $todasMensagens;
        var_dump($todasMensagens);

        if ($tipoUsuario === 'id_usuario') {
            header("Location: ../views/info-usuario.php");
            include '../views/info-usuario.php';
            exit();
        } else {
            header("Location: ../views/info-ong.php");
            include '../views/info-ong.php';
            exit();
        }
    }
}

if (isset($_GET['action'])) {
    $controller = new ConversaController();
    switch ($_GET['action']) {
        case 'envia_mensagem':
            $controller->enviaMensagem();
            break;
        case 'envia_mensagem_adotante':
            $controller->enviaMensagemAdotante();
            break;
        case 'envia_mensagem_ong':
            $controller->enviaMensagemOng();
            break;
        case 'mostrar_todas_mensagens_adotante':
            $controller->mostrarTodasMensagens('id_usuario');
            break;
        case 'mostrar_todas_mensagens_ong':
            $controller->mostrarTodasMensagens('id_ong');
            break;
        default:
            echo "Ação não reconhecida.";
            break;
    }
}
