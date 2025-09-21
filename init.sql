-- EPSS Self-Assessment (AdminLTE 3.2 + i18n + Import + Approvals)

CREATE DATABASE IF NOT EXISTS epss CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE epss;

-- Drop in reverse dependency order
DROP TABLE IF EXISTS questionnaire_response_item;
DROP TABLE IF EXISTS questionnaire_response;
DROP TABLE IF EXISTS questionnaire_item;
DROP TABLE IF EXISTS questionnaire;
DROP TABLE IF EXISTS users;

-- Users
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','supervisor','staff') NOT NULL DEFAULT 'staff',
  full_name VARCHAR(255),
  email VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Questionnaires
CREATE TABLE questionnaire (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Questionnaire items
CREATE TABLE questionnaire_item (
  id INT AUTO_INCREMENT PRIMARY KEY,
  questionnaire_id INT NOT NULL,
  linkId VARCHAR(50) NOT NULL,
  text TEXT NOT NULL,
  type ENUM('text','textarea','boolean') NOT NULL DEFAULT 'text',
  FOREIGN KEY (questionnaire_id) REFERENCES questionnaire(id) ON DELETE CASCADE
);

-- Responses
CREATE TABLE questionnaire_response (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  questionnaire_id INT NOT NULL,
  status ENUM('submitted','approved','rejected') NOT NULL DEFAULT 'submitted',
  reviewed_by INT NULL,
  reviewed_at TIMESTAMP NULL,
  review_comment TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (questionnaire_id) REFERENCES questionnaire(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Response items
CREATE TABLE questionnaire_response_item (
  id INT AUTO_INCREMENT PRIMARY KEY,
  response_id INT NOT NULL,
  linkId VARCHAR(50) NOT NULL,
  answer TEXT,
  FOREIGN KEY (response_id) REFERENCES questionnaire_response(id) ON DELETE CASCADE
);

-- Seed accounts
INSERT INTO users (username,password,role,full_name,email) VALUES
('admin', '$2y$10$examplehashforadmin', 'admin', 'Administrator', 'admin@epss.systemsdelight.com'),
('super', '$2y$10$examplehashforsuper', 'supervisor', 'Supervisor', 'supervisor@epss.systemsdelight.com');

-- Seed questionnaire
INSERT INTO questionnaire (title, description) VALUES
('Monthly Stock Assessment','Check pharmaceutical stock availability across hubs.');

INSERT INTO questionnaire_item (questionnaire_id,linkId,text,type) VALUES
(1,'q1','Do you have stock-outs?','boolean'),
(1,'q2','Which medicines are in shortage?','textarea'),
(1,'q3','Date of last order?','text');
