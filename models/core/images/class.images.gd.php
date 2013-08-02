<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle GD library functionality
 *
 * Create image files
 * Read image files
 * Resize image files
 * Optional file output
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   images
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class gdlibrary
{
 protected static $instance;
 private $handle=NULL;
 private $flag='0x001';
 private $command='0x001';
 private function __construct($configuration, $path, $filename, $output, $key, $string)
 {
  if ($this->checklib($this->flag)) {
   return $this->main($configuration, $path, $filename, $output, $key, $string);
  } else {
   echo 'The gd image extensions are not loaded.';
   unset($instance);
   exit;
  }
 }
 private function checklib($flag)
 {
  if ((function_exists('imagecreatefromgif'))||(function_exists('imagecreatefromjpeg'))||(function_exists('imagecreatefrompng'))) {
   return TRUE;
  } else {
   return FALSE;
  }
 }
 public static function instance($configuration, $path=NULL, $filename=NULL, $output=NULL, $key=NULL, $string=NULL)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration, $path, $filename, $output, $key, $string);
  }
  return self::$instance;
 }
 public function main($configuration, $path, $filename, $output, $key, $string)
 {
  $this->setheader($filename);
/*
  switch($command) {
   case '0x001':
    // create image from empty file
    $img = $this->create($path, $filename, $width, $height, $output);
   case '0x002':
    // create image from existing file
    $img = $this->createfrom($path, $filename, $width, $height, $output);
   case '0x003':
    // open existing file
    $img = NULL;
   case '0x004':
    // resize existing image
    $img = NULL;
   default:
    $img = NULL;
  }
*/
 }
/*
 private function setheader($image)
 {
  preg_match('/(.*)\.(jpg|jpeg|png|gif)/i', $image, $match);
  if ((count($match))>0)&&(!empty($match[3]))) {
   switch($match[3]) {
    case preg_match('/(.*)\.([jpg|jpeg])/i', $image):echo 1;
     //header("Content-type: image/jpeg");
    case preg_match('/(.*)\.(png)/i', $image):echo 2;
     //header("Content-type: image/png");
    case preg_match('/(.*)\.(gif)/i', $image):echo 3;
     //header("Content-type: image/gif");
    default: echo 4;
     //header("Content-type: image/jpeg");
   }
  } else {
   header("Content-type: image/jpeg");
  }
 }
*/
 private function type($image)
 {
  switch($image) {
   case preg_match('/[*.jpg|*.jpeg]/', $image):
    return imagecreatefromjpeg($image);
   case preg_match('/[*.png]/', $image):
    return imagecreatefrompng($image);
   case preg_match('/[*.gif]/', $image):
    return imagecreatefromgif($image);
   default:
    return FALSE;
  }
 }
 private function create($path, $filename, $width, $height, $output)
 {
  return imagecreate($width, $height);
 }
 private function createfrom($path, $filename, $width, $height, $output)
 {
  return $this->type($path.$filename);
 }
 private function open($path, $file)
 {
  
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
