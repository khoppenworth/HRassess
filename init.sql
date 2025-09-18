-- Database schema for EPSS Self-Assessment

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    role ENUM('staff', 'admin') NOT NULL
);

INSERT INTO users (username, password, role)
VALUES ('admin', SHA2('admin123', 256), 'admin');

CREATE TABLE questionnaire (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE questionnaire_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questionnaire_id INT,
    linkId VARCHAR(100),
    text TEXT,
    type VARCHAR(50),
    FOREIGN KEY (questionnaire_id) REFERENCES questionnaire(id)
);

CREATE TABLE questionnaire_response (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    questionnaire_id INT,
    authored TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (questionnaire_id) REFERENCES questionnaire(id)
);

CREATE TABLE questionnaire_response_item (
    id INT AUTO_INCREMENT PRIMARY KEY,
    response_id INT,
    linkId VARCHAR(100),
    answer TEXT,
    FOREIGN KEY (response_id) REFERENCES questionnaire_response(id)
);
