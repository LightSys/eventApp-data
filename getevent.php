<?php
header('Content-Type: application/json');

include("connection.php");


///// General data


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
		"year" => $get_event_res["year"],
		"logo" => $get_event_res["logo"]
	)
);


///// Themes

if (!($get_themes_stmt = $db->prepare("SELECT * FROM themes where event_ID=:id"))) {
	die();
}

if (!$get_themes_stmt->bindValue(":id", $get_event_res["internal_ID"])) {
	die();
}

if (!$get_themes_stmt->execute()) {
	die();
}

if($get_themes_res = $get_themes_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["theme"] = array();

	do {
		$output["theme"][] = array(
			$get_themes_res["theme_name"] => $get_themes_res["theme_color"],
		);
	} while ($get_themes_res = $get_themes_stmt->fetch(PDO::FETCH_ASSOC));
}

///// Contact page sections


if (!($get_cpages_stmt = $db->prepare("SELECT * FROM contact_page_sections where event_ID=:id"))) {
	die();
}

if(!$get_cpages_stmt->bindValue(":id",$get_event_res["internal_ID"])) {
	die();
}

if(!$get_cpages_stmt->execute()) {
	die();
}

// If we have a contact page section then include the contact_page section.
if($get_cpages_res = $get_cpages_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["contact_page"] = array(
		"nav" => $get_event_res["contact_nav"],
		"icon" => $get_event_res["contact_icon"]
	);

	do {
		$output["contact_page"]["section_" . $get_cpages_res["ID"]] = array(
			"header" => $get_cpages_res["header"],
			"content" => $get_cpages_res["content"],
			"id" => $get_cpages_res["ID"]-1
		);
	} while($get_cpages_res = $get_cpages_stmt->fetch(PDO::FETCH_ASSOC));
}


////// Contacts


