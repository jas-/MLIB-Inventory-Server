DELIMITER //

DROP PROCEDURE IF EXISTS Inv_Search//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_Search(IN regex CHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns all computer & monitor records matching wildcard'
BEGIN
 SELECT c.model AS Model, c.hostname AS Computer, c.sku AS SKU, c.uuic AS UUIC, c.serial AS Serial, c.location AS Location, c.eowd AS EOWD, c.opd AS OPD, c.notes AS Notes, m.hostname AS Monitor, m.sku AS MSKU, m.serial AS MSerial, m.model AS MModel, m.mmodel AS MMModel, m.location AS MLocation, m.eowd AS MEOWD FROM `inventory_computer` c
  LEFT JOIN `inventory_monitors` m ON c.hostname = m.hostname
   WHERE c.hostname LIKE regex OR m.hostname LIKE regex OR c.sku LIKE regex OR c.uuic LIKE regex OR c.serial LIKE regex OR m.hostname LIKE regex OR m.sku LIKE regex OR m.serial LIKE regex OR c.location LIKE regex OR m.location LIKE regex OR c.model LIKE regex or m.mmodel LIKE regex OR m.model LIKE regex
    ORDER BY computer, monitor ASC;
END//

-- Return current inventory list
DROP PROCEDURE IF EXISTS Inv_List//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_List()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns current inventory list'
BEGIN
 SELECT `hostname` AS Computer, `sku` AS SKU, `serial` AS Serial, `uuic` AS UUIC, `model` AS Model, `location` AS Location, `eowd` AS EOWD, `opd` AS OPD, `notes` AS Notes FROM `inventory_computer` ORDER BY `hostname` ASC;
END//

DROP PROCEDURE IF EXISTS Inv_ListMonitor//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_ListMonitor(IN `h` CHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns monitor(s) by hostname'
BEGIN
 SELECT `hostname` AS Monitor, `sku` AS SKU, `serial` AS Serial, `model` AS Model, `mmodel` AS MModel, `location` AS Location, `eowd` AS EOWD FROM `inventory_monitors` WHERE `hostname` = h ORDER BY `hostname` ASC;
END//

-- Search computer by sku, uuic or serial
DROP PROCEDURE IF EXISTS Inv_ComputerSearch//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_ComputerSearch(IN `s` VARCHAR(128), IN `u` VARCHAR(128), IN `sl` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update computer'
BEGIN
 SELECT * FROM `inventory_computer` WHERE `sku` LIKE s OR `uuic` LIKE u OR `serial` LIKE sl;
END//

-- Add/Update computer
DROP PROCEDURE IF EXISTS Inv_ComputerAddUpdate//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_ComputerAddUpdate(IN `m` VARCHAR(128), IN `s` VARCHAR(128), IN `u` VARCHAR(128), IN `sl` VARCHAR(128), IN `h` VARCHAR(128), IN `l` VARCHAR(128), IN `e` VARCHAR(32), IN `o` VARCHAR(32), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update computer'
BEGIN
 IF (SELECT COUNT(*) FROM `inventory_computer` WHERE `sku` = s OR `uuic` = u OR `serial` = sl <= 0)
 THEN
  INSERT INTO `inventory_computer` (`model`, `sku`, `uuic`, `serial`, `hostname`, `location`, `eowd`, `opd`, `notes`) VALUES (m, s, u, sl, h, l, e, o, n);
  SELECT ROW_COUNT() AS affected;
 ELSE
  UPDATE `inventory_computer` SET `hostname`=h, `model`=m, `location`=l, `notes`=n WHERE `sku`=s AND `uuic`=u AND `serial`=sl;
  SELECT ROW_COUNT() AS affected;
 END IF;
END//

-- Delete computer
DROP PROCEDURE IF EXISTS Inv_ComputerDelete//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_ComputerDelete(IN `id` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete computer'
BEGIN
 DELETE FROM `inventory_computer` WHERE `id`=id;
 SELECT ROW_COUNT() AS affected;
END//

-- Search models
DROP PROCEDURE IF EXISTS Inv_ModelSearch//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_ModelSearch(IN `model` VARCHAR(128), IN `token` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Search computer records by model'
BEGIN
 SELECT `model` FROM `inventory_models`;
END//

