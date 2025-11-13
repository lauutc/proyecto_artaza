-- Inicializar categorías básicas
USE lc_store;

INSERT IGNORE INTO categories (slug, name) VALUES
('remeras', 'Remeras'),
('buzos-hoodies', 'Buzos / Hoodies'),
('accesorios', 'Accesorios'),
('jeans', 'Jeans'),
('cargos', 'Cargos'),
('bermudas', 'Bermudas');

