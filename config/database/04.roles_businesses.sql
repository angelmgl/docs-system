CREATE TABLE role_businesses (
    role_id INT(11) UNSIGNED NOT NULL,
    business_id INT(11) UNSIGNED NOT NULL,
    user_id INT(11) UNSIGNED NOT NULL,

    PRIMARY KEY (role_id, business_id, user_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (business_id) REFERENCES businesses(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Si las búsquedas van a ser comúnmente por business_id y role_id juntos
CREATE INDEX idx_business_role ON role_businesses(business_id, role_id);

-- Si las búsquedas por user_id son comunes y no siempre en conjunción con role_id y business_id
CREATE INDEX idx_user_id ON role_businesses(user_id);
