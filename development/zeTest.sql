DROP DATABASE IF EXISTS `zeTest`;
CREATE DATABASE `zeTest`;

USE `zeTest`;

DROP TABLE IF EXISTS `types`;
CREATE TABLE `types` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type` char(128) DEFAULT "undefined",
  PRIMARY KEY (`id`),
  INDEX (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

insert into types (`type`) values ("labs"), ("staff"), ("laptops");

DROP TABLE IF EXISTS `location`;
CREATE TABLE `location` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `location` char(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `names`;
CREATE TABLE `names` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `hostname` char(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `models`;
CREATE TABLE `models` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `model` char(128) NOT NULL,
  `eowd` char(32) NOT NULL,
  `opd` char(32) NOT NULL,
  `type` char(128) DEFAULT "undefined",
  `description` char(128) NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`model`),
  INDEX `type` (`type`),
  FOREIGN KEY (`type`)
   REFERENCES `types` (`type`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

insert into models (`model`,`type`) values ("940", "labs"), ("965", "staff"), ("980", "laptops");

DROP TABLE IF EXISTS `computers`;
CREATE TABLE `computers` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `hostname` char(128) NOT NULL,
  `model` char(128) NOT NULL,
  `sku` char(128) NOT NULL,
  `uuic` char(128) NOT NULL,
  `serial` char(128) NOT NULL,
  `location` char(128) NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`,`uuic`,`serial`,`hostname`),
  INDEX `model` (`model`),
  FOREIGN KEY (`model`)
   REFERENCES `models` (`model`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `monitors`;
CREATE TABLE `monitors` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `hostname` char(128) NOT NULL,
  `model` char(128) NOT NULL,
  `mmodel` char(128) NOT NULL,
  `sku` int(10) NOT NULL,
  `serial` char(128) NOT NULL,
  `location` char(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`,`serial`),
  INDEX `model` (`model`),
  FOREIGN KEY (`model`)
   REFERENCES `models` (`model`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

DROP TABLE IF EXISTS `rma`;
CREATE TABLE `rma` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `date` char(12) NOT NULL,
  `hostname` char(128) NOT NULL,
  `sku` char(128) NOT NULL,
  `uuic` char(128) NOT NULL,
  `serial` char(128) NOT NULL,
  `incorrect` tinyint(1) NOT NULL,
  `part` char(128) NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;
