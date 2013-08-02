DROP DATABASE IF EXISTS `inventory`;
CREATE DATABASE `inventory`;

GRANT USAGE ON *.* TO `inventoryAdmin`@`localhost`;
DROP USER `inventoryAdmin`@`localhost`;
FLUSH PRIVILEGES;

CREATE USER `inventoryAdmin`@`localhost` IDENTIFIED BY 's3cr3+p@$w0rd';
GRANT SELECT, INSERT, UPDATE, DELETE, REFERENCES, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE ON `inventory`.* TO `inventoryAdmin`@`localhost`;
FLUSH PRIVILEGES;

USE `inventory`;

DROP TABLE IF EXISTS `inventory_computer`;
CREATE TABLE `inventory_computer` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `model` varchar(128) NOT NULL,
  `sku` varchar(128) NOT NULL,
  `uuic` varchar(128) NOT NULL,
  `serial` varchar(128) NOT NULL,
  `hostname` varchar(128) NOT NULL,
  `location` varchar(128) NOT NULL,
  `eowd` varchar(32) NOT NULL,
  `opd` varchar(32) NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`,`uuic`,`serial`,`hostname`),
  FOREIGN KEY (`model`)
   REFERENCES `inventory_computer` (`model`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `inventory_models`;
CREATE TABLE `inventory_models` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `model` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `description` varchar(128) NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `model` (`model`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `inventory_monitors`;
CREATE TABLE `inventory_monitors` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `sku` int(10) NOT NULL,
  `serial` varchar(128) NOT NULL,
  `hostname` varchar(128) NOT NULL,
  `model` varchar(128) NOT NULL,
  `mmodel` varchar(128) NOT NULL,
  `location` varchar(128) NOT NULL,
  `eowd` varchar(128) NOT NULL,
  `order` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`,`serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `inventory_rma`;
CREATE TABLE `inventory_rma` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `date` varchar(12) NOT NULL,
  `hostname` varchar(128) NOT NULL,
  `sku` varchar(128) NOT NULL,
  `uuic` varchar(128) NOT NULL,
  `serial` varchar(128) NOT NULL,
  `incorrect` tinyint(1) NOT NULL,
  `part` varchar(128) NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) NOT NULL,
  `session_data` longtext NOT NULL,
  `session_expire` int(10) NOT NULL,
  `session_agent` varchar(64) NOT NULL,
  `session_ip` varchar(64) NOT NULL,
  `session_referrer` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
