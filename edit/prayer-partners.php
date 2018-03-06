<html>
	<?php include("../templates/head.php"); ?>
	
	<body>
		<?php include("../templates/left-nav.php"); ?>

		<style>
			#prayerpartners {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Prayer Partners</h1>
			<form id="PPartnersForm">
				<div id="sectionCards">
				</div>
				<div class="btn" onclick="addGroup()">Add Group</div>
				<input type="submit" value="Submit">
			</form>
		</section>

	</body>

	<script>
		var counter = 0;
		var partnerCounter = 0;
		
		$(document).ready(function() {
			addGroup();
		});

		function addGroup() {
			guestCounter = 0;
			var html = '<div class="contact-card">Partners: <div id="partners' +
						 counter + '"><input type="text" name="partner' + guestCounter + '"><br></div>' +						
						 '<div class="btn" onclick="addPartner(' + counter + ')">Add Partner</div>';
			addFields(html, 'sectionCards');
			counter++;
		}

		function addPartner(num) {
			var html = '<input type="text" name="partner' + counter + '"><br>';
			addFields(html, 'partners' + num);
			partnerCounter++;
		}
	</script>

</html>
