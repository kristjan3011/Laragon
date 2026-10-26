-- Mario Käpakodu: andmebaasi skeem ja algandmed
-- Käivitamine: mysql -uroot < sql/schema.sql

CREATE DATABASE IF NOT EXISTS mario_kapakodu
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE mario_kapakodu;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS contact_requests;
DROP TABLE IF EXISTS animals;
DROP TABLE IF EXISTS breeds;
DROP TABLE IF EXISTS species;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE species (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE breeds (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    species_id INT UNSIGNED NOT NULL,
    name VARCHAR(80) NOT NULL,
    UNIQUE KEY uq_breed (species_id, name),
    CONSTRAINT fk_breed_species FOREIGN KEY (species_id) REFERENCES species (id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE animals (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    species_id INT UNSIGNED NOT NULL,
    breed_id INT UNSIGNED NULL,
    sex ENUM('Isane', 'Emane') NOT NULL,
    birth_date DATE NOT NULL,
    size VARCHAR(40) NULL,
    color VARCHAR(80) NULL,
    location VARCHAR(120) NULL,
    neutered TINYINT(1) NOT NULL DEFAULT 0,
    chipped TINYINT(1) NOT NULL DEFAULT 0,
    short_description VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status ENUM('Saadaval', 'Broneeritud', 'Hoolduses', 'Kodus') NOT NULL DEFAULT 'Saadaval',
    image VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_animal_species FOREIGN KEY (species_id) REFERENCES species (id),
    CONSTRAINT fk_animal_breed FOREIGN KEY (breed_id) REFERENCES breeds (id) ON DELETE SET NULL,
    INDEX idx_animal_name (name)
) ENGINE=InnoDB;

CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'toimetaja') NOT NULL DEFAULT 'toimetaja',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE contact_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(80) NOT NULL,
    email VARCHAR(120) NOT NULL,
    topic ENUM('Looma adopteerimine', 'Vabatahtlik töö', 'Annetamine', 'Üldine küsimus') NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Uus', 'Vastatud') NOT NULL DEFAULT 'Uus',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Algandmed
INSERT INTO species (id, name) VALUES (1, 'Koer'), (2, 'Kass');

INSERT INTO breeds (species_id, name) VALUES
    (1, 'Golden retriever'),
    (1, 'Segu'),
    (2, 'Tabby'),
    (2, 'Segu');

-- Parool on algselt "muuda-mind" (bcrypt). Muuda see kohe pärast esimest sisselogimist.
INSERT INTO users (name, email, password_hash, role) VALUES
    ('Administraator', 'admin@varjupaik.ee', '$2y$10$N6G919vTBVAw.Gk4cq1is.pi05zAGXMlHoqRyDVrLplA7BSQXh4pK', 'admin');

INSERT INTO animals (name, species_id, breed_id, sex, birth_date, size, color, location, neutered, chipped, short_description, description, status, image) VALUES
    ('Rex', 1, 1, 'Isane', '2024-10-26', 'Keskmine (15–25 kg)', 'Pruun, must', 'Tallinn', 1, 1,
     'Sõbralik ja energiline koer, kes armastab pikki jalutuskäike ja mänge.',
     'Rex on sõbralik, energiline ja väga inimsõbralik koer. Ta armastab jalutuskäike, mängimist ja seltskonda. Rex sobib hästi aktiivsele perele.',
     'Saadaval', 'assets/images/rex.png'),
    ('Mimi', 2, 3, 'Emane', '2020-10-26', 'Väike (3–5 kg)', 'Tabby, valge', 'Tallinn', 1, 1,
     'Väike nurrumootor, kes otsib oma igavest kodu.',
     'Mimi on vaikne ja hellitust armastav kass. Ta sobib rahulikku koju, kus on palju päikest ja oma nurk.',
     'Saadaval', 'assets/images/mimi.png'),
    ('Musti', 1, 2, 'Isane', '2023-10-26', 'Keskmine (15–25 kg)', 'Pruun, must', 'Tartu', 1, 1,
     'Rahulik ja ustav kaaslane, kes naudib inimeste seltskonda.',
     'Musti on rahulik ja ustav koer, kes naudib inimeste seltskonda ning sobib lastega peredesse.',
     'Saadaval', 'assets/images/musti.png'),
    ('Triibik', 2, 3, 'Isane', '2025-10-26', 'Väike (3–5 kg)', 'Triibuline', 'Tallinn', 0, 1,
     'Mänguhimuline ja uudishimulik kassipoeg, kes ootab oma kodu.',
     'Triibik on mänguhimuline kassipoeg, kes vajab aktiivset peret ja palju mänguasju.',
     'Saadaval', 'assets/images/triibik.png'),
    ('Bruno', 1, 1, 'Isane', '2021-10-26', 'Suur (25–40 kg)', 'Kuldne', 'Pärnu', 1, 1,
     'Rõõmsameelne koer, kes armastab pikki jalutuskäike.',
     'Bruno on rõõmsameelne koer, kes armastab pikki jalutuskäike ja looduses olemist.',
     'Saadaval', 'assets/images/rex.png'),
    ('Miisu', 2, 3, 'Emane', '2022-10-26', 'Väike (3–5 kg)', 'Tabby, valge', 'Tallinn', 1, 0,
     'Rahulik kass, kes naudib vaikust ja pehmeid pesasid.',
     'Miisu on rahulik kass, kes naudib vaikust ja pehmet voodit akna all.',
     'Saadaval', 'assets/images/mimi.png'),
    ('Bella', 1, 2, 'Emane', '2025-10-01', 'Keskmine (15–25 kg)', 'Pruun', 'Tallinn', 0, 1,
     'Noor ja mänguhimuline koer, kes ootab aktiivset peret.',
     'Bella on noor ja mänguhimuline koer, kes ootab aktiivset peret ja head treeningut.',
     'Saadaval', 'assets/images/musti.png'),
    ('Nurr', 2, 4, 'Isane', '2023-10-26', 'Keskmine (3–5 kg)', 'Triibuline', 'Tallinn', 1, 1,
     'Sõbralik ja uudishimulik kass, kes otsib armastavat kodu.',
     'Nurr on sõbralik ja uudishimulik kass, kes otsib armastavat kodu ja inimesi, kellega koos mängida.',
     'Saadaval', 'assets/images/triibik.png'),
    ('Tuisk', 2, 3, 'Emane', '2021-10-26', 'Väike (3–5 kg)', 'Hall triibuline', 'Narva', 1, 1,
     'Hellust ja vaikne kass, kes otsib rahulikku kodu.',
     'Tuisk on hellust ja vaikne kass, kes otsib rahulikku kodu.',
     'Kodus', 'assets/images/mimi.png'),
    ('Lumi', 1, 2, 'Emane', '2024-06-01', 'Keskmine (15–25 kg)', 'Valge', 'Tallinn', 0, 1,
     'Kaval ja seltsiv koer, kes sobib aktiivsesse peresse.',
     'Lumi on kaval ja seltsiv koer, kes sobib aktiivsesse peresse.',
     'Broneeritud', 'assets/images/rex.png');

INSERT INTO contact_requests (name, email, topic, message, status) VALUES
    ('Kati Mae', 'kati@example.com', 'Looma adopteerimine', 'Tahaksin tutvuda Rexiga.', 'Uus'),
    ('Martin Tamm', 'martin@example.com', 'Üldine küsimus', 'Kas vastuvõtt on laupäeval?', 'Uus'),
    ('Liisa Kask', 'liisa@example.com', 'Vabatahtlik töö', 'Soovin aidata kõndida koeri.', 'Vastatud');
