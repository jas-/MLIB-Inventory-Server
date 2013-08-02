<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Input validation
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

class validation {
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
 public function __do($array, $type=false)
 {
  if (is_array($array)) {
   return $this->__switch($array);
  } else {
   return $this->__perform($array, $type);
  }
 }
 private function __switch($array)
 {
  foreach($array as $key => $value) {
   if (is_array($value)) {
    $this->__switch($value);
   } else {
    if (!empty($value)) {
     switch($value) {
      case (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[-_~!@#$%^&*()_+{}|\;:"\'<>?]{1,128}$/Di')))):
      //case eregi('^[ -!#$%&\'*+\\./=?^_`{|}~<>.,]$', $value):
      //case preg_match('/^[\_\-\%\^\&\$\#\@\!]$/Di', $value):
       $array[$key] = $this->__perform($value, 'special');
       continue;
      case (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[0-9]{1,128}$/Di')))):
      //case eregi('^[0-9]{1,}$', $value):
      //case ((preg_match('/^[\d+]{1,}$/Di', $value))||(is_numeric($value))):
       $array[$key] = $this->__perform($value, 'integer');
       continue;
      case (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z]{1,128}$/Di')))):
      //case eregi('^[a-z]{1,}$', $value):
      //case preg_match('/^[a-z]\{1,}$/Di', $value):
       $array[$key] = $this->__perform($value, 'string');
       continue;
      case (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z0-9]{1,128}$/Di')))):
      //case eregi('^([a-z0-9]){1,}$', $value):
      //case preg_match('/^[a-z0-9]{1,}$/Di', $value):
       $array[$key] = $this->__perform($value, 'alpha');
       continue;
      case (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[0-9]{1,128}\.[0-9]{2}$/Di')))):
      //case eregi('^[0-9]{1,65}\.[0-9]{2}$', $value):
      //case preg_match('/^[0-9]{1,65}\.[0-9]{2}$/D', $value):
       $array[$key] = $this->__perform($value, 'decimal');
       continue;
      case (filter_var($value, FILTER_VALIDATE_BOOLEAN)):
      //case eregi('^[0|1]{1}|[true|false]$', $value):
      //case ((preg_match('/^(0|1){1}$/', $value))||(preg_match('/^(true|false)$/', $value))):
       $array[$key] = $this->__perform($value, 'boolean');
       continue;
      case (filter_var($value, FILTER_VALIDATE_EMAIL)):
      //case eregi('^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$', $value):
      //case preg_match('/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,5})$/D', $value):
       $array[$key] = $this->__perform($value, 'email');
       continue;
      case (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[0-9]{3}\-[0-9{3}\-[0-9]{4}$/Di')))):
      //case eregi('^[0-9]{3}\-[0-9]{3}\-[0-9]{4}$', $value):
      //case preg_match('/^[\d+]{3}\-[\d+]{3}\-[\d+]{4}$/D', $value):
       $array[$key] = $this->__perform($value, 'phone');
       continue;
      case ($this->__IPv4($value)===true):
       $array[$key] = $this->__perform($value, 'ipv4');
       continue;
      case ($this->__URI($value)===true):
       $array[$key] = $this->__perform($value, 'uri');
       continue;
      default:
       $array[$key] = $this->__perform($value, 'alpha');
     }
    }
   }
  }
  return $array;
 }
 private function __perform($item, $type)
 {
  switch($type) {
   case 'integer':
    return filter_var($item, FILTER_SANITIZE_NUMBER_INT);
   case 'string':
    return filter_var($item, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_LOW|FILTER_FLAG_ENCODE_HIGH);
   case 'alpha':
    return filter_var($item, FILTER_SANITIZE_MAGIC_QUOTES);
   case 'decimal':
    return filter_var($item, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
   case 'boolean':
    return filter_var((bool)$item, FILTER_SANITIZE_NUMBER_INT);
   case 'email':
    return filter_var($item, FILTER_SANITIZE_EMAIL, FILTER_FLAG_ENCODE_LOW|FILTER_FLAG_ENCODE_HIGH);
   case 'phone':
    return filter_var($item, FILTER_SANITIZE_STRING);
   case 'ipv4':
    return filter_var($item, FILTER_SANITIZE_STRING);
   case 'uri':
    return filter_var($item, FILTER_SANITIZE_URL);
   case 'special':
    return filter_var($item, FILTER_UNSAFE_RAW, FILTER_FLAG_ENCODE_AMP);
   default:
    return filter_var($item, FILTER_SANITIZE_STRING, FILTER_FLAG_ENCODE_AMP);
  }
 }
 private function __IPv4($ip=null)
 {
  if ((preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}$/", $ip))&&(!empty($ip))) {
   $x = 0;
   for ($i=1; $i<=3; $i++) {
    if (!(substr($ip, 0, strpos($ip, ".")) >= "0" && substr($ip, 0, strpos($ip, ".")) <= "255")) {
     $x = 1;
    }
    $ip = substr($ip, strpos($ip, ".")+1);
   }
   if (!($ip >= "0" && $ip <= "255")) {
    $x = 1;
   }
  } else {
   $x = 1;
  }
  return ($x===0) ? true : false;
 }
 private function __URI($uri)
 {
  $domain = "([a-z0-9][-[:alnum:]]*[[:alnum:]] )(\.[[:alpha:]][-[:alnum:]]*[[:alpha:]] )+";
  $dir = "(/[[:alpha:]][-[:alnum:]]*[[:alnum:]] )*";
  $page = "(/[[:alpha:]][-[:alnum:]]*\.[[:alpha:]]{3,5})?";
  $getstring = "(\?([[:alnum:]][-_%[:alnum:]]*=[-_%[:alnum:]]+)(&([[:alnum:]][-_%[:alnum:]]*=[-_%[:alnum:]]+) )*)?";
  $pattern = $domain . $dir . $page . $getstring;
  return (eregi($pattern, $uri)) ? true : false;
 }
 public function type($v, $t)
 {
  if (!empty($v)) {
   switch($t) {
    case 'special':
     return (filter_var($v, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[-_~!@#$%^&*()_+{}|\;:"\'<>?]{1,128}$/Di')))) ? true : false;
    case 'integer':
     return (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[0-9]{1,128}$/Di')))) ? true : false;
    case 'string':
     return (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z]{1,128}$/Di')))) ? true : false;
    case 'alpha':
     return (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z0-9]{1,128}$/Di')))) ? true : false;
    case 'decimal':
     return (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[0-9]{1,128}\.[0-9]{2}$/Di')))) ? true : false;
    case 'boolean':
     return (filter_var($value, FILTER_VALIDATE_BOOLEAN)) ? true : false;
    case 'email':
     return (filter_var($value, FILTER_VALIDATE_EMAIL)) ? true : false;
    case 'phone':
     return (filter_var($value, FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[0-9]{3}\-[0-9{3}\-[0-9]{4}$/Di')))) ? true : false;
    case 'ipv4':
     return ($this->__IPv4($value)) ? true : false;
    case 'uri':
     return ($this->__URI($value)) ? true : false;
   }
  } else {
   $x = false;
  }
  return $x;
 }
 private function __string($string)
 {
  return ((is_string($string))&&(preg_match('/[a-z]/i', $string))) ? true : false;
 }
 private function __integer($integer)
 {
  return ((is_int($integer))&&(preg_match('/[0-9]/', $integer))) ? true : false;
 }
 public function __clone() {
  trigger_error('Cloning prohibited', E_USER_ERROR);
 }
 public function __wakeup() {
  trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
 }
 public function __destruct()
 {
  unset($instance);
  return true;
 }
}
?>
