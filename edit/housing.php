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
			var html = '<div class="card"><div class="input">Host: <input type="text" name="host' + counter + '"></div>'
						+ '<div class="input">Driver: <input type="text" name="driver' + counter + '"></div>'
						+ '<div class="input">Guests: <div id="guests' + counter + '"><input type="text" name="guest' + guestCounter + '"></div></div>'
						+ '<div class="btn" onclick="addGuest(' + counter + ')">Add Guest</div>';
			addFields(html, 'sectionCards');
			counter++;
		}

		function addGuest(num) {
			var html = '<input type="text" name="guest' + counter + '">';
			addFields(html, 'guests' + num);
			guestCounter++;
		}
	</script>
</html>
