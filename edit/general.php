<?php include("../helper.php"); ?>

<?php
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

		// reroute to this page with the new event id
		header("Location: ".full_url($_SERVER)."?id=".$id);
		die();
	}	

    if(isset($_POST['name']))
	{
		if (!($stmt = $db->prepare("UPDATE event SET name = :name, time_zone = :time_zone, welcome_message = :welcome_message visible = :visible, logo = :logo WHERE id = :id"))) {
			// die(0);
		}
		
		$name = $_POST['name'];
		$timeZone = $_POST['timezone'];
		$welcomeMessage = $_POST['welcome'];
		$visible = $_POST['visible'];
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
		
		if (!($stmt->bindValue(':name', $name))) {
			// die(1);
		}
		if (!($stmt->bindValue(':time_zone', $timeZone))) {
			// die(2);
		}
		if (!($stmt->bindValue(':welcome_message', $welcomeMessage))) {
			// die(3);
		}
		if (!($stmt->bindValue(':id', $id))) {
			// die(4);
		}	
		if (!($stmt->bindValue(':visible', $visible))) {
			// die(5);
		}	
		if (!($stmt->bindValue(':logo', $logo))) {
			// die(6);
		}	
		if(!($stmt->execute())) {
			// die(7);
		}
	}
	
	include("../templates/check-event-exists.php");
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
						<div class="input">Event Name:<input type="text" name="name"></div>
						<div class="input">Logo:<input type="file" name="logo"></div>
						<div class="input">Time Zone:<input type="text" name="timezone"></div>
						<div class="input">Welcome Message:<input type="text" name="welcome"></div>
						<div class="input">Visible:<input type="checkbox" name="visible" value="false" checked="unchecked"></div>
					</div>
					<br>
					<div class="btn" id="save">Save</div>
				</form>
		</section>
	</body>

	<?php include("../templates/head.php"); ?>

</html>
