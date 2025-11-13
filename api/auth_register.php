<?php
// Registro de usuario (POST formulario). Respuesta en HTML simple.
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo '<p>Método no permitido</p>';
  exit;
}

// Acepta form urlencoded. Si viene JSON, también lo considera.
$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!is_array($payload) || empty($payload)) { $payload = $_POST; }

$email = isset($payload['email']) ? trim($payload['email']) : '';
$password = isset($payload['password']) ? (string)$payload['password'] : '';
$password2 = isset($payload['password2']) ? (string)$payload['password2'] : '';
$full_name = isset($payload['full_name']) ? trim($payload['full_name']) : null;

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  http_response_code(400);
  echo '<p>Email inválido.</p>';
  exit;
}
if (strlen($password) < 6) {
  http_response_code(400);
  echo '<p>La contraseña debe tener al menos 6 caracteres.</p>';
  exit;
}
if ($password2 !== '' && $password !== $password2) {
  http_response_code(400);
  echo '<p>Las contraseñas no coinciden.</p>';
  exit;
}

try {
  $stmt = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
  $stmt->execute([':email' => $email]);
  if ($stmt->fetch()) {
    http_response_code(409);
    echo '<p>El email ya está registrado.</p>';
    exit;
  }

  $hash = password_hash($password, PASSWORD_BCRYPT);
  $stmt = $pdo->prepare('INSERT INTO users (email, password_hash, full_name) VALUES (:email, :hash, :name)');
  $stmt->execute([':email' => $email, ':hash' => $hash, ':name' => $full_name]);

  echo '<p>Cuenta creada con éxito.</p><p><a href="../lauty_login.html">Volver</a></p>';
} catch (Throwable $e) {
  http_response_code(500);
  echo '<p>Error al registrar: ' . htmlspecialchars($e->getMessage()) . '</p>';
}
