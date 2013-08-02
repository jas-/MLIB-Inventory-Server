<?php

/* prevent direct access */
if (!defined('__SITE')) exit('No direct calls please...');

/**
 * Installer class
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

class install {
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
 public function uuid() {
  return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff),
                 mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000,
                 mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff),
                 mt_rand(0, 0xffff), mt_rand(0, 0xffff));
 }
 public function geolocation($ip)
 {
  $opts = array('http'=>array('method'=>'GET','header'=>'Accept-language: en\r\n'));
  $context = stream_context_create($opts);
  $ex = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip, false, $context));
  return $ex;
 }
 public function parsegeo($data, $ip, $config)
 {
  $settings['organizationName'] = $ip;
  $settings['organizationalUnitName'] = $ip;
  $settings['emailAddress'] = $ip;
  $settings['localityName'] = (!empty($data['geoplugin_city'])) ?
                               $data['geoplugin_city'] :
                               $config['dn']['localityName'];
  $settings['stateOrProvinceName'] = (!empty($data['geoplugin_region'])) ?
                                      $data['geoplugin_region'] :
                                      $config['dn']['stateOrProvinceName'];
  $settings['countryName'] = (!empty($data['geoplugin_countryCode'])) ?
                              $data['geoplugin_countryCode'] :
                              $config['dn']['CountryName'];
  $settings['commonName'] = ((!empty($data['geoplugin_latitude']))&&
                             (!empty($data['geoplugin_longitude']))) ?
                              $data['geoplugin_latitude'].
                              '::'.$data['geoplugin_longitude'] : $ip;
  return $settings;
 }
 public function _16($string)
 {
  return substr($string, round(strlen($string)/3, 0, PHP_ROUND_HALF_UP), 16);
 }
 public function setDN($ssl, $handles)
 {
  if (empty($ssl)) {
   $a = $this->parsegeo($this->geolocation($_SERVER['REMOTE_ADDR']),
                                           $_SERVER['REMOTE_ADDR'],
                                           $ssl['dn']);
   $a = $handles['filter']->__do($a);
   $sql = sprintf('CALL ConfigurationOpenSSLDNUpdate("%s", "%s", "%s", "%s", "%s",
                                                     "%s", "%s", @x)',
                   $handles['db']->sanitize($a['countryName']),
                   $handles['db']->sanitize($a['stateOrProvinceName']),
                   $handles['db']->sanitize($a['localityName']),
                   $handles['db']->sanitize($a['organizationName']),
                   $handles['db']->sanitize($a['organizationalUnitName']),
                   $handles['db']->sanitize($a['commonName']),
                   $handles['db']->sanitize($a['emailAddress']));
   try {
    $x = $handles['db']->query($sql);
    return ($x['@x']==='0') ? false : true;
   } catch (PDOException $e) {
    return false;
   }
  }
  return false;
 }
 public function setup($results, $handles)
 {
  if ($results['@x']==='0') {
   $pass = $handles['openssl']->aesEnc($this->uuid(), $this->uuid(),
                                       $this->_16($this->uuid()));
   $x = $handles['openssl']->genPriv($pass);
   $a = $handles['openssl']->genPub();

   $pvkey = $handles['openssl']->aesEnc($x, $pass, $this->_16($pass));
   $pkey = $handles['openssl']->aesEnc($a['key'], $pass,
                                       $this->_16($pass));

   $sql = sprintf('CALL ConfigurationUpdate("%s", "%s", "%s", "%d", "%s", "%d",
                                            "%s", "%s", "%s", @x)',
                  $handles['db']->sanitize($_POST['title']),
                  $handles['db']->sanitize($_POST['templates']),
                  $handles['db']->sanitize('cache'),
                  $handles['db']->sanitize(1),
                  $handles['db']->sanitize($_POST['email']),
                  $handles['db']->sanitize($_POST['timeout']),
                  $handles['db']->sanitize($pkey),
                  $handles['db']->sanitize($pvkey),
                  $handles['db']->sanitize($pass));
   try {
    $i = $handles['db']->query($sql);
    return ($i['@x']==='0') ? false : true;
   } catch (PDOException $e) {
    return false;
   }
  }
  return true;
 }
 public function acct($handles, $configuration)
 {
  $password = $handles['openssl']->penc($_POST['password'],
                                        $handles['openssl']->aesDenc($configuration['pkey'],
                                                                     $configuration['pass'],
                                                                     $this->_16($configuration['pass'])));
  $sql = sprintf('CALL AuthenticationUserAddUpdate("%s", "%s", "%s", "%s", "%s")',
                 $handles['db']->sanitize(sha1($_POST['email'])),
                 $handles['db']->sanitize($_POST['email']),
                 $handles['db']->sanitize($password),
                 $handles['db']->sanitize('admin'),
                 $handles['db']->sanitize('admin'));
  try {
   $x = $handles['db']->query($sql);
   return ($x['@x']==='0') ? false : true;
  } catch (PDOException $e) {
   return false;
  }
 }
 public function permissions($handles)
 {
  $sql = sprintf('CALL ResourcesAddNew("%s", "%s", "%s", "%s", "%d", "%d", "%s",
                                       "%d", "%d", @x)',
                 $handles['db']->sanitize(sha1($_POST['email'])),
                 $handles['db']->sanitize($_POST['email']),
                 $handles['db']->sanitize($_POST['email']),
                 $handles['db']->sanitize('admin'),
                 $handles['db']->sanitize(1), $handles['db']->sanitize(1),
                 $handles['db']->sanitize($_POST['email']),
                 $handles['db']->sanitize(1), $handles['db']->sanitize(1));
  try {
   $i = $handles['db']->query($sql);
   return ($i['@x']==='0') ? false : true;
  } catch (PDOException $e) {
   return false;
  }
 }
 public function templates($folder)
 {
  if (is_dir($folder)) {
   if (($handle = opendir($folder))!==false) {
    while (($dir = readdir($handle))!==false) {
     if (($dir!=='.')&&($dir!=='..')) {
      $dirs[] = preg_replace('/..\//', '', $folder).'/'.$dir;
     }
    }
   } else {
    return false;
   }
  }
  return (count($dirs)!==0) ? $dirs : false;
 }
 public function genselect($array, $name)
 {
  if ($array!==false) {
   $m = '<select name="templates" style="width: 61%">';
   foreach($array as $key => $value) {
    if (!empty($value)) {
     $m .= '<option value="'.$value.'">'.$value.'</option>';
    }
   }
   $m .= '</select>';
  }
  return (!empty($m)) ? $m : false;
 }
 public function __do($dbs, $filter, $variables, $errors, $language)
 {
  $variables['error'] = $errors[$language]['\x0iB'];
  $variables['class'] = 'good';
  $db = dbconn::instance(array('hostname'=>'localhost',
                               'username'=>$_POST['ruser'],
                               'password'=>$_POST['rpassword'],
                               'database'=>''));
  if (is_object($db)) {

   if (($x = $this->_createdb($db, $errors))!==true) {
    $variables['error'] = $x;
   }

   if (($x = $this->_createdbuser($db, $errors))!==true) {
    $variables['error'] = $x;
   }

   if (($x = $this->_usedb($db, $errors))!==true) {
    $variables['error'] = $x;
   }

   if (($x = $this->_setupdb($db, $errors))!==true) {
    $variables['error'] = $x;
   }

   if (($x = $this->_setupdbuser($db, $errors))!==true) {
    $variables['error'] = $x;
   }

   if (($x = $this->_importsp($db))!==true) {
    $variables['error'] = $x;
   }

   if ((!is_array($ssl['dn']))&&(count($ssl['dn'])>=0)) {
    if ($this->setDN($ssl['dn'], array('db'=>$db, 'filter'=>$filter,
                                       'language'=>$language))===false) {
     $variables['error'] = $errors[$languages]['\x0i0'];
    }
   }
   $ssl['dn'] = $db->query('CALL ConfigurationOpenSSLDNSelect()');
   $openssl = openssl::instance($ssl);
   $results = $db->query('CALL ConfigurationCheck(@x)');
   if ($results['@x']==='0') {
    if ((empty($_POST['title']))&&(empty($_POST['templates']))&&
        (empty($_POST['timeout']))&&(empty($_POST['email']))) {
     $configuration = array('title'=>'empty title', 'templates'=>'templates/default',
                            'cache'=>'cache', 'private'=>1, 'email'=>'admin',
                            'timeout'=>3600);
    }
    if ($this->setup($results, array('db'=>$db, 'filter'=>$filter, 'openssl'=>$openssl,
                                     'language'=>$language))===false) { echo 1;
     $variables['error'] = $errors[$language]['\x0i1'];
    }
   }
   $configuration = $db->query('CALL ConfigurationSelect()');
   if ((!empty($_POST['email']))&&(!empty($_POST['password']))) {
    if ($this->acct(array('db'=>$db, 'filter'=>$filter, 'openssl'=>$openssl),
                    $configuration)===false) {
     $variables['error'] = $errors[$language]['\x0i2'];
    }
    if ($this->permissions(array('db'=>$db, 'filter'=>$filter))===false) {
     $variables['error'] = $errors[$language]['\x0i3'];
    }
   }
   $variables['class'] = ($variables['error']!==$errors[$language]['\x0iB']) ? 'error' : 'good';
   return $variables;
  }
 }
 public function _createdb($db, $errors)
 {
  $a = sprintf('CREATE DATABASE %s', $db->sanitize($_POST['dbname']));
  try {
   $db->query($a);
  } catch (PDOException $e) {
   $error = $errors[$language]['\x0i4'];
  }
  return (isset($error)) ? $error : true;
 }
 public function _createdbuser($db, $errors)
 {
  $error = true;
  $b = sprintf('CREATE USER "%s"@"%s" IDENTIFIED BY "%s"',
               $db->sanitize($_POST['uname']), $db->sanitize($_POST['server']),
               $db->sanitize($_POST['upassword']));
  try {
   $db->query($b);
  } catch (PDOException $e) {
   $error = $errors[$language]['\x0i5'];
  }
  return (isset($error)) ? $error : true;
 }
 public function _usedb($db, $errors)
 {
  $c = sprintf('USE %s', $db->sanitize($_POST['dbname']));
  try {
   $db->query($c);
  } catch(PDOException $e) {
   $error = $errors[$language]['\x0i6'];
  }
  return (isset($error)) ? $error : true;
 }
 public function _setupdb($db, $errors)
 {
  if (file_exists('sql/database-schema.sql')) {
   $d = implode("\n", file('sql/database-schema.sql'));
   try {
    $db->query($d);
   } catch(PDOException $e) {
    $error = $errors[$language]['\x0i7'];
   }
  } else {
   $error = $errors[$language]['\x0i7'];
  }
  return (isset($error)) ? $error : true;
 }
 public function _setupdbuser($db, $errors)
 {
  $e = sprintf('GRANT SELECT, INSERT, UPDATE, DELETE, REFERENCES, INDEX,
                CREATE TEMPORARY TABLES, LOCK TABLES, TRIGGER, EXECUTE ON
                `%s`.* TO "%s"@"%s"', $db->sanitize($_POST['dbname']),
                $db->sanitize($_POST['uname']), $db->sanitize($_POST['server']));
  try {
   $db->query($e);
  } catch (PDOException $e) {
   $error = $errors[$language]['\x0i8'];
  }
  return (isset($error)) ? $error : true;
 }
 public function _importsp($db)
 {
  if (file_exists('sql/prepared-statements.sql')) {
   $cmd = sprintf('mysql -u %s --password=%s --database %s < sql/prepared-statements.sql',
                  $db->sanitize($_POST['ruser']), $db->sanitize($_POST['rpassword']),
                  $db->sanitize($_POST['dbname']));
   /* flipping importing stored procedures fails with PDO */
   `$cmd`;
  }
  return true;
 }
 public function _getsslcnf($db, $errors)
 {
  try {
   $cnf = $db->query('CALL ConfigurationOpenSSLCNFSelect()');
  } catch (PDOException $e) {
   $error = $errors[$language]['\x0i10'];
  }
  return ((is_array($cnf))&&(count($cnf)>=6)) ? $cnf : $error;
 }
 public function _getssldn($db, $errors)
 {
  try {
   $dn = $db->query('CALL ConfigurationOpenSSLDNSelect()');
  } catch (PDOException $e) {
   $error = $errors[$language]['\x0i11'];
  }
  return ((is_array($dn))&&(count($dn)>=7)) ? $dn : $error;
 }
 public function _set($array)
 {
  $array['ruser'] = (!empty($array['ruser'])) ? $array['ruser'] : '';
  $array['ruserErr'] = '*';
  $array['rpassword'] = (!empty($array['rpassword'])) ? $array['rpassword'] : '';
  $array['rpasswordErr'] = '*';
  $array['dbname'] = (!empty($array['dbname'])) ? $array['dbname'] : '';
  $array['dbnameErr'] = '*';
  $array['server'] = (!empty($array['server'])) ? $array['server'] : '';
  $array['serverErr'] = '*';
  $array['uname'] = (!empty($array['uname'])) ? $array['uname'] : '';
  $array['unameErr'] = '*';
  $array['upassword'] = (!empty($array['upassword'])) ? $array['upassword'] : '';
  $array['upasswordErr'] = '*';
  $array['ptitle'] = (!empty($array['ptitle'])) ? $array['title'] : '';
  $array['ptitleErr'] = '*';
  $array['timeout'] = (!empty($array['timeout'])) ? $array['timeout'] : '';
  $array['timeoutErr'] = '*';
  $array['email'] = (!empty($array['email'])) ? $array['email'] : '';
  $array['emailErr'] = '*';
  $array['password'] = (!empty($array['password'])) ? $array['password'] : '';
  $array['passwordErr'] = '*';
  return $array;
 }
 public function _isempty($array, $errors, $lang, $e, $t)
 {
  $err['ruser'] = (empty($array['ruser'])) ? 'Root username: '.$errors[$lang]['\x0v5'] : false;
  $err['ruserErr'] = (empty($array['ruser'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  $err['rpassword'] = (empty($array['rpassword'])) ? 'Root password: '.$errors[$lang]['\x0v5'] : false;
  $err['rpasswordErr'] = (empty($array['rpassword'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  $err['dbname'] = (empty($array['dbname'])) ? 'Database name: '.$errors[$lang]['\x0v5'] : false;
  $err['dbnameErr'] = (empty($array['dbname'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  $err['server'] = (empty($array['server'])) ? 'Server address: '.$errors[$lang]['\x0v5'] : false;
  $err['serverErr'] = (empty($array['server'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  $err['uname'] = (empty($array['uname'])) ? 'Username: '.$errors[$lang]['\x0v5'] : false;
  $err['unameErr'] = (empty($array['uname'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  $err['upassword'] = (empty($array['upassword'])) ? 'Password: '.$errors[$lang]['\x0v5'] : false;
  $err['upasswordErr'] = (empty($array['upassword'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  $err['ptitle'] = (empty($array['ptitle'])) ? 'Project title: '.$errors[$lang]['\x0v5'] : false;
  $err['ptitleErr'] = (empty($array['ptitle'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  $err['timeout'] = (empty($array['timeout'])) ? 'Timeout: '.$errors[$lang]['\x0v5'] : false;
  $err['timeoutErr'] = (empty($array['timeout'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  $err['email'] = (empty($array['email'])) ? 'Email: '.$errors[$lang]['\x0v5'] : false;
  $err['emailErr'] = (empty($array['email'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  $err['password'] = (empty($array['password'])) ? 'Password: '.$errors[$lang]['\x0v5'] : false;
  $err['passwordErr'] = (empty($array['password'])) ? $e->_imglink('help.php', 'required', $t.'/images/icons/icon-warning.png') : '';
  return $err;
 }
 public function _valid($array, $filter, $errors, $lang)
 {
  if (count($array)>=10) {
   $err['ruser'] = (!filter_var($array['ruser'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z0-9]{1,24}$/')))) ? 'Root username: '.$errors[$lang]['\x0v0'] : false;
   $err['rpassword'] = (!filter_var($array['rpassword'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z0-9-_]{1,24}$/')))) ? 'Root password: '.$errors[$lang]['\x0v0'] : false;
   $err['dbname'] = (!filter_var($array['dbname'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z0-9-_]{1,24}$/')))) ? 'Database name: '.$errors[$lang]['\x0v0'] : false;
   $err['server'] = (!filter_var($array['server'], FILTER_VALIDATE_IP)) ? 'Server address: '.$errors[$lang]['\x0v2'] : false;
   $err['uname'] = (!filter_var($array['uname'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z0-9-_]{1,24}$/')))) ? 'Username: '.$errors[$lang]['\x0v0'] : false;
   $err['upassword'] = (!filter_var($array['upassword'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z0-9-_]{1,24}$/')))) ? 'Password: '.$errors[$lang]['\x0v0'] : false;
   $err['ptitle'] = (!filter_var($array['ptitle'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z0-9-_]{1,24}$/')))) ? 'Project title: '.$errors[$lang]['\x0v0'] : false;
   $err['timeout'] = (!filter_var($array['timeout'], FILTER_VALIDATE_INT)) ? 'Timeout: '.$errors[$lang]['\x0v1'] : false;
   $err['email'] = (!filter_var($email, FILTER_VALIDATE_EMAIL)) ? 'Email: '.$errors[$lang]['\x0v3'] : false;
   $err['password'] = (!filter_var($array['password'], FILTER_VALIDATE_REGEXP, array('options'=>array('regexp'=>'/^[a-z0-9-_]{1,24}$/')))) ? 'Password: '.$errors[$lang]['\x0v4'] : false;
  }
  return (isset($err)) ? $err : false;
 }
 public function _validate($array, $filter, $errors, $lang)
 {
  if (count($array)>=10) {
   $err['ruser'] = ((empty($array['ruser']))&&($filter->type($array['ruser'], 'string')===false)) ? 'Root username: '.$errors[$lang]['\x0v0'] : false;
   $err['rpassword'] = ((empty($array['rpassword']))&&($filter->type($array['rpassword'], 'string')===false)) ? 'Root password: '.$errors[$lang]['\x0v0'] : false;
   $err['dbname'] = ((empty($array['dbname']))&&($filter->type($array['dbname'], 'string')===false)) ? 'Database name: '.$errors[$lang]['\x0v0'] : false;
   $err['server'] = ((empty($array['server']))&&($filter->type($array['server'], array('string', 'integer'))===false)) ? 'Server address: '.$errors[$lang]['\x0v2'] : false;
   $err['uname'] = ((empty($array['uname']))&&($filter->type($array['uname'], 'string')===false)) ? 'Username: '.$errors[$lang]['\x0v0'] : false;
   $err['upassword'] = ((empty($array['upassword']))&&($filter->type($array['upassword'], 'string')===false)) ? 'Password: '.$errors[$lang]['\x0v0'] : false;
   $err['ptitle'] = ((empty($array['ptitle']))&&($filter->type($array['title'], 'string')===false)) ? 'Project title: '.$errors[$lang]['\x0v0'] : false;
   $err['timeout'] = ((empty($array['timeout']))&&($filter->type($array['timeout'], 'integer')===false)) ? 'Timeout: '.$errors[$lang]['\x0v1'] : false;
   $err['email'] = ((empty($array['email']))&&($filter->type($array['email'], 'email')===false)) ? 'Email: '.$errors[$lang]['\x0v3'] : false;
   $err['password'] = ((empty($array['password']))&&($filter->type($array['password'], 'password')===false)) ? 'Password: '.$errors[$lang]['\x0v4'] : false;
  }
  return (isset($err)) ? $err : false;
 }
 public function __clone() {
  trigger_error('Cloning prohibited', E_USER_ERROR);
 }
 public function __wakeup() {
  trigger_error('Deserialization of singleton prohibited ...', E_USER_ERROR);
 }
 private function __destruct()
 {
  unset(self::$instance);
  return true;
 }
}
?>
