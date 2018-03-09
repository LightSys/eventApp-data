<?php	
	include("../connection.php");
	include("../helper.php");

	$event_id = getEventId();
    if( isset($_POST['action']) )
	{
		if($_POST['action'] == 'addAttendee') {
			$stmt = $db->prepare("INSERT into attendees(event_ID, sequential_ID) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from info_page where event_ID=:event_id) as temp))");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();
		}		
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
			
		}
		header("Location: ".full_url($_SERVER)."?id=".$_POST['id']);
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
			<form id="attendeeForm" action = "attendees.php" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updateAttendee">
				<div id="attendeeCards">
				<?php			
					$id = $_GET["id"];
					$get_info_page_stmt = $db->prepare("SELECT * FROM attendees where event_ID=:id order by sequential_ID asc");
					$get_info_page_stmt->bindValue(":id",$event_id);
					$get_info_page_stmt->execute();

					while($get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="card"><div class="input">Name: <input type="text" name="name['.$get_info_page_res["sequential_ID"].']" 
							value = \''.$get_info_page_res["name"].'\'></div></div>';
					}
				?>
				</div>
				<div class="btn" onclick="addAttendee()">+ Add Attendee</div>
				<input type="submit" value="Submit">
			</form>
		</section>
		<form id = "addAttendee" action = "attendees.php" method="post">	
			<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
			<input type="hidden" name="action" value = "addAttendee">
		</form>

	</body>

	<script>		
		function addAttendee() {
			$("#addAttendee").submit();
		}
	</script>
</html>

