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
 * @category   encryption
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class encryption
{
 protected static $instance;
 private $flag='0x001';
 private $handle=NULL;
 private $algorithm=NULL;
 private $mode=NULL;
 private $source=NULL;
 private $iv=NULL;
 private $privkey=NULL;
 private $pubkey=NULL;
 public $cipher=array();
 private function __construct($configuration)
 {
  if (function_exists('mcrypt_module_open')) {
   $this->main($configuration);
  } else {
   echo 'The mcrypt extensions are not loaded.';
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
 private function decide($flag)
 {
  if (function_exists('mcrypt_module_open')) {
   $this->flag = $flag;
  } else {
   $this->flag = '0x002';
  }
 }
 public function main($configuration)
 {
  $this->flag = $this->type();
  if ((isset($configuration['algorithm']))&&(isset($configuration['mode']))&&(isset($configuration['source']))&&(isset($configuration['key']))&&(isset($configuration['pub']))&&(isset($configuration['iv']))) {
   $this->algorithm = $algorithm;
   $this->mode = $mode;
   $this->source = $source;
   $this->key = $key;
   $this->pub = $pub;
   $this->iv = $iv;
  } else {
   do {
    $this->randomizer();
    $this->configure();
    $this->handle = $this->open($this->algorithm, '', $this->mode, $this->source);
   } while(($this->test($this->handle)===FALSE)||($this->test($this->handle)===NULL));
   if (is_resource($this->handle)) {
    $this->cipher['algorithm'] = $this->algorithm;
    $this->cipher['mode'] = $this->mode;
    $this->cipher['source'] = $this->source;
    $this->privkey = $this->chkkey($this->algorithm, $this->mode, $this->privatekeys($this->encode($this->shared($configuration))));
    $this->pubkey = $this->publickeys($this->privkey);
    $this->iv = $this->createiv($this->algorithm, $this->mode, $this->source);
    $this->cipher['privatekey'] = $this->convert($this->privkey, '0x001');
    $this->cipher['publickey'] = $this->pubkey ;
    $this->cipher['iv'] = $this->convert($this->iv, '0x001');
   }
  }
  $this->init($this->handle, $this->privkey, $this->iv);
  return $this->cipher;
 }
 private function configure($algorithm=NULL, $mode=NULL, $source=NULL)
 {
  if ((isset($algorithm))||($algorithm!==NULL)) {
   $this->algorithm = $algorithm;
  } else {
   $this->algorithm = $this->randAlgorithm();
  }
  if ((isset($source))||($source!==NULL)) {
   $this->source = $source;
  } else {
   $this->source = $this->randSource();
  }
  if ((isset($mode))||($mode!==NULL)) {
   $this->mode = $mode;
  } else {
   $this->mode = $this->randMode();
  }
 }
 private function randAlgorithm()
 {
  $this->randomizer();
  return array_rand(array_flip(mcrypt_list_algorithms()), 1);
 }
 private function randSource()
 {
  $this->randomizer();
  $sources = array(MCRYPT_DEV_RANDOM, MCRYPT_DEV_URANDOM, MCRYPT_RAND);
  return array_rand(array_flip($sources), 1);
 }
 private function randMode()
 {
  $this->randomizer();
  $modes = array(MCRYPT_MODE_ECB, MCRYPT_MODE_STREAM);
  return array_rand(array_flip($modes), 1);
 }
 private function test($handle)
 {
  if (is_resource($handle)) {
   return mcrypt_enc_self_test($handle);
  }
 }
 private function open($cipher, $directory, $mode, $wdirectory)
 {
  return mcrypt_module_open($cipher, $directory, $mode, $wdirectory);
 }
 private function createiv($algo, $mode, $source)
 {
  return mcrypt_create_iv(mcrypt_get_iv_size($algo, $mode), $source);
 }
 private function compativ($iv)
 {
  return base64_encode($iv);
 }
 private function chkkey($algo, $mode, $key)
 {
  return substr(hash('sha256', $key, true), 0, mcrypt_get_key_size($algo, $mode));
 }
 private function init($handle, $key, $iv)
 {
  return mcrypt_generic_init($handle, $key, $iv);
 }
 public function enc($handle, $data)
 {
  return base64_encode(mcrypt_generic($this->handle, $data));
 }
 public function denc($algo, $key, $data, $mode, $iv)
 {
  return rtrim(mcrypt_decrypt($algo, $key, base64_decode($data), $mode, base64_decode($iv)));
 }
 public function convert($private, $function)
 {
  if ((isset($function))&&($function==='0x001')) {
   return $this->hex($private);
  } else {
   return $this->bin($private);
  }
 }
 private function hex($key)
 {
  return bin2hex($key);
 }
 private function bin($key)
 {
  $data=NULL;
  $hexLenght = strlen($key);
  if($hexLenght % 2 != 0 || preg_match("/[^\da-fA-F]/", $key)) { $binString = -1; }
  unset($binString);
  for($x=1; $x<=$hexLenght / 2; $x++) {
   $data .= chr(hexdec(substr($key, 2 * $x - 2, 2)));
  }
  return $data;
 }
 private function type()
 {
  if (function_exists('mhash')) {
   $type = '0x001';
  } elseif (function_exists('sha1')) {
   $type = '0x002';
  } elseif (function_exists('crypt')) {
   $type = '0x003';
  } else {
   $type = '0x004';
  }
  return $type;
 }
 private function randomizer()
 {
  return srand((double) microtime(time())*rand());
 }
 private function randomstr()
 {
  return preg_replace('/\$1\$/', crypt(crypt(time())/(60*60*24)), md5(crypt(crypt(time())/(60*60*24))));
 }
 private function base($array)
 {
  if (count($array)>0) {
   $base = array_rand(array_flip($array), 3);
  } else {
   return FALSE;
  }
  return $base;
 }
 private function shared($configuration)
 {
  $files=array(); $file=NULL; $dir=NULL;
  if ((isset($configuration['folder']))&&(is_dir($configuration['folder']))) {
   if ($dir = opendir($configuration['folder'])) {
    while($file = readdir($dir)) {
     if ((is_dir($file))&&($file!=='.')&&($file!=='..')) {
      $this->shared($file);
      continue;
     } else {
      if (($file!=='.')&&($file!=='..')) {
       $files[]=preg_replace('/[.jpg|.png|.gif]/', $this->randomstr(), $file);
      }
     }
    }
    closedir($dir);
   } else {
    return $this->altshared();
   }
  } else {
   return $this->altshared();
  }
  return $this->base($files);
 }
 private function altshared()
 {
  for ($x=0;$x<3;$x++) {
   $files[$x] = $this->randomstr();
  }
  return $files;
 }
 private function encode($base)
 {
  if (count($base)===3) {
   foreach($base as $key => $value) {
    switch ($this->flag) {
     case '0x001':
      $keys[$key] = mhash(MHASH_SHA1, sha1($value));
     case '0x002':
      $keys[$key] = sha1($value);
     case '0x003':
      $keys[$key] = crypt($value, $this->randomizer());
     case '0x004':
      $keys[$key] = md5($value);
     default:
      $keys[$key] = base64_encode($value);
    }
   }
   return $keys;
  } else {
   return FALSE;
  }
 }
 private function privatekeys($keys)
 {
  $private=NULL;
  if (count($keys)>0) {
   foreach($keys as $key => $value) {
    $private .= $value.':';
   }
   return rtrim($private, ':');
  } else {
   return FALSE;
  }
 }
 private function publickeys($private)
 {
  return (isset($private)) ? md5($private) : FALSE;
 }
 private function __destruct()
 {
  if (isset($this->handle)) {
   unset($this);
   mcrypt_generic_deinit($this->handle);
   mcrypt_module_close($this->handle);
  }
  return;
 }
}
?>
