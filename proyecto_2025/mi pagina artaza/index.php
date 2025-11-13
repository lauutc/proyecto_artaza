<?php
// Página principal - muestra lauty_login.html
// Ejecutar: php -S localhost:8000

session_start();
header('Content-Type: text/html; charset=utf-8');

$loginFile = __DIR__ . '/lauty_login.html';

if (!file_exists($loginFile)) {
  die('Error: No se encontró lauty_login.html');
}

$content = file_get_contents($loginFile);

// Procesar código PHP dentro del HTML
$content = preg_replace_callback(
  '/<\?php\s*(.*?)\s*\?>/s',
  function($matches) {
    ob_start();
    try {
      eval($matches[1]);
    } catch (Throwable $e) {
      echo '<!-- Error: ' . htmlspecialchars($e->getMessage()) . ' -->';
    }
    return ob_get_clean();
  },
  $content
);

echo $content;
