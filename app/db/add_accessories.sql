-- ==========================================================
-- Add Accessories Products (uses image files in images/accessories)
-- Uses explicit category_id mapping: Cameras=1, Drones=2, Accessories=3
-- Currency: RM
-- ==========================================================

INSERT INTO `products` (`sku`, `name`, `description`, `price`, `currency`, `image_path`, `category_id`, `stock`, `featured`) VALUES

('ACC-BG-R20', 'Battery Grip BG-R20', 'Battery grip for extended shooting', 1699.00, 'RM', 'images/accessories/Battery Grip BG-R20 Price 1699.png', 3, 10, 0),
('ACC-LP-E6P', 'Battery Pack LP-E6P', 'Rechargeable battery pack', 529.00, 'RM', 'images/accessories/Battery Pack LP-E6P Price 529.png', 3, 25, 0),
('ACC-EF100-2_8L-MACRO-IS', 'EF100mm f/2.8L Macro IS USM', 'Telephoto macro lens', 4999.00, 'RM', 'images/accessories/EF100mm f 2.8L MARCO IS USM price 4999.png', 3, 6, 0),
('ACC-EF24-105-4L-IS-II', 'EF24-105mm f/4L IS II USM', 'Versatile standard zoom lens', 6399.00, 'RM', 'images/accessories/EF24-105mm f 4L IS II USM price 6399.jpg', 3, 8, 0),
('ACC-EF24-70-2_8L-II', 'EF24-70mm f/2.8L II USM', 'Professional standard zoom lens', 9299.00, 'RM', 'images/accessories/EF24-70mm f 2.8L II USM Price is 9299.jpg', 3, 7, 0),
('ACC-EF50-1_8-STM', 'EF50mm f/1.8 STM', 'Compact prime lens', 599.00, 'RM', 'images/accessories/EF50mm f 1.8 STM Price is 599.jpg', 3, 20, 0),
('ACC-EF70-200-2_8L-IS-III', 'EF70-200mm f/2.8L IS III USM', 'Pro telephoto zoom with IS', 10399.00, 'RM', 'images/accessories/EF70-200mm f 2.8L IS III USM Price is 10399.jpg', 3, 5, 0),
('ACC-RF-EXT-2X', 'EXTENDER RF2.0X', 'RF mount 2x extender', 2899.00, 'RM', 'images/accessories/EXTENDER RF2.0X price is 2899.jpg', 3, 9, 0),
('ACC-RFS-3_9-3_5-DUAL-FISHEYE', 'RF-S 3.9mm f/3.5 STM Dual Fisheye', 'Dual fisheye lens for VR capture', 5659.00, 'RM', 'images/accessories/RF-S3.9mm f 3.5 STM Dual Fisheye Price 5659.jpg', 3, 4, 0),
('ACC-RFS-7_8-4-DUAL', 'RF-S 7.8mm f/4 STM Dual', 'Ultra-wide dual lens', 0.00, 'RM', 'images/accessories/RF-S7.8mm f 4 STM Dual.jpg', 3, 0, 0),
('ACC-RF45-1_2-STM', 'RF 45mm f/1.2 STM', 'Fast prime lens', 1879.00, 'RM', 'images/accessories/RF45mm f 1.2STM Price is 1879.jpg', 3, 12, 0),
('ACC-RF75-300-4-5_6', 'RF 75-300mm f/4-5.6', 'Telephoto zoom lens', 969.00, 'RM', 'images/accessories/RF75-300mm F4-5.6 Price is 969.jpg', 3, 10, 0),
('ACC-ER-SC3', 'Shoe Cover ER-SC3', 'Protective shoe cover', 69.00, 'RM', 'images/accessories/Shoe Cover ER-SC3 Price 69.png', 3, 50, 0),
('ACC-EL-5', 'Speedlite EL-5', 'High-performance speedlite', 1999.00, 'RM', 'images/accessories/Speedlite EL-5 Price 1999.png', 3, 14, 0),
('ACC-HG-100TBR', 'Tripod Grip HG-100TBR', 'Compact tripod grip', 499.00, 'RM', 'images/accessories/Tripod Grip HG-100TBR Price 499.png', 3, 18, 0);

-- Note: RF-S 7.8mm entry has price 0.00 as the filename did not include a price.
-- Update it later via:
--   UPDATE products SET price = <RM_value> WHERE sku = 'ACC-RFS-7_8-4-DUAL';
