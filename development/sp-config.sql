DELIMITER //

-- Config_ exists?
DROP PROCEDURE IF EXISTS Config_Check//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_Check(OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Config_ settings exist?'
BEGIN
 SELECT COUNT(`id`) INTO @x FROM `configuration`;
 SELECT @x;
END//

-- Use compression?
DROP PROCEDURE IF EXISTS Config_USECompression//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_USECompression(OUT n INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Is compress enabled?'
BEGIN
 SELECT `compression` FROM `configuration`;
END//

-- Use encryption?
DROP PROCEDURE IF EXISTS Config_USEEncryption//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_USEEncryption(OUT n INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Is encryption enabled?'
BEGIN
 SELECT `encryption` FROM `configuration`;
END//

-- Update configuration
DROP PROCEDURE IF EXISTS Config_Update//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_Update(IN `title` VARCHAR(128), IN `templates` VARCHAR(255), IN `cache` VARCHAR(255), IN `private` BOOLEAN, IN `email` VARCHAR(45), IN `timeout` INT(10), IN `pkey` LONGTEXT, IN `pvkey` LONGTEXT, IN `pass` VARCHAR(64))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update configuration'
BEGIN
 DECLARE x INT;
 SET x = 0;
 SELECT COUNT(*) INTO x FROM `configuration`;
 IF x > 0 THEN
  UPDATE `configuration` SET `title`=title, `templates`=templates, `cache`=cache, `private`=private, `email`=email, `timeout`=timeout, `pkey`=pkey, `pvkey`=pvkey, `pass`=pass WHERE `id`=1;
 ELSE
  INSERT INTO `configuration` (`title`, `templates`, `cache`, `private`, `email`, `timeout`, `pkey`, `pvkey`, `pass`) VALUES (title, templates, cache, private, email, timeout, pkey, pvkey, pass);
 END IF;
 SET x = ROW_COUNT();
 SELECT x;
END//

-- Get configuration options
DROP PROCEDURE IF EXISTS Config_Select//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_Select()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Select configuration data'
BEGIN
 SELECT * FROM `configuration`;
END//

-- Add/Update OpenSSL CNF options
DROP PROCEDURE IF EXISTS Config_OpenSSLCNFUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_OpenSSLCNFUpdate(IN `config` VARCHAR(64), IN `encrypt_key` VARCHAR(64), IN `private_key_type` VARCHAR(64), IN `digest_algorithm` VARCHAR(64), IN `private_key_bits` INT(4), IN `x509_extensions` VARCHAR(32), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update OpenSSL CNF options'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `configuration_openssl_cnf`;
 IF @x > 0 THEN
  UPDATE `configuration_openssl_cnf` SET `config`=config, `encrypt_key`=encrypt_key, `private_key_type`=private_key_type, `digest_algorithm`=digest_algorithm, `private_key_bits`=private_key_bits, `x509_extensions`=x509_extensions WHERE `id`=1;
 ELSE
  INSERT INTO `configuration_openssl_cnf` (`config`, `encrypt_key`, `private_key_type`, `digest_algorithm`, `private_key_bits`, `x509_extensions`) VALUES (config, encrypt_key, private_key_type, digest_algorithm, private_key_bits, x509_extensions);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Get OpenSSL CNF configuration options
DROP PROCEDURE IF EXISTS Config_OpenSSLCNFSelect//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_OpenSSLCNFSelect()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Select OpenSSL CNF configuration data'
BEGIN
 SELECT `config`, `encrypt_key`, `private_key_type`, `digest_algorithm`, `private_key_bits`, `x509_extensions` FROM `configuration_openssl_cnf`;
END//

-- Add/Update OpenSSL DN options
DROP PROCEDURE IF EXISTS Config_OpenSSLDNUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_OpenSSLDNUpdate(IN `countryName` VARCHAR(64), IN `stateOrProvinceName` VARCHAR(64), IN `localityName` VARCHAR(64), IN `organizationName` VARCHAR(64), IN `organizationalUnitName` VARCHAR(64), IN `commonName` VARCHAR(64), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update OpenSSL DN options'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `configuration_openssl_dn`;
 IF @x > 0 THEN
  UPDATE `configuration_openssl_dn` SET `countryName`=countryName, `stateOrProvinceName`=stateOrProvinceName, `localityName`=localityName, `organizationName`=organizationName, `organizationalUnitName`=organizationalUnitName, `commonName`=commonName WHERE `id`=1;
 ELSE
  INSERT INTO `configuration_openssl_dn` (`countryName`, `stateOrProvinceName`, `localityName`, `organizationName`, `organizationalUnitName`, `commonName`) VALUES (countryName, stateOrProvinceName, localityName, organizationName, organizationalUnitName, commonName);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Get OpenSSL DN configuration options
DROP PROCEDURE IF EXISTS Config_OpenSSLDNSelect//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_OpenSSLDNSelect()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Select OpenSSL DN configuration data'
BEGIN
 SELECT `countryName`, `stateOrProvinceName`, `localityName`, `organizationName`, `organizationalUnitName`, `commonName` FROM `configuration_openssl_dn`;
END//

-- Update LDAP configuration
DROP PROCEDURE IF EXISTS Config_LDAPUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_LDAPUpdate(IN `key` VARCHAR(255), IN `domain` VARCHAR(255), IN `servers` VARCHAR(255), IN `port` INT(5), IN `username` VARCHAR(20), IN `password` VARCHAR(255), IN `bind_dn` VARCHAR(255), IN `base_dn` VARCHAR(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update LDAP configuration'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `configuration_ldap`;
 IF @x > 0 THEN
  UPDATE `configuration_ldap` SET `key`=@key, `domain`=@domain, `servers`=@servers, `port`=@port, `username`=@username, `password`=@password, `bind_dn`=@bind_dn, `base_dn`=@base_dn WHERE `id`=1;
 ELSE
  INSERT INTO `configuration_ldap` (`key`, `domain`, `servers`, `port`, `username`, `password`, `bind_dn`, `base_dn`) VALUES (@key, @salt, @domain, @servers, @port, @username, @password, @bind_dn, @base_dn);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

-- Update Memcache configuration
DROP PROCEDURE IF EXISTS Config_MemcacheUpdate//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Config_MemcacheUpdate(IN `server` VARCHAR(128), IN `port` INT(8), IN `timeout` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update Memcache configuration'
BEGIN
 SET @x = 0;
 SELECT COUNT(*) INTO @x FROM `configuration_memcache`;
 IF @x > 0 THEN
  UPDATE `configuration_memcache` SET `server`=@server, `port`=@port, `timeout`=@timeout WHERE `id`=1;
 ELSE
  INSERT INTO `configuration_memcache` (`server`, `port`, `timeout`) VALUES (@server, @port, @timeout);
 END IF;
 SET @x = ROW_COUNT();
 SELECT @x;
END//

DELIMITER ;
