<html>
	<?php include("../templates/head.php"); ?>
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
			<form id="scheduleForm">
				<div id="scheduleDiv">
					<!-- Date: <input type="date" name="date"><br>
					Start Time: <input type="text" name="starttime"><br>
					Length: <input type="text" name="length"><br>
					Description: <input type="text" name="description"><br>
					Location: <input type="text" name="location"><br>
					Category: <input type="text" name="category"><br> -->
				</div>
				<div class="btn" onclick="addScheduleItem()">+ Add Schedule </div>
				<input type="submit" value="Submit">
			</form>
		</section>
	</body>
	<script>
		var contactCounter = 0;
		
		$(document).ready(function() {
			addScheduleItem();
		});

		function addScheduleItem() {
			var html = '<div class="contact-card">Date: <input type="date" name="date"><br>' +
				'Start Time: <input type="text" name="starttime"><br>' +
				'Length: <input type="text" name="length"><br>' +
				'Description: <input type="text" name="description"><br>' +
				'Location: <input type="text" name="location"><br>' +
				'Category: <input type="text" name="category"><br></div>';
			addFields(html, 'scheduleForm');
			contactCounter++;
		}
	</script>
</html>
