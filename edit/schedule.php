<?php session_start(); 
include("../connection.php");
include("../helper.php");
secure();
$event_id = getEventId();

if(isset($_POST['action'])) {

	$stmt = $db->prepare("UPDATE schedule_items set date=:date, start_time=:start_time, length=:length, description=:description, location=:location, category=:category where event_ID=:id and sequential_ID=:sequence");
	$stmt->bindValue(":id",$event_id);

	foreach ($_POST['date'] as $key => $value) {
		$stmt->bindValue(":sequence",$key);
		$stmt->bindValue(":date",$value);
		$stmt->bindValue(":start_time",$_POST['starttime'][$key]);
		$stmt->bindValue(":length",$_POST['length'][$key]);
		$stmt->bindValue(":description",$_POST['description'][$key]);
		$stmt->bindValue(":location",$_POST['location'][$key]);
		$stmt->bindValue(":category",$_POST['category'][$key]);
		$stmt->execute();
	}

	if($_POST['action'] == "addItem") {
		$stmt = $db->prepare('INSERT into schedule_items(event_ID, sequential_ID, date,start_time,length,description,location,category) values (:id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from schedule_items where event_ID=:id) as temp), CURDATE(), "0000", "", "","", "#000000")');
		$stmt->bindValue(":id",$event_id);
		$stmt->execute();
	}

	else if($_POST['action'] == "deleteItem") {
		$stmt = $db->prepare("DELETE from schedule_items where event_ID=:id and sequential_ID=:sequence");
		$stmt->bindValue(":id",$event_id);
		$stmt->bindValue(":sequence", $_POST['sequence']);
		$stmt->execute();
	}

	header("Location: schedule.php?id=".$_POST['id']);
	die();
}

include("../templates/check-event-exists.php");

        

?>

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
			<form id="updateForm" method="post">
				<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
				<input type = "hidden" name="action">
				<input type = "hidden" name="sequence">
				<div id="scheduleDiv">
					<?php 
					$get_schedule_stmt = $db->prepare("SELECT * FROM schedule_items where event_ID=:id order by date,start_time asc");
					$get_schedule_stmt->bindValue(":id",$event_id);
					$get_schedule_stmt->execute();



					while($get_schedule_res = $get_schedule_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="card">'; 
						echo '<div class="btn" onclick="deleteItem('.$get_schedule_res["sequential_ID"].')">X</div>';
						echo '<div class="input">Date: <input type="date" name="date[' . $get_schedule_res["sequential_ID"] . ']" value="'. date("Y-m-d",strtotime($get_schedule_res["date"])).'"></div>'; 
						echo '<div class="input">Start Time: <input type="text" name="starttime[' . $get_schedule_res["sequential_ID"] . ']" value="'. $get_schedule_res["start_time"].'"></div>';
						echo '<div class="input">Length: <input type="text" name="length[' . $get_schedule_res["sequential_ID"] . ']" value="'. $get_schedule_res["length"].'"></div>';
						echo '<div class="input">Description: <input type="text" name="description[' . $get_schedule_res["sequential_ID"] . ']" value="'. $get_schedule_res["description"].'"></div>';
						echo '<div class="input">Location: <input type="text" name="location[' . $get_schedule_res["sequential_ID"] . ']" value="'. $get_schedule_res["location"].'"></div>';

						echo '<div class="input">Category: ';

						$get_themes_stmt = $db->prepare("SELECT * from themes where event_ID=:id");
						$get_themes_stmt->bindValue(":id",$event_id);
						$get_themes_stmt->execute();

						echo '<select name="category[' . $get_schedule_res["sequential_ID"] . ']" value="'. $get_schedule_res["category"] . '">';
						while($get_theme_res = $get_themes_stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<option>' . $get_theme_res['theme_name'] . '</option>';
						}
						echo '</select></div>';
						echo '</div>';
					}
					?>	
				</div>
				<div class="btn" onclick="addScheduleItem()">+ Add Schedule </div>
				<div class="btn" id="save" onclick="save()">Save</div>
			</form>
		</section>
	</body>
	<?php include("../templates/head.php"); ?>
	<script>

		function addScheduleItem() {
			document.forms['updateForm']['action'].value = "addItem";
			$("#updateForm").submit();
		}

		function save() {
			document.forms['updateForm']['action'].value = "updateAll";			
			$("#updateForm").submit();
		}

		function deleteItem(sequential_id) {
			document.forms['updateForm']['action'].value = "deleteItem";
			document.forms['updateForm']['sequence'].value = sequential_id;
			$("#updateForm").submit();
		}
	</script>
</html>

