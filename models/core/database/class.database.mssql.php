<?PHP

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Handle mssql database functionality
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

class mssqlDBconn
{
 private $dbconn;
 private $connectionstring;
 private static $instance = null;
 private function __construct($settings)
 {
  if (function_exists('mssql_connect')) {
   return $this->dbconn = new msSQL($settings);
  } else {
   echo 'The mssql extensions are not loaded.';
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
 public function query($query) { return $this->dbconn->query($query); }
 public function results($link) { return $this->dbconn->results($link); }
 public function affected($link) { return $this->dbconn->affected($link); }
 public function error($link) { return $this->dbconn->error($link); }
}
class msSQL extends mssqlDBconn
{
 private $dbconn;
 private $configuration;
 public function __construct($configuration)
 {
  $this->configuration = $configuration;
  $this->dbconn = mssql_connect($configuration['hostname'], $configuration['username'], $configuration['password']);
  mssql_select_db($this->dbconn, $configuration['database']);
  return $this->dbconn;
 }
 public function query($query)
 {
  return mssql_query($query);
 }
 public function results($link)
 {
  $x=array(); $y;
  while ($y=mssql_fetch_assoc($link)) {
   $x[] = $y;
  }
  return $x;
 }
 public function affected($link)
 {
  return mssql_rows_affected($link);
 }
 public function error($link=null)
 {
  return mssql_get_last_message();
 }
 public function close($link)
 {
  return mssql_close($link);
 }
 public function __destruct()
 {
  if ($this->dbconn) {
   $this->dbconn->close();
  }
 }
}
?>
