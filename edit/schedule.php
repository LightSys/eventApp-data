<html>
	<head>
		<link rel="stylesheet" href="../styles/styles.css" />
	</head>
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<section id="main">
			<h1>Schedule</h1>
			<form>
				Date: <input type="date" name="date"><br>
				Start Time: <input type="text" name="starttime"><br>
				Length: <input type="text" name="length"><br>
				Description: <input type="text" name="description"><br>
				Location: <input type="text" name="location"><br>
				Category: <input type="text" name="category"><br>	
				<input type="submit" value="Submit">
			</form>
		</section>
	</body>
</html>
