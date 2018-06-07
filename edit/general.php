<?php
	session_start();
	
	include("../helper.php");
	
	include("../connection.php");
	
	eventSecure();
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
			die(" Hi i am here error count != 1");
		}
		$get_event_res = $get_event_res[0];
		$internalEventID = $get_event_res["internal_ID"];
		// Create two blank contact page sections
		for($i = 0; $i < 2; $i++) {
			$new_contact_pages_stmt = $db->prepare("INSERT into contact_page_sections SET event_ID = :internalEventID, sequential_ID = (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from contact_page_sections where event_ID=:internalEventID) as temp)");
			$new_contact_pages_stmt->bindValue('internalEventID',$internalEventID);
			$new_contact_pages_stmt->execute();
		}
		
		$stmt = $db->prepare("UPDATE event SET admin = :admin WHERE internal_ID = :id");
		$stmt->bindValue(':admin', $_SESSION["username"]);
		
		$stmt->bindValue(':id',$internalEventID);
		
		$stmt->execute();
		
		// reroute to this page with the new event id
		header("Location: ".full_url($_SERVER)."?id=".$id);
		die();
	}
	secure();
        $get_event_stmt = $db->prepare("SELECT name,time_zone,welcome_message, visible, custom_tz, view_remote, contact_nav,contact_icon,sched_nav,sched_icon,housing_nav,housing_icon,prayer_nav,prayer_icon,notif_nav,notif_icon FROM event WHERE ID =:id");
	
        $get_event_stmt->bindValue(":id", $_GET["id"]);
	
	$get_event_stmt->execute();
        $get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($get_event_res) != 1) {
                die();
        }
        $get_event_res = $get_event_res[0];
        
    if(isset($_POST['name'])) {
		$stmt = $db->prepare("UPDATE event SET name = :name, time_zone = :time_zone, custom_tz = :custom, view_remote = :remote, welcome_message = :welcome_message, visible = :visible, logo = :logo, contact_nav= :contact_nav,contact_icon= :contact_icon,sched_nav= :sched_nav,sched_icon= :sched_icon,housing_nav= :housing_nav,housing_icon= :housing_icon,prayer_nav= :prayer_nav,prayer_icon= :prayer_icon,notif_nav= :notif_nav,notif_icon= :notif_icon WHERE id = :id");
		$name = $_POST['name'];
		$timeZone = $_POST['timezone'];
		$welcomeMessage = $_POST['welcome'];
		$visible = isset($_POST['visible']);
		$custom = isset($_POST['custom']);
		$remote = isset($_POST['remote']);
		$id = $_POST["id"];
		$logo = null;
		// If the user specified a logo file
		if(isset($_FILES["logo"]["name"])) {
			//die( "attempted upload"); we got this far
			// The directory to save the file to
			$uploaddir = '../temp/';
			// Get the full path to save the uploaded file to
			$uploadfile = $uploaddir . basename($_FILES['logo']['name']);
			// Try to upload the file
			if(move_uploaded_file($_FILES['logo']['tmp_name'], $uploadfile)) {
				$logo = base64_encode(file_get_contents($uploadfile));
			//	die("encoding should be successful"); failed by this point
				echo "<p>File succesfully uploaded</p>";
			} else {
			//	die("error uploading file"); //apparently there is a permission failure
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
		$stmt->bindValue(':custom', $custom);
		$stmt->bindValue(':remote', $remote);
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
		header("Location: general.php?id=".$_POST['id']);
		die();
	}
	
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

				<form method = "post" enctype="multipart/form-data" id="form">

					<div class="card">

						<input type="hidden" name="id" value="<?php echo($_GET['id'])?>">

						<input type="hidden" name="contact_icon" maxlength="100" value="ic_contact">

						<input type="hidden" name="sched_icon" maxlength="100" value="ic_schedule">

						<input type="hidden" name="housing_icon" maxlength="100" value="ic_house">

						<input type="hidden" name="prayer_icon" maxlength="100" value="ic_group">

						<input type="hidden" name="notif_icon" maxlength="100" value="ic_bell">

						<div class="input">Event Name:<input type="text" name="name" maxlength="100" value="<?php echo $get_event_res["name"] ?>"></div>

						<div class="input">Logo:<input type="file" name="logo" ></div>

						<div class="input">Time Zone:<input type="text" name="timezone" maxlength="9" value="<?php echo $get_event_res["time_zone"] ?>"></div>

						<div class="input">Welcome Message:<input type="text" name="welcome" maxlength="100" value="<?php echo $get_event_res["welcome_message"] ?>"></div>

						<div class="input">Contact Page Nav:<input type="text" name="contact_nav" maxlength="25" value="<?php echo $get_event_res["contact_nav"] ?>"></div>

						<div class="input">Schedule Page Nav:<input type="text" name="sched_nav" maxlength="25" value="<?php echo $get_event_res["sched_nav"] ?>"></div>

						<div class="input">Housing Page Nav:<input type="text" name="housing_nav" maxlength="25" value="<?php echo $get_event_res["housing_nav"] ?>"></div>

						<div class="input">Prayer Partners Page Nav:<input type="text" name="prayer_nav" maxlength="25" value="<?php echo $get_event_res["prayer_nav"] ?>"></div>

						<div class="input">Notification Page Nav:<input type="text" name="notif_nav" maxlength="25" value="<?php echo $get_event_res["notif_nav"] ?>"></div>

                                                <div class="input">Allow a user to create a custom timezone:<input autocomplete="off" type="checkbox" name="custom" value="true" <?php echo ($get_event_res["custom_tz"]) ? "checked" : ""; ?>></div>

                                                <div class="input">Allow a user to attend remotely:<input autocomplete="off" type="checkbox" name="remote" value="true" <?php echo ($get_event_res["view_remote"]) ? "checked" : ""; ?>></div>

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
