DELIMITER //

-- Display computer devices using Read-Only view
-- This procedure limits user access to raw data in the event of a
-- SQL injection vulnerability
-- Please see viewInventoryComputers view
DROP PROCEDURE IF EXISTS `ComputerList`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `ComputerList`()
 DETERMINISTIC
 SQL SECURITY DEFINER
 COMMENT 'List all records'
BEGIN
 SELECT * FROM `viewInventoryComputers`;
END//

-- Search computer devices using Read-Only view
-- This procedure limits user access to raw data in the event of a
-- SQL injection vulnerability
-- Please see viewInventoryComputers view
DROP PROCEDURE IF EXISTS `ComputerSearch`;
CREATE  DEFINER=`{RO}`@`{SERVER}` PROCEDURE `ComputerSearch`(IN `s` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY DEFINER
 COMMENT 'Search for computer'
BEGIN
 SELECT * FROM `viewInventoryComputers` WHERE `Hostname` LIKE s OR `Model` LIKE s OR `SKU` LIKE s OR `UUIC` LIKE s OR `Serial` LIKE s OR `eowd` LIKE UNIX_TIMESTAMP(s) OR `opd` LIKE UNIX_TIMESTAMP(s) ORDER BY `hostname` ASC;
END//

-- Add/Updates computer record
-- Creates new entries for hostname, models & warranty tables if they don't alreay exist
-- Args: hostname, model, sku, uuic, serial, eowd (end of warranty date), opd (original purchase date) & notes
DROP PROCEDURE IF EXISTS `ComputerAddUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ComputerAddUpdate`(IN `h` CHAR(128), IN `m` CHAR(128), IN `s` CHAR(128), IN `u` CHAR(128), IN `sl` CHAR(128), IN `e` CHAR(32), IN `o` CHAR(32), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update computer'
BEGIN

  -- Lookup existing ID for hostname, model & warranty
  SELECT `id` INTO @hid FROM `hostnames` WHERE `hostname` = h;
  SELECT `id` INTO @mid FROM `models` WHERE `model` = m;
  SELECT `id` INTO @wid FROM `warranty` WHERE `eowd` = UNIX_TIMESTAMP(e) AND `opd` = UNIX_TIMESTAMP(o);

  -- Determine if we are going to add or update a record
  SELECT COUNT(*) INTO @exists FROM `computers` WHERE `sku` = s OR `serial` = sl;

  -- If an id doesn't exist for the hostname create one
  IF (@hid <= 0 OR @hid = '' OR @hid IS NULL) THEN
    INSERT INTO `hostnames` (`hostname`) VALUES (h);
    SELECT `id` INTO @hid FROM `hostnames` WHERE `hostname` = h;
  END IF;

  -- If an id doesn't exist for the model create one
  IF (@mid <= 0 OR @mid = '' OR @mid IS NULL) THEN
    INSERT INTO `models` (`model`) VALUES (m);
    SELECT `id` INTO @mid FROM `models` WHERE `model` = m;
  END IF;

  -- If an id doesn't exist for the warranty create one
  IF (@wid <= 0 OR @wid = '' OR @wid IS NULL) THEN
    INSERT INTO `warranty` (`eowd`, `opd`) VALUES (UNIX_TIMESTAMP(e), UNIX_TIMESTAMP(o));
    SELECT `id` INTO @wid FROM `warranty` WHERE `eowd` = UNIX_TIMESTAMP(e) AND `opd` = UNIX_TIMESTAMP(o);
  END IF;

  -- Add or update the computer record
  IF (@exists <= 0) THEN
    INSERT INTO `computers` (`hostname`, `model`, `sku`, `uuic`, `serial`, `warranty`, `notes`) VALUES (@hid, @mid, s, u, sl, @wid, n);
    SELECT ROW_COUNT() AS affected;
  ELSE
    UPDATE `computers` SET `hostname`=@hid, `model`=@mid, `warranty`=@wid, `notes`=n WHERE `sku`=s AND `uuic`=u AND `serial`=sl;
    SELECT 2 AS affected;
  END IF;
END//

-- Add/Updates computer record
-- Updates existing computer, hostname, model & warranty information
-- Args: id, hostname, model, sku, uuic, serial, eowd (end of warranty date), opd (original purchase date)
DROP PROCEDURE IF EXISTS `ComputerUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ComputerUpdate`(IN `i` BIGINT, IN `h` CHAR(128), IN `m` CHAR(128), IN `s` CHAR(128), IN `u` CHAR(128), IN `sl` CHAR(128), IN `e` CHAR(32), IN `o` CHAR(32), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Update computer record'
ComputerUpdate:BEGIN

  -- Lookup existing ID for hostname, model & warranty
  SELECT `hostname` INTO @hid FROM `computers` WHERE `id` = i;
  SELECT `model` INTO @mid FROM `computers` WHERE `id` = i;
  SELECT `warranty` INTO @wid FROM `computers` WHERE `id` = i;

  -- Does a record exist matching the id given?
  SELECT COUNT(*) INTO @exists FROM `computers` WHERE `id` = i;

  -- If a hostname record exists update it
  IF (@hid > 0 OR @hid != '' OR @hid IS NOT NULL) THEN
    UPDATE `hostnames` SET `hostname` = h WHERE `id` = @hid;
  ELSE
    SELECT -1 AS affected;
    LEAVE ComputerUpdate;
  END IF;

  -- If a model record exists update it
  IF (@mid > 0 OR @mid != '' OR @mid IS NOT NULL) THEN
    UPDATE `models` SET `model` = m WHERE `id` = @mid;
  ELSE
    SELECT -1 AS affected;
    LEAVE ComputerUpdate;
  END IF;

  -- If a warranty record exists update it
  IF (@wid > 0 OR @wid != '' OR @wid IS NOT NULL) THEN
    UPDATE `warranty` SET `eowd` = UNIX_TIMESTAMP(e), `opd` = UNIX_TIMESTAMP(o) WHERE `id` = @wid;
  ELSE
    SELECT -1 AS affected;
    LEAVE ComputerUpdate;
  END IF;

  -- Update the computer record
  IF (@exists > 0) THEN
    UPDATE `computers` SET `hostname`=@hid, `model`=@mid, `sku`=s, `uuic`=u, `serial`=sl, `warranty`=@wid, `notes`=n WHERE `id`=i;
    SELECT ROW_COUNT() AS affected;
  ELSE
    SELECT -1 AS affected;
    LEAVE ComputerUpdate;
  END IF;

END//

-- Remove computer record by ID
-- Args: id
DROP PROCEDURE IF EXISTS `ComputerDelete`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `ComputerDelete`(IN `i` BIGINT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete computer'
BEGIN

  -- Lookup existing ID for hostname, model & warranty
  SELECT `hostname` INTO @hid FROM `computers` WHERE `id` = i;

  -- If a hostname record exists remove it
  IF (@hid > 0 OR @hid != '' OR @hid IS NOT NULL) THEN
    DELETE FROM `hostnames` WHERE `id` = @hid;
    SELECT ROW_COUNT() AS affected;
  ELSE
    SELECT -1 AS affected;
  END IF;

END//

DELIMITER ;
