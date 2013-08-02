DELIMITER //

DROP PROCEDURE IF EXISTS Configuration_keys_add//
CREATE DEFINER='licesning'@'localhost' PROCEDURE Configuration_keys_add(IN `countryName` VARCHAR(64), IN `stateOrProvinceName` VARCHAR(64), IN `localityName` VARCHAR(64), IN `organizationalName` VARCHAR(64), IN `organizationalUnitName` VARCHAR(64), IN `commonName` VARCHAR(64), IN `emailAddress` VARCHAR(64), IN `privateKey` LONGTEXT, IN `publicKey` LONGTEXT, IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates users key pair'
BEGIN
 INSERT INTO `configuration_openssl_keys` (`countryName`, `stateOrProvinceName`, `localityName`, `organizationalName`, `organizationalUnitName`, `commonName`, `emailAddress`, `privateKey`, `publicKey`) VALUES (countryName, stateOrProvinceName, localityName, organizationalName, organizationalUnitName, commonName, emailAddress, HEX(AES_ENCRYPT(privateKey, SHA1(sKey))), HEX(AES_ENCRYPT(publicKey, SHA1(sKey)))) ON DUPLICATE KEY UPDATE `countryName`=countryName, `stateOrProvinceName`=stateOrProvinceName, `localityName`=localityName, `organizationalName`=organizationalName, `organizationalUnitName`=organizationalUnitName, `commonName`=commonName, `emailAddress`=emailAddress, `privateKey`=HEX(AES_ENCRYPT(privateKey, SHA1(sKey))), `publicKey`=HEX(AES_ENCRYPT(publicKey, SHA1(sKey)));
END//

DROP PROCEDURE IF EXISTS Configuration_keys_get//
CREATE DEFINER='licesning'@'localhost' PROCEDURE Configuration_keys_get(IN `emailAddress` VARCHAR(64), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieves OpenSSL keypair by email address'
BEGIN
 SELECT `countryName`, `stateOrProvinceName`, `localityName`, `organizationalName`, `organizationalUnitName`, `commonName`, `emailAddress`, AES_DECRYPT(BINARY(UNHEX(privateKey)), SHA1(sKey)) AS privateKey, AES_DECRYPT(BINARY(UNHEX(publicKey)), SHA1(sKey)) AS publicKey FROM `configuration_openssl_keys` WHERE `emailAddress`=emailAddress;
END//

DELIMITER ;
