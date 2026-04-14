-- ============================================
-- BANGTILENYDOOR ACADEMY - DATABASE SETUP
-- Run this in phpMyAdmin or MySQL command line
-- ============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS bangtilenydoor_db;
USE bangtilenydoor_db;

-- ============================================
-- 1. STUDENT INQUIRIES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS student_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(50),
    country VARCHAR(100),
    education_level VARCHAR(100),
    interest_type ENUM('fully_funded', 'partial', 'online_degree', 'distance') DEFAULT 'fully_funded',
    message TEXT,
    referral_source VARCHAR(100),
    status ENUM('new', 'contacted', 'processing', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
);

-- ============================================
-- 2. NEWSLETTER SUBSCRIBERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(120) UNIQUE NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email)
);

-- ============================================
-- 3. CONTACT MESSAGES TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(50),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_read (is_read)
);

-- ============================================
-- 4. ADMIN USERS TABLE
-- ============================================
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(120),
    full_name VARCHAR(100),
    role ENUM('admin', 'editor', 'viewer') DEFAULT 'viewer',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- 5. SCHOLARSHIPS TABLE (for future use)
-- ============================================
CREATE TABLE IF NOT EXISTS scholarships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(100),
    university VARCHAR(255),
    type ENUM('full', 'partial') DEFAULT 'full',
    deadline DATE,
    amount VARCHAR(100),
    description TEXT,
    eligibility TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- INSERT DEFAULT ADMIN USER
-- Default password is: admin123 (change after first login)
-- ============================================
INSERT INTO admin_users (username, password, full_name, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin');

-- ============================================
-- INSERT SAMPLE SCHOLARSHIPS (Optional)
-- ============================================
INSERT INTO scholarships (name, country, university, type, deadline, amount) VALUES
('Fulbright Foreign Student Program', 'USA', 'Various Universities', 'full', '2025-10-15', 'Full Tuition + Stipend'),
('DAAD Scholarships', 'Germany', 'German Universities', 'full', '2025-09-30', '€10,000+ per year'),
('Chevening Scholarships', 'UK', 'UK Universities', 'full', '2025-11-05', 'Full Tuition + Living'),
('Erasmus Mundus', 'Europe', 'European Consortium', 'full', '2026-01-15', '€15,000+ per year'),
('Commonwealth Scholarships', 'UK', 'UK Universities', 'full', '2025-12-01', 'Full Tuition');

-- ============================================
-- VERIFY TABLES CREATED
-- ============================================
SHOW TABLES;