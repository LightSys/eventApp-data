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
				<div class="btn" id="save">Save</div>
			</form>
		</section>

	</body>

	<script>
		var counter = 0;
		var guestCounters = [];
		// var guestCounter = 0;
		
		$(document).ready(function() {
			addHost();
		});

		function addHost() {
			guestCounters[counter] = 0;
			var html = '<div class="card"><div class="input">Host: <input type="text" name="host' + counter + '"></div>'
						+ '<div class="input">Driver: <input type="text" name="driver' + counter + '"></div>'
						+ '<div class="input">Guests: <div id="guests' + counter + '"><input type="text" name="guest' + guestCounters[counter] + '"></div></div>'
						+ '<div class="btn" onclick="addGuest(' + counter + ')">Add Guest</div>';
			addFields(html, 'sectionCards');
			console.log("addHost, " + counter);
			counter++;
		}

		function addGuest(num) {
			var html = '<input type="text" name="guest' + guestCounters[num] + '">';
			addFields(html, 'guests' + num);
			console.log("addGuest " + guestCounters[num]);
			guestCounters[num]++;
		}

		$( "#save" ).click(function() {
			$( "#housingForm" ).submit();
		});
	</script>
</html>
