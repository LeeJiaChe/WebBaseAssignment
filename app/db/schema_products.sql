-- schema_products.sql
-- Run this script to create a minimal `products` table used by the admin product CRUD pages.

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `sku` VARCHAR(64) NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
  `currency` VARCHAR(8) NOT NULL DEFAULT 'USD',
  `image_path` VARCHAR(255) DEFAULT NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ux_products_sku` (`sku`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example seed data (optional)
INSERT INTO `products` (sku, name, description, price, currency, image_path, featured, created_at) VALUES
('CANON-R5','Canon EOS R5','Full-frame mirrorless camera',3899.00,'USD',NULL,1,NOW()),
('SONY-A7','Sony A7 III','Versatile mirrorless camera',1999.00,'USD',NULL,1,NOW());
