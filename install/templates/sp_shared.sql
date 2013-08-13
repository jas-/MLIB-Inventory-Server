DELIMITER //

DROP PROCEDURE IF EXISTS `InvSearch`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `InvSearch`(IN regex CHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns all computer & monitor records matching wildcard'
BEGIN
 SELECT c.model AS Model, c.hostname AS Computer, c.sku AS SKU, c.uuic AS UUIC, c.serial AS Serial, c.location AS Location, c.eowd AS EOWD, c.opd AS OPD, c.notes AS Notes, m.hostname AS Monitor, m.sku AS MSKU, m.serial AS MSerial, m.model AS MModel, m.mmodel AS MMModel, m.location AS MLocation, m.eowd AS MEOWD FROM `computers` c
  LEFT JOIN `monitors` m ON c.hostname = m.hostname
   WHERE c.hostname LIKE regex OR m.hostname LIKE regex OR c.sku LIKE regex OR c.uuic LIKE regex OR c.serial LIKE regex OR m.hostname LIKE regex OR m.sku LIKE regex OR m.serial LIKE regex OR c.location LIKE regex OR m.location LIKE regex OR c.model LIKE regex or m.mmodel LIKE regex OR m.model LIKE regex
    ORDER BY computer, monitor ASC;
END//

DROP PROCEDURE IF EXISTS `InvSearchDetails`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `InvSearchDetails`(IN regex CHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns details objects'
BEGIN
 SELECT c.hostname AS Computer, c.sku AS SKU, c.uuic AS UUIC, c.serial AS Serial, m.hostname AS Monitor, m.sku AS SKU, m.serial AS Serial FROM `computers` c
  LEFT JOIN `monitors` m ON c.hostname = m.hostname
   WHERE c.hostname LIKE regex OR m.hostname LIKE regex
    ORDER BY computer, monitor ASC;
END//

DROP PROCEDURE IF EXISTS `InvValid`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `InvValid`(IN `h` VARCHAR(128), IN `s` VARCHAR(128), IN `u` VARCHAR(128), IN `sl` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Validate computer/monitor record'
BEGIN
 SELECT COUNT(*) AS Computers FROM `computers` WHERE `sku`=s XOR `uuic`=u XOR `serial`=sl AND `hostname`=h;
 SELECT COUNT(*) AS Monitors FROM `monitors` WHERE `sku`=s XOR `uuic`=u XOR `serial`=sl AND `hostname`=h;
END//

DELIMITER ;
