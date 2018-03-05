<html>
	<?php include("../templates/head.php"); ?>
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#contacts {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Contacts</h1>
			<form>
				<div id="person1">
				Name: <input type="text" name="name"><br>
				Address: <input type="text" name="address"><br>
				Phone: <input type="text" name="phone"><br>
				<input type="submit" value="Submit">
			</form>
		</section>

	</body>
</html>
