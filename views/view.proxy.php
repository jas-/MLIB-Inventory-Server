<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Proxy view (conditionally handles XMLHttpRequests)
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   views
 * @discussion Handle XMLHttpRequests
 * @author     jason.gerfen@gmail.com
 * @copyright  2008-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.3
 */

/**
 *! @class proxyView
 *  @abstract Handles XMLHttpRequests
 */
class proxyView
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
  *! @function __construct
  *  @abstract Initializes singleton for proxyView class
  *  @param registry array - Global array of class objects
  */
 public function __construct($registry)
 {
  $this->registry = $registry;
  $do = (!empty($_POST['key'])) ? 'key' : $this->registry->router->action;
  exit($this->registry->libs->JSONencode($this->__decide($do)));
 }

 /**
  *! @function instance
  *  @abstract Creates non-deserializing, non-cloneable instance object
  *  @param configuration array - server, username, password, database
  *  @return Singleton - Singleton object
  */
 public static function instance($configuration)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration);
  }
  return self::$instance;
 }

 /**
  *! @function __decide
  *  @abstract Switch/Case to decide what to do
  *  @param args array - Array of arguments
  */
 private function __decide($cmd)
 {
  $x = false;
  if (!empty($cmd)){
   $cmd = $this->registry->val->__do($cmd, 'string');
   switch($cmd){
    case 'authenticate':
     $auth = authentication::instance($this->registry);
     $x = $auth->__do($this->registry->val->__do($_POST));
     break;
    case 'key':
     $keyring = new keyring($this->registry,
                            $this->registry->val->__do($_POST));
     $x = $keyring->__public($this->registry->val->__do($_POST['email']));
     break;
    default:
     $x = false;
     break;
   }
  }
  return $x;
 }
}

?>