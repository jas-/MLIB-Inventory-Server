DELIMITER //

DROP PROCEDURE IF EXISTS Users_add//
CREATE DEFINER='jas'@'localhost' PROCEDURE Users_add(IN `email` VARCHAR(128), IN `password` LONGTEXT, IN `lvl` VARCHAR(40), IN `grp` VARCHAR(128), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates user accounts'
BEGIN
 INSERT INTO `authentication` (`email`, `password`, `level`, `group`) VALUES (HEX(AES_ENCRYPT(email, SHA1(sKey))), HEX(AES_ENCRYPT(password, SHA1(sKey))), HEX(AES_ENCRYPT(lvl, SHA1(sKey))), HEX(AES_ENCRYPT(grp, SHA1(sKey)))) ON DUPLICATE KEY UPDATE `email`=HEX(AES_ENCRYPT(email, SHA1(sKey))), `password`=HEX(AES_ENCRYPT(password, SHA1(sKey))), `level`=HEX(AES_ENCRYPT(lvl, SHA1(sKey))), `group`=HEX(AES_ENCRYPT(grp, SHA1(sKey)));
END//

DROP PROCEDURE IF EXISTS Users_verify//
CREATE DEFINER='jas'@'localhost' PROCEDURE Users_verify(IN `email` VARCHAR(128), IN `password` LONGTEXT, IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Verifies user account for authentication'
BEGIN
 SELECT COUNT(*) FROM `configuration_openssl_keys` WHERE `email`=HEX(AES_ENCRYPT(email, SHA1(sKey))) AND `password`=HEX(AES_ENCRYPT(password, SHA1(sKey)));
END//

DELIMITER ;

--SELECT COUNT(AES_DECRYPT(BINARY(UNHEX(email)), SHA1(sKey)), AES_DECRYPT(BINARY(UNHEX(password)), SHA1(sKey))) FROM `configuration_openssl_keys` WHERE `email`=HEX(AES_ENCRYPT(email, SHA1(sKey))) AND `password`=HEX(AES_ENCRYPT(password, SHA1(sKey)));
