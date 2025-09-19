
-- Users table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL
);

INSERT INTO users (username, password, role) VALUES
('admin', '$2b$12$btthwXtm8WY97S6udOfI7uynbJDfGrgdCzJmrapZ.Pu.wXsZ4W/9y', 'admin');

-- Questionnaire table
CREATE TABLE questionnaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO questionnaire (title, description) VALUES
('Monthly Stock Assessment', 'Check pharmaceutical stock availability.');

-- Questionnaire items
CREATE TABLE questionnaire_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questionnaire_id INT NOT NULL,
    linkId VARCHAR(50) NOT NULL,
    text TEXT NOT NULL,
    type VARCHAR(50) NOT NULL,
    FOREIGN KEY (questionnaire_id) REFERENCES questionnaire(id) ON DELETE CASCADE
);

INSERT INTO questionnaire_item (questionnaire_id, linkId, text, type) VALUES
(1, 'q1', 'Do you have stock-outs?', 'boolean'),
(1, 'q2', 'Which medicines are in shortage?', 'textarea'),
(1, 'q3', 'Date of last order?', 'text');

-- Questionnaire response tables
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
