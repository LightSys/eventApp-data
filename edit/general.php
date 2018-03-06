<html>
	<?php include("../templates/head.php"); ?>
	
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
				<div class="input">Event Name:<input type="text" name="name"></div>
				<div class="input">Logo:<input type="file" name="logo"></div>
				<div class="input">Time Zone:<input type="text" name="timezone"></div>
				<div class="input">Welcome Message:<input type="text" name="welcome"></div>
				<div class="input">Visible:<input type="checkbox" name="visible" value="false" checked="unchecked"></div>
				<input type="submit" value="Submit">
			</form>
		</section>
	</body>
</html>
