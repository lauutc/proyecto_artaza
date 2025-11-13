# Sistema de Productos - LC Store

## Configuración Inicial

1. **Crear la base de datos:**
   ```bash
   mysql -u root -p < db/schema.sql
   ```

2. **Inicializar categorías:**
   ```bash
   mysql -u root -p < db/init_categorias.sql
   ```

## Uso

### Ver productos
- Accede a `lauty_categoria.php` para ver todos los productos
- Filtra por categoría usando el menú lateral
- Busca productos usando el campo de búsqueda
- Ordena productos por precio o nombre

### Agregar productos
1. En `lauty_categoria.php`, haz clic en "+ Agregar Producto"
2. Completa el formulario:
   - **Categoría**: Selecciona una categoría existente
   - **Nombre**: Nombre del producto (requerido)
   - **Descripción**: Descripción opcional
   - **Precio**: Precio actual (requerido)
   - **Precio Anterior**: Precio anterior si está en oferta (opcional)
   - **Color**: Color del producto (opcional)
   - **Talle**: Talle del producto (opcional)
   - **URL de la Imagen**: URL completa de la imagen (requerido)
   - **Stock**: Cantidad disponible (requerido)

3. Haz clic en "Agregar Producto"

## Estructura de Base de Datos

- **categories**: Categorías de productos
- **products**: Productos con información completa
- Los productos se relacionan con categorías mediante `category_id`

## Notas

- Los productos inactivos (`is_active = 0`) no se muestran
- Los productos sin stock se muestran con un mensaje "Sin stock"
- Los productos con `price_old` muestran un badge "OFERTA"

