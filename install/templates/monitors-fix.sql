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

  DECLARE i BIGINT;
  DECLARE h CHAR(128);
  DECLARE m CHAR(128);
  DECLARE s CHAR(128);
  DECLARE sl CHAR(128);

  DECLARE ops CURSOR FOR SELECT * FROM `mbu` WHERE `hostname` = '';
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET c = TRUE;

  OPEN ops;
  read_loop: LOOP
    FETCH ops INTO i, h, m, s, sl;

    IF c THEN
      CLOSE ops;
      LEAVE read_loop;
    END IF;

    SELECT i AS ID, h AS Hostname, m AS Model, s AS SKU, sl AS Serial;

    IF (h IS NOT NULL OR h != '') THEN
      SET @sql1 = CONCAT('SELECT `id` INTO @hid FROM `hostnames` WHERE `hostname` = "',h,'"');
    ELSE
      SET @sql1 = CONCAT('SELECT 1 INTO @hid');
    END IF;

    PREPARE stmt FROM @sql1;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    IF m IS NOT NULL AND m NOT LIKE '' THEN
      SET @sql2 = CONCAT('SELECT `id` INTO @mid FROM `models` WHERE `model` = "',m,'"');
    ELSE
      SET @sql2 = CONCAT('SELECT 1 INTO @mid');
    END IF;

    PREPARE stmt FROM @sql2;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

    IF m IS NOT NULL AND m NOT LIKE '' THEN
      SET @sql3 = CONCAT('SELECT `id` INTO @wid FROM `warranty` WHERE `id` = "',m,'"');
    ELSE
      SET @sql3 = CONCAT('SELECT 1 INTO @wid');
    END IF;

    PREPARE stmt FROM @sql3;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;

--    SELECT h AS Hostname, @hid AS HostnameID, @mid AS ModelID, @wid AS WarrantyID, s AS SKU, sl AS Serial;

  END LOOP;

 end BLOCK1;
END//

DELIMITER ;
