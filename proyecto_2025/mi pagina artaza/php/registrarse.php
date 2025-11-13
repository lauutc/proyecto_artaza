<?php
session_start();
require_once __DIR__ . '/conexion.php';

function redirect(string $path) {
  header('Location: ' . $path, true, 302);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('../lauty_login.php');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$errors = [];
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = 'Email inválido';
}
if ($password === '') {
  $errors[] = 'La contraseña es requerida';
}

if ($errors) {
  $_SESSION['flash_errors'] = $errors;
  redirect('../lauty_login.php');
}

try {
  $stmt = $conn->prepare('SELECT id, full_name, email, password_hash FROM users WHERE email = ?');
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $res = $stmt->get_result();
  $user = $res->fetch_assoc();
  $stmt->close();

  if (!$user || !password_verify($password, $user['password_hash'])) {
    $_SESSION['flash_errors'] = ['Email o contraseña incorrectos'];
    redirect('../lauty_login.php');
  }

  $_SESSION['user_id'] = (int)$user['id'];
  $_SESSION['user_name'] = $user['full_name'] ?: $user['email'];
  $_SESSION['user_email'] = $user['email'];
  $_SESSION['flash_success'] = 'Sesión iniciada correctamente';
  redirect('../lauty_home.html');
} catch (Throwable $e) {
  error_log('Error en login: ' . $e->getMessage());
  $_SESSION['flash_errors'] = ['Error al iniciar sesión. Por favor, intenta nuevamente.'];
  redirect('../lauty_login.php');
}
