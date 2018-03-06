<html>
	<?php include("../templates/head.php"); ?>
	<head>
		<script type="text/javascript" src="../scripts/advanced.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
	</head>
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#advanced {
				background-color: grey;
				color: white;
			}
		</style>
		
		<section id="main">
			<h1>Advanced</h1>
			<form>
				Refresh: <input type="text" name="refresh"><br>
				Refresh Expire: <input type="date" name="refreshExpire"><br>
				Notifications URL: <input type="text" name="notificationsUrl"><br>
				<input type="submit" value="Submit">
			</form>
		</section>
	</body>
</html>
