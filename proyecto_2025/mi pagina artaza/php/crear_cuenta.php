<?php
session_start();
require_once __DIR__ . '/conexion.php';

function redirect(string $path) {
  header('Location: ' . $path, true, 302);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('/crear_cuenta.html');
}

$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

$errors = [];
if ($full_name === '') $errors[] = 'Nombre requerido';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email inválido';
if (strlen($password) < 6) $errors[] = 'La contraseña debe tener al menos 6 caracteres';
if ($password !== $password2) $errors[] = 'Las contraseñas no coinciden';

if ($errors) {
  $_SESSION['flash_errors'] = $errors;
  redirect('/crear_cuenta.html');
}

try {
  $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $stmt->store_result();
  if ($stmt->num_rows > 0) {
    $_SESSION['flash_errors'] = ['El email ya está registrado'];
    redirect('/crear_cuenta.html');
  }
  $stmt->close();

  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $conn->prepare('INSERT INTO users (email, password_hash, full_name) VALUES (?, ?, ?)');
  $stmt->bind_param('sss', $email, $hash, $full_name);
  $stmt->execute();
  $user_id = $stmt->insert_id;
  $stmt->close();

  $_SESSION['user_id'] = $user_id;
  $_SESSION['user_name'] = $full_name ?: $email;
  $_SESSION['user_email'] = $email;
  redirect('/');
} catch (Throwable $e) {
  $_SESSION['flash_errors'] = ['Error al registrar: ' . $e->getMessage()];
  redirect('/crear_cuenta.html');
}
