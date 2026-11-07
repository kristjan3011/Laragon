-- Ülesanne 10: menüü
CREATE TABLE IF NOT EXISTS menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nimetus VARCHAR(100) NOT NULL,
    url VARCHAR(255) NOT NULL,
    ikoon VARCHAR(100) DEFAULT NULL,
    jarjekord INT NOT NULL DEFAULT 0,
    aktiivne TINYINT(1) NOT NULL DEFAULT 1,
    loodud TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO menu (nimetus, url, ikoon, jarjekord)
SELECT * FROM (
    SELECT 'Avaleht' a, 'index.php' b, 'house' c, 1 d
    UNION ALL SELECT 'Kõik loomad', 'all_animals.php', 'heart', 2
    UNION ALL SELECT 'Kontakt', 'contact.php', 'envelope', 3
) t WHERE NOT EXISTS (SELECT 1 FROM menu);

-- Rakenduse kasutaja
CREATE USER IF NOT EXISTS 'kapakodu_app'@'localhost' IDENTIFIED BY 'kapakodu_app_pw';
GRANT SELECT, INSERT, UPDATE, DELETE ON kapakodu.* TO 'kapakodu_app'@'localhost';
