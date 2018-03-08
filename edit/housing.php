<?php include("../templates/check-event-exists.php"); ?>

<?php

include("../connection.php");

if( isset($_POST['host'] )) {
	foreach($_POST['host'] as $key => $host) {
		if (!($stmt = $db->prepare("INSERT into housing(event_ID, host_name, driver) VALUES (:event_ID, :host_name, :driver)"))) {
			die(0);
		}

		$ID = $_GET['id'];
		$driver = $_POST['driver'][$key];

		if (!($stmt->bindValue(':event_ID', $ID))) {
			die(1);
		}
		if (!($stmt->bindValue(':host_name', $host))) {
			die(2);
		}
		if (!($stmt->bindValue(':driver', $driver))) {
			die(3);
		}

		if(!($stmt->execute())) {
			die();
		}
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
			<?php echo('<form id="form" action="housing.php?id=' . $_GET['id'] . '" method="post">') ?>
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

