DELIMITER //

DROP PROCEDURE IF EXISTS `ModelList`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `ModelList`(IN `b` INT(1))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns models'
BEGIN
 IF (b >= 1)
 THEN
  SELECT * FROM `models`;
 ELSE
  SELECT `model` FROM `models` ORDER BY `model`;
 END IF;
END//

DROP PROCEDURE IF EXISTS `ModelSearch`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `ModelSearch`(IN `s` CHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Search computer records by model'
BEGIN
 SELECT * FROM `models` WHERE `model` LIKE s OR `eowd` LIKE s OR `opd` LIKE s OR `description` LIKE s OR `notes` LIKE s ORDER BY `model`;
END//

DROP PROCEDURE IF EXISTS `ModelAddUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ModelAddUpdate`(IN `m` CHAR(128), IN `e` CHAR(32), IN `o` CHAR(32), IN `d` CHAR(128), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update model'
BEGIN
 INSERT INTO `models` (`model`, `eowd`, `opd`, `description`, `notes`) VALUES (m, e, o, d, n) ON DUPLICATE KEY UPDATE `model`=m, `eowd`=e, `opd`=o, `description`=d, `notes`=n;
 SELECT ROW_COUNT() AS affected;
END//

DROP PROCEDURE IF EXISTS `ModelDelete`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ModelDelete`(IN `id` INT(255))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete model'
BEGIN
 DELETE FROM `models` WHERE `id`=id;
 SELECT ROW_COUNT() AS affected;
END//

DELIMITER ;