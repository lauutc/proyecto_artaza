USE lc_store;

INSERT INTO categories (slug, name) VALUES
  ('remeras','Remeras'),
  ('hoodies','Hoodies'),
  ('accesorios','Accesorios'),
  ('jeans','Jeans'),
  ('camperas','Camperas'),
  ('camisas','Camisas'),
  ('ofertas','Ofertas')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Productos ejemplo (algunos con price_old como oferta)
INSERT INTO products (category_id, name, description, price, price_old, color, size, image_url, stock)
SELECT c.id, 'Remera Básica Negra', 'Algodón 100%', 29900, NULL, 'negro', 'M', 'https://images.unsplash.com/photo-1489987707025-afc232f7ea0f?q=80&w=800&auto=format&fit=crop', 50
FROM categories c WHERE c.slug='remeras'
UNION ALL
SELECT c.id, 'Hoodie Oversize', 'Polar suave', 79900, 99900, 'gris', 'L', 'https://images.unsplash.com/photo-1616002854119-1c09be7424a2?q=80&w=800&auto=format&fit=crop', 20
FROM categories c WHERE c.slug='hoodies'
UNION ALL
SELECT c.id, 'Cinturón de cuero', 'Hebilla metálica', 15500, NULL, 'marron', NULL, 'https://images.unsplash.com/photo-1618354691321-c23b3d48f6ea?q=80&w=800&auto=format&fit=crop', 30
FROM categories c WHERE c.slug='accesorios'
UNION ALL
SELECT c.id, 'Jeans Baggy Negro', 'Fit suelto', 35000, NULL, 'negro', '42', 'https://images.unsplash.com/photo-1542272604-787c3835535d?q=80&w=800&auto=format&fit=crop', 40
FROM categories c WHERE c.slug='jeans'
UNION ALL
SELECT c.id, 'Campera Rompeviento', 'Resistente al agua', 89900, 129900, 'negro', 'M', 'https://images.unsplash.com/photo-1516826957135-700dedea698c?q=80&w=800&auto=format&fit=crop', 15
FROM categories c WHERE c.slug='camperas'
UNION ALL
SELECT c.id, 'Camisa Oxford Blanca', 'Corte clásico', 45900, NULL, 'blanco', 'M', 'https://images.unsplash.com/photo-1539533113208-f6df8cc8b543?q=80&w=800&auto=format&fit=crop', 25
FROM categories c WHERE c.slug='camisas';
