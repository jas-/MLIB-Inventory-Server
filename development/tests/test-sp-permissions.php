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



?>
