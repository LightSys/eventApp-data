<?php 
	include("../connection.php");
	include("../helper.php");

	$event_id = getEventId();

	if (isset($_POST['action'])) {
		if ($_POST['action'] == "addPartner") {
			$stmt = $db->prepare("UPDATE attendees set prayer_group_ID=:group_ID where event_ID=:event_ID and name=:name");
			$stmt->bindValue(":id", $event_id);

		} else if ($_POST['action'] == "addGroup") {
			$stmt = $db->prepare('INSERT into prayer_partners(event_ID, sequential_ID) values (:id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from prayer_partners where event_ID=:id) as temp), "", "")')
			$stmt->bindValue(":id", $event_id);
			$stmt->execute();
		} else if ($_POST['action'] == "updateGroups") {
			$stmt = $db->prepare("UPDATE attendees set prayer_group_ID=:group_ID where event_ID=:event_ID and name=:name");
			$stmt->bindValue(":id", $event_id);

			foreach()
		} 
		// deletes
	}

	include("../templates/check-event-exists.php"); 

?>

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
