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

  SELECT `id` INTO @hid FROM `hostnames` WHERE `hostname` = h;
  SELECT `id` INTO @mid FROM `models` WHERE `model` = m;
  SELECT `id` INTO @wid FROM `warranty` WHERE `eowd` = UNIX_TIMESTAMP(e) AND `opd` = UNIX_TIMESTAMP(o);

  SELECT COUNT(*) INTO @exists FROM `viewInventoryComputers` WHERE `SKU` = s OR `UUIC` = u OR `Serial` = sl;

  IF (@hid <= 0 OR @hid = '' OR @hid IS NULL) THEN
    INSERT INTO `hostnames` (`hostname`) VALUES (h);
    SELECT `id` INTO @hid FROM `hostnames` WHERE `hostname` = h;
  END IF;

  IF (@mid <= 0 OR @mid = '' OR @mid IS NULL) THEN
    INSERT INTO `models` (`model`) VALUES (m);
    SELECT `id` INTO @mid FROM `models` WHERE `model` = m;
  END IF;

  IF (@wid <= 0 OR @wid = '' OR @wid IS NULL) THEN
    INSERT INTO `warranty` (`eowd`, `opd`) VALUES (UNIX_TIMESTAMP(e), UNIX_TIMESTAMP(o));
    SELECT `id` INTO @wid FROM `warranty` WHERE `eowd` = UNIX_TIMESTAMP(e) AND `opd` = UNIX_TIMESTAMP(o);
  END IF;

  IF (@exists <= 0) THEN
    INSERT INTO `computers` (`hostname`, `model`, `sku`, `uuic`, `serial`, `warranty`, `notes`) VALUES (@hid, @mid, s, u, sl, @wid, n);
    SELECT ROW_COUNT() AS affected;
  ELSE
    UPDATE `computers` SET `hostname`=@hid, `model`=@mid, `warranty`=@wid, `notes`=n WHERE `sku`=s AND `uuic`=u AND `serial`=sl;
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
