<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle default page views
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   views
 * @discussion Handles default page views
 * @author     jason.gerfen@gmail.com
 * @copyright  2008-2012 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.3
 */

/**
 *! @class indexView
 *  @abstract Handles default page views
 */
class indexView
{

 /**
  * @var registry object
  * @abstract Global class handler
  */
 private $registry;

 /**
  *! @var instance object - class singleton object
  */
 protected static $instance;

 /**
  *! @function __construct
  *  @abstract Initializes singleton for indexView class
  *  @param registry array - Global array of class objects
  */
 private function __construct($registry)
 {
  $this->registry = $registry;
  $this->registry->tpl = new templates();
  $this->registry->tpl->strTemplateDir = $this->registry->opts['template'];
  $this->registry->tpl->strCacheDir = $this->registry->opts['caching'];
  $this->registry->tpl->boolCache=true;
  $this->registry->tpl->intTimeout=2629744;
  $this->_header();
  $this->_main();
  $this->_footer();
 }

 /**
  *! @function instance
  *  @abstract Creates non-deserializing, non-cloneable instance object
  *  @param configuration array - server, username, password, database
  *  @return Singleton - Singleton object
  */
 public static function instance($configuration)
 {
  if (!isset(self::$instance)) {
   $c = __CLASS__;
   self::$instance = new self($configuration);
  }
  return self::$instance;
 }

 /**
  *! @function _main
  *  @abstract Handles all template loading at once
  */
 private function _main()
 {
  $this->__main();
  $this->_menu();
  $this->_login();
  $this->registry->tpl->display('index.tpl', true, null, $this->registry->libs->_getRealIPv4());
 }

 /**
  *! @function _header
  *  @abstract Assigns necessary template variables and loads default header
  *            template
  */
 private function _header()
 {
  $this->registry->tpl->assign('title',
                               $this->registry->opts['title'], null, null, null);
  $this->registry->tpl->assign('timeout',
                               $this->registry->opts['timeout'], null, null, null);
  $this->registry->tpl->assign('templates',
                               $this->registry->tpl->strTemplateDir, null, null, null);
  $this->registry->tpl->assign('token',
                               $_SESSION['csrf'], null, null, null);
  $this->registry->tpl->display('header.tpl', true, null,
                                $this->registry->libs->_getRealIPv4());
 }

 /**
  *! @function _footer
  *  @abstract Loads the default footer template
  */
 private function _footer()
 {
  $this->registry->tpl->display('footer.tpl', true, null, $this->registry->libs->_getRealIPv4());
 }

 /**
  *! @function __main
  *  @abstract Creates and loads nested main template (used for primary page content)
  */
 private function __main()
 {
  $this->registry->tpl->assign('main', $this->registry->tpl->assign(null, null, 'main.tpl', true, $this->registry->libs->_getRealIPv4()), null, null);
 }

 /**
  *! @function _login
  *  @abstract Loads the login template
  */
 private function _login()
 {
  $this->registry->tpl->assign('login', $this->registry->tpl->assign(null, null, 'login.tpl', true, $this->registry->libs->_getRealIPv4()), null, null);
 }

 /**
  *! @function _menu
  *  @abstract Here a menu system is loaded
  */
 private function _menu()
 {
  $this->registry->tpl->assign('menu', $this->registry->tpl->assign(null, null, 'menu.tpl', true, $this->registry->libs->_getRealIPv4()), null, null);
 }

 /**
  *! @function __clone
  *  @abstract Prevent cloning of singleton object
  */
 public function __clone() {
  trigger_error('Cloning prohibited', E_USER_ERROR);
 }

 /**
  *! @function __wakeup
  *  @abstract Prevent deserialization of singleton object
  */
 public function __wakeup() {
  trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
 }
}
?>