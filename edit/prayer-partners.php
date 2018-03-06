<html>
	
	<body>
		<?php include("../templates/left-nav.php"); ?>

		<style>
			#prayer-partners {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Prayer Partners</h1>
			<form id="form">
				<div id="sectionCards">
				</div>
				<div class="btn" onclick="addGroup()">Add Group</div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>

	</body>
	<?php include("../templates/head.php"); ?>
	<script>
		var counter = 0;
		var partnerCounter = 0;
		
		$(document).ready(function() {
			addGroup();
		});

		function addGroup() {
			guestCounter = 0;
			var html = '<div class="card">Partners: <br><div id="partners' + counter + '">'
						+ '<div class="input"><input type="text" name="partner' + guestCounter + '"></div></div>'					
						+ '<br><div class="btn" onclick="addPartner(' + counter + ')">Add Partner</div></div>';
			addFields(html, 'sectionCards');
			counter++;
		}

		function addPartner(num) {
			var html = '<div class="input"><input type="text" name="partner' + counter + '"></div>';
			addFields(html, 'partners' + num);
			partnerCounter++;
		}
	</script>

</html>
