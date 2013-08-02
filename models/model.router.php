<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle routing of controllers
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   general
 * @discussion Handle routing to controllers
 * @author     jason.gerfen@gmail.com
 * @copyright  2008-2011 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.3
 */

/**
 *! @class router
 *  @abstract Handle routing to controllers
 */
class router
{
 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;

 /**
  * @var path string
  * @abstract Absolute path
  */
 private $path;

 /**
  * @var args array
  * @abstract Array of optional arguments
  */
 private $args = array();

 public $file;

 public $controller;

 public $action;

 public function __construct($registry){
  $this->registry = $registry;
 }

 public function setPath($path){
  $this->path = (is_dir($path)) ? $path : false;
 }

 public function loader()
 {
  $this->getController();

  if (!is_readable($this->file)){
   exit('404');
  }
  include $this->file;

  $class = $this->controller.'Controller';
  $controller = new $class($this->registry);

  $action = (!is_callable(array($controller, $this->action))) ? 'index' : $this->action;
  $controller->$action($this->registry);
 }

 private function getController()
 {
  $route = (empty($_GET['nxs'])) ? '' : $_GET['nxs'];

  if (empty($route)){
   $route = 'index';
  } else {
   $parts = explode('/', $route);
   $this->controller = $parts[0];
   if(isset( $parts[1])){
    $this->action = $parts[1];
   }
  }

  if (empty($this->controller)){
   $this->controller = 'index';
  }

  if (empty($this->action)){
   $this->action = 'index';
  }
  $this->file = $this->path.'/controller.'.$this->controller.'.php';
 }
}

?>
