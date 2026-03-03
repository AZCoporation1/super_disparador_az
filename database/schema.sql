-- ============================================
-- Super Disparador AZ - Database Schema
-- MySQL
-- ============================================

CREATE DATABASE IF NOT EXISTS super_disparador
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE super_disparador;

-- Users (SaaS multi-tenant)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    evolution_instance VARCHAR(255) DEFAULT NULL,
    evolution_base_url VARCHAR(500) DEFAULT NULL,
    evolution_instance_name VARCHAR(255) DEFAULT NULL,
    evolution_token TEXT DEFAULT NULL,
    evolution_connection_status ENUM('active','inactive','unconfigured') DEFAULT 'unconfigured',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contacts (user-scoped)
CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(255) DEFAULT NULL,
    whatsapp VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_whatsapp (user_id, whatsapp)
) ENGINE=InnoDB;

-- Tags / Categories (user-scoped)
CREATE TABLE IF NOT EXISTS tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(7) DEFAULT '#6366f1',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_tag_per_user (user_id, name)
) ENGINE=InnoDB;

-- Pivot: Contact <-> Tag
CREATE TABLE IF NOT EXISTS contact_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    contact_id INT NOT NULL,
    tag_id INT NOT NULL,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE,
    UNIQUE KEY unique_contact_tag (contact_id, tag_id)
) ENGINE=InnoDB;

-- Message Templates
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) DEFAULT 'Sem título',
    template_body TEXT NOT NULL,
    ai_enabled TINYINT(1) DEFAULT 0,
    ai_prompt TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Dispatch Logs (audit trail)
CREATE TABLE IF NOT EXISTS dispatch_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    contact_id INT NOT NULL,
    message_id INT DEFAULT NULL,
    original_message TEXT DEFAULT NULL,
    sent_message TEXT DEFAULT NULL,
    status ENUM('pending','sending','sent','failed') DEFAULT 'pending',
    error_message TEXT DEFAULT NULL,
    sent_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE,
    FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Insert a default admin user (password: admin123)
-- password_hash generated with PHP password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (name, email, password_hash) VALUES
('Admin', 'admin@disparador.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
