<?php   session_start();


 include("../helper.php");  

include("../connection.php");

secure();

$event_id = getEventID();

if( isset($_POST['action'] )) {
               
		//Saves all the housing selections whenever a action is done
		// statement to update the values
                $stmt = $db->prepare("UPDATE housing set host_name = :host_name, driver = :driver where event_ID = :event_ID and sequential_ID = :sequential_ID");

                // bind the correct values to insert to it.
                $stmt->bindValue(':event_ID', $event_id);

                // Execute the update statement for each of the hosts
                foreach($_POST['host'] as $key => $host) {
                        $driver = $_POST['driver'][$key];

                        $stmt->bindValue(':host_name', $host);
                        $stmt->bindValue(':driver', $driver);
                        $stmt->bindValue(":sequential_ID", $key);

                        $stmt->execute();
                }


                $get_housing_stmt = $db->prepare("SELECT * FROM housing where event_ID=:id order by sequential_ID asc");
                $get_housing_stmt->bindValue(":id",$event_id);
                $get_housing_stmt->execute();

                $reset_stmt = $db->prepare("UPDATE attendees set house_ID = null where house_ID=:housing_ID");
                $update_stmt = $db->prepare("UPDATE attendees set house_ID = :housing_ID where event_ID=:event_id and sequential_ID=:sequence");

                while($get_housing_res = $get_housing_stmt->fetch(PDO::FETCH_ASSOC)) {
                        $update_stmt->bindValue(':housing_ID', $get_housing_res["ID"]);
                        $reset_stmt->bindValue(':housing_ID', $get_housing_res["ID"]);
                        $reset_stmt->execute();

                        foreach($_POST['guest'][$get_housing_res["sequential_ID"]] as $key => $sequence) {
                                if($sequence == "remove") {
                                        continue;
                                }
                                $update_stmt->bindValue(":sequence",$sequence);
                                $update_stmt->bindValue(':event_id', $event_id);
                                $update_stmt->execute();
                        }
                }

	// if we are adding a new host
	if($_POST['action'] == 'addHousing') {

		// insert a new blank record into the housing table for this event
		$event_id = getEventID();

		$stmt = $db->prepare('INSERT into housing(event_ID, sequential_ID) values (:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from housing where event_ID=:event_id) as temp))');
		$stmt->bindValue(":event_id",$event_id);
		$stmt->execute();

	} 
	// delete housing
	else if ($_POST['action'] == 'deleteHousing') {
		$stmt = $db->prepare("DELETE from housing where event_ID=:id and sequential_ID=:sequence");
		$stmt->bindValue(":id",$event_id);
		$stmt->bindValue(":sequence", $_POST['sequence']);
		$stmt->execute();
	}

	// reroute to this page with the correct event id
	header("Location: housing.php?id=".$_POST['id']);
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
			<form id="form" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action">
				<input type="hidden" name="sequence">			
				<div id="sectionCards">
				<?php
					$event_id = getEventID();
					
					$get_housing_stmt = $db->prepare("SELECT * FROM housing where event_ID=:id order by sequential_ID");
					$get_housing_stmt->bindValue(":id",$event_id);
					$get_housing_stmt->execute();
					
					// look through query

					while($get_housing_res = $get_housing_stmt->fetch(PDO::FETCH_ASSOC)) { 
						echo '<div class="card">';
						echo '<div class="btn" onclick="deleteHousing('.$get_housing_res["sequential_ID"].')">X</div>';
						echo '<div class="input">Host: ';
						echo '<select name="host[' . $get_housing_res['sequential_ID'] . ']">';

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

						echo '</select>';
						echo '</div>';
						echo '<div class="input">Driver: <input type="text" name="driver[' . $get_housing_res['sequential_ID'] . ']" value = ' .$get_housing_res['driver'].'></div>';
						echo '<div class="input">Guests: ';
						echo '<div id="guests[' . $get_housing_res['sequential_ID'] . ']">';

						$get_attendees_house_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id and house_ID = :housing_ID");
						$get_attendees_house_stmt->bindValue(":id", $event_id);
						$get_attendees_house_stmt->bindValue(":housing_ID", $get_housing_res["ID"]);
						$get_attendees_house_stmt->execute();


						while($get_attendees_house_res = $get_attendees_house_stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<select name="guest[' . $get_housing_res['sequential_ID'] . '][]" autocomplete="off">';
							
							$get_attendees_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id");
							$get_attendees_stmt->bindValue(":id", $event_id);
							$get_attendees_stmt->execute();

							echo '<option value="remove">Remove</option>';

							while($get_attendees_res = $get_attendees_stmt->fetch(PDO::FETCH_ASSOC)) {
								if($get_attendees_res['sequential_ID'] == $get_attendees_house_res['sequential_ID']) {
									echo '<option value='. $get_attendees_res['sequential_ID'] .' selected>' . $get_attendees_res['name'] . '</option>';
								} else {
									echo '<option>' . $get_attendees_res['name'] . '</option>';
								}
							}

							echo '</select>';
						}

						echo '</div>';
						echo '<div class="btn" onclick="addGuest(' . $get_housing_res['sequential_ID'] . ')">Add Guest</div>';
						echo '</div></div>';
					}

				?>
				</div>

				<br>
				<div class="btn" onclick="addHost()">Add Host</div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>

	</body>

	<?php include("../templates/head.php"); ?>
	<script>
		function addHost() {
			document.forms['form']['action'].value="addHousing";
                        document.forms['form']['sequence'].value="";
			$("#form").submit();
		}

		function deleteHousing(sequential_id) {
                        document.forms['form']['action'].value="deleteHousing";
                        document.forms['form']['sequence'].value=sequential_id;
			$("#form").submit();
		}

		function addGuest(num) {
			var html = '<select name="guest[' + num + '][]" autocomplete="off"><?php
					
					$get_attendees_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id");
					$get_attendees_stmt->bindValue(":id", $event_id);
					$get_attendees_stmt->execute();

					echo '<option value="remove">Remove</option>';
					while($get_attendees_res = $get_attendees_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<option value='. $get_attendees_res['sequential_ID'] .'>' . $get_attendees_res['name'] . '</option>';
					}
					?></select>';

			addFields(html, 'guests\\[' + num + '\\]');
		}
	</script>

	
</html>
