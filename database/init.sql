-- Drop database if exists and create fresh
DROP DATABASE IF EXISTS helpdesk;
CREATE DATABASE helpdesk CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE helpdesk;

-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tickets table
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    assigned_to INT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    status ENUM('naujas', 'vykdomas', 'laukiama', 'uždarytas') DEFAULT 'naujas',
    priority ENUM('žemas', 'vidutinis', 'aukštas') DEFAULT 'vidutinis',
    rating INT NULL CHECK (rating BETWEEN 1 AND 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    closed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_assigned (assigned_to),
    INDEX idx_status (status),
    INDEX idx_priority (priority)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Comments table
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    is_internal BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_ticket (ticket_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert test users (password for all: password123)
-- Hash generated with: password_hash('password123', PASSWORD_DEFAULT)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@helpdesk.lt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sistemos Administratorius', 'admin'),
('darbuotojas1', 'darbuotojas1@helpdesk.lt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jonas Jonaitis', 'staff'),
('darbuotojas2', 'darbuotojas2@helpdesk.lt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Petras Petraitis', 'staff'),
('vartotojas1', 'vartotojas1@helpdesk.lt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Ona Onaitė', 'user'),
('vartotojas2', 'vartotojas2@helpdesk.lt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Antanas Antanaitis', 'user');

-- Insert test tickets
INSERT INTO tickets (user_id, assigned_to, title, description, status, priority) VALUES
(4, 2, 'Nepavyksta prisijungti prie sistemos', 'Bandau prisijungti, bet gaunu klaidos pranešimą "Invalid credentials"', 'vykdomas', 'aukštas'),
(4, 2, 'Slaptažodžio keitimo problema', 'Noriu pakeisti slaptažodį, bet negaunu el. laiško su nuoroda', 'naujas', 'vidutinis'),
(5, 3, 'Klausimas dėl sąskaitos faktūros', 'Kaip gauti sąskaitą faktūrą už šio mėnesio paslaugas?', 'laukiama', 'žemas'),
(5, NULL, 'Prašymas pridėti naują funkciją', 'Ar galėtumėte pridėti galimybę eksportuoti ataskaitas PDF formatu?', 'naujas', 'žemas');

-- Insert test comments
INSERT INTO comments (ticket_id, user_id, comment, is_internal) VALUES
(1, 2, 'Gavau jūsų užklausą. Tikrinama problema su prisijungimo sistema.', FALSE),
(1, 4, 'Dėkoju už greitą atsakymą!', FALSE),
(1, 2, 'Problema išspręsta. Buvo serverio konfigūracijos klaida. Prašau pabandyti dabar.', FALSE),
(3, 3, 'Laukiame papildomos informacijos iš finansų skyriaus dėl sąskaitų generavimo.', FALSE),
(3, 5, 'Supratau, palaukiu atsakymo. Dėkoju!', FALSE);

-- Update ticket #1 to closed with rating
UPDATE tickets SET status = 'uždarytas', rating = 5, closed_at = NOW() WHERE id = 1;