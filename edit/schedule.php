<?php include("../templates/check-event-exists.php"); ?>

<html>
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#schedule {
				background-color: grey;
				color: white;
			}
		</style>
		<section id="main">
			<h1>Schedule</h1>
			<form id="form" method="post">
				<div id="scheduleDiv">
				</div>
				<div class="btn" onclick="addScheduleItem()">+ Add Schedule </div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>
	</body>
	<?php include("../templates/head.php"); ?>
	<script>
		var contactCounter = 0;
		
		$(document).ready(function() {
			addScheduleItem();
		});

		function addScheduleItem() {
			var html = '<div class="card"><div class="input">Date: <input type="date" name="date"></div>' +
				'<div class="input">Start Time: <input type="text" name="starttime"></div>' +
				'<div class="input">Length: <input type="text" name="length"></div>' +
				'<div class="input">Description: <input type="text" name="description"></div>' +
				'<div class="input">Location: <input type="text" name="location"></div>' +
				'<div class="input">Category: <input type="text" name="category"></div></div>';
			addFields(html, 'scheduleDiv');
			contactCounter++;
		}
	</script>
</html>
