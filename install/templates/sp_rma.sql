DELIMITER //

DROP PROCEDURE IF EXISTS `RMAList`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `RMAList`()
 DETERMINISTIC
 SQL SECURITY DEFINER
 COMMENT 'Returns RMA list'
BEGIN
 SELECT * FROM `viewInventoryRMA`;
END//

DROP PROCEDURE IF EXISTS `RMASearch`;
CREATE DEFINER=`{RO}`@`{SERVER}` PROCEDURE `RMASearch`(IN `s` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY DEFINER
 COMMENT 'Searches monitor records'
BEGIN
 SELECT * FROM `viewInventoryRMA` WHERE `Date` LIKE s OR `Hostname` LIKE s OR `SKU` LIKE s OR `UUIC` LIKE s OR `Serial` LIKE s OR `Model` LIKE s OR `EOWD` LIKE UNIX_TIMESTAMP(s) OR `Part` LIKE s OR `Notes` LIKE s ORDER BY `Date` ASC;
END//

DROP PROCEDURE IF EXISTS `RMAAddUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `RMAAddUpdate`(IN `d` CHAR(12), IN `h` CHAR(128), IN `s` CHAR(128), IN `u` CHAR(128), IN `sl` CHAR(128), IN `m` CHAR(128), IN `e` CHAR(32), IN `p` CHAR(128), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update rma'
BEGIN
 INSERT INTO `rma` (`date`, `hostname`, `sku`, `uuic`, `serial`, `model`, `eowd`, `part`, `notes`) VALUES (UNIX_TIMESTAMP(d), h, s, u, sl, m, e, p, n) ON DUPLICATE KEY UPDATE `date`=UNIX_TIMESTAMP(d), `hostname`=h, `sku`=s, `uuic`=u, `serial`=sl, `model`=m, `eowd`=e, `part`=p, `notes`=n;
 SELECT ROW_COUNT() AS affected;
END//

DROP PROCEDURE IF EXISTS `RMAUpdate`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `RMAUpdate`(IN `i` BIGINT, IN `d` CHAR(12), IN `h` CHAR(128), IN `s` CHAR(128), IN `u` CHAR(128), IN `sl` CHAR(128), IN `m` CHAR(128), IN `e` CHAR(32), IN `p` CHAR(128), IN `n` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add/Update rma'
BEGIN
 UPDATE `rma` SET `date`=UNIX_TIMESTAMP(d), `hostname`=h, `sku`=s, `uuic`=u, `serial`=sl, `model`=m, `eowd`=e, `part`=p, `notes`=n WHERE `id` = i LIMIT 1;
 SELECT ROW_COUNT() AS affected;
END//

DROP PROCEDURE IF EXISTS `RMADelete`;
CREATE DEFINER=`{ADMIN}`@`{SERVER}` PROCEDURE `RMADelete`(IN `i` BIGINT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete rma'
BEGIN
 DELETE FROM `rma` WHERE `id`=i LIMIT 1;
 SELECT ROW_COUNT() AS affected;
END//

DELIMITER ;
