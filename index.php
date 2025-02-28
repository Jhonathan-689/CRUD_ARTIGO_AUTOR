<?php
session_start();

// Inclui a conexão com o banco de dados
require_once 'config/db_connect.php';

// Redireciona para a página de login por padrão
header("Location: views/login.php");
exit;
?>
