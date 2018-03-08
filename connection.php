<?php 
// Remove for production
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new PDO('mysql:host=localhost;dbname=eventdb', 'root', 'password');
date_default_timezone_set("America/Denver");

$db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

?>