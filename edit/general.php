<?php
	include("../helper.php");

	// include the database connection
	include("../connection.php");

	// If we are coming from the events page to create a new event
	if(isset($_POST['action']) && $_POST['action'] == 'newEvent') {

		// create a new event
		$new_event_stmt = $db->prepare("INSERT into event SET ID = UUID()");
		$new_event_stmt->execute();

		// get the id of that event
		$new_event_id_stmt = $db->prepare("SELECT * from event where internal_ID = (select MAX(internal_ID) from event)");
		$new_event_id_stmt->execute();

		$id;
		while($new_event_id = $new_event_id_stmt->fetch(PDO::FETCH_ASSOC)) {
			$id = $new_event_id['ID'];
		}

		// get the internal id
		$get_event_stmt = $db->prepare("SELECT internal_ID FROM event where ID=:id");
		$get_event_stmt->bindParam(":id",$id);
		$get_event_stmt->execute();
		$get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);
		if(count($get_event_res) != 1) {
			die("error count != 1");
		}
		$get_event_res = $get_event_res[0];
		$internalEventID = $get_event_res["internal_ID"];

		// Create two blank contact page sections
		for($i = 0; $i < 2; $i++) {
			$new_contact_pages_stmt = $db->prepare("INSERT into contact_page_sections SET event_ID = :internalEventID, sequential_ID = (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from contact_page_sections where event_ID=:internalEventID) as temp)");
			$new_contact_pages_stmt->bindValue('internalEventID',$internalEventID);
			$new_contact_pages_stmt->execute();
		}
		
		// reroute to this page with the new event id
		header("Location: ".full_url($_SERVER)."?id=".$id);
		die();
	}	

    if(isset($_POST['name'])) {
		$stmt = $db->prepare("UPDATE event SET name = :name, time_zone = :time_zone, welcome_message = :welcome_message, visible = :visible, logo = :logo, contact_nav=:contact_nav,contact_icon=:contact_icon,sched_nav=:sched_nav,sched_icon=:sched_icon,housing_nav=:housing_nav,housing_icon=:housing_icon,prayer_nav=:prayer_nav,prayer_icon=:prayer_icon,notif_nav=:notif_nav,notif_icon=:notif_icon WHERE id = :id");
		
		$name = $_POST['name'];
		$timeZone = $_POST['timezone'];
		$welcomeMessage = $_POST['welcome'];
		$visible = isset($_POST['visible']);
		$id = $_POST["id"];
		$logo = null;

		// If the user specified a logo file
		if(isset($_FILES["logo"]["name"])) {
			
			// The directory to save the file to
			$uploaddir = '../temp/';

			// Get the full path to save the uploaded file to
			$uploadfile = $uploaddir . basename($_FILES['logo']['name']);

			// Try to upload the file
			if(move_uploaded_file($_FILES['logo']['tmp_name'], $uploadfile)) {
				$logo = base64_encode(file_get_contents($uploadfile));
				echo "<p>File succesfully uploaded</p>";
			} else {
				echo "<p>Error uploading file</p>";
			}
		
			// Remove the contents of the temporary directory
			$files = glob($uploaddir); 	// get all file names
			foreach($files as $file) {  // iterate files
				if(is_file($file))
					unlink($file); 		// delete file
			}
		}
		
		$stmt->bindValue(':name', $name);
		$stmt->bindValue(':time_zone', $timeZone);
		$stmt->bindValue(':welcome_message', $welcomeMessage);
		$stmt->bindValue(':id', $id);
		$stmt->bindValue(':visible', $visible);	
		$stmt->bindValue(':logo', $logo);

		$stmt->bindValue(":contact_nav", $_POST["contact_nav"]);
		$stmt->bindValue(":contact_icon", $_POST["contact_icon"]);
		$stmt->bindValue(":sched_nav", $_POST["sched_nav"]);
		$stmt->bindValue(":sched_icon", $_POST["sched_icon"]);
		$stmt->bindValue(":housing_nav", $_POST["housing_nav"]);
		$stmt->bindValue(":housing_icon", $_POST["housing_icon"]);
		$stmt->bindValue(":prayer_nav", $_POST["prayer_nav"]);
		$stmt->bindValue(":prayer_icon", $_POST["prayer_icon"]);
		$stmt->bindValue(":notif_nav", $_POST["notif_nav"]);
		$stmt->bindValue(":notif_icon", $_POST["notif_icon"]);
		$stmt->execute();

		// reroute to this page with the new event id
		header("Location: ".full_url($_SERVER)."?id=".$_POST['id']);
		die();
	}
	
	include("../templates/check-event-exists.php");

	$get_event_stmt = $db->prepare("SELECT name, time_zone, welcome_message, visible,contact_nav,contact_icon,sched_nav,sched_icon,housing_nav,housing_icon,prayer_nav,prayer_icon,notif_nav,notif_icon FROM event where ID=:id");
	$get_event_stmt->bindValue(":id", $_GET["id"]);
	$get_event_stmt->execute();

	$get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);

	if(count($get_event_res) != 1) {
		die();
	}

	$get_event_res = $get_event_res[0];
