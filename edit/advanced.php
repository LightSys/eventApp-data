<?php include("../templates/check-event-exists.php"); ?>

<html>
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
			<form id="form" method="post">
				<div class="card">
					<div class="input">Refresh: <input type="text" name="refresh"></div>
					<div class="input">Refresh Expire: <input type="date" name="refreshExpire"></div>
					<div class="input">Notifications URL: <input type="text" name="notificationsUrl"></div>
				</div>
				<br>
				<div class="btn" id="save">Save</div>
			</form>
		</section>
	</body>
	<?php include("../templates/head.php"); ?>
</html>
