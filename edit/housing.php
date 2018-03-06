<html>
	<?php include("../templates/head.php"); ?>
	
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
			<form id="housingForm">
				<div id="sectionCards">
				</div>
				<div class="btn" onclick="addHost()">Add Host</div>
				<input type="submit" value="Submit">
			</form>
		</section>

	</body>

	<script>
		var counter = 0;
		var guestCounter = 0;
		
		$(document).ready(function() {
			addHost();
		});

		function addHost() {
			guestCounter = 0;
			var html = '<div class="contact-card">Host: <input type="text" name="host' + counter + '"><br>'
						+ 'Driver: <input type="text" name="driver' + counter + '"><br>'
						+ 'Guests: <div id="guests' + counter + '"><input type="text" name="guest' + guestCounter + '"><br></div>'
						+ '<div class="btn" onclick="addGuest(' + counter + ')">Add Guest</div>';
			addFields(html, 'sectionCards');
			counter++;
		}

		function addGuest(num) {
			var html = '<input type="text" name="guest' + counter + '"><br>';
			addFields(html, 'guests' + num);
			guestCounter++;
		}
	</script>
</html>
