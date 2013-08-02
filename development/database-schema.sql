-- Create the database & drop if it already exists
DROP DATABASE IF EXISTS `inventory2011`;
CREATE DATABASE `inventory2011`;

-- Create a default user and assign limited permissions
CREATE USER "inventory2011"@"%" IDENTIFIED BY "d3v3l0pm3n+";
GRANT SELECT, INSERT, UPDATE, DELETE, REFERENCES, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE ON `inventory2011`.* TO "inventory2011"@"%";
CREATE USER "inventory2011"@"localhost" IDENTIFIED BY "d3v3l0pm3n+";
GRANT SELECT, INSERT, UPDATE, DELETE, REFERENCES, INDEX, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE ON `inventory2011`.* TO "inventory2011"@"localhost";

-- Switch to newly created db context
USE `inventory2011`;

-- Set FK checks to 0 during table creation
SET foreign_key_checks = 0;

-- Creates a table for authentication groups
--  Primary key: id
--  Unique key: resource
--  Index: resource
DROP TABLE IF EXISTS `authentication_groups`;
CREATE TABLE IF NOT EXISTS `authentication_groups` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `group` varchar(128) NOT NULL,
  `manager` varchar(128) NOT NULL,
  `description` varchar(255) NOT NULL,
  `owner` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`resource`),
  UNIQUE KEY `group` (`group`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Create a default 'administrative' group
INSERT INTO `authentication_groups` (`resource`, `group`, `owner`) VALUES (sha1("admin"), "admin", "admin");

-- Creates a table for authentication access levels
--  Primary key: id
--  Unique key: level
DROP TABLE IF EXISTS `authentication_levels`;
CREATE TABLE IF NOT EXISTS `authentication_levels` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `level` varchar(40) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `level` (`level`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=0;

-- Create a set of default access levels
INSERT INTO `authentication_levels` (`level`) VALUES ("admin");
INSERT INTO `authentication_levels` (`level`) VALUES ("user");
INSERT INTO `authentication_levels` (`level`) VALUES ("view");

-- Creates the authentication users table
--  Primary key: id
--  Unique key: resource, emailAddress
--  Index: group, level, emailAddress
--  Foreign key details:
--   authentication.group updates from authentication_groups.group
--   authentication.level updates from authentication_levels.level
--   authentication.emailAddress updates from authentication_keys.emailAddress
DROP TABLE IF EXISTS `authentication`;
CREATE TABLE IF NOT EXISTS `authentication` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `emailAddress` varchar(128) NOT NULL,
  `password` mediumtext NOT NULL,
  `level` varchar(40) NOT NULL,
  `group` varchar(128) NOT NULL,
  `authentication_token` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resource` (`resource`),
  UNIQUE KEY `emailAddress` (`emailAddress`),
  INDEX `group` (`group`),
  CONSTRAINT `fk_groups` FOREIGN KEY (`group`)
   REFERENCES `authentication_groups` (`group`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `level` (`level`),
  CONSTRAINT `fk_levels` FOREIGN KEY (`level`)
   REFERENCES `authentication_levels` (`level`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates a table to handle RSA private, public & pkcs#7 data per user
--  Primary key: id
--  Unique key: resource
--  Index: emailAddress, ip
CREATE TABLE IF NOT EXISTS `keys` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `countryName` varchar(64) NOT NULL,
  `stateOrProvinceName` varchar(64) NOT NULL,
  `localityName` varchar(64) NOT NULL,
  `organizationName` varchar(64) NOT NULL,
  `organizationalUnitName` varchar(64) NOT NULL,
  `commonName` varchar(64) NOT NULL,
  `emailAddress` varchar(128) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `pass` longtext NOT NULL,
  `pub` longtext NOT NULL,
  `private` longtext NOT NULL,
  `certificate` longtext NOT NULL,
  `latitude` varchar(64) NOT NULL,
  `longitude` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resource` (`resource`),
  INDEX `ip` (`ip`),
  INDEX `emailAddress` (`emailAddress`),
  CONSTRAINT `fk_keys` FOREIGN KEY (`emailAddress`)
   REFERENCES `authentication` (`emailAddress`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates a table to handle RSA public key trusts
--  Primary key: id
--  Unique key: resource
--  Index: emailAddress
CREATE TABLE IF NOT EXISTS `keys_trusts` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `emailAddress` varchar(128) NOT NULL,
  `trustedEmailAddress` varchar(128) NOT NULL,
  `signed` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resource` (`resource`),
  INDEX `emailAddress` (`emailAddress`),
  CONSTRAINT `fk_key` FOREIGN KEY (`emailAddress`)
   REFERENCES `keys` (`emailAddress`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `trustedEmailAddress` (`trustedEmailAddress`),
  CONSTRAINT `fk_trusted` FOREIGN KEY (`trustedEmailAddress`)
   REFERENCES `keys` (`emailAddress`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Create a table for default application settings
--  Primary key: id
--  Unique key: email
DROP TABLE IF EXISTS `configuration`;
CREATE TABLE IF NOT EXISTS `configuration` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `templates` varchar(255) NOT NULL,
  `cache` varchar(255) NOT NULL,
  `private` tinyint(1) NOT NULL,
  `email` varchar(45) NOT NULL,
  `timeout` int(10) NOT NULL,
  `compression` int(1) NOT NULL,
  `encryption` int(1) NOT NULL,
  `pkey` LONGTEXT NOT NULL,
  `pvkey` LONGTEXT NOT NULL,
  `pass` LONGTEXT NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates table to handle default OpenSSL settings
--  Primary key: id
DROP TABLE IF EXISTS `configuration_openssl_cnf`;
CREATE TABLE `configuration_openssl_cnf` (
  `id` INT( 255 ) NOT NULL AUTO_INCREMENT,
  `config` VARCHAR( 64 ) NOT NULL,
  `encrypt_key` BOOLEAN NOT NULL,
  `private_key_type` VARCHAR( 64 ) NOT NULL,
  `digest_algorithm` VARCHAR( 64 ) NOT NULL,
  `private_key_bits` INT( 4 ) NOT NULL,
  `x509_extensions` VARCHAR( 32 ) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Set some defaults for OpenSSL settings
INSERT INTO `configuration_openssl_cnf` (`id`, `config`, `encrypt_key`, `private_key_type`, `digest_algorithm`, `private_key_bits`, `x509_extensions`) VALUES (1, 'openssl.cnf', 1, 'OPENSSL_KEYTYPE_RSA', 'sha256', 2048, 'usr_cert');

-- Creates table to handle application wide OpenSSL keys
--  Primary key: id
DROP TABLE IF EXISTS `configuration_openssl_dn`;
CREATE TABLE `configuration_openssl_dn` (
  `id` INT( 255 ) NOT NULL AUTO_INCREMENT,
  `countryName` VARCHAR( 64 ) NOT NULL ,
  `stateOrProvinceName` VARCHAR( 64 ) NOT NULL ,
  `localityName` VARCHAR( 64 ) NOT NULL ,
  `organizationName` VARCHAR( 64 ) NOT NULL ,
  `organizationalUnitName` VARCHAR( 64 ) NOT NULL ,
  `commonName` VARCHAR( 64 ) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates table to handle optional LDAP authentication sources
--  Primary key: id
--  Unique key: domain
DROP TABLE IF EXISTS `configuration_ldap`;
CREATE TABLE IF NOT EXISTS `configuration_ldap` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `key` longtext NOT NULL,
  `domain` varchar(255) NOT NULL,
  `servers` varchar(255) NOT NULL,
  `port` int(5) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bind_dn` varchar(255) NOT NULL,
  `base_dn` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `domain` (`domain`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates table to handle optional memcache functionality
--  Primary key: id
DROP TABLE IF EXISTS `configuration_memcache`;
CREATE TABLE IF NOT EXISTS `configuration_memcache` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `server` varchar(128) NOT NULL,
  `port` int(8) NOT NULL,
  `timeout` int(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates table to handle inventory model data
--  Primary key: id
--  Unique key: model
--  Index: resource
DROP TABLE IF EXISTS `inventory_models`;
CREATE TABLE IF NOT EXISTS `inventory_models` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `model` varchar(128) NOT NULL,
  `type` varchar(128) NOT NULL,
  `description` varchar(128) NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`resource`),
  UNIQUE KEY `model` (`model`),
  CONSTRAINT `fk_models` FOREIGN KEY (`model`)
   REFERENCES `inventory_computer` (`model`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates table to handle inventory computer data
--  Primary key: id
--  Unique key: sku, uuic, serial, hostname
--  Index: resource
DROP TABLE IF EXISTS `inventory_computer`;
CREATE TABLE IF NOT EXISTS `inventory_computer` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
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
  INDEX (`resource`),
  INDEX (`model`),
  UNIQUE KEY `sku` (`sku`,`uuic`,`serial`, `hostname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates table to handle inventory monitor data
--  Primary key: id
--  Unique key: sku, serial
--  Index: resource
DROP TABLE IF EXISTS `inventory_monitors`;
CREATE TABLE IF NOT EXISTS `inventory_monitors` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `sku` int(10) NOT NULL,
  `serial` varchar(128) NOT NULL,
  `hostname` varchar(128) NOT NULL,
  `model` varchar(128) NOT NULL,
  `mmodel` varchar(128) NOT NULL,
  `location` varchar(128) NOT NULL,
  `eowd` varchar(128) NOT NULL,
  `order` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`resource`),
  UNIQUE KEY `sku` (`sku`,`serial`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates table to handle inventory RMA data
--  Primary key: id
--  Index: resource
DROP TABLE IF EXISTS `inventory_rma`;
CREATE TABLE IF NOT EXISTS `inventory_rma` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `date` varchar(12) NOT NULL,
  `hostname` varchar(128) NOT NULL,
  `sku` varchar(128) NOT NULL,
  `uuic` varchar(128) NOT NULL,
  `serial` varchar(128) NOT NULL,
  `incorrect` tinyint(1) NOT NULL,
  `part` varchar(128) NOT NULL,
  `notes` longtext NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates table to handle resource permissions
--  Primary key: id
--  Unique key: resource
DROP TABLE IF EXISTS `resources`;
CREATE TABLE IF NOT EXISTS `resources` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `common_name` varchar(128) NOT NULL,
  `owner` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `resource` (`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Setup default permission based on default group
INSERT INTO `resources` (`resource`, `common_name`, `owner`) VALUES (sha1("admin"), "admin", "admin");

-- Creates table to handle resource permissions per groups
--  Primary key: id
--  Index: resource
--  Foreign key details:
--   resources_groups.resource updates from resources.resource
DROP TABLE IF EXISTS `resources_groups`;
CREATE TABLE IF NOT EXISTS `resources_groups` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `group` varchar(128) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `write` tinyint(1) NOT NULL,
  `owner` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`resource`),
  CONSTRAINT `fk_resource_groups` FOREIGN KEY (`resource`)
   REFERENCES `resources` (`resource`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates default entry permission per group based for default admin group
INSERT INTO `resources_groups` (`resource`, `group`, `read`, `write`, `owner`) VALUES (sha1("admin"), "admin", "1", "1", "admin");

-- Creates table to handle resouce permissions per user accounts
--  Primary key: id
--  Index: resource
--  Foreign key details:
--   resources_users.resource updates from resources.resource
DROP TABLE IF EXISTS `resources_users`;
CREATE TABLE IF NOT EXISTS `resources_users` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `resource` varchar(255) NOT NULL,
  `user` varchar(128) NOT NULL,
  `read` tinyint(1) NOT NULL,
  `write` tinyint(1) NOT NULL,
  `owner` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX (`resource`),
  CONSTRAINT `fk_resource_users` FOREIGN KEY (`resource`)
   REFERENCES `resources` (`resource`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Creates default entry permission per user based on default admin user
INSERT INTO `resources_users` (`resource`, `user`, `read`, `write`, `owner`) VALUES (sha1("admin"), "admin", "1", "1", "admin");

-- Creates table to handle authenticated session data
--  Primary key: id
--  Unique key: session_id
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `session_id` varchar(64) NOT NULL,
  `session_data` longtext NOT NULL,
  `session_expire` int(10) NOT NULL,
  `session_agent` varchar(64) NOT NULL,
  `session_ip` varchar(64) NOT NULL,
  `session_referrer` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `session_id` (`session_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE = utf8_general_ci AUTO_INCREMENT=0;

-- Re-enable the foreign key checks
SET foreign_key_checks = 1;
