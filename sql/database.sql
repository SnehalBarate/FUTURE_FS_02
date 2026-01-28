CREATE DATABASE IF NOT EXISTS mini_store;
USE mini_store;

CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255)
);

INSERT INTO products (name, price, image) VALUES 
('Modern Watch', 1500.00, 'https://via.placeholder.com/150'),
('Wireless Buds', 2000.00, 'https://via.placeholder.com/150'),
('Leather Bag', 2500.00, 'https://via.placeholder.com/150'),
('Smart Phone', 15000.00, 'https://via.placeholder.com/150');