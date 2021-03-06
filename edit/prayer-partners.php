<?php   

    session_start();
    include("../global.php");
    secure();

    $event_id = getEventId();
    if( isset($_POST['action']) )
	{
		inc_config_ver();


		$get_prayer_group_stmt = $db->prepare("SELECT * FROM prayer_partners where event_ID=:id order by sequential_ID asc");
		$get_prayer_group_stmt->bindValue(":id",$event_id);
		$get_prayer_group_stmt->execute();

		$reset_stmt = $db->prepare("UPDATE attendees set prayer_group_ID = null where prayer_group_ID=:prayer_group_ID");

		$stmt = $db->prepare("UPDATE attendees set prayer_group_ID = :prayer_group_ID where event_ID=:event_id and sequential_ID=:sequence");
		while($get_prayer_group_res = $get_prayer_group_stmt->fetch(PDO::FETCH_ASSOC)) {
			$stmt->bindValue(':prayer_group_ID', $get_prayer_group_res["group_ID"]);
			$reset_stmt->bindValue(':prayer_group_ID', $get_prayer_group_res["group_ID"]);
			$reset_stmt->execute();
	
			foreach($_POST['partner'][$get_prayer_group_res["sequential_ID"]] as $key => $sequence) {
				if($sequence == "remove") {
					continue;
				}
				$stmt->bindValue(":sequence",$sequence);
				$stmt->bindValue(':event_id', $event_id);
				$stmt->execute();
			}
		}
		
		if($_POST['action'] == 'addGroup') {
			$stmt = $db->prepare("INSERT into prayer_partners(event_ID, sequential_ID) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from prayer_partners where event_ID=:event_id) as temp))");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();

		}
		else if ($_POST['action'] == 'deleteGroup') {
			$stmt = $db->prepare("DELETE from prayer_partners where event_ID=:id and sequential_ID=:sequence");
			$stmt->bindValue(":id",$event_id);
			$stmt->bindValue(":sequence", $_POST['sequence']);
			$stmt->execute();
		}

		header("Location: prayer-partners.php?id=" . sanitize_id($_POST['id']));
		die();
	}
	include("../templates/check-event-exists.php");

	 

        

?>

<html>

	<?php include("../templates/head.php"); ?>
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
			<p>On this page you can create multiple prayer groups and list the people in those groups. Notice how only attendees are put in here so no contact information is given. An example of using this in a unique way is having the navigation for this page in general be 'Small Groups' and then grouping the attendees here into different small groups.</p>
			<form id="form" method="post">
				<input type="hidden" name="id" value = "<?php echo sanitize_id($_GET["id"]); ?>">
				<input type="hidden" name="action" value = "save">
				<input type="hidden" name="sequence">
				<div id="sectionCards">
					<?php			
						$id = sanitize_id($_GET["id"]);
						$get_prayer_group_stmt = $db->prepare("SELECT * FROM prayer_partners where event_ID=:id order by sequential_ID asc");
						$get_prayer_group_stmt->bindValue(":id",$event_id);
						$get_prayer_group_stmt->execute();

						while($get_prayer_group_res = $get_prayer_group_stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="card">';
							echo '<div class="btn" onclick="deleteGroup('.attrstr($get_prayer_group_res["sequential_ID"]).')">X</div>';
							echo '<div class="input">Partners:';
							echo '<div id="partners[' . attrstr($get_prayer_group_res['sequential_ID']) . ']">';

							$get_info_prayer_res = $db->prepare("SELECT * FROM attendees where event_ID=:id and prayer_group_ID = :prayer_ID");
							$get_info_prayer_res->bindValue(":id", $event_id);
							$get_info_prayer_res->bindValue(":prayer_ID", $get_prayer_group_res["group_ID"]);
							$get_info_prayer_res->execute();

							while($get_attendees_house_res = $get_info_prayer_res->fetch(PDO::FETCH_ASSOC)) {
								echo '<select name="partner[' . attrstr($get_prayer_group_res['sequential_ID']) . '][]" autocomplete="off">';
								
								$get_attendees_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id");
								$get_attendees_stmt->bindValue(":id", $event_id);
								$get_attendees_stmt->execute();

								echo '<option value="remove">Remove</option>';

								while($get_attendees_res = $get_attendees_stmt->fetch(PDO::FETCH_ASSOC)) {
									if($get_attendees_res["sequential_ID"] == $get_attendees_house_res['sequential_ID']) {
										echo '<option value="'.attrstr($get_attendees_res["sequential_ID"]).'" selected="selected">' . htmlstr($get_attendees_res['name']) . '</option>';
									}
									else {
										echo '<option value="'.attrstr($get_attendees_res["sequential_ID"]).'" >' . htmlstr($get_attendees_res['name']) . '</option>';
									}						
								}

								echo '</select>';
							}
							echo '</div>';		
							echo '<div class="btn" title="This button does not save what is on the page." onclick="addPartner(' . attrstr($get_prayer_group_res['sequential_ID']) . ')">Add Partner</div>';
							echo '</div>';
							echo '</div>';
						}
					?>
				</div>

                                <p>Please save before navigating to a new page.</p>
				<div class="btn" onclick="addGroup()">Add Group</div>
				<div class="btn" id="save" onclick="save()">Save</div>
			</form>
		</section>

	</body>

	<script>

		function save(){
			document.forms['form']['action'].value="save";
			$("#form").submit();
		}

		function addGroup() {
			document.forms['form']['action'].value="addGroup";
			$("#form").submit();
		}

		function deleteGroup(sequential_id) {
                        document.forms['form']['action'].value="deleteGroup";
                        document.forms['form']['sequence'].value=sequential_id;
			$("#form").submit();
		}

		function addPartner(num) {
			var html = '<select name="partner[' + num + '][]" autocomplete="off"><?php
					
				$get_attendees_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id");
				$get_attendees_stmt->bindValue(":id", $event_id);
				$get_attendees_stmt->execute();

				echo '<option value="remove" selected>Remove</option>';
				while($get_attendees_res = $get_attendees_stmt->fetch(PDO::FETCH_ASSOC)) {
					echo '<option value='.attrstr($get_attendees_res['sequential_ID']).'>' . attrstr(htmlstr($get_attendees_res['name'])) . '</option>';
				}
				?></select>';

			addFields(html, 'partners\\[' + num + '\\]');
		}

		function deletePartner(num, index) {

		}
	</script>

</html>
