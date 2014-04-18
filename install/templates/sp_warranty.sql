DELIMITER //

DROP PROCEDURE IF EXISTS `WarrantyList`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `WarrantyList`()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns warranty'
BEGIN
 SELECT * FROM `viewInventoryWarrantys`;
END//

DROP PROCEDURE IF EXISTS `WarrantySearch`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `WarrantySearch`(IN `s` CHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Search computer records by warranty'
BEGIN
 SELECT * FROM `warranty` WHERE `eowd` LIKE s OR `opd` LIKE s;
END//

DROP PROCEDURE IF EXISTS `WarrantyAddUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `WarrantyAddUpdate`(IN `e` CHAR(32), IN `o` CHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update warranty'
BEGIN
 INSERT INTO `warranty` (`eowd`, `opd`) VALUES (e, o) ON DUPLICATE KEY UPDATE `eowd`=e, `opd`=o;
 SELECT ROW_COUNT() AS affected;
END//

DROP PROCEDURE IF EXISTS `WarrantyUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `WarrantyUpdate`(IN `i` BIGINT, IN `e` CHAR(32), IN `o` CHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update warranty'
BEGIN
 UPDATE `warranty` SET `eowd` = e, `opd` = o WHERE `id` = i LIMIT 1;
 SELECT ROW_COUNT() AS affected;
END//

DROP PROCEDURE IF EXISTS `WarrantyDelete`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `WarrantyDelete`(IN `i` BIGINT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete warranty'
BEGIN
 DELETE FROM `warranty` WHERE `id` = i;
 SELECT ROW_COUNT() AS affected;
END//

DELIMITER ;