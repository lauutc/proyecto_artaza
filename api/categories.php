<?php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php';

$stmt = $pdo->query('SELECT id, slug, name FROM categories ORDER BY name');
echo json_encode($stmt->fetchAll());
