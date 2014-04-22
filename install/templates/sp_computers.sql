DELIMITER //

DROP PROCEDURE IF EXISTS `ComputerList`;
CREATE DEFINER=`{RI}`@`{SERVER}` PROCEDURE `ComputerList`()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'List all records'
BEGIN
 SELECT * FROM `viewInventoryComputers`;
END//

DROP PROCEDURE IF EXISTS `ComputerSearch`;
CREATE  DEFINER=`{RO}`@`{SERVER}` PROCEDURE `ComputerSearch`(IN `s` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Search for computer'
BEGIN
 SELECT * FROM `viewInventoryComputers` WHERE `Hostname` LIKE s OR `Model` LIKE s OR `SKU` LIKE s OR `UUIC` LIKE s OR `Serial` LIKE s ORDER BY `hostname` OR s LIKE `eowd` OR s LIKE `opd`;
END//

DROP PROCEDURE IF EXISTS `ComputerAddUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ComputerAddUpdate`(IN `h` CHAR(128), IN `m` CHAR(128), IN `s` CHAR(128), IN `u` CHAR(128), IN `sl` CHAR(128), IN `e` CHAR(32), IN `o` CHAR(32), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update computer'
BEGIN
  SET @hid = 1;
  SET @mid = 1;
  SELECT `id` INTO @mid FROM `models` WHERE `model` = m;
  SET @wid = 1;
  SELECT `id` INTO @wid FROM `hostnames` WHERE `hostname` = h;

  SELECT COUNT(*) FROM `hostnames` WHERE `hostname` = h INTO @hostname;
  SELECT COUNT(*) FROM `models` WHERE `model` = m INTO @model;
  SELECT COUNT(*) FROM `warranty` WHERE `eowd` = e AND `opd` = o INTO @warranty;

  IF (SELECT COUNT(*) FROM `viewInventoryComputers` WHERE `SKU` = s OR `UUIC` = u OR `Serial` = sl) <= 0 THEN

    IF (@hostname <= 0) THEN
      INSERT INTO `hostnames` (`hostname`) VALUES (h);
    ELSE
      SELECT `id` INTO @hid FROM `hostnames` WHERE `hostname` = h;
    END IF;

    IF (@warranty <= 0) THEN
      INSERT INTO `hostnames` (`hostname`) VALUES (h);
    ELSE

    END IF;


    INSERT INTO `computers` (`hostname`, `model`, `sku`, `uuic`, `serial`, `notes`) VALUES (h, m, s, u, sl, n);
    SELECT ROW_COUNT() AS affected;
  ELSE
    UPDATE `computers` SET `hostname`=h, `model`=m, `notes`=n WHERE `sku`=s AND `uuic`=u AND `serial`=sl;
    SELECT 2 AS affected;
  END IF;
END//

DROP PROCEDURE IF EXISTS `ComputerUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ComputerUpdate`(IN `i` BIGINT, IN `h` CHAR(128), IN `m` CHAR(128), IN `s` CHAR(128), IN `u` CHAR(128), IN `sl` CHAR(128), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update computer record'
BEGIN
 SELECT `hostname` INTO @Hostname FROM `computers` WHERE `id` = i;
 UPDATE `hostnames` SET `hostname` = h WHERE `hostname` = @Hostname LIMIT 1;
 SELECT ROW_COUNT() INTO @hostname_affected;
 UPDATE `computers` SET `hostname` = h, `model` = m, `sku` = s, `uuic` = u, `serial` = sl, `notes` = n WHERE `id` = i;
 SELECT ROW_COUNT() INTO @computer_affected;
 SELECT @hostname_affected AS hostname_affected, @computer_affected AS computer_affected;
END//

DROP PROCEDURE IF EXISTS `ComputerDelete`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ComputerDelete`(IN `i` BIGINT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete computer'
BEGIN
 DELETE FROM `computers` WHERE `id`=i;
 SELECT ROW_COUNT() AS affected;
END//

DELIMITER ;
