<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle memcache module session funtionality
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   sessions
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class memSession
{
 protected static $instance;
 private $memconn;
 private function __construct($configuration)
 {
		if (class_exists('Memcache')) {
   session_set_save_handler(
    array(&$this, 'open'),
    array(&$this, 'close'),
    array(&$this, 'read'),
    array(&$this, 'write'),
    array(&$this, 'destroy'),
    array(&$this, 'gc')
   );
   register_shutdown_function('session_write_close');
   $this->memconn = new Memcache;
   $this->memconn->pconnect($configuration['server'], $configuration['port'], $configuration['timeout']);
   session_start();
		} else {
			echo 'The memcache extensions are missing.';
			unset($instance);
			exit;
		}
 }
 public static function instance($configuration)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration);
  }
  return self::$instance;
 }
 private function open($configuration)
 {
  if ((!isset($this->memconn))||(!is_object($this->memconn))) {
   $this->memconn = memConn::instance($configuration);
   return (is_object($this->memconn)) ? true : false;
  }
  return false;
 }
 public function read($id)
 {
  if (isset($id)) {
   $result = $this->memconn->get($id);
   if ((isset($result))&&(!empty($result))) {
    return $this->sanitizeout($result);
   }
  }
  return "";
 }
 public function write($id, $data)
 {
  if ((isset($id))&&(isset($data))) {
   return ($this->memconn->add($this->sanitizein($id), $this->sanitizein($data))) ? true : false;
  }
  return false;
 }
 public function close()
 {
  return $this->memconn->close();
 }
 private function destroy($id)
 {
  if (isset($id)) {
   return ($this->memconn->delete($id, 0)) ? true : false;
  }
  return false;
 }
 private function sanitizein($string)
 {
  if (version_compare(PHP_VERSION, '5.2.11')>=0) {
   return addslashes(serialize($string));
  } else {
   return addslashes($string);
  }
 }
 private function sanitizeout($string)
 {
  if (version_compare(PHP_VERSION, '5.2.11')>=0) {
   return stripslashes(unserialize($string));
  } else {
   return stripslashes($string);
  }
 }
 public function regen($flag=false)
	{
  if ($flag!==false) {
   session_regenerate_id($flag);
   $this->id = session_id();
  }
  return;
	}
 public function register($name, $value)
 {
  return ((isset($name))&&(isset($value))) ? $_SESSION[$name] : false;
 }
 private function gc($timeout)
 {
  if (isset($timeout)) {
   return ($this->memconn->flush()) ? true : false;
  }
  return false;
 }
}
?>
