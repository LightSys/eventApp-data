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
		var partnerCounters = [];
		
		$(document).ready(function() {
			addGroup();
		});

		function addGroup() {
			partnerCounters[counter] = 0;
			var html = '<div class="card">Partners: <br><div id="partners' + counter + '">'
						+ '<div class="input"><input type="text" name="partner' + partnerCounters[counter] + '"></div></div>'					
						+ '<br><div class="btn" onclick="addPartner(' + counter + ')">Add Partner</div></div>';
			addFields(html, 'sectionCards');
			console.log("addGroup: counter: " + counter);
			partnerCounters[counter]++;
			counter++;
		}

		function addPartner(num) {
			var html = '<div class="input"><input type="text" name="partner' + partnerCounters[num] + '"></div>';
			addFields(html, 'partners' + num);
			partnerCounters[num]++;
		}
	</script>

</html>
