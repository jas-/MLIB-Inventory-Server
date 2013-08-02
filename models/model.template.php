<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle view loading
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   general
 * @discussion Handle loading of variables and showing of views
 * @author     jason.gerfen@gmail.com
 * @copyright  2008-2011 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.3
 */

/**
 *! @class template
 *  @abstract Handle view loading
 */
class template
{

 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;

 /**
  * @var vars array
  * @abstract Array of variables to set
  */
 private $vars = array();

 /**
  *! @function __construct
  *  @abstract Provides loading and global scope initialization of registry
  *  @param $registry object The current registry object to register
  */
 public function __construct($registry) {
  $this->registry = $registry;
 }

 /**
  *! @function __set
  *  @abstract Public method of setting key value pairs
  *  @param $index string The key to be indexed
  *  @param $value string The value associated with the key
  */
 public function __set($index, $value)
 {
  $this->vars[$index] = $value;
 }

 /**
  *! @function show
  *  @abstract Provides access to current views
  *  @param $name string The name of the file used as a view
  */
 public function show($name) {
  $path = __SITE . '/views' . '/' . $name . '.php';
  if (!file_exists($path)){
   exit('404');
  }

  foreach ($this->vars as $key => $value){
   $$key = $value;
  }
  include ($path);
 }

}

class templates
{
 public $strTemplateDir = '';
 public $strCacheDir    = '';
 public $strBeginTag    = '{';
 public $strEndTag      = '}';
 public $intTimeout     = 2629744;
 public $arrVars        = array();
 public $arrValues      = array();
 public $boolCache      = true;
 public $strBuffer      = null;

 public function __destruct()
 {
  $this->clear();
 }

 public function assign($strVar, $strValue, $strFile, $strFlag, $addr=false)
 {
  $this->arrVars[]   = $this->strBeginTag . '$' . $strVar  . $this->strEndTag;
  $this->arrValues[] = $strValue;
  if (!empty($strFile)) { return $this->display($strFile, $strFlag, "VAR", $addr); }
 }

 public function clear()
 {
  unset($this->arrVars, $this->arrValues);
 }

 public function display($strFile, $strFlag, $strCmd, $addr)
 {
  if ($this->boolCache===true) {
   if ((file_exists($this->strCacheDir.'/'.sha1($strFile.$addr).'.tpl')&&filemtime($this->strCacheDir.'/'.sha1($strFile.$addr).'.tpl')>=time()-$this->intTimeout)&&($strFlag===false)) {
    $resFile = @fopen($this->strCacheDir.'/'.sha1($strFile.$addr).'.tpl', 'r');
    $this->strBuff = fread($resFile, filesize($this->strCacheDir.'/'.sha1($strFile.$addr).'.tpl'));
    if ($strCmd==="VAR") {
     return $this->strBuff;
    } else {
     echo $this->strBuff;
    }
    @fclose($resFile);
   } else {
    $resFile = @fopen($this->strTemplateDir.'/'.$strFile, 'r');
    $strBuff = @fread($resFile, filesize($this->strTemplateDir.'/'.$strFile));
    $this->strBuff = str_replace($this->arrVars, $this->arrValues, $strBuff);
    @fclose($resFile);
    if ($strCmd==="VAR") {
     return $this->strBuff;
    } else {
     echo $this->strBuff;
    }
    $resFileCache = @fopen($this->strCacheDir.'/'.sha1($strFile.$addr).'.tpl', 'w');
    @fwrite($resFileCache, $this->strBuff);
   }
  } else {
   $resFile = @fopen($this->strTemplateDir.'/'.$strFile, 'r');
   $strBuff = @fread($resFile, filesize($this->strTemplateDir.'/'.$strFile));
   $this->strBuff = str_replace($this->arrVars, $this->arrValues, $strBuff);
   @fclose($resFile);
   if($strCmd==="VAR") {
    return $this->strBuff;
   } else {
    echo $this->strBuff;
   }
  }
 }
}
?>
