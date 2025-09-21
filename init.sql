-- EPSS Self-Assessment (Sections + Admin edits + Approvals + Import + i18n + Logs)
CREATE DATABASE IF NOT EXISTS epss CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE epss;

-- Drop in reverse dependency order
DROP TABLE IF EXISTS questionnaire_response_item;
DROP TABLE IF EXISTS questionnaire_response;
DROP TABLE IF EXISTS questionnaire_item;
DROP TABLE IF EXISTS questionnaire_section;
DROP TABLE IF EXISTS questionnaire;
DROP TABLE IF EXISTS logs;
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

-- Logs
CREATE TABLE logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  action VARCHAR(255) NOT NULL,
  meta TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Questionnaire
CREATE TABLE questionnaire (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  description TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sections
CREATE TABLE questionnaire_section (
  id INT AUTO_INCREMENT PRIMARY KEY,
  questionnaire_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  order_index INT NOT NULL DEFAULT 0,
  FOREIGN KEY (questionnaire_id) REFERENCES questionnaire(id) ON DELETE CASCADE
);

-- Items
CREATE TABLE questionnaire_item (
  id INT AUTO_INCREMENT PRIMARY KEY,
  questionnaire_id INT NOT NULL,
  section_id INT NULL,
  linkId VARCHAR(50) NOT NULL,
  text TEXT NOT NULL,
  type ENUM('text','textarea','boolean') NOT NULL DEFAULT 'text',
  order_index INT NOT NULL DEFAULT 0,
  FOREIGN KEY (questionnaire_id) REFERENCES questionnaire(id) ON DELETE CASCADE,
  FOREIGN KEY (section_id) REFERENCES questionnaire_section(id) ON DELETE SET NULL
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

-- Seeds
INSERT INTO users (username,password,role,full_name,email) VALUES
('admin','$2b$12$yubBAt0dI28zq2dWQtNahei3t6IwwN8F08pgIHwBxA5bNR9uOOCmy','admin','Administrator','admin@epss.systemsdelight.com'),
('super','$2b$12$sBH.CACR7vjQyEBBC9oaOezzUgu.0bc2thezTBU9miHgQGmhIfBw.','supervisor','Supervisor','supervisor@epss.systemsdelight.com');

INSERT INTO questionnaire (title, description) VALUES
('Monthly Stock Assessment','Check pharmaceutical stock availability across hubs.');

INSERT INTO questionnaire_section (questionnaire_id, title, description, order_index) VALUES
(1,'Availability','Basic availability checks', 1),
(1,'Procurement','Ordering and shortages', 2);

INSERT INTO questionnaire_item (questionnaire_id, section_id, linkId, text, type, order_index) VALUES
(1,1,'q1','Do you have stock-outs?','boolean',1),
(1,2,'q2','Which medicines are in shortage?','textarea',1),
(1,2,'q3','Date of last order?','text',2);
