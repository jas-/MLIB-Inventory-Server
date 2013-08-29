-- Create the database & drop if it already exists
DROP DATABASE IF EXISTS `{NAME}`;
CREATE DATABASE `{NAME}` DEFAULT CHARACTER SET=utf8 COLLATE=utf8_general_ci;

-- Assign grant priviledge for administrative account then drop it
GRANT USAGE ON *.* TO `{ADMIN}`@`{SERVER}`;
DROP USER `{ADMIN}`@`{SERVER}`;
FLUSH PRIVILEGES;

-- Create a default administrative user and assign limited permissions
CREATE USER `{ADMIN}`@`{SERVER}` IDENTIFIED BY '{ADMIN_PW}';
GRANT SELECT, INSERT, UPDATE, DELETE, REFERENCES, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE ON `{NAME}`.* TO `{ADMIN}`@`{SERVER}`;
FLUSH PRIVILEGES;

-- Assign grant priviledge for read-only account then drop it
GRANT USAGE ON *.* TO `{RO}`@`{SERVER}`;
DROP USER `{RO}`@`{SERVER}`;
FLUSH PRIVILEGES;

-- Create a default read-only user and assign limited permissions
CREATE USER `{RO}`@`{SERVER}` IDENTIFIED BY '{RO_PW}';
GRANT SELECT, REFERENCES, INDEX, LOCK TABLES, EXECUTE ON `{NAME}`.* TO `{RO}`@`{SERVER}`;
FLUSH PRIVILEGES;

-- Switch to newly created db context
USE `{NAME}`;

DROP TABLE IF EXISTS `hostnames`;
CREATE TABLE `hostnames` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `hostname` CHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`hostname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `models`;
CREATE TABLE `models` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `model` CHAR(128) NOT NULL,
  `eowd` CHAR(32) NOT NULL,
  `opd` CHAR(32) NOT NULL,
  `description` CHAR(128) NOT NULL,
  `notes` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `computers`;
CREATE TABLE `computers` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `hostname` CHAR(128) NOT NULL,
  `model` CHAR(128) NOT NULL,
  `sku` CHAR(128) NOT NULL,
  `uuic` CHAR(128) NOT NULL,
  `serial` CHAR(128) NOT NULL,
  `notes` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`sku`,`uuic`,`serial`),
  INDEX (`hostname`, `model`),
  CONSTRAINT `fk_computers2hostnames` FOREIGN KEY (`hostname`)
   REFERENCES `hostnames` (`hostname`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_computers2models` FOREIGN KEY (`model`)
   REFERENCES `models` (`model`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `monitors`;
CREATE TABLE `monitors` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `hostname` CHAR(128) NOT NULL,
  `model` CHAR(128) NOT NULL,
  `sku` CHAR(128) NOT NULL,
  `serial` CHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`sku`,`serial`),
  INDEX (`hostname`, `model`),
  CONSTRAINT `fk_monitors2hostnames` FOREIGN KEY (`hostname`)
   REFERENCES `hostnames` (`hostname`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_monitors2models` FOREIGN KEY (`model`)
   REFERENCES `models` (`model`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `rma`;
CREATE TABLE `rma` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `date` CHAR(12) NOT NULL,
  `hostname` CHAR(128) NOT NULL,
  `sku` CHAR(128) NOT NULL,
  `uuic` CHAR(128) NOT NULL,
  `serial` CHAR(128) NOT NULL,
  `model` CHAR(128) NOT NULL,
  `incorrect` TINYINT(1) NOT NULL,
  `part` CHAR(128) NOT NULL,
  `notes` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`sku`, `serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `cors`;
CREATE TABLE IF NOT EXISTS `cors` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `application` CHAR(255) NOT NULL,
  `url` LONGTEXT NOT NULL,
  `ip` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`application`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

CREATE OR REPLACE DEFINER='{RO}'@'{SERVER}'
 SQL SECURITY INVOKER
VIEW viewInventoryComputers AS
 SELECT c.id AS Id, c.hostname AS Hostname, c.model AS Model, c.sku AS SKU, c.uuic AS UUIC, c.serial AS Serial, m.eowd AS EOWD, m.opd AS OPD, m.notes AS Notes, m.description AS Description FROM computers c LEFT JOIN models m ON c.model = m.model ORDER BY c.hostname;

CREATE OR REPLACE DEFINER='{RO}'@'{SERVER}'
 SQL SECURITY INVOKER
VIEW viewInventoryMonitors AS
 SELECT m.id AS Id, m.hostname AS Hostname, m.model AS Model, m.sku AS SKU, m.serial AS Serial, mo.eowd AS EOWD, mo.opd AS OPD, mo.notes AS Notes, mo.description AS Description FROM monitors m LEFT JOIN models mo ON m.model = mo.model ORDER BY `hostname`;

CREATE OR REPLACE DEFINER='{RO}'@'{SERVER}'
 SQL SECURITY INVOKER
VIEW viewInventoryModels AS
 SELECT id AS Id, model AS Model, eowd AS EOWD, opd AS OPD, notes AS Notes, description AS Description FROM models `model`;
