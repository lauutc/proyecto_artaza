<?php
// Router para el servidor PHP incorporado
// Permite procesar archivos HTML con código PHP
// Uso: php -S localhost:8000 router.php

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestUri, PHP_URL_PATH);

// Si parse_url falla, usar la URI directamente
if ($requestPath === false || $requestPath === null) {
  $requestPath = $requestUri;
}

// Limpiar la ruta
$requestPath = str_replace('\\', '/', $requestPath);
$requestPath = trim($requestPath, '/');

// Si es la raíz o index.php, mostrar la página de login
if ($requestPath === '' || $requestPath === 'index.php' || $requestPath === '/' || $requestPath === false) {
  $file = __DIR__ . DIRECTORY_SEPARATOR . 'lauty_login.html';
  if (file_exists($file)) {
    header('Content-Type: text/html; charset=utf-8');
    
    // Leer el contenido
    $content = file_get_contents($file);
    
    // Procesar el código PHP dentro del HTML
    $content = preg_replace_callback(
      '/<\?php\s*(.*?)\s*\?>/s',
      function($matches) {
        ob_start();
        try {
          eval($matches[1]);
        } catch (Throwable $e) {
          echo '<!-- Error PHP: ' . htmlspecialchars($e->getMessage()) . ' -->';
        }
        return ob_get_clean();
      },
      $content
    );
    
    echo $content;
    exit;
  } else {
    // Si no existe el archivo, mostrar error
    http_response_code(404);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Error</title></head><body><p>No se encontró lauty_login.html en: ' . htmlspecialchars($file) . '</p></body></html>';
    exit;
  }
}

// Para otros archivos, verificar si existen
$filePath = __DIR__ . '/' . ltrim($requestPath, '/');
if (file_exists($filePath) && is_file($filePath)) {
  // Si es un archivo PHP, procesarlo
  if (pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
    include $filePath;
    exit;
  }
  // Para otros archivos, usar el comportamiento por defecto
  return false;
}

// Si no existe, mostrar error 404
http_response_code(404);
echo '<!doctype html><html><head><meta charset="utf-8"><title>404</title></head><body><p>Página no encontrada: ' . htmlspecialchars($requestPath) . '</p></body></html>';

