CREATE TABLE user_categories (
    user_id INT(11) UNSIGNED,
    category_id INT(11) UNSIGNED,
    PRIMARY KEY (user_id, category_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);