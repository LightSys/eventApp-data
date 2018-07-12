<?php
header('Content-Type: application/json');

include("connection.php");
include("helper.php");


///// General data


$get_event_stmt = $db->prepare("SELECT * FROM event where ID=:id");
$get_event_stmt->bindParam(":id",$_GET['id']);
$get_event_stmt->execute();


$get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);

if(count($get_event_res) != 1) {
	die();
}

$get_event_res = $get_event_res[0];

if(!$get_event_res["visible"]) {
	die();
}

$output = array(
	"general" => array(
		"version_num"=>$get_event_res["config_version"].",".$get_event_res["notif_version"],
		"refresh" => $get_event_res["refresh"],
		"refresh_expire" => date("m/d/Y",strtotime($get_event_res["refresh_expire"])),
		"time_zone" =>$get_event_res["TZCatagory"]."/".$get_event_res["time_zone"],
		"welcome_message" => $get_event_res["welcome_message"],
		"notifications_url" => stripFileName(). "getnotifications.php?id=" . $_GET['id'],
		"year" => $get_event_res["year"],
		"logo" => $get_event_res["logo"],
		"custom_time_zone"=> $get_event_res["custom_tz"],
		"remote_viewing"=>$get_event_res["view_remote"],
		"event_name"=>$get_event_res["name"]
	)
);


///// Themes

$get_themes_stmt = $db->prepare("SELECT * FROM themes where event_ID=:id");
$get_themes_stmt->bindValue(":id", $get_event_res["internal_ID"]);
$get_themes_stmt->execute();

$output["theme"] = array(
	array(
		"themeColor" => $get_event_res["theme_color"]
	),
	array(
		"themeMedium" => $get_event_res["theme_medium"]
	),
	array(
		"themeDark" => $get_event_res["theme_dark"]
	),
	array(
		"schedule_blank" => "#d6d4d4"
	)
);

while ($get_themes_res = $get_themes_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["theme"][] = array(
		$get_themes_res["theme_name"] => $get_themes_res["theme_color"],
	);
}

///// Contact page sections


$get_cpages_stmt = $db->prepare("SELECT * FROM contact_page_sections where event_ID=:id");
$get_cpages_stmt->bindValue(":id",$get_event_res["internal_ID"]);
$get_cpages_stmt->execute();

// If we have a contact page section then include the contact_page section.
if($get_cpages_res = $get_cpages_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["contact_page"] = array(
		"nav" => $get_event_res["contact_nav"],
		"icon" => $get_event_res["contact_icon"]
	);

	do {			$contact;
				if($get_cpages_res["sequential_ID"] != 1){
                               		 $names=explode(":",$get_cpages_res["content"]);
                               		 $tempstring="";
					 $counter=0;
                                	foreach ($names as $id){
                                        	$get_contacts_stmt = $db->prepare("SELECT name FROM contacts where event_ID=:id and ID= :CID");
	                                        $get_contacts_stmt->bindValue(":id",$get_event_res["internal_ID"]);
                                        	$get_contacts_stmt->bindValue(":CID",$id);
                                        	$get_contacts_stmt->execute();
                                        	$get_contact_res=$get_contacts_stmt->fetch(PDO::FETCH_ASSOC);
                                       		if (!$counter==0){
							$tempstring.=":";
						}
				        	$tempstring.=$get_contact_res["name"];
						$counter++;	

                                	}
					$contact=$tempstring;
                        	}
				else{
					$contact=$get_cpages_res["content"];
				}

		$output["contact_page"]["section_" . $get_cpages_res["sequential_ID"]] = array(
			"header" => $get_cpages_res["header"],
			"content" => $contact,
			"id" => "".($get_cpages_res["sequential_ID"]-1).""
		);
	} while($get_cpages_res = $get_cpages_stmt->fetch(PDO::FETCH_ASSOC));
}


////// Contacts

$get_contacts_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id");
$get_contacts_stmt->bindValue(":id",$get_event_res["internal_ID"]);
$get_contacts_stmt->execute();

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


$get_sched_item_stmt = $db->prepare("SELECT * FROM schedule_items where event_ID=:id");
$get_sched_item_stmt->bindValue(":id",$get_event_res["internal_ID"]);
$get_sched_item_stmt->execute();

