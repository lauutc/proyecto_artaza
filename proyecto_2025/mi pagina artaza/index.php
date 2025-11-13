<?php
// Simple front controller to serve the static home HTML as the default page
// Run with: php -S localhost:8000
session_start();
header('Content-Type: text/html; charset=utf-8');
$home = __DIR__ . DIRECTORY_SEPARATOR . 'lauty_home.html';
if (is_file($home)) {
  if (!empty($_SESSION['user_name'])) {
    echo '<div style="background:#f5f5f5;padding:8px 12px;text-align:center;">Hola, ' . htmlspecialchars($_SESSION['user_name']) . '!</div>';
  }
  readfile($home);
  exit;
}
echo '<!doctype html><meta charset="utf-8"><title>Inicio</title><p>No se encontró lauty_home.html</p>';
