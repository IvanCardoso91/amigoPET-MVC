<?php
// Requer os arquivos necessários
require_once 'app/controllers/UsuarioOngController.php';

// Cria uma instância do controlador
$controller = new UsuarioOngController();

// Chama o método de cadastro do controlador
$controller->cadastrar();
