DELIMITER //

DROP PROCEDURE IF EXISTS `MonitorList`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `MonitorList`()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns monitor list'
BEGIN
 SELECT * FROM `viewInventoryMonitors`;
END//

DROP PROCEDURE IF EXISTS `MonitorSearch`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `MonitorSearch`(IN `s` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Searches monitor records'
BEGIN
 SELECT * FROM `viewInventoryMonitors` WHERE `Hostname` LIKE s OR `Model` LIKE s OR `SKU` LIKE s OR `Serial` LIKE s ORDER BY `hostname`;
END//

DROP PROCEDURE IF EXISTS `MonitorAddUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `MonitorAddUpdate`(IN `h` CHAR(128), IN `m` CHAR(128), IN `s` CHAR(128), IN `sl` CHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update monitor'
BEGIN
 SELECT COUNT(*) FROM `models` WHERE `model` = m INTO @model;
 SELECT COUNT(*) FROM `hostnames` WHERE `hostname` = h INTO @hostname;

 IF (SELECT COUNT(*) FROM `monitors` WHERE `sku` = s OR `serial` = sl) <= 0
 THEN

  IF (@model <= 0)
  THEN
   INSERT INTO `models` (`model`) VALUES (m);
  END IF;

  IF (@hostname <= 0)
  THEN
   INSERT INTO `hostnames` (`hostname`) VALUES (h);
  END IF;

  INSERT INTO `monitors` (`hostname`, `model`, `sku`, `serial`) VALUES (h, m, s, sl) ON DUPLICATE KEY UPDATE `hostname`=h, `model`=m, `sku`=s, `serial`=sl;
  SELECT ROW_COUNT() AS affected;
 ELSE
  UPDATE `monitors` SET `hostname`=h, `model`=m WHERE `sku`=s AND `serial`=sl;
  SELECT 2 AS affected;
 END IF;
END//

DROP PROCEDURE IF EXISTS `MonitorUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `MonitorUpdate`(IN `i` BIGINT, IN `h` CHAR(128), IN `m` CHAR(128), IN `s` CHAR(128), IN `sl` CHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update computer record'
BEGIN
 SELECT `hostname` INTO @Hostname FROM `monitors` WHERE `id` = i;
 UPDATE `hostnames` SET `hostname` = h WHERE `hostname` = @Hostname;
 UPDATE `monitors` SET `hostname` = h, `model` = m, `sku` = s, `serial` = sl WHERE `id` = i;
 SELECT ROW_COUNT() AS affected;
END//

DROP PROCEDURE IF EXISTS `MonitorDelete`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `MonitorDelete`(IN `id` INT(255))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete monitor'
BEGIN
 DELETE FROM `monitors` WHERE `id`=id;
 SELECT ROW_COUNT() AS affected;
END//

DELIMITER ;