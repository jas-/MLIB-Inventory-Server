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

-- Handle unique hostnames
DROP TABLE IF EXISTS `hostnames`;
CREATE TABLE `hostnames` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `hostname` CHAR(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`hostname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Handles unique model definitions
DROP TABLE IF EXISTS `models`;
CREATE TABLE `models` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `model` CHAR(128) NOT NULL,
  `description` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Handles warranty definitions
DROP TABLE IF EXISTS `warranty`;
CREATE TABLE `warranty` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `eowd` CHAR(32) NULL DEFAULT NULL,
  `opd` CHAR(32) NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`eowd`, `opd`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Handles computer records
-- Linked to hostnames, models & warranty items
DROP TABLE IF EXISTS `computers`;
CREATE TABLE `computers` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `hostname` BIGINT,
  `model` BIGINT,
  `sku` CHAR(128) NOT NULL,
  `uuic` CHAR(128),
  `serial` CHAR(128) NOT NULL,
  `warranty` BIGINT,
  `notes` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`sku`,`serial`),
  INDEX (`hostname`, `model`, `warranty`),
  CONSTRAINT `fk_computers2hostnames` FOREIGN KEY (`hostname`)
   REFERENCES `hostnames` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_computers2models` FOREIGN KEY (`model`)
   REFERENCES `models` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_computers2warranty` FOREIGN KEY (`warranty`)
   REFERENCES `warranty` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Handles monitor device types
-- Linked to hostnames, models & warranty
DROP TABLE IF EXISTS `monitors`;
CREATE TABLE `monitors` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `hostname` BIGINT,
  `model` BIGINT,
  `sku` CHAR(128) NOT NULL,
  `serial` CHAR(128) NOT NULL,
  `warranty` BIGINT,
  `notes` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`sku`,`serial`),
  INDEX (`hostname`, `model`, `warranty`),
  CONSTRAINT `fk_monitors2hostnames` FOREIGN KEY (`hostname`)
   REFERENCES `hostnames` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_monitors2models` FOREIGN KEY (`model`)
   REFERENCES `models` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_monitors2warranty` FOREIGN KEY (`warranty`)
   REFERENCES `warranty` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
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
  `eowd` CHAR(32) NOT NULL,
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
 SQL SECURITY DEFINER
VIEW viewInventoryComputers AS
 SELECT c.id AS Id, h.hostname AS Hostname, m.model AS Model, c.sku AS SKU, c.uuic AS UUIC, c.serial AS Serial, (CASE WHEN eowd IS NULL THEN "" ELSE FROM_UNIXTIME(eowd, "%Y-%m-%d") END) AS EOWD, (CASE WHEN opd IS NULL THEN "" ELSE FROM_UNIXTIME(opd, "%Y-%m-%d") END) AS OPD, m.description AS Description, c.notes AS Notes FROM computers c LEFT JOIN hostnames h ON h.id = c.hostname LEFT JOIN models m ON c.model = m.id LEFT JOIN warranty w ON c.warranty = w.id ORDER BY `hostname` ASC;

CREATE OR REPLACE DEFINER='{RO}'@'{SERVER}'
 SQL SECURITY DEFINER
VIEW viewInventoryMonitors AS
 SELECT m.id AS Id, h.hostname AS Hostname, mo.model AS Model, m.sku AS SKU, m.serial AS Serial, (CASE WHEN eowd IS NULL THEN "" ELSE FROM_UNIXTIME(eowd, "%Y-%m-%d") END) AS EOWD, (CASE WHEN opd IS NULL THEN "" ELSE FROM_UNIXTIME(opd, "%Y-%m-%d") END) AS OPD, mo.description AS Description, m.notes AS Notes FROM monitors m LEFT JOIN hostnames h ON h.id = m.hostname LEFT JOIN models mo ON m.model = mo.id LEFT JOIN warranty w ON m.warranty = w.id ORDER BY `hostname` ASC;

CREATE OR REPLACE DEFINER='{RO}'@'{SERVER}'
 SQL SECURITY DEFINER
VIEW viewInventoryModels AS
 SELECT id AS Id, model AS Model, description AS Description FROM models ORDER BY `model`;

CREATE OR REPLACE DEFINER='{RO}'@'{SERVER}'
 SQL SECURITY DEFINER
VIEW viewInventoryWarranty AS
 SELECT id AS Id, (CASE WHEN eowd IS NULL THEN "" ELSE FROM_UNIXTIME(eowd, "%Y-%m-%d") END) AS EOWD, (CASE WHEN opd IS NULL THEN "" ELSE FROM_UNIXTIME(opd, "%Y-%m-%d") END) AS OPD FROM warranty ORDER BY OPD ASC;

CREATE OR REPLACE DEFINER='{RO}'@'{SERVER}'
 SQL SECURITY DEFINER
VIEW viewInventoryRMA AS
 SELECT id AS Id, incorrect AS Problem, FROM_UNIXTIME(date, '%Y-%m-%d') AS Date, hostname AS Hostname, model AS Model, sku AS SKU, uuic AS UUIC, serial AS Serial, eowd AS EOWD, part AS Part, notes AS Notes FROM rma ORDER BY `Date` ASC;

CREATE OR REPLACE DEFINER='{RO}'@'{SERVER}'
 SQL SECURITY DEFINER
VIEW viewInventoryCORS AS
 SELECT id AS Id, application AS Application, url AS URL, ip AS IP FROM cors ORDER BY `application`;