-- Add/Update model
DROP PROCEDURE IF EXISTS Inv_ModelAddUpdate//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_ModelAddUpdate(IN `model` VARCHAR(128), IN `type` VARCHAR(128), IN `description` VARCHAR(128), IN `notes` LONGTEXT, OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update model'
BEGIN
 INSERT INTO `inventory_model` (`model`, `type`, `description`, `notes`) VALUES (model, type, description, notes) ON DUPLICATE KEY UPDATE `model`=model, `type`=type, `description`=description, `notes`=notes;
 SELECT ROW_COUNT() AS affected;
END//

-- Select all models
DROP PROCEDURE IF EXISTS Inv_ModelAll//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_ModelAll()
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns all models as array'
BEGIN
 SELECT `model` FROM `inventory_models`;
END//

-- Delete model
DROP PROCEDURE IF EXISTS Inv_ModelDelete//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_ModelDelete(IN `id` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete model'
BEGIN
 DELETE FROM `inventory_model` WHERE `id`=id;
 SELECT ROW_COUNT() AS affected;
END//

-- Add/Update monitors
DROP PROCEDURE IF EXISTS Inv_MonitorAddUpdate//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_MonitorAddUpdate(IN `s` VARCHAR(128), IN `sl` VARCHAR(128), IN `h` VARCHAR(128), IN `m` VARCHAR(128), IN `mm` VARCHAR(128), IN `l` VARCHAR(128), IN `e` VARCHAR(128), IN `o` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update monitor'
BEGIN
 IF (SELECT COUNT(*) FROM `inventory_monitors` WHERE `sku` = s OR `serial` = sl) <= 0
 THEN
  INSERT INTO `inventory_monitors` (`sku`, `serial`, `hostname`, `model`, `mmodel`, `location`, `eowd`, `order`) VALUES (s, sl, h, m, mm, l, e, o) ON DUPLICATE KEY UPDATE `sku`=s, `serial`=sl, `hostname`=h, `model`=m, `mmodel`=mm, `location`=l, `eowd`=e, `order`=o;
  SELECT ROW_COUNT() AS affected;
 ELSE
  UPDATE `inventory_monitors` SET `hostname`=h, `model`=m, `mmodel`=mm, `location`=l WHERE `sku`=s AND `serial`=sl;
  SELECT ROW_COUNT() AS affected;
 END IF;
END//

-- Delete monitor
DROP PROCEDURE IF EXISTS Inv_MonitorDelete//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_MonitorDelete(IN `id` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete monitor'
BEGIN
 DELETE FROM `inventory_monitors` WHERE `id`=id;
 SELECT ROW_COUNT() AS affected;
END//

-- Validate record
DROP PROCEDURE IF EXISTS Inv_ValidateRecord//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_ValidateRecord(IN `h` VARCHAR(128), IN `s` VARCHAR(128), IN `u` VARCHAR(128), IN `sl` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Validate computer/monitor record'
BEGIN
 SELECT COUNT(*) FROM `inventory_computer` WHERE `sku`=s XOR `uuic`=u XOR `serial`=sl AND `hostname`=h;
END//

-- Add/Update rma
DROP PROCEDURE IF EXISTS Inv_RMAAddUpdate//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_RMAAddUpdate(IN `d` VARCHAR(12), IN `h` VARCHAR(128), IN `s` VARCHAR(128), IN `u` VARCHAR(128), IN `sl` VARCHAR(128), IN `m` VARCHAR(128), IN `i` INT(1), IN `p` VARCHAR(128), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update rma'
BEGIN
 INSERT INTO `inventory_rma` (`date`, `hostname`, `sku`, `uuic`, `serial`, `model`, `incorrect`, `part`, `notes`) VALUES (d, s, sl, h, m, mm, l, e, o) ON DUPLICATE KEY UPDATE `sku`=s, `serial`=sl, `hostname`=h, `model`=m, `mmodel`=mm, `location`=l, `eowd`=e, `order`=o;
 SELECT ROW_COUNT() AS affected;
END//

-- Delete monitor
DROP PROCEDURE IF EXISTS Inv_RMADelete//
CREATE DEFINER='inventoryAdmin'@'localhost' PROCEDURE Inv_RMADelete(IN `id` INT(255), OUT `x` INT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete rma'
BEGIN
 DELETE FROM `inventory_rma` WHERE `id`=id LIMIT 1;
 SELECT ROW_COUNT() AS affected;
END//

DELIMITER ;
