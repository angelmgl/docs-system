CREATE TABLE password_resets (
    user_id INT(11) UNSIGNED NOT NULL,
    recovery_code VARCHAR(15) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);