DELIMITER //

-- Create a query validator to be used with other procedures/functions
-- to help prevent SQL injections
DROP FUNCTION IF EXISTS `checkQuery`//
CREATE DEFINER=`inventory2011`@`localhost` FUNCTION `checkQuery`(`sqlStmt` VARCHAR(1024)) RETURNS INT(1)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Tests query for invalid commands'
BEGIN
 DECLARE invalid VARCHAR(1024) DEFAULT '(SUBSTRING|COLLATE|HEX|BIN|CONCAT|LOAD DATA INFILE|LOAD_FILE|CHAR|DROP|\;|\-\-|HAVING|1=1|CAST|CONVERT|UNION ALL SELECT|UNION SELECT|BENCHMARK|WAITFOR|\#|\@\@|SHUTDOWN|WAIT|VERSION)';
 DECLARE x INT(1) DEFAULT 0;
 SELECT sqlStmt REGEXP invalid INTO x;
 RETURN x;
END//

-- Create a function to generate a random key
-- to be used for AES encryption of field data
DROP FUNCTION IF EXISTS `randomPasswordGenerator`//
CREATE DEFINER=`inventory2011`@`localhost` FUNCTION `randomPasswordGenerator`() RETURNS varchar(64) CHARSET utf8
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Creates and returns a random 256 character string'
BEGIN
 DECLARE charCount TINYINT(1) DEFAULT 0;
 DECLARE charDiceRoll TINYINT(2);
 DECLARE randomChar CHAR(1);
 DECLARE randomPassword CHAR(128) DEFAULT '';
 REPEAT
  SET charCount = charCount + 1;
  SET charDiceRoll = 1 + FLOOR(RAND() * 94);
  IF (charDiceRoll <= 32) THEN
   SET randomChar = ELT(charDiceRoll,'`', '~', '!', '@', '#', '$', '%', '^','&', '*', '(', ')', '-', '=', '_', '+','[', ']', '{', '}', '\\', '/', '|', '?',';', ':', '\'', '"', ',', '.', '<', '>');
  ELSEIF (charDiceRoll >= 33) AND (charDiceRoll <= 68) THEN
   SET charDiceRoll = charDiceRoll - 33;
   SET randomChar = CONV(charDiceRoll, 10, 36);
  ELSE
   SET charDiceRoll = charDiceRoll - 59;
   SET randomChar = LOWER(CONV(charDiceRoll, 10, 36));
  END IF;
  SET randomPassword = CONCAT(randomPassword, randomChar);
 UNTIL (charCount = 64)
 END REPEAT;
 RETURN randomPassword;
END//

-- Creates a function to determine if a key exists and creates one
-- if it does not exist
DROP PROCEDURE IF EXISTS `setKey`//
CREATE DEFINER=`inventory2011`@`localhost` PROCEDURE `setKey`(OUT `n` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Creates new random key and saves to defaults table'
BEGIN
 DECLARE x INT DEFAULT 0;
 DECLARE nKey VARCHAR(128) DEFAULT '';
 SELECT COUNT(*) FROM `configuration_defaults` WHERE `defaultKey`='' INTO x;
 IF (x > 0) THEN
  SELECT randomPasswordGenerator() INTO nKey;
  UPDATE `configuration_defaults` SET `defaultKey`=nKey WHERE `defaultKey`='';
 ELSE
  SELECT randomPasswordGenerator() INTO nKey;
  INSERT INTO `configuration_defaults` (`defaultKey`) VALUES (nKey);
 END IF;
 SET @n = ROW_COUNT();
END//

-- Returns a configured AES encryption key to be used with encrypted
-- fields of database tables. It will call a setKey procedure if none is found
DROP FUNCTION IF EXISTS `getKey`//
CREATE DEFINER=`inventory2011`@`localhost` FUNCTION `getKey`() RETURNS VARCHAR(128) CHARSET utf8
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Gets the random key'
BEGIN
 DECLARE c INT DEFAULT 0;
 DECLARE nKey VARCHAR(128) DEFAULT '0';
 SELECT COUNT(*) FROM `configuration_defaults` WHERE `defaultKey`!='' INTO c;
 IF (c > 0) THEN
  SELECT `defaultKey` FROM `configuration_defaults` INTO nKey;
 ELSE
  CALL setKey(@x);
  SELECT `defaultKey` FROM `configuration_defaults` INTO nKey;
 END IF;
 RETURN nKey;
END//

DELIMITER ;
