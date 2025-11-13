<?php
// Archivo de prueba para verificar la conexión
header('Content-Type: text/plain; charset=UTF-8');

echo "=== Test de Conexión a Base de Datos ===\n\n";

// Incluir archivo de conexión
require_once __DIR__ . '/conexion.php';

if ($conn && !$conn->connect_error) {
  echo "✓ Conexión exitosa a la base de datos\n";
  echo "Base de datos: lc_store\n";
  echo "Charset: utf8mb4\n\n";
  
  // Verificar si la tabla users existe
  $result = $conn->query("SHOW TABLES LIKE 'users'");
  if ($result && $result->num_rows > 0) {
    echo "✓ La tabla 'users' existe\n\n";
    
    // Contar usuarios
    $result = $conn->query("SELECT COUNT(*) as total FROM users");
    if ($result) {
      $row = $result->fetch_assoc();
      echo "Usuarios registrados: " . $row['total'] . "\n";
    }
  } else {
    echo "✗ La tabla 'users' NO existe. Ejecuta el archivo schema.sql\n";
  }
  
  $conn->close();
} else {
  echo "✗ Error de conexión\n";
  if ($conn) {
    echo "Error: " . $conn->connect_error . "\n";
  }
}

