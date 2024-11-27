<?php
// config/database.php

class Database
{
    private $host = "amigopet-dev.com.br";
    private $db_name = "u304006048_amigopetdb";
    private $username = "u304006048_amigopetdbuser";
    private $password = "Iv@ng7h3d4f1";
    public $conn;

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
