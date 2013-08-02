<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle kadm5 module usage
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

class kerberos
{
 protected static $instance;
 private $handle;
 private $options=array();
 private function __construct($configuration)
 {
  if ((extension_loaded('kadm5'))&&(function_exists('kadm5_init_with_password'))) {
   $this->main($configuration);
  } else {
   echo 'The kadm5 extensions are not available';
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
 {
  if (function_exists('kadm5_init_with_password')) {
   $this->init($configuration);
  } else {
   $this->handle = 'The KADM5 Pear extension is not installed. Aborting.';
  }
 }
 private function setoptions($configuration)
 {
  $this->options[KADM5_PRINC_EXPIRE_TIME] = (!empty($configuration['timeout'])) ? $configuration['timeout'] : 0;
  $this->options[KADM5_POLICY] = (!empty($configuration['policy'])) ? $configuration['policy'] : 'default';
 }
 private function init($configuration)
 {
  $this->handle = kadm5_init_with_password($configuration['servers'], $configuration['realm'], $configuration['principal'], $configuration['password']);
 }
 public function authenticate($username, $password, $realm)
 {
  return kadm5_create_principal($this->handle, $username.'@'.$realm, $password, $this->options);
 }
 private function __destruct()
 {
  if (isset($this->handle)) {
   kadm5_destroy($this->handle);
  }
  return;
 }
}
?>
