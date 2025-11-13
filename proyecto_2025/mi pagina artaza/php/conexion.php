<?php
// Desactivar reporte de errores estricto para mejor compatibilidad
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Configuración de la base de datos
$DB_HOST = '127.0.0.1';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'lc_store';
$DB_PORT = 3306;

// Intentar conectar a la base de datos
$conn = null;
try {
  $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME, $DB_PORT);
  
  // Verificar si hay error de conexión
  if ($conn->connect_error) {
    throw new Exception('Error de conexión: ' . $conn->connect_error);
  }
  
  // Configurar charset
  $conn->set_charset('utf8mb4');
  
} catch (Exception $e) {
  // Log del error
  error_log('Error de conexión a la base de datos: ' . $e->getMessage());
  
  // Mostrar mensaje amigable
  http_response_code(500);
  die('Error de conexión a la base de datos. Por favor, verifica que MySQL esté corriendo y que la base de datos "lc_store" exista.');
}
