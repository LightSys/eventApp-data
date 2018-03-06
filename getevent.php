<?php
header('Content-Type: application/json');

include("connection.php");

if (!($get_event_stmt = $db->prepare("SELECT * FROM event where ID=:id"))) {
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
	"general" => array(
		"refresh" => $get_event_res["refresh"],
		"refresh_expire" => date("m/d/Y",strtotime($get_event_res["refresh_expire"])),
		"time_zone" => $get_event_res["time_zone"],
		"welcome_message" => $get_event_res["welcome_message"],
		"notifications_url" => strtok(full_url($_SERVER),'?') . "?id=" . $_GET['id'] . "&type=notifications",
		"logo" => $get_event_res["logo"]
	)
);

if (!($get_cpages_stmt = $db->prepare("SELECT * FROM contact_page_sections where event_ID=:id"))) {
	die();
}

if(!$get_cpages_stmt->bindValue(":id",$get_event_res["internal_ID"])) {
	die();
}

if(!$get_cpages_stmt->execute()) {
	die();
}

// If we have a contact page then include that information.
if($get_cpages_res = $get_cpages_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["contact_page"] = array(
		"nav" => $get_event_res["contact_nav"],
		"icon" => $get_event_res["contact_icon"]
	);

	do {
		$output["contact_page"]["section_" . $get_cpages_res["ID"]] = array(
			"header" => $get_cpages_res["header"],
			"content" => $get_cpages_res["content"],
			"id" => $get_cpages_res["ID"]
		);
	} while($get_cpages_res = $get_cpages_stmt->fetch(PDO::FETCH_ASSOC));
}

if (!($get_contacts_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id"))) {
	die();
}

if(!$get_contacts_stmt->bindValue(":id",$get_event_res["internal_ID"])) {
	die();
}

if(!$get_contacts_stmt->execute()) {
	die();
}

// If we have a contact page then include that information.
if($get_contacts_res = $get_contacts_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["contacts"] = array();

	do {
		$output["contacts"][$get_contacts_res["name"]] = array(
			"address" => $get_contacts_res["address"],
			"phone" => $get_contacts_res["phone"],
		);
	} while($get_contacts_res = $get_contacts_stmt->fetch(PDO::FETCH_ASSOC));
}

echo json_encode($output);

?>