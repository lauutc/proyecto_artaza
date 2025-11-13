<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$limit = isset($_GET['limit']) ? max(1, min(100, (int)$_GET['limit'])) : 50;

if ($category !== '') {
  $sql = 'SELECT p.id, p.name, p.description, p.price, p.price_old, p.color, p.size, p.image_url, p.stock,
                 c.slug AS category_slug, c.name AS category_name
          FROM products p
          JOIN categories c ON c.id = p.category_id
          WHERE c.slug = :slug AND p.is_active = 1
          ORDER BY p.created_at DESC
          LIMIT :limit';
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':slug', $category, PDO::PARAM_STR);
  $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
  $stmt->execute();
  echo json_encode($stmt->fetchAll());
  exit;
}

$sql = 'SELECT p.id, p.name, p.description, p.price, p.price_old, p.color, p.size, p.image_url, p.stock,
               c.slug AS category_slug, c.name AS category_name
        FROM products p
        JOIN categories c ON c.id = p.category_id
        WHERE p.is_active = 1
        ORDER BY p.created_at DESC
        LIMIT :limit';
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
echo json_encode($stmt->fetchAll());
