<?php include("../templates/check-event-exists.php"); ?>

<?php

include("../connection.php");

if( isset($_POST['action'] )) {

	if($_POST['action'] == 'addHousing') {

		$event_id = $_GET['id'];

		$stmt = $db->prepare('INSERT into housing(event_ID, sequential_ID) values (:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from housing where event_ID=:event_id) as temp), "", "")');
		$stmt->bindValue(":event_id",$event_id);
		$stmt->execute();

		$housing_id = $db->lastInsertId();

		$stmt = $db->prepare('INSERT into housing(event_ID, sequential_ID) values (:event_id, :sequential_ID)');
		$stmt->bindValue(':event_id', $event_id);
		$stmt->bindValue(":sequential_ID",$housing_id);
		$stmt->execute();

	} else if ($_POST['action'] == 'updateHousing') {	

		// die("update housing");

		// UPDATE contacts set name = :name, address = :address, phone = :phone, event_ID = :event_id"
		if (!($stmt = $db->prepare("UPDATE housing set event_ID = :event_ID, host_name = :host_name, driver = :driver where event_ID = :id and sequential_ID = :sequence"))) {
			die(0);
		}

		foreach($_POST['host'] as $key => $host) {
			$ID = $_GET['id'];
			$driver = $_POST['driver'][$key];

			echo $ID . "<br>";
			echo $driver . "<br>";
			echo $host . "<br>";
			
			$stmt->bindValue(":sequence", $key);
			$stmt->bindValue(':event_ID', $ID);
			$stmt->bindValue(':host_name', $host);
			$stmt->bindValue(':driver', $driver);
			$stmt->bindValue(":sequence", $_POST['sequence']);
			$stmt->execute();
		}
	}
}
 ?>

<html>
	
	
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
			<?php echo('<form id="form" action="housing.php?id=' . $_GET['id'] . '" method="post">') ?>
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updateHousing">
				
				<?php
					$event_id = $_GET["id"];
					$get_housing_stmt = $db->prepare("SELECT * FROM housing where event_ID=:id");
					$get_housing_stmt->bindValue(":id",$event_id);
					$get_housing_stmt->execute();


					// look through query
					while($get_housing_res = $get_housing_stmt->fetch(PDO::FETCH_ASSOC)){ 
						echo '<div id="sectionCards"><div class="card"><div class="input">Host: <input type="text" name="host[]" value = '.$get_housing_res['host_name'].'></div>'
						. '<div class="input">Driver: <input type="text" name="driver[]" value = ' .$get_housing_res['driver'].'></div>'
						. '<div class="input">Guests: <div id="guests[]"><select id="contact[]"><option>contact name</option></select></div><br><br>'
						. '<div class="btn" onclick="addGuest([])">Add Guest</div></div></div>';
					}
				?>
				<br>
				<div class="btn" onclick="addHost()">Add Host</div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>
		<form id = "addHousing" action = "housing.php?id=<?php echo $_GET["id"]?>" method="post">
			<input type = "hidden" name="sequence" value="">
			<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
			<input type="hidden" name="action" value = "addHousing">
		</form>

	</body>

	<?php include("../templates/head.php"); ?>
	<script>
		function addHost() {
			// $("#addHousing > #sequence").value(sequential_ID);
			$("#addHousing").submit();
		}

		function addGuest(num) {
			var html = '<select id="contact[]"><option>contact name</option></select>';
			addFields(html, 'guests' + num);
		}
	</script>

	
</html>