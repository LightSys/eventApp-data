<?php
header('Content-Type: application/json');

include("global.php");

$cur_config = isset($_GET['config'])?intval($_GET['config']):-1;
$cur_notify = isset($_GET['notify'])?intval($_GET['notify']):-1;

$get_event_stmt = $db->prepare("SELECT internal_ID,notif_nav,notif_icon,config_version,notif_version FROM event where ID=:id");
$get_event_stmt->bindParam(":id",$_GET['id']);
$get_event_stmt->execute();
$get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);

if(count($get_event_res) != 1) {
	die();
}

$get_event_res = $get_event_res[0];

$output = array();
$output["version_num"] = $get_event_res["config_version"].",".$get_event_res["notif_version"];

if ($get_event_res && intval($get_event_res["config_version"]) != $cur_config) {
	include("getevent_data.php");
	add_data();
}

if ($get_event_res && intval($get_event_res["notif_version"]) != $cur_notify) {
	include("getnotifications_data.php");
	add_notify();
}

echo json_encode_noescape($output);

?>
