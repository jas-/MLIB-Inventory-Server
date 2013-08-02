<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle authentication
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
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

/**
 *! @class authentication
 *  @abstract Performs primary and additional authentication mechanisms
 */
class authentication
{
 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;

 /**
  *! @var instance object - class singleton object
  */
 protected static $instance;

 /**
  *! @var keys array
  *  @abstract array containing private and public keys per email
  */
 protected static $keys = array();

 /**
  *! @function __construct
  *  @abstract Initializes singleton for proxyView class
  *  @param registry array - Global array of class objects
  */
 private function __construct($registry)
 {
  $this->registry = $registry;
  $this->__setup($registry);
 }

 public static function instance($registry)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($registry);
  }
  return self::$instance;
 }

 /**
  *! @function __do
  *  @abstract Performs initial authentication
  */
 public function __do($creds)
 {
  // decrypt supplied authentication credentials

  // perform authentication using decrypted credentials

  // push public key to client keyring

  // switch to users private key (ssl initialization)

  // generate authentication token

  // register token within users account

  // return boolean
 }

 /**
  *! @function __redo
  *  @abstract Decodes and re-authenticates user
  */
 public function __redo($token)
 {
  // verify supplied token matches entry in users table

  // decode token

  // verify unique computer and timeout information

  // perform new token generation and registration per user account

  // return boolean
 }

 /**
  *! @function __decrypt
  *  @abstract Handle decryption of submitted form data
  */
 private function __decrypt($obj)
 {
  // returns decrypted object
 }

 /**
  *! @function __decode
  *  @abstract Handle decoding of authentication token
  */
 private function __decode($token)
 {
  // returns array of elements within authentication token
 }

 /**
  *! @function __hijack
  *  @abstract Performs anti-session hijacking validations
  */
 private function __hijack($array)
 {
  // performs validation of current session data to array of unique computer
  // identifiers located within decoded authentication token array
 }

 /**
  *! @function __genToken
  *  @abstract Creates unique token based on visiting machine and
  *            authenticated user information
  */
 private function __genToken($obj)
 {
  // users timestamp and additional unique identifiers to be used during
  // authentication and re-authentication processes
 }

 public function __clone() {
  trigger_error('Cloning prohibited', E_USER_ERROR);
 }
 public function __wakeup() {
  trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
 }
 private function __destruct()
 {
  unset($this->instance);
  return true;
 }
}
?>
