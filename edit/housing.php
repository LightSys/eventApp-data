<?php include("../templates/check-event-exists.php"); ?>

<?php
 // FIXME: this doesn't work
 if( isset($_POST['host'])) {
	foreach($_POST['host'] as $key => $host) {
		if (!($stmt = $db->prepare("INSERT INTO housing (ID, event_ID, host_name, driver) VALUES (:event_ID, :host_name, :driver)"))) {
			
		}

		$ID = "c24343ee-218a-11e8-9e9c-525400bb1e83";
		// $host = $_POST['host'][$key];
		$driver = $_POST['driver'][$key];

		if (!($stmt->bindValue(':ID', $ID))) {	}
		if (!($stmt->bindValue(':event_ID', $ID))) {	}
		if (!($stmt->bindValue(':host_name', $host))) {	}
		if (!($stmt->bindValue(':driver', $driver))) {	}
	}
 }
?>

<html>
	
	
	<body>
		<?php include("../templates/left-nav.php"); ?>

		<style>
			#housing {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Housing</h1>
			<form id="form" action="housing.php" method="post">
				<div id="sectionCards">
				</div>
				<div class="btn" onclick="addHost()">Add Host</div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>

	</body>

	<?php include("../templates/head.php"); ?>
	<script>

		$(document).ready(function() {
			addHost();
		});


		// FIXME: broke ability to add guests
		function addHost() {
			var html = '<div class="card"><div class="input">Host: <input type="text" name="host[]"></div>'
						+ '<div class="input">Driver: <input type="text" name="driver[]"></div>'
						+ '<div class="input">Guests: <div id="guests[]"><select id="contact[]"><option>contact name</option></select></div><br><br>'
						+ '<div class="btn" onclick="addGuest([])">Add Guest</div></div>';
			addFields(html, 'sectionCards');
		}

		function addGuest(num) {
			var html = '<select id="contact[]"><option>contact name</option></select>'
			addFields(html, 'guests' + num);
		}
	</script>

	
</html>

