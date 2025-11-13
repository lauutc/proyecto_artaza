<?php
session_start();
require_once __DIR__ . '/php/conexion.php';

// Obtener categorías
$categorias = [];
try {
  $stmt = $conn->query("SELECT id, slug, name FROM categories ORDER BY name ASC");
  while ($row = $stmt->fetch_assoc()) {
    $categorias[] = $row;
  }
} catch (Exception $e) {
  error_log('Error al obtener categorías: ' . $e->getMessage());
}

// Obtener productos
$productos = [];
$category_id = isset($_GET['categoria']) ? (int)$_GET['categoria'] : null;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$order = isset($_GET['orden']) ? $_GET['orden'] : 'nuevo';

try {
  $sql = "SELECT p.id, p.name, p.description, p.price, p.price_old, p.color, 
                 p.size, p.image_url, p.stock, p.created_at,
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
  
  // Ordenar
  switch ($order) {
    case 'precio_asc':
      $sql .= " ORDER BY p.price ASC";
      break;
    case 'precio_desc':
      $sql .= " ORDER BY p.price DESC";
      break;
    case 'nombre':
      $sql .= " ORDER BY p.name ASC";
      break;
    default:
      $sql .= " ORDER BY p.created_at DESC";
  }
  
  $stmt = $conn->prepare($sql);
  if ($params) {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $result = $stmt->get_result();
  
  while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
  }
  $stmt->close();
} catch (Exception $e) {
  error_log('Error al obtener productos: ' . $e->getMessage());
}

