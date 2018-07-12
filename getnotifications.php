<?php
header('Content-Type: application/json');

include("connection.php");
include("helper.php");

$get_event_stmt = $db->prepare("SELECT internal_ID,notif_nav,notif_icon,config_version,notif_version FROM event where ID=:id");

$get_event_stmt->bindParam(":id",$_GET['id']);

$get_event_stmt->execute();

$get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);

if(count($get_event_res) != 1) {
	die();
}

$get_event_res = $get_event_res[0];

$output = array(
	"notifications" => array(
		"version_num"=>$get_event_res["config_version"].",".$get_event_res["notif_version"],
		"nav" => $get_event_res["notif_nav"],
		"icon" => $get_event_res["notif_icon"]
	)
);

$get_notif_stmt = $db->prepare("SELECT * FROM notifications where event_ID=:id");

$get_notif_stmt->bindValue(":id",$get_event_res["internal_ID"]);

$get_notif_stmt->execute();

// If we have a contact page section then include the contact_page section.
while($get_notif_res = $get_notif_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["notifications"][$get_notif_res["ID"]] = array(
		"title" => $get_notif_res["title"],
		"body" => $get_notif_res["body"],
		"date" => date("m/d/Y H:i:s",strtotime($get_notif_res["date"])),
		"refresh" => ($get_notif_res["refresh"] == 0) ? false : true
	);
} 

echo json_encode_noescape($output);

?>
