<?php
// API para obtener categorías desde la base de datos
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
  $sql = "SELECT id, slug, name, created_at FROM categories ORDER BY name ASC";
  $result = $conn->query($sql);
  
  $categorias = [];
  while ($row = $result->fetch_assoc()) {
    $categorias[] = [
      'id' => (int)$row['id'],
      'slug' => $row['slug'],
      'name' => $row['name'],
      'created_at' => $row['created_at']
    ];
  }
  
  echo json_encode(['success' => true, 'categorias' => $categorias], JSON_UNESCAPED_UNICODE);
  
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

