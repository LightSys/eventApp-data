<?php session_start();	
    include("../connection.php");
    include("../helper.php");
    
    secure($_GET["id"]);

    $event_id=getEventId();	
	
    if( isset($_POST['action']) )
	{
		if($_POST['action'] == 'addAttendee') {
			//add a blank attendee record
			$stmt = $db->prepare("INSERT into attendees(event_ID, sequential_ID) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from attendees where event_ID=:event_id) as temp))");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();
		}		
		
		//update all attendee records in the event 
		else if ($_POST['action'] == 'updateAttendee') {		
			$stmt = $db->prepare("UPDATE attendees set name = :name
				where event_ID=:event_id and sequential_ID=:sequence");
		
			foreach($_POST['name'] as $key => $name) {		
				
				$stmt->bindValue(":sequence",$key);
				$stmt->bindValue(':name', $name);
				$stmt->bindValue(':event_id', $event_id);
				$stmt->execute();
			}
		}
		else if ($_POST['action'] == 'deleteAttendee') {
			//delete attendee record
			$stmt = $db->prepare("DELETE from attendees where event_ID=:id and sequential_ID=:sequence");
			$stmt->bindValue(":id",$event_id);
			$stmt->bindValue(":sequence", $_POST['sequence']);
			$stmt->execute();
		}
		
		// Redirect to the original address with parameters intact since they are dropped on form submit.
		// The records just added or updated will be added to the page
		header("Location: attendees.php?id=".$_POST['id']);
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
			<form id="attendeeForm" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updateAttendee">
				<div id="attendeeCards">
				<?php			
					$id = $_GET["id"];
					$get_info_page_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id order by sequential_ID asc");
					$get_info_page_stmt->bindValue(":id",$event_id);
					$get_info_page_stmt->execute();
					
					//populate the form with the event attendees
					while($get_contact_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="card">';
						echo '<div class="btn" onclick="deleteAttendee('.$get_contact_page_res["sequential_ID"].')">X</div>';
						echo '<div class="input">Name: <input type="text" name="name['.$get_contact_page_res["sequential_ID"].']" 
							value = \''.$get_contact_page_res["name"].'\'></div></div>';
					}
				?>
				</div>
				<div class="btn" onclick="addAttendee()">+ Add Attendee</div>
				<div class="btn" id="save" onclick="save()">Save</div>
			</form>
		</section>
		<!--Form to be submitted when the add attendee button is clicked.
			This allows the postinng of data-->
		<form id = "addAttendee" method="post">	
			<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
			<input type="hidden" name="action" value = "addAttendee">
		</form>
		
		<!--Form to be submitted when the delete attendee button is clicked-->
		<form id="deleteAttendee" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="deleteAttendee">
			<input type = "hidden" name="sequence" value="">
		</form>

	</body>

	<script>		
		function addAttendee() {
			$("#addAttendee").submit();
		}
		
		function save() {
			$("#attendeeForm").submit();
		}
		
		function deleteAttendee(sequential_id) {
			$('#deleteAttendee > input[name="sequence"]').val(sequential_id);
			$("#deleteAttendee").submit();
		}
	</script>
</html>

