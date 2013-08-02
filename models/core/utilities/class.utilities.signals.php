<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle signals functionality
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
 *! @function signals
 *  @abstract Handles signals handling
 */
class signals
{

 protected static $instance;
 private $handle;
 private function __construct($configuration)
 {
  if (function_exists('pcntl_fork')) {
   return;
  } else {
   echo 'The pcntl extensions are not available';
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

 private function __destruct()
 {
  if (isset($this->handle)) {
   unset($this->handle);
  }
  return;
 }
}
?>
