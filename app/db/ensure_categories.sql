-- ==========================================================
-- Ensure required categories exist with fixed ids
-- Mapping: 1=Cameras, 2=Drones, 3=Accessories
-- Run this BEFORE update_product_categories.sql to avoid FK errors
-- ==========================================================

START TRANSACTION;

INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
  (1, 'Cameras', 'cameras', 'Professional Cameras')
ON DUPLICATE KEY UPDATE
  `name`=VALUES(`name`),
  `slug`=VALUES(`slug`),
  `description`=VALUES(`description`);

INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
  (2, 'Drones', 'drones', 'Aerial Photography')
ON DUPLICATE KEY UPDATE
  `name`=VALUES(`name`),
  `slug`=VALUES(`slug`),
  `description`=VALUES(`description`);

INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
  (3, 'Accessories', 'accessories', 'Camera Accessories')
ON DUPLICATE KEY UPDATE
  `name`=VALUES(`name`),
  `slug`=VALUES(`slug`),
  `description`=VALUES(`description`);

COMMIT;
