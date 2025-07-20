-- CourierPro Database Schema
-- MySQL Database for Courier Management System

CREATE DATABASE IF NOT EXISTS courier_management;
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
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Couriers table
CREATE TABLE couriers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tracking_number VARCHAR(50) UNIQUE NOT NULL,
    sender_id INT,
    sender_name VARCHAR(100) NOT NULL,
    sender_contact VARCHAR(20) NOT NULL,
    sender_address TEXT NOT NULL,
    receiver_id INT,
    receiver_name VARCHAR(100) NOT NULL,
    receiver_contact VARCHAR(20) NOT NULL,
    receiver_city VARCHAR(50) NOT NULL,
    receiver_address TEXT NOT NULL,
    status ENUM('pending', 'picked-up', 'in-transit', 'delivered', 'cancelled') DEFAULT 'pending',
    weight DECIMAL(10,2),
    dimensions VARCHAR(100),
    delivery_fee DECIMAL(10,2),
    estimated_delivery DATE,
    actual_delivery TIMESTAMP NULL,
    notes TEXT,
    created_by INT NOT NULL,
    assigned_agent INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (assigned_agent) REFERENCES users(id) ON DELETE SET NULL
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

-- Agent assignments
CREATE TABLE agent_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    agent_id INT NOT NULL,
    branch_id INT NOT NULL,
    assigned_date DATE NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (agent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(id) ON DELETE CASCADE
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

-- Insert default admin user
INSERT INTO users (name, email, password, role, phone, status) VALUES
('System Administrator', 'admin@courierpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '+1-555-0100', 'active'),
('John Agent', 'agent@courierpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'agent', '+1-555-0101', 'active'),
('Jane Customer', 'customer@courierpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'customer', '+1-555-0102', 'active');

-- Insert default branch
INSERT INTO branches (name, address, city, phone) VALUES
('Main Branch', '123 Business Street', 'Corporate City', '+1-555-0100');

-- Insert default settings
INSERT INTO settings (setting_key, setting_value, description) VALUES
('company_name', 'CourierPro Ltd.', 'Company name'),
('company_email', 'contact@courierpro.com', 'Company email'),
('company_phone', '+1-555-0100', 'Company phone'),
('default_delivery_time', '48', 'Default delivery time in hours'),
('base_delivery_fee', '10.00', 'Base delivery fee'),
('per_km_rate', '1.50', 'Per kilometer rate');

-- Sample courier data
INSERT INTO couriers (tracking_number, sender_name, sender_contact, sender_address, receiver_name, receiver_contact, receiver_city, receiver_address, status, created_by) VALUES
('CP20241201001', 'John Doe', '+1-555-1001', '123 Main St, City A', 'Jane Smith', '+1-555-1002', 'New York', '456 Oak Ave, New York', 'in-transit', 1),
('CP20241201002', 'Alice Johnson', '+1-555-1003', '789 Pine St, City B', 'Bob Wilson', '+1-555-1004', 'Los Angeles', '321 Elm St, Los Angeles', 'delivered', 1),
('CP20241201003', 'Mike Brown', '+1-555-1005', '654 Cedar Ave, City C', 'Sarah Davis', '+1-555-1006', 'Chicago', '987 Maple Dr, Chicago', 'pending', 1);