<?php 
require_once __DIR__ . '/CRUD ARTIGO_AUTOR/models/UserModel.php';

class AuthController{
  private $userModel;

  public function __construct(){
    $this->userModel = new UserModel();
  }

  // Registrar um novo usuário
  public function register($name, $email, $password){
    $token = bin2hex(random_bytes(32));
    if($this->userModel->registerUser($name, $email, $password, $token)){
      // Enviar um email com email com link de ativação
      return "Usuario registrado com sucesso! Verifique seu e-mail para ativação.";
    }
    return "Erro ao registrar, tente novamente mais tarde!";
  }

  // Ativar conta do usuário pelo token
  public function activate($token){
    if($this->userModel->activateAccont($token)){
      return "Conta ativada com sucesso! Você já pode fazer o login.";
    }
    return "Token inválido ou conta já ativada.";
  }

  // Login do usuário
  public function login($email, $password){
    $user = $this->userModel->verifyLogin($email, $password);
    if(is_array($user)){
      session_start();
      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      return "Login bem-sucedido!";
    }elseif($user === 'Conta inativa'){
      return "Sua conta ainda não foi ativada. Verifique seu e-mail";
    }
    return "Credenciais incorretas.";
  }

  // Logout do usuário
  public function logout(){
    session_start();
    session_unset();
    session_destroy();
    return "Logout realizado com sucesso.";
  }

  // Recuperação de senha
  public function forgotPassword($email){
    $token = bin2hex(random_bytes(32));
    if($this->userModel->generateResetToken($email, $token)){
      // Aqui para enviar o email com o link para redefinição de senha
      return "Um e-mail foi enviado com você fazer a redefinição de senha.";
    }
    return "E-mail não foi encontrado.";
  }

  // Redefinir senha
  public function resetPassword($token, $newPassword){
    if($this->userModel->resetPassword($token, $newPassword)){
      return "Senha redefinida com sucesso!";
    }
    return "Token Inválido ou expirado.";
  }
}




?>