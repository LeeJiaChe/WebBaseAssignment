-- ==========================================================
-- Update product categories + currency alignment
-- Mapping: Cameras=1, Drones=2, Accessories=3
-- Run after initial inserts to normalize existing data
-- ==========================================================

-- Accessories → 3
UPDATE products
SET category_id = 3
WHERE sku LIKE 'ACC-%'
   OR image_path LIKE 'images/accessories/%';

-- Drones (DJI) → 2
UPDATE products
SET category_id = 2
WHERE name LIKE 'DJI%'
   OR image_path LIKE 'images/dji_product/%';

-- Cameras (Canon, Sony, Fujifilm, Insta360) → 1
UPDATE products
SET category_id = 1
WHERE name LIKE 'Canon%'
   OR name LIKE 'Sony%'
   OR name LIKE 'Fujifilm%'
   OR name LIKE 'Insta360%'
   OR image_path LIKE 'images/canon_product/%'
   OR image_path LIKE 'images/sony_product/%'
   OR image_path LIKE 'images/fujifilm_product/%'
   OR image_path LIKE 'images/insta360_product/%';

-- Currency alignment to RM (if any rows still use other codes)
UPDATE products
SET currency = 'RM'
WHERE currency <> 'RM';

-- Optional: set featured flag for key items (tweak as you like)
-- UPDATE products SET featured = 1 WHERE name LIKE 'Canon EOS %' OR name LIKE 'DJI %';
