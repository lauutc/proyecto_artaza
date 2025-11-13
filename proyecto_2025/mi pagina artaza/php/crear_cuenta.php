<?php
// Iniciar sesión
session_start();

// Headers para evitar errores
header('Content-Type: text/html; charset=UTF-8');

// Función para redireccionar
function redirect($path) {
  header('Location: ' . $path, true, 302);
  exit;
}

// Verificar que sea método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  redirect('../lauty_registro.html');
}

// Incluir conexión a la base de datos
require_once __DIR__ . '/conexion.php';

// Verificar que la conexión esté activa
if (!$conn || $conn->connect_error) {
  $_SESSION['flash_errors'] = ['Error de conexión a la base de datos. Por favor, contacta al administrador.'];
  redirect('../lauty_registro.html');
}

// Obtener y limpiar datos del formulario
$full_name = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

// Validar datos
$errors = [];
if (empty($full_name)) {
  $errors[] = 'El nombre es requerido';
}
if (empty($email)) {
  $errors[] = 'El email es requerido';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = 'El email no es válido';
}
if (empty($password)) {
  $errors[] = 'La contraseña es requerida';
} elseif (strlen($password) < 6) {
  $errors[] = 'La contraseña debe tener al menos 6 caracteres';
}
if ($password !== $password2) {
  $errors[] = 'Las contraseñas no coinciden';
}

// Si hay errores, redirigir de vuelta
if (!empty($errors)) {
  $_SESSION['flash_errors'] = $errors;
  redirect('../lauty_registro.html');
}

// Verificar si el email ya existe
try {
  $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
  if (!$stmt) {
    throw new Exception('Error al preparar la consulta: ' . $conn->error);
  }
  
  $stmt->bind_param('s', $email);
  $stmt->execute();
  $result = $stmt->get_result();
  
  if ($result->num_rows > 0) {
    $stmt->close();
    $_SESSION['flash_errors'] = ['El email ya está registrado'];
    redirect('../lauty_registro.html');
  }
  $stmt->close();

  // Crear hash de la contraseña
  $hash = password_hash($password, PASSWORD_DEFAULT);
  if (!$hash) {
    throw new Exception('Error al generar el hash de la contraseña');
  }

  // Insertar nuevo usuario
  $stmt = $conn->prepare('INSERT INTO users (email, password_hash, full_name) VALUES (?, ?, ?)');
  if (!$stmt) {
    throw new Exception('Error al preparar la inserción: ' . $conn->error);
  }
  
  $stmt->bind_param('sss', $email, $hash, $full_name);
  
  if (!$stmt->execute()) {
    throw new Exception('Error al ejecutar la inserción: ' . $stmt->error);
  }
  
  $user_id = $stmt->insert_id;
  $stmt->close();

  // Guardar datos en sesión
  $_SESSION['user_id'] = (int)$user_id;
  $_SESSION['user_name'] = $full_name ?: $email;
  $_SESSION['user_email'] = $email;
  $_SESSION['flash_success'] = 'Cuenta creada exitosamente. ¡Bienvenido!';
  
  // Redirigir a la página principal
  redirect('../lauty_home.html');
  
} catch (Exception $e) {
  error_log('Error al crear cuenta: ' . $e->getMessage());
  $_SESSION['flash_errors'] = ['Error al registrar. Por favor, intenta nuevamente. Si el problema persiste, contacta al administrador.'];
  redirect('../lauty_registro.html');
} catch (Throwable $e) {
  error_log('Error inesperado al crear cuenta: ' . $e->getMessage());
  $_SESSION['flash_errors'] = ['Error inesperado. Por favor, intenta nuevamente.'];
  redirect('../lauty_registro.html');
}
