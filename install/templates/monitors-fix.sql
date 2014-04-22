DELIMITER //

DROP PROCEDURE IF EXISTS FIX//
CREATE DEFINER='root'@'localhost' PROCEDURE FIX()
 DETERMINISTIC
 MODIFIES SQL DATA
 SQL SECURITY INVOKER
 COMMENT 'Fix hostnames for monitor records'
BEGIN
 BLOCK1: begin
  DECLARE c BOOLEAN DEFAULT FALSE;

  DECLARE i BIGINT DEFAULT NULL;
  DECLARE h CHAR(128) DEFAULT NULL;
  DECLARE m CHAR(128) DEFAULT NULL;
  DECLARE s CHAR(128) DEFAULT NULL;
  DECLARE sl CHAR(128) DEFAULT NULL;

  DECLARE ops CURSOR FOR SELECT * FROM `mbu`;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET c = TRUE;

  OPEN ops;
  read_loop: LOOP
    FETCH ops INTO i, h, m, s, sl;

    IF c THEN
      CLOSE ops;
      LEAVE read_loop;
    END IF;

    IF h IS NOT NULL AND h NOT LIKE '' THEN
      SET @sql = CONCAT('SELECT `id` INTO @hid FROM `hostnames` WHERE `hostname` = "',h,'"');
    ELSE
      SET @sql = CONCAT('SELECT 1 INTO @hid');
    END IF;

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    IF m IS NOT NULL AND m NOT LIKE '' THEN
      SET @sql = CONCAT('SELECT `id` INTO @mid FROM `models` WHERE `model` = "',m,'"');
    ELSE
      SET @sql = CONCAT('SELECT 1 INTO @mid');
    END IF;

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    IF m IS NOT NULL AND m NOT LIKE '' THEN
      SET @sql = CONCAT('SELECT `id` INTO @wid FROM `warranty` WHERE `id` = "',m,'"');
    ELSE
      SET @sql = CONCAT('SELECT 1 INTO @wid');
    END IF;

    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    SELECT @hid AS HostnameID, @mid AS ModelID, @wid AS WarrantyID, s AS SKU, sl AS Serial;

  END LOOP;

 end BLOCK1;
END//

DELIMITER ;
