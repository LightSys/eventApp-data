<?php
session_start();
session_unset();
session_destroy();
header("Location: http://10.5.11.121/emmett/eventApp-data/login.php");
die();
?>
