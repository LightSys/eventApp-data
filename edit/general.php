<?php	
	include("../connection.php");
    if( isset($_POST['name']))
	{
		if (!($stmt = $db->prepare("UPDATE event SET name = :name, time_zone = :time_zone, welcome_message = :welcome_message visible = :visible, logo = :logo WHERE id = :id"))) {
			die(0);
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
			$uploaddir = '../temp/logo/';

			// Get the full path to save the uploaded file to
			$uploadfile = $uploaddir . basename($_FILES['logo']['name']);

			// Try to upload the file
			if(move_uploaded_file($_FILES['logo']['tmp_name'], $uploadfile)) {
				$logo = file_get_contents($uploadfile);
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
			die(1);
		}
		if (!($stmt->bindValue(':time_zone', $timeZone))) {
			die(2);
		}
		if (!($stmt->bindValue(':welcome_message', $welcomeMessage))) {
			die(3);
		}
		if (!($stmt->bindValue(':id', $id))) {
			die(4);
		}	
		if (!($stmt->bindValue(':visible', $visible))) {
			die(5);
		}	
		if (!($stmt->bindValue(':logo', $logo))) {
			die(6);
		}	
		if(!($stmt->execute())) {
			die(7);
		}
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
<<<<<<< HEAD
			<form action = "general.php" method = "post" enctype="multipart/form-data">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<div class="input">Event Name:<input type="text" name="name"></div>
				<div class="input">Logo:<input type="file" name="logo" id="logo"></div>
				<div class="input">Time Zone:<input type="text" name="timezone"></div>
				<div class="input">Welcome Message:<input type="text" name="welcome"></div>
				<div class="input">Visible:<input type="checkbox" name="visible" value="false" checked="unchecked"></div>
				<input type="submit" value="Submit">
			</form>
		</section>
	</body>
</html>
=======
				<form id="form" method="post">
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
>>>>>>> e03a138f7d3e8502113020a6783506562d6e0e0c
