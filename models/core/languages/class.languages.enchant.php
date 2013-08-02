<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle enchant language module functionality
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   languages
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class enchant
{
 protected static $instance;
 public $handle;
 public $language;
 public $lhandle;
 private function __construct($configuration, $language)
 {
  if (extension_loaded('enchant')) {
   return $this->main($configuration, $language);
  } else {
   echo 'The enchant language extension is not loaded.';
   unset($instance);
   exit;
  }
 }
 public static function instance($configuration, $language=NULL)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration, $language);
  }
  return self::$instance;
 }
 private function main($configuration, $language)
 {
  $this->language = $this->set($configuration, $language);
  $this->handle = $this->init();
  if ($this->exists($this->handle, $this->language)) {
   $this->lhandle = $this->load($this->handle, $this->language);
  }
 }
 private function set($configuration, $language)
 {
  return (!empty($language)) ? $language : $configuration['default'];
 }
 private function init()
 {
  return enchant_broker_init();
 }
 private function exists($handle, $language)
 {
  return (enchant_broker_dict_exists($handle, $language)) ? TRUE : FALSE;
 }
 private function load($handle, $language)
 {
  return (enchant_broker_request_dict($handle, $language)) ? TRUE : FALSE;
 }
 public function check($word)
 {
  return (!enchant_dict_check($this->lhandle, $word)) ? enchant_dict_suggest($this->lhandle, $word) : $word;
 }
 private function __destruct()
 {
  if (isset($this->handle)) {
   unset($this->handle, $this->language);
  }
  return;
 }
}
?>
