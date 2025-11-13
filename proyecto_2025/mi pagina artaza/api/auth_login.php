<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

// Acepta JSON o application/x-www-form-urlencoded
$raw = file_get_contents('php://input');
$payload = json_decode($raw, true);
if (!is_array($payload)) { $payload = $_POST; }

$email = isset($payload['email']) ? trim($payload['email']) : '';
$password = isset($payload['password']) ? (string)$payload['password'] : '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
  http_response_code(400);
  echo json_encode(['error' => 'Credenciales inválidas']);
  exit;
}

try {
  $stmt = $pdo->prepare('SELECT id, email, password_hash, full_name FROM users WHERE email = :email LIMIT 1');
  $stmt->execute([':email' => $email]);
  $user = $stmt->fetch();

  if (!$user || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Email o contraseña incorrectos']);
    exit;
  }

  // Iniciar sesión PHP mínima
  if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
  $_SESSION['user_id'] = (int)$user['id'];
  $_SESSION['user_email'] = $user['email'];
  $_SESSION['user_name'] = $user['full_name'];

  echo json_encode([
    'ok' => true,
    'user' => [
      'id' => (int)$user['id'],
      'email' => $user['email'],
      'full_name' => $user['full_name']
    ]
  ]);
} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Login failed', 'message' => $e->getMessage()]);
}
