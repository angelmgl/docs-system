CREATE TABLE images (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255),
    width INT(11) NOT NULL,
    height INT(11) NOT NULL,
    document_id INT(11) UNSIGNED NOT NULL,

    FOREIGN KEY (document_id) REFERENCES image_docs(id) ON DELETE CASCADE
);