// Mostrar mensajes flash
$flash_errors = $_SESSION['flash_errors'] ?? [];
$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash_errors'], $_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="es-AR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Productos | LC Store</title>
  <style>
    :root {
      --color-bg: #ffffff;
      --color-surface: #000000;
      --color-surface-light: #111111;
      --color-text: #000000;
      --color-text-light: #ffffff;
      --color-text-muted: #666666;
      --color-accent: #000000;
      --spacing-xs: 0.5rem;
      --spacing-sm: 1rem;
      --spacing-md: 1.5rem;
      --spacing-lg: 2rem;
      --spacing-xl: 3rem;
      --font-family: 'Segoe UI', Arial, sans-serif;
      --font-size-base: 16px;
      --border-radius: 8px;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      margin: 0;
      font-family: var(--font-family);
      font-size: var(--font-size-base);
      line-height: 1.6;
      background: var(--color-bg);
      color: var(--color-text);
    }
    header {
      background: var(--color-surface);
      color: var(--color-text-light);
    }
    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: var(--spacing-sm) var(--spacing-md);
      flex-wrap: wrap;
      gap: var(--spacing-sm);
    }
    .logo {
      font-weight: 800;
      font-size: 22px;
      color: var(--color-text-light);
    }
    .search {
      display: flex;
      align-items: center;
      gap: 8px;
      background: var(--color-bg);
      border-radius: 999px;
      padding: 6px 12px;
      max-width: 520px;
      flex: 1;
      min-width: 200px;
    }
    .search input {
      border: none;
      outline: none;
      width: 100%;
      font-size: var(--font-size-base);
    }
    .search button {
      background: transparent;
      border: none;
      cursor: pointer;
      font-size: 1.2em;
    }
    nav.primary {
      background: var(--color-surface-light);
      padding: var(--spacing-xs) 0;
    }
    nav.primary ul {
      list-style: none;
      display: flex;
      gap: 32px;
      margin: 0;
      padding: 0;
      justify-content: center;
      flex-wrap: wrap;
    }
    nav.primary a {
      color: var(--color-text-light);
      text-decoration: none;
      font-weight: bold;
      padding: var(--spacing-xs) var(--spacing-sm);
      border-radius: var(--border-radius);
      transition: background 0.2s;
    }
    nav.primary a:hover {
      background: #222222;
    }
    header h1 {
      text-align: center;
      padding: var(--spacing-sm);
      margin: 0;
      font-size: 1.5rem;
    }
    main {
      display: grid;
      grid-template-columns: 280px 1fr;
      gap: 0;
    }
    aside.sidebar {
      background: #fff;
      padding: 24px;
      border-right: 1px solid #ddd;
    }
    section.content {
      background: #fff;
      padding: 30px;
    }
    .breadcrumb {
      margin: 12px 0 16px;
      font-size: 14px;
      color: #444;
    }
    .breadcrumb ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: flex;
      gap: 8px;
    }
    .breadcrumb li + li::before {
      content: ">";
      color: #777;
      margin: 0 4px;
    }
    .breadcrumb a {
      color: #444;
      text-decoration: none;
    }
    .breadcrumb a:hover {
      text-decoration: underline;
    }
    .toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 20px;
    }
    .toolbar form select {
      padding: 8px 10px;
      border: 1px solid #ccc;
      border-radius: var(--border-radius);
      background: #fff;
      font-size: var(--font-size-base);
    }
    .add-product-btn {
      padding: 8px 16px;
      background: #000;
      color: #fff;
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
      font-size: 14px;
    }
    .add-product-btn:hover {
      background: #333;
    }
    .title {
      font-weight: 700;
      font-size: 18px;
      margin-bottom: 12px;
    }
    .side-group {
      margin-bottom: 22px;
    }
    .side-group h4 {
      margin: 0 0 10px 0;
      font-size: 14px;
      color: #333;
    }
    .side-group ul {
      list-style: none;
      margin: 0;
      padding: 0;
      display: grid;
      gap: 8px;
    }
    .side-group a {
      color: #111;
      text-decoration: none;
    }
    .side-group a:hover {
      text-decoration: underline;
    }
    .side-group a.active {
      font-weight: bold;
      color: #000;
    }
    .products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 30px;
      margin-top: 18px;
    }
    .product {
      background: #f1f1f1;
      border-radius: 10px;
      padding: 15px;
      text-align: center;
      position: relative;
    }
    .product img {
      width: 100%;
      height: 220px;
      object-fit: cover;
      border-radius: var(--border-radius);
    }
    .badge {
      position: absolute;
      top: 10px;
      left: 10px;
      background: #ff0000;
      color: #fff;
      font-size: 12px;
      padding: 4px 8px;
      border-radius: 4px;
      font-weight: 700;
    }
    .price {
      margin-top: 6px;
      display: flex;
      gap: 8px;
      justify-content: center;
      align-items: baseline;
    }
    .price .old {
      color: #777;
      text-decoration: line-through;
      font-size: 13px;
    }
    .price .current {
      color: #111;
      font-weight: 700;
    }
    .flash-message {
      padding: 12px;
      margin-bottom: 20px;
      border-radius: 4px;
    }
    .flash-message.error {
      background: #fee;
      color: #c00;
      border: 1px solid #fcc;
    }
    .flash-message.success {
      background: #efe;
      color: #060;
      border: 1px solid #cfc;
    }
    .modal {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 1000;
      align-items: center;
      justify-content: center;
    }
    .modal.active {
      display: flex;
    }
    .modal-content {
      background: #fff;
      padding: 30px;
      border-radius: 8px;
      max-width: 500px;
      width: 90%;
      max-height: 90vh;
      overflow-y: auto;
    }
    .modal-content h2 {
      margin-bottom: 20px;
    }
    .modal-content form {
      display: grid;
      gap: 15px;
    }
    .modal-content label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
    }
    .modal-content input,
    .modal-content select,
    .modal-content textarea {
      width: 100%;
      padding: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
    }
    .modal-content textarea {
      resize: vertical;
      min-height: 80px;
    }
    .modal-actions {
      display: flex;
      gap: 10px;
      justify-content: flex-end;
      margin-top: 20px;
    }
    .btn {
      padding: 10px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }
    .btn-primary {
      background: #000;
      color: #fff;
    }
    .btn-secondary {
      background: #ccc;
      color: #000;
    }
    footer {
      background: var(--color-surface);
      color: var(--color-text-light);
      text-align: center;
      padding: var(--spacing-sm);
      margin-top: var(--spacing-xl);
      font-size: 0.9rem;
    }
    @media (max-width: 768px) {
      .topbar {
        flex-direction: column;
        align-items: stretch;
      }
      .search {
        max-width: 100%;
      }
      main {
        grid-template-columns: 1fr;
      }
      aside.sidebar {
        border-right: none;
        border-bottom: 1px solid #ddd;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="topbar">
      <div class="logo">LC</div>
      <form class="search" action="lauty_categoria.php" method="get">
        <input type="text" name="q" placeholder="Buscar productos..." value="<?= htmlspecialchars($search) ?>" />
        <?php if ($category_id): ?>
          <input type="hidden" name="categoria" value="<?= $category_id ?>" />
        <?php endif; ?>
        <button type="submit" title="Buscar">🔍</button>
      </form>
    </div>
    <nav class="primary" aria-label="Navegación principal">
      <ul>
        <li><a href="lauty_home.html">Inicio</a></li>
        <li><a href="lauty_categoria.php">Productos</a></li>
        <li><a href="lauty_ofertas.html">Sale</a></li>
        <li><a href="curriculum.html">Curriculum</a></li>
        <li><a href="blog_personal.html">Blog</a></li>
        <li><a href="lauty_preg_frec.html">Preguntas</a></li>
        <li><a href="lauty_quienes.html">¿Quienes somos?</a></li>
        <li><a href="lauty_login.html">Login</a></li>
        <li><a href="curso.html">Curso</a></li>
      </ul>
    </nav>
    <h1>Productos</h1>
  </header>

  <main>
    <aside class="sidebar" aria-label="Categorías y filtros">
      <div class="side-group">
        <div class="title">Categorías</div>
        <ul>
          <li><a href="lauty_categoria.php" class="<?= !$category_id ? 'active' : '' ?>">Todas</a></li>
          <?php foreach ($categorias as $cat): ?>
            <li><a href="lauty_categoria.php?categoria=<?= $cat['id'] ?>" class="<?= $category_id == $cat['id'] ? 'active' : '' ?>"><?= htmlspecialchars($cat['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </aside>
    <section class="content" aria-label="Listado de productos">
      <nav class="breadcrumb" aria-label="Ruta de navegación">
        <ul>
          <li><a href="lauty_home.html">Inicio</a></li>
          <li>Productos</li>
        </ul>
      </nav>
      
      <?php if (!empty($flash_errors)): ?>
        <div class="flash-message error">
          <?php foreach ($flash_errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      
      <?php if ($flash_success): ?>
        <div class="flash-message success">
          <p><?= htmlspecialchars($flash_success) ?></p>
        </div>
      <?php endif; ?>
      
      <div class="toolbar">
        <a href="#" class="add-product-btn" onclick="document.getElementById('modal-agregar').classList.add('active'); return false;">+ Agregar Producto</a>
        <form action="lauty_categoria.php" method="get">
          <?php if ($category_id): ?>
            <input type="hidden" name="categoria" value="<?= $category_id ?>" />
          <?php endif; ?>
          <?php if ($search): ?>
            <input type="hidden" name="q" value="<?= htmlspecialchars($search) ?>" />
          <?php endif; ?>
          <label for="orden" style="display:none;">Ordenar por</label>
          <select id="orden" name="orden" onchange="this.form.submit()">
            <option value="nuevo" <?= $order === 'nuevo' ? 'selected' : '' ?>>Más nuevo</option>
            <option value="precio_asc" <?= $order === 'precio_asc' ? 'selected' : '' ?>>Precio: Menor a Mayor</option>
            <option value="precio_desc" <?= $order === 'precio_desc' ? 'selected' : '' ?>>Precio: Mayor a Menor</option>
            <option value="nombre" <?= $order === 'nombre' ? 'selected' : '' ?>>Nombre A-Z</option>
          </select>
        </form>
      </div>

      <div class="products">
        <?php if (empty($productos)): ?>
          <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #666;">
            <p>No hay productos disponibles.</p>
            <p style="margin-top: 10px;"><a href="#" onclick="document.getElementById('modal-agregar').classList.add('active'); return false;" style="color: #000; text-decoration: underline;">Agregar el primer producto</a></p>
          </div>
        <?php else: ?>
          <?php foreach ($productos as $producto): ?>
            <article class="product">
              <?php if ($producto['price_old']): ?>
                <div class="badge">OFERTA</div>
              <?php endif; ?>
              <?php if ($producto['image_url']): ?>
                <img src="<?= htmlspecialchars($producto['image_url']) ?>" alt="<?= htmlspecialchars($producto['name']) ?>" />
              <?php else: ?>
                <img src="https://via.placeholder.com/220x220?text=Sin+imagen" alt="<?= htmlspecialchars($producto['name']) ?>" />
              <?php endif; ?>
              <h4><?= htmlspecialchars($producto['name']) ?></h4>
              <div class="price">
                <?php if ($producto['price_old']): ?>
                  <div class="old">$<?= number_format($producto['price_old'], 2, ',', '.') ?></div>
                <?php endif; ?>
                <div class="current">$<?= number_format($producto['price'], 2, ',', '.') ?></div>
              </div>
              <?php if ($producto['stock'] <= 0): ?>
                <div style="margin-top: 8px; color: #c00; font-size: 12px;">Sin stock</div>
              <?php endif; ?>
            </article>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <!-- Modal para agregar producto -->
  <div id="modal-agregar" class="modal">
    <div class="modal-content">
      <h2>Agregar Nuevo Producto</h2>
      <form action="php/agregar_producto.php" method="post">
        <div>
          <label for="category_id">Categoría *</label>
          <select id="category_id" name="category_id" required>
            <option value="">Seleccionar categoría</option>
            <?php foreach ($categorias as $cat): ?>
              <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label for="name">Nombre del Producto *</label>
          <input type="text" id="name" name="name" required maxlength="150" />
        </div>
        <div>
          <label for="description">Descripción</label>
          <textarea id="description" name="description" maxlength="1000"></textarea>
        </div>
        <div>
          <label for="price">Precio *</label>
          <input type="number" id="price" name="price" step="0.01" min="0" required />
        </div>
        <div>
          <label for="price_old">Precio Anterior (opcional)</label>
          <input type="number" id="price_old" name="price_old" step="0.01" min="0" />
        </div>
        <div>
          <label for="color">Color</label>
          <input type="text" id="color" name="color" maxlength="32" />
        </div>
        <div>
          <label for="size">Talle</label>
          <input type="text" id="size" name="size" maxlength="16" />
        </div>
        <div>
          <label for="image_url">URL de la Imagen *</label>
          <input type="url" id="image_url" name="image_url" required maxlength="512" />
        </div>
        <div>
          <label for="stock">Stock *</label>
          <input type="number" id="stock" name="stock" min="0" value="0" required />
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-agregar').classList.remove('active');">Cancelar</button>
          <button type="submit" class="btn btn-primary">Agregar Producto</button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    <p>© 2025 LC Store. Todos los derechos reservados.</p>
  </footer>
</body>
</html>

