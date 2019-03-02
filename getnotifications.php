<?php
header('Content-Type: application/json');

include("global.php");
include("getnotifications_data.php");

$output = array();

add_notify();

echo json_encode_noescape($output);

?>
