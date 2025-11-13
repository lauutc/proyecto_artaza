<?php
session_start();
require_once __DIR__ . '/connection.php';

function redirect(string $path) {
  header('Location: ' . $path, true, 302);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('/lauty_login.html');
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
  redirect('/lauty_login.html');
}

try {
  $stmt = $conn->prepare('SELECT id, full_name, email, password_hash FROM users WHERE email = ?');
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $res = $stmt->get_result();
  $user = $res->fetch_assoc();
  $stmt->close();

  if (!$user || !password_verify($password, $user['password_hash'])) {
    redirect('/lauty_login.html');
  }

  $_SESSION['user_id'] = (int)$user['id'];
  $_SESSION['user_name'] = $user['full_name'] ?: $user['email'];
  $_SESSION['user_email'] = $user['email'];
  redirect('/');
} catch (Throwable $e) {
  redirect('/lauty_login.html');
}
