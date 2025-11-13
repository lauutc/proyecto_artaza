<!DOCTYPE html>
<html lang="es-AR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registro | LC Store</title>
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
      background: var(--color-bg);
      color: var(--color-text);
      font-family: var(--font-family);
      font-size: var(--font-size-base);
      line-height: 1.6;
      margin: 0;
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
      color: var(--color-text-light);
    }
    form {
      max-width: 350px;
      margin: 40px auto;
      padding: 20px;
      border: 1px solid #eee;
      border-radius: var(--border-radius);
      background: #fafafa;
    }
    h2 {
      text-align: center;
      font-size: 1.3em;
      margin-bottom: 1.2em;
    }
    label {
      display: block;
      margin: 12px 0 4px;
      font-size: 1em;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 8px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 1em;
    }
    button {
      width: 100%;
      padding: 10px;
      background: #222;
      color: #fff;
      border: none;
      border-radius: 4px;
      font-size: 1em;
      cursor: pointer;
      margin-top: 10px;
      transition: background 0.2s;
    }
    button:hover {
      background: #444;
    }
    .alt {
      text-align: center;
      margin-top: 12px;
      font-size: 0.95em;
    }
    .alt a {
      color: var(--color-surface);
      text-decoration: none;
    }
    .alt a:hover {
      text-decoration: underline;
    }
    footer {
      background: var(--color-surface);
      color: var(--color-text-light);
      text-align: center;
      padding: var(--spacing-md);
      margin-top: 40px;
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
      nav.primary ul {
        gap: 16px;
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="topbar">
      <div class="logo">LC</div>
      <form class="search" action="#" method="get">
        <input type="text" name="q" placeholder="Buscar productos..." />
        <button type="submit" title="Buscar">🔍</button>
      </form>
    </div>
    <nav class="primary" aria-label="Navegación principal">
      <ul>
        <li><a href="lauty_home.html">Inicio</a></li>
        <li><a href="lauty_categoria.html">Productos</a></li>
        <li><a href="lauty_ofertas.html">Sale</a></li>
        <li><a href="curriculum.html">Curriculum</a></li>
        <li><a href="blog_personal.html">Blog</a></li>
        <li><a href="lauty_preg_frec.html">Preguntas</a></li>
        <li><a href="lauty_quienes.html">¿Quienes somos?</a></li>
        <li><a href="lauty_login.php">Login</a></li>
        <li><a href="curso.html">Curso</a></li>
      </ul>
    </nav>
    <h1>Registro</h1>
  </header>
  <form method="post" action="php/crear_cuenta.php">
    <h2>Crear cuenta</h2>
    <?php
    session_start();
    if (isset($_SESSION['flash_errors'])) {
      echo '<div style="background: #fee; color: #c00; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 0.9em;">';
      foreach ($_SESSION['flash_errors'] as $error) {
        echo '<p style="margin: 0 0 5px 0;">' . htmlspecialchars($error) . '</p>';
      }
      echo '</div>';
      unset($_SESSION['flash_errors']);
    }
    if (isset($_SESSION['flash_success'])) {
      echo '<div style="background: #efe; color: #060; padding: 10px; border-radius: 4px; margin-bottom: 15px; font-size: 0.9em;">';
      echo '<p style="margin: 0;">' . htmlspecialchars($_SESSION['flash_success']) . '</p>';
      echo '</div>';
      unset($_SESSION['flash_success']);
    }
    ?>
    <label for="full_name">Nombre completo</label>
    <input type="text" id="full_name" name="full_name" placeholder="ej: María Perez" required maxlength="120" />
    <label for="email">Email</label>
    <input type="email" id="email" name="email" placeholder="ej: tunombre@email.com" required maxlength="160" />
    <label for="password">Contraseña</label>
    <input type="password" id="password" name="password" placeholder="Mínimo 6 caracteres" required maxlength="50" />
    <label for="password2">Confirmar Contraseña</label>
    <input type="password" id="password2" name="password2" placeholder="Repetir contraseña" required maxlength="50" />
    <button type="submit">CREAR CUENTA</button>
    <div class="alt">¿Ya tenés cuenta? <a href="lauty_login.php">Iniciá sesión</a></div>
  </form>
  <footer>
    <p>© 2025 LC Store. Todos los derechos reservados.</p>
  </footer>
</body>
</html>

