DELIMITER //

-- Use compression?
DROP PROCEDURE IF EXISTS Crud_USECompression//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Crud_USECompression(OUT n INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Is compress enabled?'
BEGIN
 SELECT `compression` FROM `configuration`;
END//

-- Use encryption?
DROP PROCEDURE IF EXISTS Crud_USEEncryption//
CREATE DEFINER='inventory2011'@'localhost' PROCEDURE Crud_USEEncryption(OUT n INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Is encryption enabled?'
BEGIN
 SELECT `encryption` FROM `configuration`;
END//

DELIMITER ;
