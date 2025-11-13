<?php
// Procesar agregar nuevo producto
session_start();
require_once __DIR__ . '/conexion.php';

function redirect($path, $message = null) {
  if ($message) {
    $_SESSION['flash_message'] = $message;
  }
  header('Location: ' . $path, true, 302);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  redirect('../lauty_categoria.php', 'Método no permitido');
}

// Obtener y validar datos
$category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
$name = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');
$price = isset($_POST['price']) ? (float)$_POST['price'] : 0;
$price_old = isset($_POST['price_old']) && $_POST['price_old'] ? (float)$_POST['price_old'] : null;
$color = trim($_POST['color'] ?? '');
$size = trim($_POST['size'] ?? '');
$image_url = trim($_POST['image_url'] ?? '');
$stock = isset($_POST['stock']) ? (int)$_POST['stock'] : 0;

$errors = [];

if ($category_id <= 0) {
  $errors[] = 'La categoría es requerida';
}
if (empty($name)) {
  $errors[] = 'El nombre del producto es requerido';
}
if ($price <= 0) {
  $errors[] = 'El precio debe ser mayor a 0';
}
if (empty($image_url)) {
  $errors[] = 'La URL de la imagen es requerida';
}

if (!empty($errors)) {
  $_SESSION['flash_errors'] = $errors;
  redirect('../lauty_categoria.php');
}

try {
  // Verificar que la categoría existe
  $stmt = $conn->prepare('SELECT id FROM categories WHERE id = ?');
  $stmt->bind_param('i', $category_id);
  $stmt->execute();
  $result = $stmt->get_result();
  if ($result->num_rows === 0) {
    $stmt->close();
    $_SESSION['flash_errors'] = ['La categoría seleccionada no existe'];
    redirect('../lauty_categoria.php');
  }
  $stmt->close();
  
  // Insertar producto
  $stmt = $conn->prepare('INSERT INTO products (category_id, name, description, price, price_old, color, size, image_url, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
  $stmt->bind_param('issdssssi', $category_id, $name, $description, $price, $price_old, $color, $size, $image_url, $stock);
  
  if (!$stmt->execute()) {
    throw new Exception('Error al insertar producto: ' . $stmt->error);
  }
  
  $stmt->close();
  
  $_SESSION['flash_success'] = 'Producto agregado exitosamente';
  redirect('../lauty_categoria.php');
  
} catch (Exception $e) {
  error_log('Error al agregar producto: ' . $e->getMessage());
  $_SESSION['flash_errors'] = ['Error al agregar producto. Por favor, intenta nuevamente.'];
  redirect('../lauty_categoria.php');
}

