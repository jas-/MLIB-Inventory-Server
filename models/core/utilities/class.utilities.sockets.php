<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle sockets functionality
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   utilities
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

/**
 *! @class sockets
 *  @abstract Handles socket connection functionality
 */
class sockets
{

 /**
  * @var instance object
  * @abstract This class handler
  */
 protected static $instance;

 /**
  *! @function __construct
  *  @abstract Class initialization.
  *  @param $configuration array Array of database sepecific options
  */
 private function __construct($configuration)
 {
  return;
 }

 /**
  *! @function instance
  *  @abstract Class initialization. Singleton object
  *  @param $configuration array Array of database sepecific options
  */
 public static function instance($configuration)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration);
  }
  return self::$instance;
 }

 private function __destruct()
 {
  return;
 }
}
?>