// If we have a schedule items then include that section.
if($get_sched_item_res = $get_sched_item_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["schedule"] = array(
		"nav" => $get_event_res["sched_nav"],
		"icon" => $get_event_res["sched_icon"]
	);

	do {
		$date = date("m/d/Y",strtotime($get_sched_item_res["date"]));
	
		$get_name =$db->prepare("SELECT name FROM contacts WHERE event_ID = :EID AND ID= :CID");
		$get_name->bindValue(":EID",$get_event_res["internal_ID"]);
		$get_name->bindValue(":CID",$get_sched_item_res["location"]);
		$get_name->execute();
		
		$get_name_res=$get_name->fetch(PDO::FETCH_ASSOC);

		if ($get_sched_item_res["location"] == ""){
			$locationName = "null";
		} else {
			$locationName = $get_name_res["name"]; 
		}

		$themeID = $get_sched_item_res["category"];
		
		$get_theme = $db->prepare("SELECT theme_name FROM themes WHERE ID=:id");
		$get_theme -> bindValue(":id",$themeID);
		$get_theme -> execute();
		$theme_name_res = $get_theme->fetch(PDO::FETCH_ASSOC);

		if ($themeID == ""){
			$themeName = "schedule_blank";
		} else {
			$themeName = $theme_name_res["theme_name"];
		}

		// Create the key $date if necessary, then use the $var[] = ... syntax to push a dictionary onto the array of items on that date.
		$output["schedule"][$date][] = array(
			"start_time" => $get_sched_item_res["start_time"]+1-1,
			"length" => $get_sched_item_res["length"]+1-1,
			"description" => $get_sched_item_res["description"],
			"location" => $locationName,
			"category" => $themeName,
		);
		
	} while($get_sched_item_res = $get_sched_item_stmt->fetch(PDO::FETCH_ASSOC));
}


///// Housing


$get_housing_stmt = $db->prepare("SELECT * FROM housing where event_ID=:id");
$get_housing_stmt->bindValue(":id",$get_event_res["internal_ID"]);
$get_housing_stmt->execute();

// If we have a housing items then include that section.
if($get_housing_res = $get_housing_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["housing"] = array(
		"nav" => $get_event_res["housing_nav"],
		"icon" => $get_event_res["housing_icon"]
	);

	do {

		$get_guest_stmt = $db->prepare("SELECT * FROM attendees where house_ID=:id");
		$get_guest_stmt->bindValue(":id",$get_housing_res["ID"]);
		$get_guest_stmt->execute();

		$str = "";
		$first = true;
		while($get_guest_res = $get_guest_stmt->fetch(PDO::FETCH_ASSOC)) {
			if(!$first) {
				$str =$str . "\n";
			}
			else {
				$first = false;
			}
			$str =$str. $get_guest_res["name"];
		}
		$get_name =$db->prepare("SELECT name FROM contacts WHERE event_ID = :EID AND ID= :CID");
                $get_name->bindValue(":EID",$get_event_res["internal_ID"]);
                $get_name->bindValue(":CID",$get_housing_res["host_name"]);
                $get_name->execute();

		$get_name_res=$get_name->fetch(PDO::FETCH_ASSOC);
		
		$output["housing"][$get_name_res["name"]] = array(
			"driver" => $get_housing_res["driver"],
			"students" => $str,
		);

	} while($get_housing_res = $get_housing_stmt->fetch(PDO::FETCH_ASSOC));
}

///// Prayer Partners


$get_prayer_partners_stmt = $db->prepare("SELECT * FROM prayer_partners where event_ID=:id");
$get_prayer_partners_stmt->bindValue(":id",$get_event_res["internal_ID"]);
$get_prayer_partners_stmt->execute();

// Includes a prayer partner page if there is one
if($get_prayer_partners_res = $get_prayer_partners_stmt->fetch(PDO::FETCH_ASSOC)) {
	$output["prayer_partners"] = array();

	$output["prayer_partners"][] = array(
		"nav" => $get_event_res["prayer_nav"],
		"icon" => $get_event_res["prayer_icon"]
	);

	do {

		$get_attendee_stmt = $db->prepare("SELECT * FROM attendees where prayer_group_ID=:id");
		$get_attendee_stmt->bindValue(":id",$get_prayer_partners_res["group_ID"]);
		$get_attendee_stmt->execute();

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


$get_info_page_stmt = $db->prepare("SELECT * FROM info_page where event_ID=:id");
$get_info_page_stmt->bindValue(":id",$get_event_res["internal_ID"]);
$get_info_page_stmt->execute();

// Includes any additional information pages
if($get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
	
	$output["information_page"] = array();

	do {
		$page = "page_" . $get_info_page_res["sequential_ID"];
		$output["information_page"][$page] = array();

		$output["information_page"][$page][] = array(
			"nav" => $get_info_page_res["nav"],
			"icon" => $get_info_page_res["icon"],
		);

		$get_info_section_stmt = $db->prepare("SELECT * FROM info_page_sections where info_page_ID=:id");
		$get_info_section_stmt->bindValue(":id",$get_info_page_res["ID"]);
		$get_info_section_stmt->execute();

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
