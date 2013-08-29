DELIMITER //

DROP PROCEDURE IF EXISTS `ModelList`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `ModelList`()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns models'
BEGIN
 SELECT * FROM `viewInventoryModels`;
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
 INSERT INTO `models` (`model`, `eowd`, `opd`, `description`, `notes`) VALUES (m, UNIX_TIMESTAMP(e), UNIX_TIMESTAMP(o), d, n) ON DUPLICATE KEY UPDATE `model`=m, `eowd`=UNIX_TIMESTAMP(e), `opd`=UNIX_TIMESTAMP(o), `description`=d, `notes`=n;
 SELECT ROW_COUNT() AS affected;
END//

DROP PROCEDURE IF EXISTS `ModelUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ModelUpdate`(IN `i` BIGINT, IN `m` CHAR(128), IN `e` CHAR(32), IN `o` CHAR(32), IN `d` CHAR(128), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update model'
BEGIN
 UPDATE `models` SET `model` = m, `eowd` = UNIX_TIMESTAMP(e), `opd` = UNIX_TIMESTAMP(o), `description` = d, `notes` = n WHERE `id` = i;
 SELECT ROW_COUNT() AS affected;
END//

DROP PROCEDURE IF EXISTS `ModelDelete`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ModelDelete`(IN `i` BIGINT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete model'
BEGIN
 DELETE FROM `models` WHERE `id`=i;
 SELECT ROW_COUNT() AS affected;
END//

DELIMITER ;