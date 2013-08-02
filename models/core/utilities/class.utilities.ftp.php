<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle mcrypt encryption
 *
 * Generates random cipher, mode, iv to use for each request unless specified
 * Generates random private & public key pair (private should never be
 * transmitted back to client)
 * Automatically converts private key to a hexadecimal equivelant for
 * safe cookie, session and database storage if needed
 * Encrypts and decrypts data using libmcrypt
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
class ftp
{
 protected static $instance;
 private $handle;
 private function __construct($configuration, $server, $port, $username, $password)
 {
  if (function_exists('ftp_connect')) {
   return $this->main($configuration, $server, $port, $username, $password);
  } else {
   echo 'ftp extension not loaded';
   unset($instance);
   exit;
  }
 }
 public static function instance($configuration, $server=NULL, $port=NULL, $username=NULL, $password=NULL)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration, $server, $port, $username, $password);
  }
  return self::$instance;
 }
 private function main($configuration, $server, $port, $username, $password)
 {
  $this->handle = $this->connect($configuration, $server, $port);
  echo '<pre>'; print_r(var_dump($this->connect($configuration, $server, $port))); echo '</pre>';
  $this->mode($this->handle, $configuration);
  if ($this->handle) {
   echo '<pre>'; print_r(var_dump($this->authenticate($this->handle, $username, $password))); echo '</pre>';
  }
 }
 private function connect($configuration, $server, $port)
 {
  return ftp_connect($server, $port, $configuration['timeout']);
 }
 private function authenticate($handle, $username, $password)
 {
  return ftp_login($handle, $username, $password);
 }
 private function mode($handle, $configuration)
 {
  return ($configuration['mode']===TRUE) ? ftp_pasv($handle, TRUE) : ftp_pasv($handle, FALSE);
 }
 private function __destruct()
 {
  if (isset($this->handle)) {
   ftp_close($this->handle);
   unset($this->handle);
  }
  return;
 }
}
?>
