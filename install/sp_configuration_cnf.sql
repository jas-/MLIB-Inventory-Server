DELIMITER //

DROP PROCEDURE IF EXISTS Configuration_cnf_add//
CREATE DEFINER='licesning'@'localhost' PROCEDURE Configuration_cnf_add(IN `config` VARCHAR(64), IN `encrypt_key` INT(1), IN `private_key_type` VARCHAR(64), IN `digest_algorithm` VARCHAR(64), IN `private_key_bits` INT(4), IN `x509_extensions` VARCHAR(32), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or updates OpenSSL configuration'
BEGIN
 SET x=0;
 SELECT COUNT(*) INTO x FROM `configuration_openssl_cnf`;
 IF x>0 THEN
  INSERT INTO `configuration_openssl_cnf` (`config`, `encrypt_key`, `private_key_type`, `digest_algorithm`, `private_key_bits`, `x509_extensions`) VALUES (config, encrypt_key, private_key_type, digest_algorithm, private_key_bits, x509_extensions) ON DUPLICATE KEY UPDATE `config`=config, `encrypt_key`=encrypt_key, `private_key_type`=private_key_type, `digest_algorithm`=digest_algorithm, `x509_extennsions`=x509_extensions;
 ELSE
  UPDATE `configuration_openssl_cnf` SET `config`=config, `encrypt_key`=encrypt_key, `private_key_type`=private_key_type, `digest_algorithm`=digest_algorithm, `x509_extennsions`=x509_extensions;
 END IF;
 SET x=ROW_COUNT();
 SELECT x;
END//

DROP PROCEDURE IF EXISTS Configuration_cnf_get//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Configuration_cnf_get()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieves OpenSSL configuration'
BEGIN
 SELECT `config`,`encrypt_key`,`private_key_type`,`digest_algorithm`,`private_key_bits`,`x509_extensions`,`encrypt_key_cipher` FROM `configuration_openssl_cnf`;
END//

DELIMITER ;
