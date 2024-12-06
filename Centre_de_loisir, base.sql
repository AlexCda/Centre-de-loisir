SET AUTOCOMMIT = 0;
START TRANSACTION;
-- ---------------------------------------------------------------------------------------
-- Base de données : centre de loisir
--
DROP DATABASE
    IF EXISTS centre_de_loisir;
CREATE DATABASE centre_de_loisir
    DEFAULT CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE centre_de_loisir;
--
-- ---------------------------------------------------------------------------------------
-- Structure de la table Activités
--
DROP TABLE IF EXISTS Activities;
CREATE TABLE Activities (
    numActivity INT(11) NOT NULL AUTO_INCREMENT,
    nameActivity VARCHAR(30) DEFAULT NULL,
    unitCost INT(11) DEFAULT NULL,
    unitPrice INT(11) DEFAULT NULL,
    PRIMARY KEY (numActivity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Contenu de la table Activities
--
INSERT INTO Activities (numActivity, nameActivity, unitCost, unitPrice) VALUES
(1, 'Escalade', 5, 10),
(2, 'Raquette', 8, 15),
(3, 'Cirque', 3, 7),
(4, 'Tricot', 6, 12),
(5, 'Danse', 7, 14),
(6, 'Rock acrobatique', 10, 20),
(7, 'Peinture', NULL, 8),
(8, 'Cuisine', 12, NULL),
(9, 'Échecs', NULL, NULL),
(10, 'Badminton', 4, 9);
--
-- ---------------------------------------------------------------------------------------
-- Structure de la table Members
--
DROP TABLE IF EXISTS Members;
CREATE TABLE Members (
    numMember INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(30) DEFAULT NULL,
    address VARCHAR(80) DEFAULT NULL,
    postCode VARCHAR(6) DEFAULT NULL,
    city VARCHAR(30) DEFAULT NULL,
    dateBirth  DATE DEFAULT NULL,
    PRIMARY KEY (numMember)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Contenu de la table Members
--
INSERT INTO Members (numMember, name, address, postCode, city, dateBirth) VALUES
(1, 'Dupont Marie', '15 rue des Lilas', '75001', 'Paris', '1990-05-15'),
(2, 'Martin Jean', '7 avenue des Roses', '69002', 'Lyon', '1985-11-22'),
(3, 'Durand Sophie', '3 place de la Liberté', NULL, 'Marseille', '1992-08-30'),
(4, 'Lefebvre Pierre', '22 boulevard des Champs', '33000', 'Bordeaux', '1988-02-10'),
(5, 'Moreau Isabelle', NULL, '59000', 'Lille', '1995-07-18'),
(6, 'Garcia Antoine', '9 rue du Commerce', '44000', 'Nantes', NULL),
(7, 'Roux Camille', '18 avenue Jean Jaurès', '31000', 'Toulouse', '1993-12-05'),
(8, 'Fournier Lucas', '5 rue de la Paix', '67000', 'Strasbourg', '1991-09-25'),
(9, 'Girard Emma', NULL, NULL, 'Nice', '1997-03-14'),
(10, 'Robin Thomas', '11 rue des Écoles', '35000', 'Rennes', '1989-06-20');
--
-- ---------------------------------------------------------------------------------------
-- Structure de la table Sessions
--
DROP TABLE IF EXISTS Sessions;
CREATE TABLE Sessions (
    numSession INT(11) NOT NULL AUTO_INCREMENT,
    numActivity INT(11) NOT NULL,
    startDate DATE DEFAULT NULL,
    startTime TIME DEFAULT NULL,
    PRIMARY KEY (numSession),
    KEY SessionsActivities_numActivity (numActivity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Contenu de la table Sessions
--
INSERT INTO Sessions (numSession, numActivity, startDate, startTime) VALUES
(1, 1, '2024-10-01', '09:00:00'),
(2, 2, '2024-10-01', '14:30:00'),
(3, 3, '2024-10-02', '10:15:00'),
(4, 4, '2024-10-03', NULL),
(5, 5, '2024-10-04', '16:45:00'),
(6, 6, NULL, '11:30:00'),
(7, 7, '2024-10-06', '13:00:00'),
(8, 8, '2024-10-07', '18:00:00'),
(9, 9, '2024-10-08', NULL),
(10, 10, '2024-10-09', '15:30:00'),
(11, 2, '2024-10-03', '14:30:00'),
(12, 3, '2024-10-05', '10:15:00'),
(13, 4, '2024-10-20', NULL),
(14, 2, '2024-10-11', '16:45:00');
--
-- ---------------------------------------------------------------------------------------
-- Structure de la table Attendances
--
DROP TABLE IF EXISTS Attendances;
CREATE TABLE Attendances (
    numSession INT(11) NOT NULL,
    numMember INT(11) NOT NULL,
    score INT(11) DEFAULT NULL,
    PRIMARY KEY (numSession, numMember),
    KEY AttendancesSessions_numSession (numSession),
    KEY AttendancesMembers_numMember (numMember)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
--
-- Relations pour la table Attendances
--   numSession
--       Sessions -> numSession
--   numMember
--       Members -> numMember
--
-- Contenu de la table Attendances
--
INSERT INTO Attendances (numSession, numMember, score) VALUES
(1, 1, 8),
(1, 2, 7),
(2, 3, NULL),
(2, 4, 9),
(3, 5, 6),
(3, 6, NULL),
(4, 7, 8),
(4, 8, 7),
(5, 9, NULL),
(5, 10, 10),
(6, 1, 9),
(6, 3, 8),
(7, 2, NULL),
(7, 4, 7),
(8, 5, 6),
(8, 7, 8),
(9, 6, NULL),
(9, 8, 9),
(10, 9, 7),
(10, 10, NULL);
--
-- ---------------------------------------------------------------------------------------
-- Valeurs d'autoincrément pour les tables
--
ALTER TABLE Activities
    MODIFY numActivity INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE Members
    MODIFY numMember INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
ALTER TABLE Sessions
    MODIFY numSession INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- ---------------------------------------------------------------------------------------
-- Contraintes pour les tables
--
ALTER TABLE Sessions
    ADD CONSTRAINT SessionsActivities_numActivity FOREIGN KEY (numActivity) REFERENCES Activities (numActivity) ON DELETE CASCADE;
ALTER TABLE Attendances
    DROP CONSTRAINT IF EXISTS AttendancesSession_numSession,
    ADD CONSTRAINT AttendancesSession_numSession 
    FOREIGN KEY (numSession) REFERENCES Sessions (numSession) ON DELETE CASCADE,
    
    DROP CONSTRAINT IF EXISTS AttendancesMember_numMember,
    ADD CONSTRAINT AttendancesMember_numMember 
    FOREIGN KEY (numMember) REFERENCES Members (numMember) ON DELETE CASCADE;
--
-- ---------------------------------------------------------------------------------------
-- Métadonnées
--
USE phpmyadmin;
--
-- Métadonnées pour la base de données centre_de_loisir
--
INSERT INTO phpmyadmin.pma__pdf_pages (db_name, page_descr) VALUES
    ('centre_de_loisir', 'Centre_de_loisir');
--
SET @LAST_PAGE = LAST_INSERT_ID();
--
INSERT INTO phpmyadmin.pma__table_coords (db_name, table_name, pdf_page_number, x, y) VALUES
    ('centre_de_loisir', 'Activities', @LAST_PAGE, 362, 46),
    ('centre_de_loisir', 'Members', @LAST_PAGE, 622, 46),
    ('centre_de_loisir', 'Sessions', @LAST_PAGE, 93, 69),
    ('centre_de_loisir', 'Attendances', @LAST_PAGE, 362, 200);
-- ---------------------------------------------------------------------------------------
COMMIT;
SET AUTOCOMMIT = 1;
