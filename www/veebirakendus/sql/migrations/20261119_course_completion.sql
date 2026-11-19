-- Olemasoleva mario_kapakodu andmebaasi andmeid säilitav uuendus (MySQL 8).
-- Käivita valitud andmebaasil: mysql -uroot mario_kapakodu < see_fail.sql
-- Skripti võib käivitada korduvalt. Olemasolevate kirjete lisajat ega looma ei oletata.
-- MySQL DDL teeb implicit COMMIT-i; tee enne oma varukoopia.
SET NAMES utf8mb4;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'animals' AND COLUMN_NAME = 'created_by');
SET @ddl = IF(@column_exists = 0,
    'ALTER TABLE animals ADD COLUMN created_by INT UNSIGNED NULL AFTER breed_id', 'SELECT 1');
PREPARE course_stmt FROM @ddl;
EXECUTE course_stmt;
DEALLOCATE PREPARE course_stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'animals' AND COLUMN_NAME = 'created_by'
      AND REFERENCED_TABLE_NAME = 'users' AND REFERENCED_COLUMN_NAME = 'id');
SET @ddl = IF(@constraint_exists = 0,
    'ALTER TABLE animals ADD CONSTRAINT fk_animal_creator FOREIGN KEY (created_by) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE', 'SELECT 1');
PREPARE course_stmt FROM @ddl;
EXECUTE course_stmt;
DEALLOCATE PREPARE course_stmt;

SET @column_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'contact_requests' AND COLUMN_NAME = 'animal_id');
SET @ddl = IF(@column_exists = 0,
    'ALTER TABLE contact_requests ADD COLUMN animal_id INT UNSIGNED NULL AFTER id', 'SELECT 1');
PREPARE course_stmt FROM @ddl;
EXECUTE course_stmt;
DEALLOCATE PREPARE course_stmt;

SET @constraint_exists = (SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'contact_requests' AND COLUMN_NAME = 'animal_id'
      AND REFERENCED_TABLE_NAME = 'animals' AND REFERENCED_COLUMN_NAME = 'id');
SET @ddl = IF(@constraint_exists = 0,
    'ALTER TABLE contact_requests ADD CONSTRAINT fk_request_animal FOREIGN KEY (animal_id) REFERENCES animals (id) ON DELETE SET NULL ON UPDATE CASCADE', 'SELECT 1');
PREPARE course_stmt FROM @ddl;
EXECUTE course_stmt;
DEALLOCATE PREPARE course_stmt;
