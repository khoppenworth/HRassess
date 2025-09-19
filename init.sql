-- Minimal schema for HRassess

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') NOT NULL DEFAULT 'staff',
  full_name VARCHAR(150),
  email VARCHAR(150),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- default admin (username: admin, password: admin123) - change immediately!
INSERT INTO users (username, password, role, full_name, email)
VALUES ('admin', '$2y$10$0ZkV4cHjxwFh3R2s7cV1zO8s4uTz0d8h2c1nKq1Xy3JwzJtQh3mQK', 'admin', 'Default Admin', 'admin@example.com')
ON DUPLICATE KEY UPDATE username=username;

CREATE TABLE IF NOT EXISTS questionnaires (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  xml_id VARCHAR(255),
  version VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS questionnaire_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  questionnaire_id INT NOT NULL,
  link_id VARCHAR(255),
  question_text TEXT NOT NULL,
  type VARCHAR(50) NOT NULL,
  FOREIGN KEY (questionnaire_id) REFERENCES questionnaires(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS questionnaire_options (
  id INT AUTO_INCREMENT PRIMARY KEY,
  item_id INT NOT NULL,
  option_text TEXT NOT NULL,
  is_correct TINYINT(1) DEFAULT 0,
  FOREIGN KEY (item_id) REFERENCES questionnaire_items(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS responses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  questionnaire_id INT,
  payload JSON,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  FOREIGN KEY (questionnaire_id) REFERENCES questionnaires(id) ON DELETE SET NULL
);
