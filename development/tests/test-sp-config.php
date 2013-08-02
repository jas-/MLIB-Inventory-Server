<?php

/* simple db settings */
$settings['database'] = array('hostname'=>'localhost',
                              'username'=>'inventory2011',
                              'password'=>'d3v3l0pm3n+',
                              'database'=>'inventory2011');

/* configuration settings exist? */
if (!is_array($settings)) {
 /* error in installation (empty configuration) */
 exit('Error with configuration settings');
}

/* ensure settings have values */
if ((empty($settings['database']['hostname']))&&
    (empty($settings['database']['username']))&&
    (empty($settings['database']['password']))&&
    (empty($settings['database']['database']))) {
 /* call the installer? */
 exit('Calling installer');
}

/* database handle */
require 'class.database.mysql.php';
$db = dbconn::instance($settings['database']);

echo '<pre>'; print_r($db); echo '</pre>';

/* Get application settings
try {
 $s = $db->query('CALL ConfigurationCheck(@x)');
 if ($s['@x']==='0') {
  exit('Missing configuration, calling installer');
 }
 try {
  $settings = $db->query('CALL ConfigurationSelect()');
 } catch(PDOException $e) {
  exit('Error retreiving existing configuration');
 }
} catch(PDOException $e) {
 exit('Error retrieving count of existing configuration');
}
*/
?>
