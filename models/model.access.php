<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Manage access lists (allow/deny)
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   access
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

/**
 *! @class access
 *  @abstract Handles allow/deny list
 */
class access {

 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;

 /**
  * @var instance object
  * @abstract This class handler
  */
 protected static $instance;

 /**
  *! @function init
  *  @abstract Creates singleton for allow/deny class
  *  @param $args array Array of registry items
  */
 public static function init($args)
 {
  if (self::$instance == NULL)
   self::$instance = new self($args);
  return self::$instance;
 }

 /**
  *! @function __construct
  *  @abstract Class initialization and ip to access/deny processing
  *  @param $args array Array of registry items
  */
 public function __construct($registry)
 {
  $this->registry = $registry;
  $this->_do();
 }

 /**
  *! @function _do
  *  @abstract Perform access list to visitor allow/deny functions
  */
 public function _do()
 {
  $a = $this->__compare($this->__visitor(), $this->_get('allow'));
  $b = $this->__compare($this->__visitor(), $this->_get('deny'));
  return (($a)&&(!$b)||((!$a)&&(!$b))) ? true : false;
 }

 /**
  *! @function _get
  *  @abstract Retrieves currently configured list of allowed/denied hosts
  */
 private function _get($t)
 {
  $list = 0;
  try{
   $list = $this->registry->libs->__flatten($this->registry->db->query($this->__query($t), true));
  } catch(PDOException $e){
   // error handling
  }
  return $list;
 }

 /**
  *! @function __visitor
  *  @abstract Retrieves and assigns the visiting address
  */
 private function __visitor()
 {
  return $this->registry->libs->_getRealIPv4();
 }

 /**
  *! @function __query
  *  @abstract Generates SQL query to access list of allowed/denied hosts
  */
 private function __query($type)
 {
  return sprintf('SELECT `'.$type.'` FROM `configuration_access` WHERE `'.$type.'` != ""');
 }

 /**
  *! @function __compare
  *  @abstract Performs comparisions on hosts, ips and additional network
  *            declarations
  */
 private function __compare($i, $l)
 {
  $a = false; $x = false;
  if (count($l)>0){
   foreach($l as $k => $v){
    $n = (class_exists('ipFilter')) ? new ipFilter($v) : false;
    if ($n){
     $a = $n->check($i);
     if($a) $x=true;
    }
    unset($n);
   }
  }
  return $x;
 }

 public function __destruct()
 {
  return;
 }
}
?>
