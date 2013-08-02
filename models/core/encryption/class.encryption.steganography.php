<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle steganography encryption methods
 *
 * Depends on additional classes
 * - class.encryption.mcrypt.php
 * - class.encryption.openssl.php
 * - class.images.gd.php
 *
 * Accepts image and optional data arguments (supports png, gif and jpg image types)
 * Creates steganography type resource (public key and encrypted data hidden in image)
 * Decrypts steganography type resource (data within supplied image)
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

class steganography
{
 protected static $instance;
 private $flag='0x001';
 private $handle=NULL;
 private $algorithm=NULL;
 private $digest=NULL;
 private $privatekey=NULL;
 private $publickey=NULL;
 private $opt=array();
 private $dn=array();
 public $keys=array();
 public $output;
 private function __construct($configuration, $username, $password)
 {
  if (function_exists('openssl_pkey_new')) {
   $this->main($configuration, $username, $password);
  } else {
   echo 'The openssl extensions are not loaded.';
   unset($instance);
   exit;
  }
 }
 public static function instance($configuration, $username=NULL, $password=NULL)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration, $username, $password);
  }
  return self::$instance;
 }
 public function main($configuration, $username, $password)
 {
  $this->flag = $this->decide($this->flag);
  $this->cipher = $this->configure($this->flag);
  $this->setOpt($configuration);
  $this->setDN($configuration, $username);
  $this->genPriv($this->dn, $this->opt, $username, $password);
  $this->genPub($this->handle);
  $csr = $this->signReq($this->dn, $this->privatekey, $this->opt);
  $this->sign($csr, $this->privatekey, 365, $this->opt);
  $this->write($configuration['config']['keysdir'], (empty($username)) ? 'new-priv-key' : $username.'-private.key', $this->privatekey);
  $this->write($configuration['config']['keysdir'], (empty($username)) ? 'new-pub-key' : $username.'-public.key', $this->publickey['key']);
  $this->keys['private'] = $this->privatekey;
  $this->keys['public'] = $this->publickey;
  return $this->keys;
 }
 private function decide($flag)
 {
  if ((function_exists('openssl_get_cipher_methods'))&&(function_exists('openssl_get_md_methods'))) {
   $this->flag = $flag;
  } else {
   $this->flag = '0x002';
  }
 }
 private function configure($flag, $algorithm=NULL, $digest=NULL, $length=NULL)
 {
  if ((isset($algorithm))||($algorithm!==NULL)) {
   $this->algorithm = $algorithm;
  } else {
   $this->algorithm = $this->randAlgorithm($flag);
  }
  if ((isset($digest))||($digest!==NULL)) {
   $this->digest = $digest;
  } else {
   $this->digest = $this->randDigests($flag);
  }
 }
 private function setOpt($configuration)
 {
  $this->opt = $configuration['config'];
 }
 private function setDN($configuration)
 {
  $this->dn = $configuration['dn'];
 }
 private function randAlgorithm($flag)
 {
  $this->randomizer();
  if ($flag==='0x001') {
   return array_rand(array_flip(openssl_get_cipher_methods(true)), 1);
  } else {
   //$algorithms = array('AES-128-CBC', 'AES-128-CFB', 'AES-128-CFB1', 'AES-128-CFB8', 'AES-128-ECB', 'AES-128-OFB', 'AES-192-CBC', 'AES-192-CFB', 'AES-192-CFB1', 'AES-192-CFB8', 'AES-192-ECB', 'AES-192-OFB', 'AES-256-CBC', 'AES-256-CFB', 'AES-256-CFB1', 'AES-256-CFB8', 'AES-256-ECB', 'AES-256-OFB', 'BF-CBC', 'BF-CFB', 'BF-ECB', 'BF-OFB', 'CAST5-CBC', 'CAST5-CFB', 'CAST5-ECB',  'CAST5-OFB', 'DES-CBC', 'DES-CFB', 'DES-CFB1', 'DES-CFB8', 'DES-ECB', 'DES-EDE', 'DES-EDE-CBC', 'DES-EDE-CFB', 'DES-EDE-OFB', 'DES-EDE3', 'DES-EDE3-CBC', 'DES-EDE3-CFB', 'DES-EDE3-OFB', 'DES-OFB', 'DESX-CBC', 'IDEA-CBC', 'IDEA-CFB', 'IDEA-ECB', 'IDEA-OFB', 'RC2-40-CBC', 'RC2-64-CBC', 'RC2-CBC', 'RC2-CFB', 'RC2-ECB', 'RC2-OFB', 'RC4', 'RC4-40', 'aes-128-cbc', 'aes-128-cfb', 'aes-128-cfb1', 'aes-128-cfb8', 'aes-128-ecb', 'aes-128-ofb', 'aes-192-cbc', 'aes-192-cfb', 'aes-192-cfb1', 'aes-192-cfb8', 'aes-192-ecb', 'aes-192-ofb', 'aes-256-cbc', 'aes-256-cfb', 'aes-256-cfb1', 'aes-256-cfb8', 'aes-256-ecb', 'aes-256-ofb', 'bf-cbc', 'bf-cfb', 'bf-ecb', 'bf-ofb', 'cast5-cbc', 'cast5-cfb', 'cast5-ecb', 'cast5-ofb', 'des-cbc', 'des-cfb', 'des-cfb1', 'des-cfb8', 'des-ecb', 'des-ede', 'des-ede-cbc', 'des-ede-cfb', 'des-ede-ofb', 'des-ede3', 'des-ede3-cbc', 'des-ede3-cfb', 'des-ede3-ofb', 'des-ofb', 'desx-cbc', 'idea-cbc', 'idea-cfb', 'idea-ecb', 'idea-ofb', 'rc2-40-cbc', 'rc2-64-cbc', 'rc2-cbc', 'rc2-cfb', 'rc2-ecb', 'rc2-ofb', 'rc4', 'rc4-40');
   //$algorithms = array(OPENSSL_KEYTYPE_DSA, OPENSSL_KEYTYPE_DH, OPENSSL_KEYTYPE_RSA);
   $algorithms = array(OPENSSL_ALGO_DSS1, OPENSSL_ALGO_SHA1, OPENSSL_ALGO_MD5);
   return array_rand(array_flip($algorithms), 1);
  }
 }
 private function randDigests($flag)
 {
  $this->randomizer();
  if ($flag==='0x001') {
   return array_rand(array_flip(openssl_get_md_methods(true)), 1);
  } else {
   $digests = array('DSA', 'DSA-SHA', 'MD2', 'MD4', 'MD5', 'RIPEMD160', 'SHA', 'SHA1', 'SHA224', 'SHA256', 'SHA384', 'SHA512', 'dsaEncryption', 'dsaWithSHA', 'DSA-SHA1', 'DSS1', 'RSA-MD5', 'RSA-SHA1', 'RSA-SHA256', 'RSA-SHA512', 'ssl3-md5', 'ssl3-sha');
   return array_rand(array_flip($digests), 1);
  }
 }
 private function genPriv($dn, $opt, $username='', $password='')
 {
  $this->handle = openssl_pkey_new();
  return openssl_pkey_export($this->handle, $this->privatekey, $password);
 }
 private function genPub($key)
 {
  $results = openssl_pkey_get_details($key);
  $this->publickey = $results;
 }
 private function signReq($dn, $private, $opt)
 {
  return openssl_csr_new($dn, $private, $opt['cnf']);
 }
 private function sign($csr, $private, $days, $opt)
 {
  return openssl_csr_sign($csr, NULL, $private, $days, $opt);
 }
 public function enc($private, $data, $password)
 {
  if ((!empty($private))&&(!empty($data))) {
   $res = openssl_get_privatekey($private, $password);
   openssl_private_encrypt($data, $this->output, $res);
   return $this->output;
  } else {
   return FALSE;
  }
 }
 public function denc($crypt, $key)
 {
  $res = openssl_get_publickey($key['key']);
  openssl_public_decrypt($crypt, $this->output, $res);
  return $this->output;
 }
 private function randomizer()
 {
  return srand((double) microtime(time())*rand());
 }
 private function read($path, $file)
 {
  $return=FALSE;
  if ((!empty($path))&&(!empty($file))&&(!empty($data))) {
   if (is_dir($path)) {
    if ((is_file($path.$file))&&(is_writable($path.$file))) {
     if (($handle=fopen($path.$file, 'rb'))!==FALSE) {
      if (flock($handle, LOCK_EX)) {
       while (($data = fread($handle, strlen($data)))!==FALSE) {
        $return .= $data;
        flock($handle, LOCK_UN);
        fflush($handle);
        fclose($handle);
       }
      } else {
       $return = FALSE;
      }
     } else {
      $return = FALSE;
     }
    } else {
     $return = FALSE;
    }
   } else {
    $return = FALSE;
   }
  } else {
   $return = FALSE;
  }
  return $return;
 }
 private function create($path, $file)
 {
  $return=FALSE;
  if ((!empty($path))&&(!empty($file))) {
   if (is_dir($path)) {
    if (touch($path.$file)) {
     $return = TRUE;
    }
   } else {
    $return = FALSE;
   }
  } else {
   $return = FALSE;
  }
  return $return;
 }
 private function write($path, $file, $data)
 {
  $return=FALSE;
  if ((!empty($path))&&(!empty($file))&&(!empty($data))) {
   if (is_dir($path)) {
    if ((is_file($path.$file))&&(is_writable($path.$file))) {
     if (($handle=fopen($path.$file, 'w+'))!==FALSE) {
      if (flock($handle, LOCK_EX)) {
       if (fwrite($handle, $data, strlen($data))) {
        flock($handle, LOCK_UN);
        fflush($handle);
        fclose($handle);
       }
      } else {
       $return = FALSE;
      }
     } else {
      $return = FALSE;
     }
    } else {
     if ($this->create($path, $file)!==FALSE) {
      return $this->write($path, $file, $data);
     }
    }
   } else {
    $return = FALSE;
   }
  } else {
   $return = FALSE;
  }
  return $return;
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
