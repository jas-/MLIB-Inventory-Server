<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle loading of classes
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

class autoloader {

 public static $loader;
 public static $path;
 private $files = array();

 public static function instance($path)
 {
  if (self::$loader == NULL)
   self::$loader = new self($path);
  return self::$loader;
 }

 public function __construct($path)
 {
  static::$path = (!empty($path)) ? $path : $_SERVER['DOCUMENT_ROOT'];
  $this->find(static::$path);
  $this->load(static::$path);
 }

 private function load($vpath)
 {
  if (is_array($this->files)){
   foreach($this->files as $key => $value){
    if (file_exists($value)){
     require_once($value);
    }
   }
  }
 }

 public function find($path)
 {
  $dir = ''; $file = ''; $files = array();
  if (is_dir($path)){
   if ($dir = opendir($path)){
    while (($file = readdir($dir))!==false){
     if ($this->exclude($file)===true){
      if ($this->dir($path.'/'.$file)===true){
       $this->find($path.'/'.$file);
      } else {
       array_push($this->files, $path.'/'.$file);
      }
     }
    }
   }
  }
 }

 private function file($file)
 {
  return (is_file($file)) ? true : false;
 }

 private function dir($dir)
 {
  return (is_dir($dir)) ? true : false;
 }

 private function exclude($dir)
 {
  return (($dir!=='.')&&($dir!=='..')) ? true : false;
 }

}
?>
