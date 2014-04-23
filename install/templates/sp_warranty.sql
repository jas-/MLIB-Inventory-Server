DELIMITER //

DROP PROCEDURE IF EXISTS `WarrantyList`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `WarrantyList`()
 DETERMINISTIC
 SQL SECURITY DEFINER
 COMMENT 'Returns warranty'
BEGIN
 SELECT * FROM `viewInventoryWarranty`;
END//

DROP PROCEDURE IF EXISTS `WarrantySearch`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `WarrantySearch`(IN `s` CHAR(128))
 DETERMINISTIC
 SQL SECURITY DEFINER
 COMMENT 'Search computer records by warranty'
BEGIN
  SET @search = UNIX_TIMESTAMP(s);
  SELECT * FROM `warranty` WHERE `eowd` LIKE @s OR `opd` LIKE @s;
END//

DROP PROCEDURE IF EXISTS `WarrantyAddUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `WarrantyAddUpdate`(IN `e` CHAR(32), IN `o` CHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update warranty'
BEGIN
  SET @eowd = UNIX_TIMESTAMP(e);
  SET @opd = UNIX_TIMESTAMP(o);
  INSERT INTO `warranty` (`eowd`, `opd`) VALUES (@eowd, @opd) ON DUPLICATE KEY UPDATE `eowd`=@eowd, `opd`=@opd;
  SELECT ROW_COUNT() AS affected;
END//

DROP PROCEDURE IF EXISTS `WarrantyUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `WarrantyUpdate`(IN `i` BIGINT, IN `e` CHAR(32), IN `o` CHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update warranty'
BEGIN
  SET @eowd = UNIX_TIMESTAMP(e);
  SET @opd = UNIX_TIMESTAMP(o);
  UPDATE `warranty` SET `eowd` = @eowd, `opd` = @opd WHERE `id` = i LIMIT 1;
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
