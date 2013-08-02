<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Libraries
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

class libraries {
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

 /**
  * @function _dbEngine
  * @abstract Determine database engine and class
  * @param $opt Option passed for database class to load
  */
 function _dbEngine($opt)
 {
  switch($opt){
   case 'mssql':
    $eng = 'mssqlDBconn';
    break;
   case 'pgsql':
    $eng = 'pgSQLDBconn';
    break;
   case 'mysql':
    $eng = 'mysqlDBconn';
    break;
   default:
    $eng = 'mysqlDBconn';
    break;
  }
  return $eng;
 }

 /**
  * @function _16
  * @abstract Creates substring of argument
  * @param $string string String to return sub-string of
  */
 function _16($string)
 {
  return substr($string, round(strlen($string)/3, 0, PHP_ROUND_HALF_UP), 16);
 }

 /**
  * @function _uuid
  * @abstract Generates a random GUID
  */
 function uuid() {
  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff),
                 mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000,
                 mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff),
                 mt_rand(0, 0xffff), mt_rand(0, 0xffff));
 }

 /**
  * @function _getRealIPv4
  * @abstract Try all methods of obtaining 'real' IP address
  */
 function _getRealIPv4()
 {
  return (getenv('HTTP_CLIENT_IP') && $this->_ip(getenv('HTTP_CLIENT_IP'))) ?
           getenv('HTTP_CLIENT_IP') :
            (getenv('HTTP_X_FORWARDED_FOR') && $this->_forwarded(getenv('HTTP_X_FORWARDED_FOR'))) ?
              $this->_forwarded(getenv('HTTP_X_FORWARDED_FOR')) :
               (getenv('HTTP_X_FORWARDED') && $this->_ip(getenv('HTTP_X_FORWARDED'))) ?
                 getenv('HTTP_X_FORWARDED') :
                  (getenv('HTTP_X_FORWARDED_HOST') && $this->_ip(getenv('HTTP_FORWARDED_HOST'))) ?
                    getenv('HTTP_X_FORWARDED_HOST') :
                     (getenv('HTTP_X_FORWARDED_SERVER') && $this->_ip(getenv('HTTP_X_FORWARDED_SERVER'))) ?
                       getenv('HTTP_X_FORWARDED_SERVER') :
                        (getenv('HTTP_X_CLUSTER_CLIENT_IP') && $this->_ip(getenv('HTTP_X_CLIUSTER_CLIENT_IP'))) ?
                          getenv('HTTP_X_CLUSTER_CLIENT_IP') :
                           getenv('REMOTE_ADDR');
 }

 /**
  * @function _ip
  * @abstract Attempts to determine if IP is non-routeable
  */
 function _ip($ip)
 {
  if (!empty($ip) && ip2long($ip)!=-1 && ip2long($ip)!=false){
   $nr = array(array('0.0.0.0','2.255.255.255'),
               array('10.0.0.0','10.255.255.255'),
               array('127.0.0.0','127.255.255.255'),
               array('169.254.0.0','169.254.255.255'),
               array('172.16.0.0','172.31.255.255'),
               array('192.0.2.0','192.0.2.255'),
               array('192.168.0.0','192.168.255.255'),
               array('255.255.255.0','255.255.255.255'));
   foreach($nr as $r){
    $min = ip2long($r[0]);
    $max = ip2long($r[1]);
    if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max)) return false;
   }
   return true;
  } else {
   return false;
  }
 }

 /**
  * @function _forwarded
  * @abstract A helper for HTTP_X_FORWARDED_FOR, loops over comma
  *           separated list of proxies associated with request
  */
 function _forwarded($l)
 {
  if (!empty($l)){
   foreach (explode(',', $l) as $i){
    if (_ip(trim($i))) {
     return (!_ip(trim($i))) ? false : $i;
    }
   }
  } else {
   return false;
  }
 }

 /**
  *! @function __flatten
  *  @abstract Flattens a multi-dimensional array into one array
  */
 public function __flatten($a)
 {
  $x = array();
  if (count($a)>0){
   foreach($a as $k => $v){
    if (is_array($v)){
     $x[] = $this->__flatten($v);
    } else {
     $x[] = $v;
    }
   }
  } else {
   $x = $a;
  }
  return $x;
 }

 /*!
  * @function response
  * @abstract Handle older versions of PHP that do not have json_encode, json_decode
  * @param $array Array Nested array of configuration options
  * @return object A JSON object
  */
 public function JSONencode($array){
  if (!function_exists('json_encode')) {
   return self::arr2json($array);
  } else {
   return json_encode($array);
  }
 }

 /*!
  * @function arr2json
  * @abstract Private function to create a JSON object
  * @param $array Array Associative array
  * @return object The resulting JSON object
  */
 private function arr2json($array)
 {
  if (is_array($array)) {
   foreach($array as $key => $value) $json[] = $key . ':' . self::php2js($value);
   if(count($json)>0) return '{'.implode(',',$json).'}';
   else return '';
  }
 }

 /*!
  * @function php2js
  * @abstract Private function using to determine array value type
  * @param $value String|INT|BOOL|NULL|ARRAY Mixed
  * @return STRING|INT|BOOL|NULL|ARRAY The typecasted variable
  */
 public function php2js($value)
 {
  if(is_array($value)) return self::arr2json($val);
  if(is_string($value)) return '"'.addslashes($value).'"';
  if(is_bool($value)) return 'Boolean('.(int) $value.')';
  if(is_null($value)) return '""';
  return $value;
 }

 /*!
  * @function geolocation
  * @abstract Public function to retrieve GEO location data
  * @param $ip String IPv4 string
  * @return object The results of the GEO object
  */
 public function geolocation($ip)
 {
  $opts = array('http'=>array('method'=>'GET','header'=>'Accept-language: en\r\n'));
  $context = stream_context_create($opts);
  $ex = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip, false, $context));
  return $ex;
 }

 /*!
  * @function parsegeo
  * @abstract Public function to parse GEO data
  * @param $data Object Parses and returns the GEO data as an array
  * @return Array The CN data returned from the GEO lookup
  */
 public function parsegeo($data)
 {
  $settings['localityName']        = (!empty($data['geoplugin_city'])) ?
                                             $data['geoplugin_city'] :
                                             false;
  $settings['stateOrProvinceName'] = (!empty($data['geoplugin_region'])) ?
                                             $data['geoplugin_region'] :
                                             false;
  $settings['countryName']         = (!empty($data['geoplugin_countryCode'])) ?
                                             $data['geoplugin_countryCode'] :
                                             false;
  return $settings;
 }

 /**
  * @function _salt
  * @abstract Generate a random salt value of specified length based on input
  */
 public function _salt($string, $len=null)
 {
  return (!empty($len)) ?
          hash('sha512', str_pad($string, (strlen($string) + $len),
                                 substr(hash('sha512', $string),
                                        @round((float)strlen($string)/3, 0,
                                              PHP_ROUND_HALF_UP),
                                        ($len - strlen($string))),
                                 STR_PAD_BOTH)) :
          hash('sha512', substr($string,
                                @round((float)strlen($string)/3, 0,
                                       PHP_ROUND_HALF_UP), 16));
}
 /**
  * @function _hash
  * @abstract Mimic bcrypt
  */
 public function _hash($string, $salt=null)
 {
  return (CRYPT_BLOWFISH==1) ?
          (!empty($salt)) ?
           crypt($string, "\$2a\$07\$".substr($salt, 0, CRYPT_SALT_LENGTH)) :
           crypt($string, $this->_salt("\$2a\$07\$".substr($string, 0, CRYPT_SALT_LENGTH))) :
          false;
 }

 /**
  * @function _serialize
  * @abstract Perform serialization of sent POST data. This is required for the
  *           jQuery.AJAX plug-in checksum verification as the current PHP
  *           serialize() function will not create an accurate hash
  */
 function _serialize($array)
 {
  if (count($array)>0){
   $x = '';
   foreach($array as $key => $value){
    $x .= $key.'='.$value.'&';
   }
   $x = substr($x, 0, -1);
  }
  return (strlen($x)>0) ? $x : false;
 }

 public function __clone() {
  trigger_error('Cloning prohibited', E_USER_ERROR);
 }
 public function __wakeup() {
  trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
 }
 public function __destruct()
 {
  unset($this->instance);
  return true;
 }
}
?>
