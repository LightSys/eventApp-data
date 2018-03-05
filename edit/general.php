<html>
	<head>
		<link rel="stylesheet" href="../styles/styles.css" />
	</head>
	
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
			<form>
				Event Name: <input type="text" name="name"><br>
				Logo: <input type="file" name="logo"><br>
				Time Zone: <input type="text" name="timezone"><br>
				Welcome Message: <input type="text" name="welcome"><br>
				<input type="submit" value="Submit">
			</form>
		</section>
	</body>
</html>