if (!($get_contacts_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id"))) {
	die();
}

if(!$get_contacts_stmt->bindValue(":id",$get_event_res["internal_ID"])) {
	die();
}

if(!$get_contacts_stmt->execute()) {
	die();
}

// If we have a contacts then include that section.
if($get_contacts_res = $get_contacts_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["contacts"] = array();

	do {
		$output["contacts"][$get_contacts_res["name"]] = array(
			"address" => $get_contacts_res["address"],
			"phone" => $get_contacts_res["phone"],
		);
	} while($get_contacts_res = $get_contacts_stmt->fetch(PDO::FETCH_ASSOC));
}


///// Schedule


if (!($get_sched_item_stmt = $db->prepare("SELECT * FROM schedule_items where event_ID=:id"))) {
	die();
}

if(!$get_sched_item_stmt->bindValue(":id",$get_event_res["internal_ID"])) {
	die();
}

if(!$get_sched_item_stmt->execute()) {
	die();
}

// If we have a schedule items then include that section.
if($get_sched_item_res = $get_sched_item_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["schedule"] = array(
		"nav" => $get_event_res["sched_nav"],
		"icon" => $get_event_res["sched_icon"]
	);

	do {
		$date = date("m/d/Y",strtotime($get_sched_item_res["date"]));

		// Create the key $date if necessary, then use the $var[] = ... syntax to push a dictionary onto the array of items on that date.
		$output["schedule"][$date][] = array(
			"start_time" => $get_sched_item_res["start_time"],
			"length" => $get_sched_item_res["length"],
			"description" => $get_sched_item_res["description"],
			"location" => $get_sched_item_res["location"],
			"category" => $get_sched_item_res["category"],
		);
		
	} while($get_sched_item_res = $get_sched_item_stmt->fetch(PDO::FETCH_ASSOC));
}


///// Housing


if (!($get_housing_stmt = $db->prepare("SELECT * FROM housing where event_ID=:id"))) {
	die();
}

if(!$get_housing_stmt->bindValue(":id",$get_event_res["internal_ID"])) {
	die();
}

if(!$get_housing_stmt->execute()) {
	die();
}

// If we have a housing items then include that section.
if($get_housing_res = $get_housing_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["housing"] = array(
		"nav" => $get_event_res["housing_nav"],
		"icon" => $get_event_res["housing_icon"]
	);

	do {

		if (!($get_guest_stmt = $db->prepare("SELECT * FROM attendees where house_ID=:id"))) {
			die();
		}

		if(!$get_guest_stmt->bindValue(":id",$get_housing_res["ID"])) {
			die();
		}

		if(!$get_guest_stmt->execute()) {
			die();
		}

		$str = "";
		$first = true;
		while($get_guest_res = $get_guest_stmt->fetch(PDO::FETCH_ASSOC)) {
			if(!$first) {
				$str .= "\n";
			}
			else {
				$first = false;
			}
			$str .= $get_guest_res["name"];
		}
		
		$output["housing"][$get_housing_res["host_name"]] = array(
			"driver" => $get_housing_res["driver"],
			"students" => $str,
		);

	} while($get_housing_res = $get_housing_stmt->fetch(PDO::FETCH_ASSOC));
}

///// Prayer Partners


if (!($get_prayer_partners_stmt = $db->prepare("SELECT * FROM prayer_partners where event_ID=:id"))) {
	die();
}

if(!$get_prayer_partners_stmt->bindValue(":id",$get_event_res["internal_ID"])) {
	die();
}

if(!$get_prayer_partners_stmt->execute()) {
	die();
}

// Includes a prayer partner page if there is one
if($get_prayer_partners_res = $get_prayer_partners_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["prayer_partners"] = array();

	$output["prayer_partners"][] = array(
		"nav" => $get_event_res["prayer_nav"],
		"icon" => $get_event_res["prayer_icon"]
	);

	do {

		if (!($get_attendee_stmt = $db->prepare("SELECT * FROM attendees where prayer_group_ID=:id"))) {
			die();
		}

		if(!$get_attendee_stmt->bindValue(":id",$get_prayer_partners_res["group_ID"])) {
			die();
		}

		if(!$get_attendee_stmt->execute()) {
			die();
		}

		$str = "";
		$first = true;
		while($get_attendee_res = $get_attendee_stmt->fetch(PDO::FETCH_ASSOC)) {
			if(!$first) {
				$str .= "\n";
			}
			else {
				$first = false;
			}
			$str .= $get_attendee_res["name"];
		}
		
		$output["prayer_partners"][] = array(
			"students" => $str,
		);

	} while($get_prayer_partners_res = $get_prayer_partners_stmt->fetch(PDO::FETCH_ASSOC));
}

///// Additional Information Pages


if (!($get_info_page_stmt = $db->prepare("SELECT * FROM info_page where event_ID=:id"))) {
	die();
}

if(!$get_info_page_stmt->bindValue(":id",$get_event_res["internal_ID"])) {
	die();
}

if(!$get_info_page_stmt->execute()) {
	die();
}

// Includes any additional information pages
if($get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
	
	$output["information_page"] = array();

	do {
		$page = "page_" + $get_info_page_res["ID"];
		$output["information_page"][$page] = array();

		$output["information_page"][$page][] = array(
			"nav" => $get_event_res["prayer_nav"],
			"icon" => $get_event_res["prayer_icon"]
		);

		if (!($get_info_section_stmt = $db->prepare("SELECT * FROM info_page_sections where info_page_ID=:id"))) {
			die();
		}

		if(!$get_info_section_stmt->bindValue(":id",$get_info_page_res["ID"])) {
			die();
		}

		if(!$get_info_section_stmt->execute()) {
			die();
		}

		while($get_info_section_res = $get_info_section_stmt->fetch(PDO::FETCH_ASSOC)) {
			$output["information_page"][$page][] = array(
				"title" => $get_info_section_res["header"],
				"description" => $get_info_section_res["content"],
			);
		}

	} while($get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC));
}

echo json_encode_noescape($output);

?>
