<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle access logging
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   logging
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

/**
 *! @class logging
 *  @abstract Handle logging for application usage
 */
class logging {

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
  *  @abstract Class initialization to handle access logging
  *  @param $args array Array of registry items
  */
 public function __construct($registry)
 {
  $this->registry = $registry;
  $x = $this->_main();
  if (class_exists('validation')){
   $this->registry->val = validation::init();
  }
  try{
   $sql = $this->_create($this->registry->val->__do($x));
   $this->registry->db->query($sql);
  } catch(Exception $e){
   // add some error handling class stuff
  }
 }

 /**
  *! @function _ip
  *  @abstract Returns the associated IP
  */
 private function _ip()
 {
  return $this->registry->libs->_getRealIPv4();
 }

 /**
  *! @function _host
  *  @abstract Attempts to get hostname using DNS records
  */
 private function _host()
 {
  return gethostbyaddr($this->registry->libs->_getRealIPv4());
 }

 /**
  *! @function _browser
  *  @abstract Returns browser id
  */
 private function _browser()
 {
  return getenv('HTTP_USER_AGENT');
 }

 /**
  *! @function _request
  *  @abstract Returns the request string
  */
 private function _request()
 {
  return getenv('QUERY_STRING');
 }

 /**
  *! @function _time
  *  @abstract Returns request time
  */
 private function _time()
 {
  return $_SERVER['REQUEST_TIME'];
 }

 /**
  *! @function _create
  *  @abstract Creates a new SQL statement
  */
 private function _create($array)
 {
  return sprintf('CALL Logs_Add("%s", "%s", "%s", "%s", "%s", "%s")',
                 $array['guid'], $array['adate'], $array['ip'],
                 $array['hostname'], $array['agent'], $array['query']);
 }

 /**
  *! @function _main
  *  @abstract Gathers up required logging information
  */
 private function _main()
 {
  $x['guid'] = $_SESSION['csrf'];
  $x['adate'] = $this->_time();
  $x['ip'] = $this->_ip();
  $x['hostname'] = $this->_host();
  $x['agent'] = $this->_browser();
  $x['query'] = $this->_request();
  return $x;
 }

 public function __destruct()
 {
  unset($this->init);
 }
}
?>
