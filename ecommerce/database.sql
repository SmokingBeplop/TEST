CREATE DATABASE IF NOT EXISTS shopnest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE shopnest;

DROP TABLE IF EXISTS products;
CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  icon VARCHAR(8) NOT NULL DEFAULT '📦',
  badge VARCHAR(32) DEFAULT NULL,
  category VARCHAR(64) NOT NULL,
  name VARCHAR(255) NOT NULL,
  description TEXT DEFAULT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  original_price DECIMAL(10,2) DEFAULT NULL,
  reviews VARCHAR(64) DEFAULT NULL,
  in_stock TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO products (icon,badge,category,name,description,price,original_price,reviews,in_stock) VALUES
('📱','Sale','Electronics','Smartphone Pro X12','Premium 6.7" phone with fast performance.','12999.00','16500.00','⭐⭐⭐⭐⭐ (142 reviews)',1),
('👟','', 'Footwear','AirRun Classic Sneakers','Lightweight sneakers built for everyday comfort.','2499.00',NULL,'⭐⭐⭐⭐☆ (87 reviews)',1),
('💻','New','Electronics','UltraBook Slim 15"','Ultra-thin laptop with long battery life.','34500.00','38000.00','⭐⭐⭐⭐⭐ (56 reviews)',1),
('⌚','', 'Accessories','SmartWatch Series 7','Track workouts and notifications on the go.','4299.00',NULL,'⭐⭐⭐⭐☆ (203 reviews)',1),
('🎧','Hot','Electronics','Wireless Earbuds Pro V3','Noise-isolating earbuds with crystal-clear audio.','1899.00','2499.00','⭐⭐⭐⭐⭐ (319 reviews)',1),
('👜','', 'Fashion','Genuine Leather Handbag','Crafted leather handbag with premium details.','3750.00',NULL,'⭐⭐⭐⭐☆ (74 reviews)',1),
('🖱️','', 'Electronics','Gaming Mouse RGB 16000dpi','Precision mouse with customizable RGB lighting.','1250.00',NULL,'⭐⭐⭐⭐⭐ (198 reviews)',1),
('🌿','', 'Beauty','Organic Skincare Set','Natural skincare essentials for glowing skin.','899.00',NULL,'⭐⭐⭐⭐☆ (45 reviews)',1);

DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  phone VARCHAR(32) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer', 'seller') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (name, email, password, role) VALUES
('Juan Dela Cruz', 'juan@email.com', '$2y$10$dummyhash', 'customer'),
('Seller Account', 'seller@shopnest.com', '$2y$10$dummyhash', 'seller');
