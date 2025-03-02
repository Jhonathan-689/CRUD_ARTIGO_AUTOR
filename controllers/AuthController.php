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

  private function sendActivationEmail($email, $token)
  {
    $mail = new PHPMailer(true);

    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'autoresartigosltcloud@gmail.com';
      $mail->Password = 'mhil advs laih tlmw';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;
      $mail->CharSet = 'UTF-8';

      $mail->setFrom('autoresartigosltcloud@gmail.com', 'Autores Artigos LT Cloud');
      $mail->addAddress($email);

      $mail->isHTML(true);
      $mail->Subject = 'Ativação de Conta';

      $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
      $activationLink = rtrim($baseUrl, '/') . "/controllers/ActivateController.php?token=" . urlencode($token);

      $mail->Body = "
    <h1>Ativação de Conta</h1>
    <p>Olá, obrigado por se registrar!</p>
    <p>Clique no botão abaixo para ativar sua conta:</p>
    <p>
        <a href='$activationLink' 
           style='display: inline-block; padding: 12px 20px; font-size: 16px; color: white; background-color: #28a745; text-decoration: none; border-radius: 5px;'>
           ✅ Ativar Conta
        </a>
    </p>
    <p>Se não foi você que solicitou, ignore este e-mail.</p>
";

      $mail->AltBody = "Ativação de Conta\n\nOlá, obrigado por se registrar!\nClique no link abaixo para ativar sua conta:\n$activationLink\n\nSe não foi você que solicitou, ignore este e-mail.";

      if ($mail->send()) {
        return true;
      } else {
        error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
        return false;
      }

    } catch (Exception $e) {
      error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
      return false;
    }
  }

  private function sendResetEmail($email, $token)
  {
    $mail = new PHPMailer(true);
    try {
      $mail->isSMTP();
      $mail->Host = 'smtp.gmail.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'autoresartigosltcloud@gmail.com';
      $mail->Password = 'mhil advs laih tlmw';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;
      $mail->CharSet = 'UTF-8';

      $mail->setFrom('autoresartigosltcloud@gmail.com', 'Autores Artigos Lt Cloud');
      $mail->addAddress($email);

      $mail->isHTML(true);
      $mail->Subject = 'Redefinição de Senha';

      $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
      $resetLink = "$baseUrl/controllers/ResetPasswordController.php?token=$token";

      $mail->Body = "
            <h1>Recuperação de Senha</h1>
            <p>Clique no link abaixo para redefinir sua senha:</p>
            <p><a href='$resetLink'>Redefinir Senha</a></p>
            <p>Se não foi você que solicitou, ignore este e-mail.</p>
        ";

      $mail->send();
      return true;
    } catch (Exception $e) {
      return "Erro ao enviar e-mail: {$mail->ErrorInfo}";
    }
  }

  public function register($name, $email, $password, $role)
  {
    if ($this->userModel->emailExists($email)) {
      return "Este e-mail já está cadastrado!";
    }

    $token = bin2hex(random_bytes(32));

    if ($role === 'user') {
      if ($this->userModel->registerUser($name, $email, $password, $token)) {
        $this->sendActivationEmail($email, $token);
        return true;
      }
    } elseif ($role === 'author') {
      if ($this->authorModel->createAuthor($name, $email, $password, $token)) {
        $this->sendActivationEmail($email, $token);
        return true;
      }
    }
    return "Erro ao cadastrar. Tente novamente mais tarde!";
  }

  public function activateAccount($token)
  {
    error_log("Tentando ativar conta com token: " . $token);

    $userActivated = $this->userModel->activateAccount($token);
    $authorActivated = $this->authorModel->activateAccount($token);

    if ($userActivated) {
      error_log("Usuário ativado com sucesso.");
      return "Conta ativada com sucesso! Você já pode fazer o login.";
    } elseif ($authorActivated) {
      error_log("Autor ativado com sucesso.");
      return "Conta ativada com sucesso! Você já pode fazer o login.";
    } else {
      error_log("Erro: Token inválido ou conta já ativada.");
      return "Token inválido ou conta já ativada.";
    }
  }

  public function login($email, $password)
  {
    $user = $this->userModel->verifyLogin($email, $password);

    if (is_array($user)) {
      session_start();

      $_SESSION['user_id'] = $user['id'];
      $_SESSION['user_name'] = $user['name'];
      $_SESSION['role'] = $user['role'];

      header("Location: ../views/dashboard.php");
      exit();
    }

    return $user;
  }

  public function forgotPassword($email)
  {
    require_once __DIR__ . '/../models/AuthorModel.php';
    require_once __DIR__ . '/../models/UserModel.php';

    $authorModel = new AuthorModel();
    $userModel = new UserModel();

    $user = $userModel->getUserByEmail($email);
    $author = $authorModel->getAuthorByEmail($email);

    if (!$user && !$author) {
      return "Este e-mail não está cadastrado.";
    }

    $token = bin2hex(random_bytes(32));

    if ($user) {
      $userModel->generateResetToken($email, $token);
    } elseif ($author) {
      $authorModel->generateResetToken($email, $token);
    }

    $this->sendResetEmail($email, $token);

    return "Um e-mail foi enviado, verifique para fazer a redefinição de senha.";
  }

  public function resetPassword($token, $newPassword)
  {
    $userUpdated = $this->userModel->resetPassword($token, $newPassword);
    $authorUpdated = $this->authorModel->resetPassword($token, $newPassword);

    if ($userUpdated || $authorUpdated) {
      return "Senha redefinida com sucesso!";
    }

    return "Token Inválido ou expirado.";
  }

}
