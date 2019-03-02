<?php 
// Remove for production
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

$db = new PDO($config['db'], $config['dbuser'], $config['dbpass']);
date_default_timezone_set($config['tz']);

$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

?>
