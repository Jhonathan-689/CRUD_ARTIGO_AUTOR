<?php
session_start();

require_once 'config/db_connect.php';
header("Location: /views/login.php");

exit();
?>
