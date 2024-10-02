<?php
require_once 'UsuarioController.php';
require_once '../../../config/database.php';

$db = new Database();

$controller = new UsuarioController($db);
$controller->cadastrarUsuario();
