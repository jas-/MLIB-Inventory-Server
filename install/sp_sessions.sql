DELIMITER //

-- Retrieve current session
DROP PROCEDURE IF EXISTS Session_Search//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Search(IN `session_id` VARCHAR(128), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Retrieve current session'
BEGIN
 SELECT `session_id`,AES_DECRYPT(BINARY(UNHEX(session_data)), SHA1(sKey)) AS session_data,`session_expire`,`session_agent`,`session_ip`,`session_referer` FROM `sessions` WHERE `session_id`=session_id;
END//

DROP PROCEDURE IF EXISTS Session_Add//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Add(IN `session_id` VARCHAR(64), `session_data` LONGTEXT, `session_expire` INT(10), `session_agent` VARCHAR(64), `session_ip` VARCHAR(64), `session_referer` VARCHAR(64), IN `sKey` LONGTEXT)
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or update existing session id & data'
BEGIN
 INSERT INTO `sessions` (`session_id`,`session_data`,`session_expire`,`session_agent`,`session_ip`,`session_referer`) VALUES (session_id, HEX(AES_ENCRYPT(session_data, SHA1(sKey))), session_expire, session_agent, session_ip, session_referer) ON DUPLICATE KEY UPDATE `session_id`=session_id, `session_data`=HEX(AES_ENCRYPT(session_data, SHA1(sKey))), `session_expire`=session_expire;
END//

DROP PROCEDURE IF EXISTS Session_Destroy//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Destroy(IN `session_id` VARCHAR(64))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Delete users sessions id'
BEGIN
 DELETE FROM `sessions` WHERE `session_id`=session_id LIMIT 1;
END//

DROP PROCEDURE IF EXISTS Session_Timeout//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Session_Timeout(IN `session_expire` INT(10))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Expire session based on timeout option'
BEGIN
 DELETE FROM `sessions` WHERE `session_expire`>session_expire LIMIT 1;
END//

DELIMITER ;
