<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['error' => 'Method not allowed']);
  exit;
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload || !isset($payload['items']) || !is_array($payload['items'])) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid payload']);
  exit;
}

$email = isset($payload['email']) ? trim($payload['email']) : null;
$country = isset($payload['country']) ? trim($payload['country']) : null;
$postal_code = isset($payload['postal_code']) ? trim($payload['postal_code']) : null;

try {
  $pdo->beginTransaction();

  // Calcular totales
  $subtotal = 0;
  foreach ($payload['items'] as $item) {
    $productId = (int)$item['product_id'];
    $qty = max(1, (int)$item['quantity']);
    $stmt = $pdo->prepare('SELECT id, name, price FROM products WHERE id = :id AND is_active = 1');
    $stmt->execute([':id' => $productId]);
    $p = $stmt->fetch();
    if (!$p) { throw new Exception('Producto inválido: ' . $productId); }
    $subtotal += ($p['price'] * $qty);
  }
  $total = $subtotal; // aquí podrías sumar envío o descuentos

  // Crear orden
  $stmt = $pdo->prepare('INSERT INTO orders (user_id, status, subtotal, total, email, country, postal_code) VALUES (NULL, "pending", :subtotal, :total, :email, :country, :postal)');
  $stmt->execute([
    ':subtotal' => $subtotal,
    ':total' => $total,
    ':email' => $email,
    ':country' => $country,
    ':postal' => $postal_code,
  ]);
  $orderId = (int)$pdo->lastInsertId();

  // Insertar items
  foreach ($payload['items'] as $item) {
    $productId = (int)$item['product_id'];
    $qty = max(1, (int)$item['quantity']);
    $stmt = $pdo->prepare('SELECT id, name, price FROM products WHERE id = :id');
    $stmt->execute([':id' => $productId]);
    $p = $stmt->fetch();
    $unit = (float)$p['price'];
    $line = $unit * $qty;
    $stmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total) VALUES (:order, :product, :name, :price, :qty, :line)');
    $stmt->execute([
      ':order' => $orderId,
      ':product' => $productId,
      ':name' => $p['name'],
      ':price' => $unit,
      ':qty' => $qty,
      ':line' => $line,
    ]);
  }

  $pdo->commit();
  echo json_encode(['order_id' => $orderId, 'status' => 'pending', 'subtotal' => $subtotal, 'total' => $total]);
} catch (Throwable $e) {
  $pdo->rollBack();
  http_response_code(400);
  echo json_encode(['error' => $e->getMessage()]);
}
