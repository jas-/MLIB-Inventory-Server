<?PHP

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle postgres database functionality
 *
 *
 * LICENSE: This source file is subject to version 3.01 of the GPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/licenses/gpl.html.  If you did not receive a copy of
 * the GPL License and are unable to obtain it through the web, please
 *
 * @category   database
 * @package    phpMyFramework
 * @author     Original Author <jason.gerfen@gmail.com>
 * @copyright  2010 Jason Gerfen
 * @license    http://www.gnu.org/licenses/gpl.html  GPL License 3
 * @version    0.1
 */

class pgSQLDBconn
{
 private $dbconn;
 private $connectionstring;
 private static $instance = null;
 
 private function __construct($settings)
 {
  if (function_exists('pg_connect')) {
   $this->connectionstring = $this->createstring($settings);
   return $this->dbconn = new pgSQL($this->connectionstring);
  } else {
   echo 'The postgres extensions are not loaded.';
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
 private function createstring($settings)
 {
  return 'host='.$settings['hostname'].' dbname='.$settings['database'].' user='.$settings['username'].' password='.$settings['password'];
 }
 public function query($query) { return $this->dbconn->query($query); }
 public function results($link) { return $this->dbconn->results($link); }
 public function affected($link) { return $this->dbconn->affected($link); }
 public function sanitize($string) { return $this->dbconn->sanitize($string); }
 public function error($link) { return $this->dbconn->error($link); }
}

class pgSQL extends pgSQLDBconn
{
 private $dbconn;
 public function __construct($connectionstring)
 {
  $this->dbconn = pg_connect($connectionstring);
  return $this->dbconn;
 }
 public function query($query)
 {
  return pg_query($query);
 }
 public function results($link)
 {
  return pg_fetch_array($link, NULL, PGSQL_ASSOC);
 }
 public function affected($link)
 {
  return pg_affected_rows($link);
 }
 public function sanitize($string)
 {
  return pg_escape_string($string);
 }
 public function error($link)
 {
  return pg_last_error($link);
 }
 public function close($link)
 {
  return pg_close($link);
 }
 public function __destruct()
 {
  if ($this->dbconn) {
   $this->dbconn->close();
  }
 }
}
?>