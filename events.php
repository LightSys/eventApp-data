
<?php include("connection.php"); ?>
<html>
	<head>
		<link rel="stylesheet" href="styles/events.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	</head>
	


	<body>		
		<div class="center-row">
			<div class="event-btn">
				<div class="btn-contents">
					<h2>Select Event</h2>
					<form id="selectedEvent" method="get" action="edit/general.php">
						<select name="id">
							<?php
								// Get all the events from the database and add them to a selector
								$get_events_stmt = $db->prepare("SELECT * from event");
								$get_events_stmt->execute();

								while($get_events_res = $get_events_stmt->fetch(PDO::FETCH_ASSOC)) {
									echo '<option value="' . $get_events_res['ID'] . '">' . $get_events_res['name'] . '</option>';
								}
							?>
						</select>
						<input type="submit" value="Select">
					</form>
				</div>
			</div>

			<div class="event-btn" onclick="newEvent();">
				<div class="btn-contents">
					<h2>Create New Event</h2>
					<form id="addEvent" action="edit/general.php" method="post">
						<input type="hidden" name="action" value="newEvent">
					</form>
				</div>
			</div>

		</div>
	</body>

	<script>
		function newEvent() {
			$("#addEvent").submit();
		}
	</script>

</html>
