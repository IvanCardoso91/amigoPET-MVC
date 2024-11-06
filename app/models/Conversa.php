<?php
class Conversa
{
    private $conn;
    private $table_name = "conversas";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function enviarMensagem($id_usuario, $id_animal, $id_ong, $mensagem)
    {
        $query = "INSERT INTO " . $this->table_name . " (id_usuario, id_animal, id_ong, mensagem, enviado_por)
                  VALUES (?, ?, ?, ?, 'adotante')";

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param('iiis', $id_usuario, $id_animal, $id_ong, $mensagem);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function enviarMensagemAdotante($id_usuario, $id_animal, $id_ong, $mensagem)
    {
        $query = "INSERT INTO " . $this->table_name . " (id_usuario, id_animal, id_ong, mensagem, enviado_por)
                  VALUES (?, ?, ?, ?, 'adotante')";

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param('iiis', $id_usuario, $id_animal, $id_ong, $mensagem);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function enviarMensagemOng($id_usuario, $id_animal, $id_ong, $mensagem)
    {
        $query = "INSERT INTO " . $this->table_name . " (id_usuario, id_animal, id_ong, mensagem, enviado_por)
                  VALUES (?, ?, ?, ?, 'ong')";

        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return false;
        }

        $stmt->bind_param('iiis', $id_usuario, $id_animal, $id_ong, $mensagem);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function exibirTodasMensagens($tipoUsuario)
    {
        if ($tipoUsuario === 'id_usuario') {
            $query = "SELECT c.mensagem, c.enviado_por, c.data_envio, a.raca AS nome_animal, ua.nome_completo AS nome_usuario
            FROM conversas c
            JOIN animal a ON c.id_animal = a.id_animal
            JOIN usuario_adotante ua ON c.id_usuario = ua.id_usuario
            WHERE c.id_usuario = ?
            ORDER BY c.data_envio ASC";
        } else {
            $query = "SELECT c.mensagem, c.enviado_por, c.data_envio, c.id_animal, c.id_usuario AS id_adotante, ua.nome_completo AS nome_adotante, 
            a.raca AS nome_animal, uo.nome_fantasia AS nome_ong
            FROM conversas c
            JOIN animal a ON c.id_animal = a.id_animal
            JOIN usuario_ong uo ON c.id_ong = uo.id_ong
            JOIN usuario_adotante ua ON c.id_usuario = ua.id_usuario
            WHERE c.id_ong  = ?
            ORDER BY c.data_envio ASC";
        }

        $stmt = $this->conn->prepare($query);

        if ($tipoUsuario === 'id_usuario') {
            $stmt->bind_param("i", $_SESSION[$tipoUsuario]);
        } else {
            $stmt->bind_param("i", $_SESSION[$tipoUsuario]);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $mensagens = [];
        while ($row = $result->fetch_assoc()) {
            $mensagens[] = $row;
        }

        return $mensagens;
    }
}
