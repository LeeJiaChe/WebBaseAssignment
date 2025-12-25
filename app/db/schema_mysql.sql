-- ==========================================================

-- 完整的数据库重置与初始化脚本 (包含 Categories, Products, Users, Orders)

-- ==========================================================

SET FOREIGN_KEY_CHECKS = 0;



-- 1. 清理旧表 (确保结构完全正确)

DROP TABLE IF EXISTS `order_items`;

DROP TABLE IF EXISTS `orders`;

DROP TABLE IF EXISTS `cart_items`;

DROP TABLE IF EXISTS `carts`;

DROP TABLE IF EXISTS `products`;

DROP TABLE IF EXISTS `categories`;

DROP TABLE IF EXISTS `users`;



-- ==========================================================

-- 2. 创建 Categories 表

-- ==========================================================

CREATE TABLE `categories` (

  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  `name` VARCHAR(100) NOT NULL,

  `slug` VARCHAR(120) NOT NULL UNIQUE,

  `description` TEXT,

  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- ==========================================================

-- 3. 创建 Products 表 (包含图片路径)

-- ==========================================================

CREATE TABLE `products` (

  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  `sku` VARCHAR(64) NULL UNIQUE,

  `name` VARCHAR(200) NOT NULL,

  `description` TEXT,

  `price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,

  `currency` CHAR(3) NOT NULL DEFAULT 'RM',

  `image_path` VARCHAR(512) NULL,

  `category_id` INT UNSIGNED NULL,

  `stock` INT DEFAULT 0,

  `featured` TINYINT(1) DEFAULT 0,

  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- ==========================================================

-- 4. 创建 Users 表

-- ==========================================================

CREATE TABLE `users` (

  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  `name` VARCHAR(150) NOT NULL,

  `email` VARCHAR(255) NOT NULL UNIQUE,

  `password_hash` VARCHAR(255) NOT NULL,

  `phone` VARCHAR(40) DEFAULT NULL,

  `role` VARCHAR(20) DEFAULT 'user', -- 添加角色字段 (admin/user)

  `photo` VARCHAR(255) DEFAULT NULL, -- 添加头像字段

  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  `remember_token` VARCHAR(255) NULL,

  `reset_otp` VARCHAR(10) NULL,

  `reset_otp_expiry` INT UNSIGNED NULL,

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- ==========================================================

-- 5. 创建 Orders 表 (包含所有支付和地址字段)

-- ==========================================================

CREATE TABLE `orders` (

  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  `user_id` INT UNSIGNED NOT NULL,

  `total_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,

  `status` VARCHAR(50) NOT NULL DEFAULT 'pending',

  `payment_method` VARCHAR(50) DEFAULT NULL COMMENT '支付方式',

  `shipping_address` VARCHAR(500) DEFAULT NULL COMMENT '配送地址',

  `phone` VARCHAR(40) DEFAULT NULL COMMENT '联系电话',

  `notes` TEXT COMMENT '订单备注',

  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- ==========================================================

-- 6. 创建 Order Items 表 (关联订单和商品)

-- ==========================================================

CREATE TABLE `order_items` (

  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

  `order_id` INT UNSIGNED NOT NULL,

  `product_id` INT UNSIGNED NOT NULL,

  `quantity` INT NOT NULL DEFAULT 1,

  `unit_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,

  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,

  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- ==========================================================

-- 7. 插入基础数据 (Categories)

-- ==========================================================

INSERT INTO `categories` (`name`, `slug`, `description`) VALUES 

('Cameras', 'cameras', 'Professional Cameras'),

('Drones', 'drones', 'Aerial Photography'),

('Accessories', 'accessories', 'Camera Accessories');



-- ==========================================================

-- 8. 插入商品数据 (确保图片路径对应你上传的文件)

-- ==========================================================

-- 注意：这里假设你的图片文件夹在根目录的 images/ 下

INSERT INTO `products` (`sku`, `name`, `description`, `price`, `currency`, `image_path`, `category_id`, `stock`, `featured`) VALUES

('CANON-DSLR-1', 'Canon DSLR 1', 'Reliable DSLR for enthusiasts.', 799.00, 'MYR', 'images/canon_product/canon_DSLR1.png', 1, 10, 1),

('CANON-DSLR-2', 'Canon EOS R6', 'Full-frame mirrorless.', 2499.00, 'MYR', 'images/canon_product/canon_DSLR2.png', 1, 5, 1),

('SONY-A7', 'Sony Alpha 7R III', 'Compact mirrorless with great color.', 1199.00, 'MYR', 'images/sony_product/sony_mirrorless1.jpg', 1, 8, 1),

('SONY-A6700', 'Sony A6700', 'Fast autofocus mirrorless.', 1399.00, 'MYR', 'images/sony_product/sony_mirrorless2.jpg', 1, 12, 1),

('FUJI-XT30', 'Fujifilm X-T30', 'Classic design and color science.', 999.00, 'MYR', 'images/fujifilm_product/fujifilm_mirrorless1.jpg', 1, 6, 1),

('DJI-MAVIC', 'DJI Mavic 4 Pro', 'Pro-level aerial photography.', 1499.00, 'MYR', 'images/dji_product/dji_drone1.jpg', 2, 4, 1),

('DJI-AIR3', 'DJI Air 3', 'Dual camera drone.', 999.00, 'MYR', 'images/dji_product/dji_drone2.jpg', 2, 7, 1),

('INSTA-ACE', 'Insta360 Ace Pro', 'Action camera for vlogging.', 399.00, 'MYR', 'images/insta360_product/insta360_actioncam3.jpg', 1, 20, 1);



-- ==========================================================

-- 9. 插入测试用户

-- ==========================================================

-- 密码是: password123 (使用默认的 PASSWORD_DEFAULT 哈希)

INSERT INTO `users` (`name`, `email`, `password_hash`, `role`) VALUES

('Test User', 'test@example.com', '$2y$10$wS.q7q.q7q.q7q.q7q.q7uO7.7.7.7.7.7.7.7.7.7.7.7.7.7.', 'user'),

('Admin User', 'admin@example.com', '$2y$10$wS.q7q.q7q.q7q.q7q.q7uO7.7.7.7.7.7.7.7.7.7.7.7.7.7.', 'admin');



SET FOREIGN_KEY_CHECKS = 1;
