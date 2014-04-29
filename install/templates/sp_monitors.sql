DELIMITER //

-- Display monitor devices using Read-Only view
-- This procedure limits user access to raw data in the event of a
-- SQL injection vulnerability
-- Please see viewInventoryMonitors view
DROP PROCEDURE IF EXISTS `MonitorList`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `MonitorList`()
 DETERMINISTIC
 SQL SECURITY DEFINER
 COMMENT 'List all records'
BEGIN
 SELECT * FROM `viewInventoryMonitors`;
END//

-- Search monitor devices using Read-Only view
-- This procedure limits user access to raw data in the event of a
-- SQL injection vulnerability
-- Please see viewInventoryMonitors view
DROP PROCEDURE IF EXISTS `MonitorSearch`;
CREATE  DEFINER=`{RO}`@`{SERVER}` PROCEDURE `MonitorSearch`(IN `s` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY DEFINER
 COMMENT 'Search for monitor'
BEGIN
 SELECT * FROM `viewInventoryMonitors` WHERE `Hostname` LIKE s OR `Model` LIKE s OR `SKU` LIKE s OR `Serial` LIKE s OR `eowd` LIKE UNIX_TIMESTAMP(s) OR `opd` LIKE UNIX_TIMESTAMP(s) ORDER BY `hostname` ASC;
END//

-- Add/Updates monitor record
-- Creates new entries for hostname, models & warranty tables if they don't alreay exist
-- Args: hostname, model, sku, uuic, serial, eowd (end of warranty date), opd (original purchase date) & notes
DROP PROCEDURE IF EXISTS `MonitorAddUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `MonitorAddUpdate`(IN `h` CHAR(128), IN `m` CHAR(128), IN `s` CHAR(128), IN `sl` CHAR(128), IN `e` CHAR(32), IN `o` CHAR(32), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update monitor'
BEGIN

  -- Lookup existing ID for hostname, model & warranty
  SELECT `id` INTO @hid FROM `hostnames` WHERE `hostname` = h;
  SELECT `id` INTO @mid FROM `models` WHERE `model` = m;
  SELECT `id` INTO @wid FROM `warranty` WHERE `eowd` = UNIX_TIMESTAMP(e) AND `opd` = UNIX_TIMESTAMP(o);

  -- Determine if we are going to add or update a record
  SELECT COUNT(*) INTO @exists FROM `monitors` WHERE `SKU` = s OR `Serial` = sl;

  -- If an id doesn't exist for the hostname create one
  IF (@hid <= 0 OR @hid = '' OR @hid IS NULL) THEN
    INSERT INTO `hostnames` (`hostname`) VALUES (h) ON DUPLICATE KEY UPDATE `hostname` = h;
    SELECT LAST_INSERT_ID() INTO @hid;
  END IF;

  -- If an id doesn't exist for the model create one
  IF (@mid <= 0 OR @mid = '' OR @mid IS NULL) THEN
    INSERT INTO `models` (`model`) VALUES (m) ON DUPLICATE KEY UPDATE `model` = m;
    SELECT LAST_INSERT_ID() INTO @mid;
  END IF;

  -- If an id doesn't exist for the warranty create one
  IF (@wid <= 0 OR @wid = '' OR @wid IS NULL) THEN
    INSERT INTO `warranty` (`eowd`, `opd`) VALUES (UNIX_TIMESTAMP(e), UNIX_TIMESTAMP(o)) ON DUPLICATE KEY UPDATE `eowd` = UNIX_TIMESTAMP(e), `opd` = UNIX_TIMESTAMP(o);
    SELECT LAST_INSERT_ID() INTO @wid;
  END IF;

  -- Add or update the monitor record
  IF (@exists <= 0) THEN
    INSERT INTO `monitors` (`hostname`, `model`, `sku`, `serial`, `warranty`, `notes`) VALUES (@hid, @mid, s, sl, @wid, n);
    SELECT ROW_COUNT() AS affected;
  ELSE
    UPDATE `monitors` SET `hostname`=@hid, `model`=@mid, `warranty`=@wid, `notes`=n WHERE `sku`=s AND `serial`=sl;
    SELECT 2 AS affected;
  END IF;
END//

-- Add/Updates monitor record
-- Updates existing monitor, hostname, model & warranty information
-- Args: id, hostname, model, sku, uuic, serial, eowd (end of warranty date), opd (original purchase date)
DROP PROCEDURE IF EXISTS `MonitorUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `MonitorUpdate`(IN `i` BIGINT, IN `h` CHAR(128), IN `m` CHAR(128), IN `s` CHAR(128), IN `sl` CHAR(128), IN `e` CHAR(32), IN `o` CHAR(32), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update monitor record'
MonitorUpdate:BEGIN

  -- Lookup existing ID for hostname, model & warranty
  SELECT `hostname` INTO @hid FROM `monitors` WHERE `id` = i;
  SELECT `id` INTO @mid FROM `models` WHERE `model` = m;
  SELECT `warranty` INTO @wid FROM `monitors` WHERE `warranty` = i;

  -- Does a record exist matching the id given?
  SELECT COUNT(*) INTO @exists FROM `monitors` WHERE `id` = i;

  -- If a hostname record exists update it
  IF (@hid > 0 OR @hid != '' OR @hid IS NOT NULL) THEN
    UPDATE `hostnames` SET `hostname` = h WHERE `id` = @hid;
  ELSE
    INSERT INTO `hostnames` (`hostname`) VALUES (h) ON DUPLICATE KEY UPDATE `hostname` = h;
    SELECT LAST_INSERT_ID() INTO @hid;
  END IF;

  -- If a model record exists update it
  IF (@mid > 0 OR @mid != '' OR @mid IS NOT NULL) THEN
    UPDATE `models` SET `model` = m WHERE `id` = @mid;
  ELSE
    INSERT INTO `models` (`model`) VALUES (m) ON DUPLICATE KEY UPDATE `model` = m;
    SELECT LAST_INSERT_ID() INTO @mid;
  END IF;

  -- If a warranty record exists update it
  IF (@wid > 0 OR @wid != '' OR @wid IS NOT NULL) THEN
    UPDATE `warranty` SET `eowd` = UNIX_TIMESTAMP(e), `opd` = UNIX_TIMESTAMP(o) WHERE `id` = @wid;
  ELSE
    INSERT INTO `warranty` (`eowd`, `opd`) VALUES (UNIX_TIMESTAMP(e), UNIX_TIMESTAMP(o)) ON DUPLICATE KEY UPDATE `eowd`=UNIX_TIMESTAMP(e), `opd`=UNIX_TIMESTAMP(o);
    SELECT LAST_INSERT_ID() INTO @wid;
  END IF;

  -- Update the monitor record
  IF (@exists > 0) THEN
    SET @sql = CONCAT('UPDATE `monitors` SET `hostname`=@hid, `model`=@mid, `sku`=',s,', `serial`=',sl,', `warranty`=@wid, `notes`=',n,' WHERE `id`=',i,'');
    UPDATE `monitors` SET `hostname`=@hid, `model`=@mid, `sku`=s, `serial`=sl, `warranty`=@wid, `notes`=n WHERE `id`=i;
    SELECT ROW_COUNT() AS affected;
  ELSE
    SELECT -1 AS affected;
    LEAVE MonitorUpdate;
  END IF;

END//

-- Remove monitor record by ID
-- Args: id
DROP PROCEDURE IF EXISTS `MonitorDelete`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `MonitorDelete`(IN `i` BIGINT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete monitor'
BEGIN

  -- Lookup existing ID for hostname
  SELECT `hostname` INTO @hid FROM `monitors` WHERE `id` = i;

  -- If a hostname record exists remove it
  IF (@hid > 0 OR @hid != '' OR @hid IS NOT NULL) THEN
    DELETE FROM `hostnames` WHERE `id` = @hid;
    SELECT ROW_COUNT() AS affected;
  ELSE
    SELECT -1 AS affected;
  END IF;

END//

DELIMITER ;
