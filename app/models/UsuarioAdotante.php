<?php

class Usuario
{
    private $conn;

    public function __construct($db)
    {
        echo "chegou aquiMODEL";
        echo "<pre>";
        print_r($db); // Exibe os dados enviados via POST
        echo "</pre>";
        $this->conn = $db;
    }

    // Função para cadastrar usuário
    public function cadastrar($nome_completo, $email, $senha, $telefone, $cpf)
    {
        $query = "INSERT INTO usuarios (nome_completo, email, senha, telefone, cpf) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssss", $nome_completo, $email, $senha, $telefone, $cpf);
        return $stmt->execute();
    }
}
