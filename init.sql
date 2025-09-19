-- init.sql (EPSS Self-Assessment with i18n)
CREATE DATABASE IF NOT EXISTS epss CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE epss;

-- Users
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  full_name VARCHAR(255),
  email VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO users (username, password, role, full_name, email) VALUES
('admin', '$2b$12$oy82lhiZRQVdjCPMk7H7yuAcYugiB.ondv8aF2PcR2TmV7ZRly4aK', 'admin', 'Administrator', 'admin@epss.systemsdelight.com');

-- Questionnaire
DROP TABLE IF EXISTS questionnaire_item;
DROP TABLE IF EXISTS questionnaire_response_item;
DROP TABLE IF EXISTS questionnaire_response;
DROP TABLE IF EXISTS questionnaire;

CREATE TABLE questionnaire (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
INSERT INTO questionnaire (title, description) VALUES
('Monthly Stock Assessment', 'Check pharmaceutical stock availability across hubs.');

-- Questionnaire items
CREATE TABLE questionnaire_item (
  id INT AUTO_INCREMENT PRIMARY KEY,
  questionnaire_id INT NOT NULL,
  linkId VARCHAR(50) NOT NULL,
  text TEXT NOT NULL,
  type ENUM('text','textarea','boolean') NOT NULL DEFAULT 'text',
  FOREIGN KEY (questionnaire_id) REFERENCES questionnaire(id) ON DELETE CASCADE
);
INSERT INTO questionnaire_item (questionnaire_id, linkId, text, type) VALUES
(1, 'q1', 'Do you have stock-outs?', 'boolean'),
(1, 'q2', 'Which medicines are in shortage?', 'textarea'),
(1, 'q3', 'Date of last order?', 'text');

-- Responses
CREATE TABLE questionnaire_response (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  questionnaire_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (questionnaire_id) REFERENCES questionnaire(id) ON DELETE CASCADE
);

CREATE TABLE questionnaire_response_item (
  id INT AUTO_INCREMENT PRIMARY KEY,
  response_id INT NOT NULL,
  linkId VARCHAR(50) NOT NULL,
  answer TEXT,
  FOREIGN KEY (response_id) REFERENCES questionnaire_response(id) ON DELETE CASCADE
);
