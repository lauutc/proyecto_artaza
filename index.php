<?php
// Página principal - muestra lauty_login.html
// Ejecutar desde este directorio: php -S localhost:8000

session_start();
header('Content-Type: text/html; charset=utf-8');

// Ruta al archivo de login en el subdirectorio
$projectDir = __DIR__ . DIRECTORY_SEPARATOR . 'proyecto_2025' . DIRECTORY_SEPARATOR . 'mi pagina artaza';
$loginFile = $projectDir . DIRECTORY_SEPARATOR . 'lauty_login.html';

if (!file_exists($loginFile)) {
  http_response_code(404);
  die('Error: No se encontró lauty_login.html. Ruta buscada: ' . htmlspecialchars($loginFile));
}

// Leer el contenido
$content = file_get_contents($loginFile);

// Ajustar rutas relativas para que apunten al subdirectorio correcto
$basePath = 'proyecto_2025/mi pagina artaza/';
$content = str_replace(
  ['href="php/', 'action="php/', 'href="lauty_', 'action="lauty_'],
  ['href="' . $basePath . 'php/', 'action="' . $basePath . 'php/', 'href="' . $basePath . 'lauty_', 'action="' . $basePath . 'lauty_'],
  $content
);

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

