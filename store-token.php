<?php

include("global.php");

$event_id = $_POST["eventID"];
$token = $_POST["deviceToken"];

echo "THIS IS THE EVENT ECHOED:".$event_id;
echo "\nTHIS IS THE TOKEN ECHOED:". $token;

// $sql = 'insert into device_tokens (event_ID, token) values ('.$event_id.', '.$token.')';
// echo $sql;
$stmt = $db->prepare('INSERT into device_tokens(event_ID, token) values(:event_id, :token)');
$stmt->bindValue(':event_id', $event_id);
$stmt->bindValue(':token', $token);
//echo "THIS IS TEH QUERY: " . $stmt;
$stmt->execute();

// echo "\nTHIS MEANS THAT WE WIN";


?>
