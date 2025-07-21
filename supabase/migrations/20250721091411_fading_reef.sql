-- Complete CourierPro Database Schema
-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS courier_management;
CREATE DATABASE courier_management;
USE courier_management;

-- Users table (for admin, agents, customers)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'agent', 'customer') NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    branch_id INT DEFAULT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Branches table
CREATE TABLE branches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    phone VARCHAR(20),
    manager_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Couriers table
CREATE TABLE couriers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tracking_number VARCHAR(50) UNIQUE NOT NULL,
    sender_name VARCHAR(100) NOT NULL,
    sender_contact VARCHAR(20) NOT NULL,
    sender_address TEXT NOT NULL,
    receiver_name VARCHAR(100) NOT NULL,
    receiver_contact VARCHAR(20) NOT NULL,
    receiver_city VARCHAR(50) NOT NULL,
    receiver_address TEXT NOT NULL,
    status ENUM('pending', 'picked-up', 'in-transit', 'delivered', 'cancelled') DEFAULT 'pending',
    weight DECIMAL(10,2) DEFAULT 0,
    dimensions VARCHAR(100),
    delivery_fee DECIMAL(10,2) DEFAULT 0,
    estimated_delivery DATE,
    actual_delivery TIMESTAMP NULL,
    notes TEXT,
    created_by INT NOT NULL,
    assigned_agent INT,
    branch_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (assigned_agent) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE SET NULL
);

-- Courier status history
CREATE TABLE courier_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    courier_id INT NOT NULL,
    status ENUM('pending', 'picked-up', 'in-transit', 'delivered', 'cancelled') NOT NULL,
    location VARCHAR(100),
    notes TEXT,
    updated_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (courier_id) REFERENCES couriers(id) ON DELETE CASCADE,
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- System settings
CREATE TABLE settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    description TEXT,
    updated_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (updated_by) REFERENCES users(id)
);

-- Contact messages
CREATE TABLE contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default branches
INSERT INTO branches (name, address, city, phone) VALUES
('Main Branch', '123 Business Street', 'New York', '+1-555-0100'),
('Downtown Branch', '456 Commerce Ave', 'Los Angeles', '+1-555-0101'),
('Uptown Branch', '789 Trade Center', 'Chicago', '+1-555-0102');

-- Insert default users (password is 'password123' for all)
INSERT INTO users (name, email, password, role, phone, branch_id, status) VALUES
('System Administrator', 'admin@courierpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '+1-555-0100', NULL, 'active'),
('John Agent', 'agent@courierpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', '+1-555-0101', 1, 'active'),
('Mike Agent', 'agent2@courierpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', '+1-555-0102', 2, 'active'),
('Jane Customer', 'customer@courierpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '+1-555-0103', NULL, 'active'),
('Bob Customer', 'customer2@courierpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '+1-555-0104', NULL, 'active');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('company_name', 'CourierPro Ltd.', 'Company name'),
('company_email', 'contact@courierpro.com', 'Company email'),
('company_phone', '+1-555-0100', 'Company phone'),
('company_address', '123 Business Street, Corporate City, State 12345', 'Company address'),
('default_delivery_time', '48', 'Default delivery time in hours'),
('max_delivery_time', '168', 'Maximum delivery time in hours'),
('base_delivery_fee', '10.00', 'Base delivery fee'),
('per_km_rate', '1.50', 'Per kilometer rate');

-- Sample courier data
INSERT INTO couriers (tracking_number, sender_name, sender_contact, sender_address, receiver_name, receiver_contact, receiver_city, receiver_address, status, created_by, assigned_agent, branch_id, delivery_fee) VALUES
('CP20241201001', 'John Doe', '+1-555-1001', '123 Main St, City A', 'Jane Smith', '+1-555-1002', 'New York', '456 Oak Ave, New York', 'in-transit', 1, 2, 1, 25.50),
('CP20241201002', 'Alice Johnson', '+1-555-1003', '789 Pine St, City B', 'Bob Wilson', '+1-555-1004', 'Los Angeles', '321 Elm St, Los Angeles', 'delivered', 1, 2, 1, 32.75),
('CP20241201003', 'Mike Brown', '+1-555-1005', '654 Cedar Ave, City C', 'Sarah Davis', '+1-555-1006', 'Chicago', '987 Maple Dr, Chicago', 'pending', 1, 3, 2, 18.25),
('CP20241201004', 'Emma Wilson', '+1-555-1007', '321 Birch St, City D', 'David Lee', '+1-555-1008', 'Houston', '654 Pine Ave, Houston', 'picked-up', 1, 2, 1, 28.00),
('CP20241201005', 'Chris Taylor', '+1-555-1009', '987 Maple Ave, City E', 'Lisa Garcia', '+1-555-1010', 'Phoenix', '123 Oak Dr, Phoenix', 'cancelled', 1, 3, 2, 22.50);

-- Insert status history for sample couriers
INSERT INTO courier_status_history (courier_id, status, location, notes, updated_by) VALUES
(1, 'pending', 'Origin', 'Package received', 1),
(1, 'picked-up', 'Pickup Location', 'Package picked up by agent', 2),
(1, 'in-transit', 'Distribution Center', 'Package in transit', 2),
(2, 'pending', 'Origin', 'Package received', 1),
(2, 'picked-up', 'Pickup Location', 'Package picked up', 2),
(2, 'in-transit', 'Distribution Center', 'Package in transit', 2),
(2, 'delivered', 'Destination', 'Package delivered successfully', 2),
(3, 'pending', 'Origin', 'Package received and ready for pickup', 1),
(4, 'pending', 'Origin', 'Package received', 1),
(4, 'picked-up', 'Pickup Location', 'Package collected by agent', 2),
(5, 'pending', 'Origin', 'Package received', 1),
(5, 'cancelled', 'Origin', 'Cancelled by customer request', 1);