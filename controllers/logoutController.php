<?php
session_start();
session_unset();
session_destroy();

session_start();
$_SESSION['message'] = "Conta Desconectada!";
$_SESSION['message_type'] = "success";

header("Location: ../views/login.php");
exit();
