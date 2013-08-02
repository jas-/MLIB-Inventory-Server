<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle oauth module functionality
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
class oauthlib
{
 protected static $instance;
 private $handle;
 private $options=array();
 private function __construct($configuration)
 {
  if ((extension_loaded('oauth'))||(!class_exists('OAuth'))) {
   $this->main($configuration);
  } else {
   echo 'The oauth exensions are not available.';
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
 private function main($configuration)
 { echo '<pre>'; print_r(func_get_args()); echo '</pre>';
  if (extension_loaded('oauth')) {
   $this->handle = $this->init($configuration);
   if ((!isset($_SESSION['oauth']))&&($_SESSION['oauth']!==1)) {
    print_r(var_dump($this->handle->getAccessToken($configuration['provider'])));
   }
  } else {
   $this->handle = 'The oAuth extension is not installed. Aborting.';
  }
 }
 private function init($configuration)
 {
  return new OAuth($configuration['key'], $configuration['secret'], $configuration['sign'], $configuration['type']);
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
