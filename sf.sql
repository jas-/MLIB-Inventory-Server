DELIMITER //
DROP PROCEDURE IF EXISTS `checkQuery`//
CREATE DEFINER=`inventoryAdmin`@`localhost` PROCEDURE `checkQuery`(`sqlStmt` VARCHAR(1024))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Tests query for invalid commands'
BEGIN
 DECLARE invalid VARCHAR(1024) DEFAULT '(SUBSTRING|COLLATE|HEX|BIN|CONCAT|LOAD DATA INFILE|LOAD_FILE|CHAR|DROP|\;|\-\-|HAVING|1=1|CAST|CONVERT|UNION ALL SELECT|UNION SELECT|BENCHMARK|WAITFOR|\#|\@\@|SHUTDOWN|WAIT|VERSION)';
 DECLARE x INT(1) DEFAULT 0;
 SELECT sqlStmt REGEXP invalid INTO x;
 IF x = 1
 THEN
  SELECT x;
 ELSE
  SET @sql=sqlStmt;
  PREPARE stmt FROM @sql;
  EXECUTE stmt;
  DEALLOCATE PREPARE stmt;
 END IF;
END//
DELIMITER ;

DELIMITER //
DROP PROCEDURE IF EXISTS `PerLab`//
CREATE DEFINER=`inventoryAdmin`@`localhost` PROCEDURE `PerLab`(IN regex CHAR(32))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Returns details objects'
BEGIN
 SELECT c.hostname AS Computer, c.sku AS SKU, c.uuic AS UUIC, c.serial AS Serial, m.hostname AS Monitor, m.sku AS SKU, m.serial AS Serial FROM `inventory_computer` c
  LEFT JOIN `inventory_monitors` m ON c.hostname = m.hostname
   WHERE c.hostname LIKE regex OR m.hostname LIKE regex
    ORDER BY computer, monitor ASC;
END//
DELIMITER ;