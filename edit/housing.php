<?php include("../helper.php"); ?>

<?php

include("../connection.php");

if( isset($_POST['action'] )) {

	//die(var_dump($_POST));

	if($_POST['action'] == 'addHousing') {

		$event_id = getEventID();

		$stmt = $db->prepare('INSERT into housing(event_ID, sequential_ID) values (:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from housing where event_ID=:event_id) as temp))');
		$stmt->bindValue(":event_id",$event_id);
		$stmt->execute();

	} else if ($_POST['action'] == 'updateHousing') {	

		$ID = getEventID();
		$stmt = $db->prepare("UPDATE housing set host_name = :host_name, driver = :driver where event_ID = :event_ID and sequential_ID = :sequential_ID");

		$stmt->bindValue(':event_ID', $ID);
		

		foreach($_POST['host'] as $key => $host) {
			// echo "foreach" . $key . "\n";
			$driver = $_POST['driver'][$key];
			
			$stmt->bindValue(':host_name', $host);
			$stmt->bindValue(':driver', $driver);
			$stmt->bindValue(":sequential_ID", $key);

			$stmt->execute();
		}
		// die();
	}

	header("Location: ".full_url($_SERVER)."?id=".$_POST['id']);
	die();
}
 ?>

 <?php include("../templates/check-event-exists.php"); ?>

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
			<form id="form" action="housing.php" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updateHousing">
				
				<?php
					$event_id = getEventID();
					
					$get_housing_stmt = $db->prepare("SELECT * FROM housing where event_ID=:id order by sequential_ID");
					$get_housing_stmt->bindValue(":id",$event_id);
					$get_housing_stmt->execute();



					// look through query
					while($get_housing_res = $get_housing_stmt->fetch(PDO::FETCH_ASSOC)){ 
						echo '<div id="sectionCards"><div class="card"><div class="input">Host: '
						. '<select name="host[' . $get_housing_res['sequential_ID'] . ']">';

						$get_hosts_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id");
						$get_hosts_stmt->bindValue(":id",$event_id);
						$get_hosts_stmt->execute();

						while($get_hosts_res = $get_hosts_stmt->fetch(PDO::FETCH_ASSOC)) {
							if ($get_housing_res['host_name'] == $get_hosts_res['name']) {
								echo '<option selected>' . $get_hosts_res['name'] . '</option>';
							} else {
								echo '<option>' . $get_hosts_res['name'] . '</option>';
							}
						}

						echo '</select>'
//						. '<input type="text" name="host[]" value = '.$get_housing_res['host_name'].'>'
						. '</div>'
						. '<div class="input">Driver: <input type="text" name="driver[' . $get_housing_res['sequential_ID'] . ']" value = ' .$get_housing_res['driver'].'></div>'
						// . '<input type = "hidden" name="sequence" value="' . $get_housing_res['sequential_ID'] . '">'
						. '<div class="input">Guests: <div id="guests[' . $get_housing_res['sequential_ID'] . ']"><select id="contact[' . $get_housing_res['sequential_ID'] . ']"><option>contact name</option></select></div><br><br>'
						. '<div class="btn" onclick="addGuest([])">Add Guest</div></div></div>';
					}
				?>
				<br>
				<div class="btn" onclick="addHost()">Add Host</div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>
		<form id = "addHousing" action = "housing.php" method="post">
			<input type = "hidden" name="sequence" value="">
			<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
			<input type="hidden" name="action" value = "addHousing">
		</form>

	</body>

	<?php include("../templates/head.php"); ?>
	<script>
		function addHost() {
			$("#addHousing").submit();
		}

		function addGuest(num) {
			var html = '<select id="contact[]"><option>contact name</option></select>';
			addFields(html, 'guests' + num);
		}
	</script>

	
</html>