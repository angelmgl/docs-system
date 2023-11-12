CREATE TABLE roles (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL
);

INSERT INTO roles (code, name) 
VALUES ('admin', 'Administrador'), ('analyst', 'Analista');