<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Error handler class
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   libraries
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010-2011 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class errors {
 protected static $instance;
 private function __construct()
 {
  return;
 }
 public static function init()
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self();
  }
  return self::$instance;
 }
 public function _text($text)
 {
  return '<p>'.$text.'</p>';
 }
 public function _link($text, $link, $hash=null)
 {
  return ($hash) ? '<a href="'.$link.'#'.$hash.'">'.$text.'</a>' : '<a href="'.$link.'">'.$text.'</a>';
 }
 public function _image($image)
 {
  return '<img src="'.$image.'">';
 }
 public function _imglink($link, $hash=null, $image)
 {
  return ($hash) ? '<a href="'.$link.'#'.$hash.'"><img src="'.$image.'"></a>' : '<a href="'.$link.'"><img src="'.$image.'"></a>';
 }
 public function __clone() {
  trigger_error('Cloning prohibited', E_USER_ERROR);
 }
 public function __wakeup() {
  trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
 }
 private function __destruct()
 {
  unset(self::$instance);
  return true;
 }
}
?>
