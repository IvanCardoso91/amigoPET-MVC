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

            // Enviando mensagem
            if ($this->conversa->enviarMensagemOng($id_usuario, $id_animal, $id_ong, $mensagem)) {
                // Retorne os dados da mensagem enviada
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'mensagem' => htmlspecialchars($mensagem),
                        'id_animal' => $id_animal,
                        'id_adotante' => $id_usuario,
                        'data_envio' => date('d/m/Y H:i'),
                        'enviado_por' => 'ong'
                    ]
                ]);
                exit();
            } else {
                echo json_encode(['success' => false, 'message' => 'Erro ao enviar a mensagem.']);
                exit();
            }
        }
    }



    // public function mostrarTodasMensagens($tipoUsuario)
    // {



    //     if ($tipoUsuario === 'id_usuario') {
    //     } else {
    //         // Pega o corpo da requisição e decodifica o JSON
    //         $input = file_get_contents('php://input');
    //         $data = json_decode($input, true);  // Decodifica o JSON para um array associativo

    //         // Agora $data['id_ong'] contém o id_ong que você enviou do JavaScript
    //         $idOng = $data['id_ong'];
    //         $todasMensagens = $this->conversa->exibirTodasMensagens($tipoUsuario, $idOng);
    //         var_dump($todasMensagens);
    //     }

    //     $_SESSION['todas_mensagens'] = $todasMensagens;

    //     if ($tipoUsuario === 'id_usuario') {
    //         echo json_encode([
    //             'success' => true,
    //             'data' => [
    //                 'mensagens' => $idOng
    //             ]
    //         ]);
    //         exit();
    //     } else {
    //         echo json_encode([
    //             'success' => true,
    //             'data' => [
    //                 'mensagens' => $todasMensagens
    //             ]
    //         ]);
    //         exit();
    //     }
    // }

    public function mostrarTodasMensagens($tipoUsuario)
    {
        // Pega o corpo da requisição e decodifica o JSON
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);  // Decodifica o JSON para um array associativo

        if (!isset($data['id_ong'])) {
            echo json_encode([
                'success' => false,
                'message' => 'ID da ONG não foi enviado corretamente'
            ]);
            exit();
        }

        $idOng = $data['id_ong'];

        // Verifica se $tipoUsuario está definido corretamente
        if (!isset($tipoUsuario)) {
            echo json_encode([
                'success' => false,
                'message' => 'Tipo de usuário não foi definido corretamente'
            ]);
            exit();
        }

        $todasMensagens = $this->conversa->exibirTodasMensagens($tipoUsuario, $idOng);

        if ($todasMensagens === false) {
            echo json_encode([
                'success' => false,
                'message' => 'Não foi encontrada nenhuma mensagem'
            ]);
            exit();
        }

        echo json_encode([
            'success' => true,
            'data' => [
                'mensagens' => $todasMensagens
            ]
        ]);
        exit();
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
