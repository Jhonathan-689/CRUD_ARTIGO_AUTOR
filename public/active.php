<?php
require_once __DIR__ . '/CRUD ARTIGO_AUTOR/controllers/AuthController.php';

$authController = new AuthController();

if (isset($_GET['token'])){
  $token = $_GET['token'];
  $message = $authController->activateAccount($token);
  echo "<h2>$message</h2>";
  echo "<p><a href='login.php'>Clique aqui para fazer login</a></p>";
} else{
  echo "<h2>Token não fornecido.</h2>";
}
?>