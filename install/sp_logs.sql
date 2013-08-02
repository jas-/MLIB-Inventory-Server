DELIMITER //

DROP PROCEDURE IF EXISTS Logs_Add//
CREATE DEFINER='licensing'@'localhost' PROCEDURE Logs_Add(IN `guid` VARCHAR(64), `adate` VARCHAR(64), `ip` VARCHAR(10), `hostname` VARCHAR(80), `agent` VARCHAR(128), `query` VARCHAR(128))
 DETERMINISTIC
 SQL SECURITY INVOKER
 COMMENT 'Add or update logs'
BEGIN
 INSERT INTO `logs` (`guid`,`adate`,`ip`,`hostname`,`agent`,`query`) VALUES (guid, adate, ip, hostname, agent, query);
END//

DELIMITER ;
