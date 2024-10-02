<?php
// config/database.php

class Database
{
    private $host = "localhost";
    private $db_name = "cadastro";
    private $username = "root";
    private $password = "";
    public $conn;

    // Método para obter a conexão com o banco de dados
    public function getConnection()
    {
        $this->conn = null;

        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

        if ($this->conn->connect_error) {
            die("Conexão falhou: " . $this->conn->connect_error);
        }

        return $this->conn;
    }
}
