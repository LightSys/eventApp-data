<?php
header('Content-Type: application/json');

include("connection.php");

if (!($get_event_stmt = $db->prepare("SELECT internal_ID FROM event where ID=:id"))) {
	die();
}

if(!$get_event_stmt->bindParam(":id",$_GET['id'])) {
	die();
}

if(!$get_event_stmt->execute()) {
	die();
}

$get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);

if(count($get_event_res) != 1) {
	die();
}

$get_event_res = $get_event_res[0];

$output = array(
	"notifications" => array(
		"nav" => $get_event_res["notif_nav"],
		"icon" => $get_event_res["notif_icon"]
	)
);

if (!($get_notif_stmt = $db->prepare("SELECT * FROM notifications where event_ID=:id"))) {
	die();
}

if(!$get_notif_stmt->bindValue(":id",$get_event_res["internal_ID"])) {
	die();
}

if(!$get_notif_stmt->execute()) {
	die();
}

// If we have a contact page section then include the contact_page section.
while($get_notif_res = $get_notif_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["notifications"][$get_notif_res["ID"]] = array(
		"title" => $get_notif_res["title"],
		"body" => $get_notif_res["body"],
		"date" => date("m/d/Y H:i:s",strtotime($get_notif_res["date"])),,
		"refresh" => ($get_notif_res["refresh"] == 0) ? false : true
	);
} 

echo json_encode($output);

?>