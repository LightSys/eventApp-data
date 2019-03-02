<?php
header('Content-Type: application/json');

include("global.php");
include("getevent_data.php");

$output = array();

add_data();

echo json_encode_noescape($output);

?>
