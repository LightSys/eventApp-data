<?php

    session_start();	

    include("../global.php");
    
    secure($_GET["id"]);
    $event_id=getEventId();	
	
    if( isset($_POST['action']) )
	{

		inc_config_ver();
		
		//update all attendee records in the event 
                $stmt = $db->prepare("UPDATE attendees set name = :name
                        where event_ID=:event_id and sequential_ID=:sequence");

                foreach($_POST['name'] as $key => $name) {

                        $stmt->bindValue(":sequence",$key);
                        $stmt->bindValue(':name', $name);
                        $stmt->bindValue(':event_id', $event_id);
                        $stmt->execute();
                }

		if($_POST['action'] == 'addAttendee'){
			//add a blank attendee record
			$stmt = $db->prepare("INSERT into attendees(event_ID, sequential_ID) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from attendees where event_ID=:event_id) as temp))");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();
		}		
		
		else if ($_POST['action'] == 'deleteAttendee'){
			//delete attendee record
			$stmt = $db->prepare("DELETE from attendees where event_ID=:id and sequential_ID=:sequence");
			$stmt->bindValue(":id",$event_id);
			$stmt->bindValue(":sequence", $_POST['sequence']);
			$stmt->execute();
		}
		
		// Redirect to the original address with parameters intact since they are dropped on form submit.
		// The records just added or updated will be added to the page
		header("Location: attendees.php?id=" . sanitize_id($_POST['id']));
		die();
	}
	
       
        
?>

<html>
	<?php include("../templates/head.php"); ?>
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#attendees {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Attendees</h1>
			<p>This is where you list the names of attendees that will be selected later as being hosted at different homes, or selected as prayer partners. Their contact information is never needed.</p>
			<form id="attendeeForm" method="post">
				<input type="hidden" name="id" value = "<?php echo sanitize_id($_GET["id"]); ?>">
				<input type="hidden" name="action">
				<input type="hidden" name="sequence">
				<div id="attendeeCards">
				<?php			
					$id = sanitize_id($_GET["id"]);
					$get_info_page_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id order by sequential_ID asc");
					$get_info_page_stmt->bindValue(":id",$event_id);
					$get_info_page_stmt->execute();
					
					//populate the form with the event attendees
					while($get_contact_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="card">';
						echo '<div class="btn" onclick="deleteAttendee('. attrstr($get_contact_page_res["sequential_ID"]) . ')">X</div>';
						echo '<div class="input">Name: <input type="text" name="name[' . attrstr($get_contact_page_res["sequential_ID"]) . ']" 
							maxlength="30" value = "' . attrstr($get_contact_page_res["name"]) . '"></div></div>';
					}
				?>
				</div>

                                <p>Please save before navigating to a new page.</p>
				<div class="btn" onclick="addAttendee()">+ Add Attendee</div>
				<div class="btn" id="save" onclick="save()">Save</div>
			</form>
		</section>

	</body>

	<script>		
		function addAttendee() {
			document.forms['attendeeForm']['action'].value="addAttendee";
			$("#attendeeForm").submit();
		}
		
		function save() {
			document.forms['attendeeForm']['action'].value="updateAttendee";			
			$("#attendeeForm").submit();
		}
		
		function deleteAttendee(sequential_id) {
			document.forms['attendeeForm']['action'].value="deleteAttendee";
			document.forms['attendeeForm']['sequence'].value=sequential_id;
			$("#attendeeForm").submit();
		}
	</script>

</html>