?>


<html>
	
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#general {
				background-color: grey;
				color: white;
			}
		</style>
		
		<section id="main">
			<h1>General</h1>
				<form action = "general.php" method = "post" enctype="multipart/form-data" id="form">
					<div class="card">
						<input type="hidden" name="id" value="<?php echo($_GET['id'])?>">
						<div class="input">Event Name:<input type="text" name="name" value="<?php echo $get_event_res["name"] ?>"></div>
						<div class="input">Logo:<input type="file" name="logo" ></div>
						<div class="input">Time Zone:<input type="text" name="timezone" value="<?php echo $get_event_res["time_zone"] ?>"></div>
						<div class="input">Welcome Message:<input type="text" name="welcome" value="<?php echo $get_event_res["welcome_message"] ?>"></div>
						<div class="input">Contact Page Nav:<input type="text" name="contact_nav" value="<?php echo $get_event_res["contact_nav"] ?>"></div>
						<div class="input">Contact Page Icon:<input type="text" name="contact_icon" value="<?php echo $get_event_res["contact_icon"] ?>"></div>
						<div class="input">Schedule Page Nav:<input type="text" name="sched_nav" value="<?php echo $get_event_res["sched_nav"] ?>"></div>
						<div class="input">Schedule Page Icon:<input type="text" name="sched_icon" value="<?php echo $get_event_res["sched_icon"] ?>"></div>
						<div class="input">Housing Page Nav:<input type="text" name="housing_nav" value="<?php echo $get_event_res["housing_nav"] ?>"></div>
						<div class="input">Housing Page Icon:<input type="text" name="housing_icon" value="<?php echo $get_event_res["housing_icon"] ?>"></div>
						<div class="input">Prayer Partners Page Nav:<input type="text" name="prayer_nav" value="<?php echo $get_event_res["prayer_nav"] ?>"></div>
						<div class="input">Prayer Partners Page Icon:<input type="text" name="prayer_icon" value="<?php echo $get_event_res["prayer_icon"] ?>"></div>
						<div class="input">Notification Page Nav:<input type="text" name="notif_nav" value="<?php echo $get_event_res["notif_nav"] ?>"></div>
						<div class="input">Notification Page Icon:<input type="text" name="notif_icon" value="<?php echo $get_event_res["notif_icon"] ?>"></div>
						<div class="input">Visible:<input autocomplete="off" type="checkbox" name="visible" value="true" <?php echo ($get_event_res["visible"]) ? "checked" : ""; ?>></div>
						<div><img src=<?php echo "'".getParentDir(2)."qr.php?id=".$_GET['id']. "'";?> alt="Mountain View">
					</div>
					<br>
					<div class="btn" id="save">Save</div>
				</form>
		</section>
	</body>

	<?php include("../templates/head.php"); ?>

</html>
