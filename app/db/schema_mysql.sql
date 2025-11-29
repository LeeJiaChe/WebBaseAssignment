-- MySQL schema and seed data for WebBased app
-- Charset and engine recommended: utf8mb4 + InnoDB

-- Create database (run externally or comment if database already exists)
-- Create database (will create if it doesn't exist and select it)
CREATE DATABASE IF NOT EXISTS `webbased` CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
USE `webbased`;

SET FOREIGN_KEY_CHECKS = 0;

-- Categories
CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Products
CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sku VARCHAR(64) NULL UNIQUE,
  name VARCHAR(200) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  currency CHAR(3) NOT NULL DEFAULT 'USD',
  image_path VARCHAR(512) NULL,
  category_id INT UNSIGNED NULL,
  stock INT DEFAULT 0,
  featured TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  phone VARCHAR(40) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Carts (simple cart model; ties to user if logged in)
CREATE TABLE IF NOT EXISTS carts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  session_id VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cart items
CREATE TABLE IF NOT EXISTS cart_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cart_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders
CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NULL,
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  currency CHAR(3) NOT NULL DEFAULT 'USD',
  status VARCHAR(40) NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order items
CREATE TABLE IF NOT EXISTS order_items (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id BIGINT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  unit_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- Indexes for common queries
CREATE INDEX IF NOT EXISTS idx_products_category ON products(category_id);
CREATE INDEX IF NOT EXISTS idx_products_featured ON products(featured);
CREATE INDEX IF NOT EXISTS idx_cart_user ON carts(user_id);

-- Seed data: categories
INSERT INTO categories (name, slug, description)
VALUES
  ('Cameras', 'cameras', 'Cameras and photography gear')
ON DUPLICATE KEY UPDATE name=VALUES(name);

-- Seed data: sample products used on homepage
-- Paths assume images live under /images/<brand>_product/<file>
INSERT INTO products (sku, name, description, price, currency, image_path, category_id, stock, featured)
VALUES
  ('CANON-DSLR-001', 'Canon DSLR 1', 'Reliable DSLR for enthusiasts.', 799.00, 'USD', '/images/canon_product/canon_DSLR1.png', (SELECT id FROM categories WHERE slug='cameras' LIMIT 1), 12, 1),
  ('SONY-MIR-001', 'Sony Mirrorless', 'Compact mirrorless with great color.', 1199.00, 'USD', '/images/sony_product/sony_mirrorless1.jpg', (SELECT id FROM categories WHERE slug='cameras' LIMIT 1), 8, 1),
  ('FUJI-X-001', 'Fujifilm X', 'Beautiful color science for creators.', 999.00, 'USD', '/images/fujifilm_product/fujifilm_mirrorless1.jpg', (SELECT id FROM categories WHERE slug='cameras' LIMIT 1), 6, 1),
  ('DJI-MAVIC-001', 'DJI Mavic', 'Pro-level aerial photography.', 1499.00, 'USD', '/images/dji_product/dji_drone1.jpg', (SELECT id FROM categories WHERE slug='cameras' LIMIT 1), 5, 1),
  ('INSTA360-ONE', 'Insta360 One', 'Action camera for immersive footage.', 399.00, 'USD', '/images/insta360_product/insta360_actioncam1.jpg', (SELECT id FROM categories WHERE slug='cameras' LIMIT 1), 20, 1)
ON DUPLICATE KEY UPDATE name=VALUES(name), price=VALUES(price), image_path=VALUES(image_path), stock=VALUES(stock), featured=VALUES(featured);

-- Optional: sample user (password_hash should be created with your app's hashing method)
INSERT INTO users (name, email, password_hash)
VALUES
  ('Demo User', 'demo@example.com', '$2y$10$examplehashreplacewithreal');

-- Example queries and usage

-- 1) Get featured products for homepage
-- SELECT id, name, price, image_path, description FROM products WHERE featured = 1 ORDER BY created_at DESC LIMIT 10;

-- 2) Create a new cart (server-side)
-- INSERT INTO carts (user_id, session_id) VALUES (NULL, 'session_abc123');
-- Then use the returned cart id to add items:
-- INSERT INTO cart_items (cart_id, product_id, quantity, unit_price)
-- VALUES (1, 2, 1, (SELECT price FROM products WHERE id=2));

-- 3) View cart contents
-- SELECT ci.id, p.name, ci.quantity, ci.unit_price, (ci.quantity * ci.unit_price) as line_total
-- FROM cart_items ci
-- JOIN products p ON ci.product_id = p.id
-- WHERE ci.cart_id = 1;

-- 4) Convert cart to order (example transaction)
-- START TRANSACTION;
-- INSERT INTO orders (user_id, total_amount, currency, status)
-- SELECT 1, SUM(ci.quantity * ci.unit_price), 'USD', 'processing' FROM cart_items ci WHERE ci.cart_id = 1;
-- SET @order_id = LAST_INSERT_ID();
-- INSERT INTO order_items (order_id, product_id, quantity, unit_price)
-- SELECT @order_id, ci.product_id, ci.quantity, ci.unit_price FROM cart_items ci WHERE ci.cart_id = 1;
-- DELETE FROM cart_items WHERE cart_id = 1;
-- COMMIT;

-- 5) Decrease product stock after order (basic example)
-- UPDATE products p JOIN order_items oi ON p.id = oi.product_id
-- SET p.stock = p.stock - oi.quantity
-- WHERE oi.order_id = @order_id;

-- How to import:
-- 1) Create database: CREATE DATABASE webbased CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- 2) Import file: mysql -u root -p webbased < schema_mysql.sql

-- Notes:
-- - Replace the placeholder password hash with a real hashed password when creating users.
-- - Add more fields (addresses, shipping, tax) as needed for your checkout flow.
-- - Consider adding unique constraints and more indexes based on query patterns.

COMMIT;
