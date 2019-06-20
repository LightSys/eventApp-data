<?php
include("global.php");
ini_set('display_errors',1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL); 
mysqli_report(MYSQLI_REPORT_ALL);


$get_log_stmt= $db->prepare("SELECT logo FROM event where ID=:id");
$get_log_stmt->bindValue(":id",$_GET['id']);
$get_log_stmt->execute();
$addr = ''; 
$row = $get_log_stmt->fetch(PDO::FETCH_BOTH);
header("Content-type: image/gif");
echo base64_decode($row['logo']); 

?>
