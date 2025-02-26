<?php 
require_once '/CRUD ARTIGO_AUTOR/config/db_connect.php';

$db = new Db_Connect();
$conn = $db->connect();

if($conn){
  echo "Conexão estabelecida com sucesso!<br>";
}else{
  echo "Erro ao conectar.";
}

?>