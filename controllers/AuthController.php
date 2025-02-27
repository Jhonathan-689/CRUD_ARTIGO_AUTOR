<?php
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../models/AuthorModel.php';
require_once __DIR__ . '/../models/UserModel.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../vendor/autoload.php';
class AuthController
{
  private $userModel;
  private $authorModel;

  public function __construct()
  {
    $this->userModel = new UserModel();
    $this->authorModel = new AuthorModel();
    
  }

  // Servidor smtp com gmail
  private function sendActivationEmail($email, $token)
  {
    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'autoresartigosltcloud@gmail.com';
      $mail->Password = 'gjvz gmic xnzu ubas';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      $mail->setFrom('autoresartigosltcloud@gmail.com', 'Autores Artigos Lt Cloud');
      $mail->addAddress($email);

      $mail->isHTML(true);
      $mail->Subject = 'Ativação de conta';
      $mail->Body = "
            <h1>Ativação de Conta</h1>
            <p>Clique no link abaixo para ativar sua conta:</p>
            <p><a href='http://seusite.com/public/active.php?token=$token'>Ativar Conta</a></p>
            <p>Se não foi você que solicitou, ignore este e-mail.</p>
        ";

      $mail->send();
      return true;
    } catch (Exception $e) {
      return "Erro ao enviar e-mail: {$mail->ErrorInfo}";
    }
  }

  // Registrar um novo usuário ou autor
  public function register($name, $email, $password, $role)
  {
    if ($this->userModel->emailExists($email)) {
      return "Erro: este e-mail já está cadastrado!";
    }

    $token = bin2hex((random_bytes(32)));

    if ($role === 'user') {
      // Cadastro de usuario
      if ($this->userModel->registerUser($name, $email, $password, $token)) {
        $this->sendActivationEmail($email, $token);
        return "Usuário cadastrado! Verifique seu e-mail para ativar sua conta.";
      } elseif ($role === 'author') {
        // Cadastro de autor
        if ($this->authorModel->createAuthor($name, $email)) {
          $this->sendActivationEmail($email, $token);
          return "Autor cadastrado! Verifique seu e-mail para ativar sua conta.";
        }
      }
      return "Erro ao cadastrar. Tente novamente mais tarde!";
    }
  }

  // Ativar conta do usuário pelo token
  public function activateAccount($token)
  {
    if ($this->userModel->activateAccount($token)) {
      return "Conta ativada com sucesso! Você já pode fazer o login.";
    }
    return "Token inválido ou conta já ativada.";
  }

  // Login do usuário
  public function login($email, $password)
{
    $user = $this->userModel->verifyLogin($email, $password);

    if (is_array($user)) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        return "Login bem-sucedido!";
    }

    return $user;
}

  // Logout do usuário
  public function logout()
  {
    session_start();
    session_unset();
    session_destroy();
    return "Logout realizado com sucesso.";
  }

  // Recuperação de senha
  public function forgotPassword($email)
  {
    $token = bin2hex(random_bytes(32));
    if ($this->userModel->generateResetToken($email, $token)) {
      // Aqui para enviar o email com o link para redefinição de senha
      return "Um e-mail foi enviado com você fazer a redefinição de senha.";
    }
    return "E-mail não foi encontrado.";
  }

  // Redefinir senha
  public function resetPassword($token, $newPassword)
  {
    if ($this->userModel->resetPassword($token, $newPassword)) {
      return "Senha redefinida com sucesso!";
    }
    return "Token Inválido ou expirado.";
  }
}
?>