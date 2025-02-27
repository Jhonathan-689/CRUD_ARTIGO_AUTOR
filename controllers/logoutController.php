<?php
session_start();
session_unset();
session_destroy();
header("Location: /CRUD_ARTIGO_AUTOR/views/login.php");
exit();
?>