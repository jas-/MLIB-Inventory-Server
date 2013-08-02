<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle openid module functionality
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   authentication
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class openid
{
 protected static $instance;
 private $handle;
 private $bound;
 private function __construct($configuration, $username, $password)
 {
  $this->main($configuration, $username, $password);
 }
 public static function instance($configuration, $username=NULL, $password=NULL)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration, $username, $password);
  }
  return self::$instance;
 }
 private function main($configuration, $username, $password)
 {

 }
 private function connect($configuration)
 {

 }
 private function setoptions($handle, $configuration)
 {

 }
 public function authenticate($configuration, $username, $password)
 {

 }
 private function __destruct()
 {
  if (isset($this->handle)) {
   unset($this->handle);
  }
  return;
 }
}
?>
