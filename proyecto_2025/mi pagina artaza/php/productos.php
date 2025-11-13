<?php
// API para obtener productos desde la base de datos
require_once __DIR__ . '/conexion.php';

header('Content-Type: application/json; charset=utf-8');

try {
  $category_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;
  $search = isset($_GET['q']) ? trim($_GET['q']) : '';
  $order = isset($_GET['orden']) ? $_GET['orden'] : 'created_at DESC';
  
  // Validar orden
  $allowed_orders = [
    'created_at DESC' => 'p.created_at DESC',
    'price ASC' => 'p.price ASC',
    'price DESC' => 'p.price DESC',
    'name ASC' => 'p.name ASC'
  ];
  $order_sql = $allowed_orders[$order] ?? 'p.created_at DESC';
  
  // Construir consulta
  $sql = "SELECT p.id, p.name, p.description, p.price, p.price_old, p.color, 
                 p.size, p.image_url, p.stock, p.is_active, p.created_at,
                 c.id as category_id, c.name as category_name, c.slug as category_slug
          FROM products p
          INNER JOIN categories c ON p.category_id = c.id
          WHERE p.is_active = 1";
  
  $params = [];
  $types = '';
  
  if ($category_id) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
    $types .= 'i';
  }
  
  if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ?)";
    $search_term = "%{$search}%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
  }
  
  $sql .= " ORDER BY {$order_sql}";
  
  $stmt = $conn->prepare($sql);
  if ($params) {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $result = $stmt->get_result();
  
  $productos = [];
  while ($row = $result->fetch_assoc()) {
    $productos[] = [
      'id' => (int)$row['id'],
      'name' => $row['name'],
      'description' => $row['description'],
      'price' => (float)$row['price'],
      'price_old' => $row['price_old'] ? (float)$row['price_old'] : null,
      'color' => $row['color'],
      'size' => $row['size'],
      'image_url' => $row['image_url'],
      'stock' => (int)$row['stock'],
      'category' => [
        'id' => (int)$row['category_id'],
        'name' => $row['category_name'],
        'slug' => $row['category_slug']
      ],
      'created_at' => $row['created_at']
    ];
  }
  
  $stmt->close();
  
  echo json_encode(['success' => true, 'productos' => $productos], JSON_UNESCAPED_UNICODE);
  
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
}

