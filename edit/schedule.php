<?php

session_start(); 
include("../global.php");
secure();
$event_id = getEventId();

if(isset($_POST['action'])) {

	inc_config_ver();

	$stmt = $db->prepare("UPDATE schedule_items set date=:date, start_time=:start_time, length=:length, description=:description, location=:location, category=:category where event_ID=:id and sequential_ID=:sequence");
	$stmt->bindValue(":id",$event_id);

	foreach ($_POST['date'] as $key => $value) {
		$stmt->bindValue(":sequence",$key);
		$stmt->bindValue(":date",$value);
		$sanatizedStartTime = str_replace (":","",$_POST['starttime'][$key]);
		$stmt->bindValue(":start_time",$sanatizedStartTime);
		if ($_POST['length'][$key]>0 && $_POST['length'][$key]<=1440){
			$stmt->bindValue(":length",$_POST['length'][$key]);
		}
		else {
			$stmt->bindValue(":length", "1");
		}	
		$stmt->bindValue(":description",$_POST['description'][$key]);
		$stmt->bindValue(":location",$_POST['location'][$key]);
		$stmt->bindValue(":category",$_POST['category'][$key]);
		$stmt->execute();
	}

	if($_POST['action'] == "addItem") {
		$stmt = $db->prepare('INSERT into schedule_items(event_ID, sequential_ID, date,start_time,length,description,location,category) values (:id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from schedule_items where event_ID=:id) as temp), CURDATE(), "0600", "1", "","", "#0000")');
		$stmt->bindValue(":id",$event_id);
		$stmt->execute();
	}

	else if($_POST['action'] == "deleteItem") {
		$stmt = $db->prepare("DELETE from schedule_items where event_ID=:id and sequential_ID=:sequence");
		$stmt->bindValue(":id",$event_id);
		$stmt->bindValue(":sequence", $_POST['sequence']);
		$stmt->execute();
	}

	if($_POST['action'] == "copyDay") {

		// 0) get the day to copy
		$dayToCopy = $_POST['day'];

		// 1) get the next Sequential ID (put into a PHP variable)
		$nextSequentialID = $db->prepare("SELECT IFNULL(MAX(temp.sequential_ID),0)+1 as nextSequentialID FROM (SELECT sequential_ID FROM schedule_items WHERE event_ID=:id) as temp");
		$nextSequentialID->bindValue(":id",$event_id);
		$nextSequentialID->execute();

		$seqRow = $nextSequentialID->fetch(PDO::FETCH_ASSOC);
		$seq_ID = $seqRow['nextSequentialID'];

		// 2) select the records for the Days to Copy (put into a PHP data structure)
		$dayToCopyDate = date("Y-m-d", strtotime($dayToCopy));
		$toCopyResults = $db->prepare("SELECT event_ID,date,start_time,length,description,location,category FROM schedule_items WHERE event_ID=:id AND date=:dayToCopy");
		$toCopyResults->bindValue(":id",$event_id);
		$toCopyResults->bindValue(":dayToCopy",$dayToCopyDate);
		$toCopyResults->execute();

		/*
		3) given #2 as a PHP Data Structure, create new records as such:
		    for each record from #2: insert as such:
				INSERT INTO schedule_items (event_ID,sequential_ID,date,start_time,length,description,location,category)
				VALUES ($event_Id,$seqId,CURDATE(),$data_start_time,$data_start_time,$data_length,$data_descr,$data_loc,
						$data_category);
				in PHP, increment the sequential_ID variable: seqId = seqId+1;
				loop until done
		*/
		while($copyRowDay = $toCopyResults->fetch(PDO::FETCH_ASSOC)) {
			$copied_event_ID = intval($copyRowDay['event_ID']);
			$copied_start_time = floatval($copyRowDay['start_time']);
			$copied_length = intval($copyRowDay['length']);
			$copied_description = $copyRowDay['description'];
			$copied_location = $copyRowDay['location'];
			$copied_category = $copyRowDay['category'];

			$toCopyInsert = $db->prepare("INSERT INTO schedule_items (event_ID,sequential_ID,date,start_time,length,description,location,category) VALUES (:event_ID_value,:sequential_ID_value,CURDATE(),:start_time_value,:length_value,:description_value,:location_value,:category_value)");

			$toCopyInsert->bindValue(":event_ID_value",$copied_event_ID);
			$toCopyInsert->bindValue(":sequential_ID_value",$seq_ID);
			$toCopyInsert->bindValue(":start_time_value",$copied_start_time);
			$toCopyInsert->bindValue(":length_value",$copied_length);
			$toCopyInsert->bindValue(":description_value",$copied_description);
			$toCopyInsert->bindValue(":location_value",$copied_location);
			$toCopyInsert->bindValue(":category_value",$copied_category);
			$toCopyInsert->execute();

			$seq_ID++;
		}

	}

	header("Location: schedule.php?id=" . sanitize_id($_POST['id']));
	die();
}

include("../templates/check-event-exists.php");

        

?>

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
			<p>This is where each activity happening in the event is created. The app will take these activities and create a calendar.</p>
			<form id="updateForm" method="post">
				<input type = "hidden" name="id" value="<?php echo sanitize_id($_GET['id']); ?>">
				<input type = "hidden" name="action">
				<input type = "hidden" name="sequence">
				<input type = "hidden" name="day">
				<div id="scheduleDiv">
					<?php 

					// return distinct days
					$get_days_stmt = $db->prepare("SELECT DISTINCT(DATE_FORMAT(DATE, '%Y-%m-%d')) AS DATE FROM schedule_items where event_ID=:id order by date asc");
					$get_days_stmt->bindValue(":id",$event_id);
                    $get_days_stmt->execute();

					$row_size = $get_days_stmt->rowCount() -1;

                    // for each day, create a card
					while($get_days_result = $get_days_stmt->fetch(PDO::FETCH_ASSOC)) {

						echo '</div>';		// wrapper
						
						$day_id = $get_days_result['DATE'];
						
						// day card
						$dayCard = "dayCard";																						// create an ID variable
						echo '<div class="card" id="' . $dayCard . '">';															// define the day card
						// echo '<div class="btn" onclick="deleteDay('.attrstr($get_schedule_res["sequential_ID"]).')">X</div>';	// delete the day card
						// echo '<div class="btn" onclick="expandCollapseDay(this);">' . ($dayCard?'V':'>') . '</div>';				// expand/collapse the day card
						echo ' <div class="btn" onclick="copyDay(\''. htmlstr($day_id) .'\')">Copy</div>';							// copy the day card
						echo '<span class="title">' . $day_id . '</span>';															// return a title for the day card

						$get_schedule_stmt = $db->prepare("SELECT * FROM schedule_items where (event_ID=:id and date=:aDate) order by date,start_time asc");
						$get_schedule_stmt->bindValue(":id",$event_id);
						$get_schedule_stmt->bindValue(":aDate",$day_id);
						$get_schedule_stmt->execute();

						$open_item = $get_schedule_stmt->rowCount() - 1;
						$cur_item = 0;

						
						// for each schedule item, create a card
						while($get_schedule_res = $get_schedule_stmt->fetch(PDO::FETCH_ASSOC)) {

							echo '<div class = "wrapper">';
							
							echo '<div class="card-inside">';
							
							// schedule item card
							$colonTime = substr_replace ($get_schedule_res["start_time"],":",strlen($get_schedule_res["start_time"]) - 2,0);
							if (strlen($colonTime) == 4)
							    $colonTime = '0' . $colonTime;


							// header/title
							echo '<div class="btn" onclick="deleteItem('.attrstr($get_schedule_res["sequential_ID"]).')">X</div>';
							echo ' <div class="btn" onclick="expandCollapse(this);">' . (($cur_item == $open_item)?'V':'>') . '</div>';
							echo '<span class="title">' . htmlstr(date("D M jS",strtotime($get_schedule_res["date"]))) . ' ' . htmlstr($colonTime) . ' - ' . htmlstr($get_schedule_res["description"]) . ' (' . htmlstr($get_schedule_res["length"]) . ' min)</span>';

							
							// detail
							echo '<div class="detail" style="display:' . (($cur_item == $open_item)?'block':'none') . ';">';
							echo '<div class="input">Date: <input type="date" name="date[' . attrstr($get_schedule_res["sequential_ID"]) . ']" value="'. attrstr(date("Y-m-d",strtotime($get_schedule_res["date"]))).'"></div>'; 
							echo '<div class="input">Start Time: <input type="time" name="starttime[' . attrstr($get_schedule_res["sequential_ID"]) . ']" value="'. attrstr($colonTime).'"></div>';
							echo '<div class="input">Length in Minutes: <input type="number" title="This must be below 2400" name="length[' . attrstr($get_schedule_res["sequential_ID"]) . ']" max="2359" value="'. attrstr($get_schedule_res["length"]).'"></div>';
							echo '<div class="input">Description: <input type="text" name="description[' . attrstr($get_schedule_res["sequential_ID"]) . ']" maxlength="150" value="'. attrstr($get_schedule_res["description"]).'"></div>';
							echo '<div class="input">Location: ';

													$get_hosts_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id");
													$get_hosts_stmt->bindValue(":id",$event_id);
													$get_hosts_stmt->execute();
						
								echo '<select name="location[' . attrstr($get_schedule_res["sequential_ID"]) . ']">';
							
							echo '<option value="">'."No Location".'</option>';
				
													while($get_hosts_res = $get_hosts_stmt->fetch(PDO::FETCH_ASSOC)) {
															if ($get_hosts_res['ID'] == $get_schedule_res['location']) {
																	echo '<option value="'. attrstr($get_hosts_res["ID"]) . '" selected>' . htmlstr($get_hosts_res["name"]) . '</option>';
															} else {
																	echo '<option value="'. attrstr($get_hosts_res["ID"]) .'">' . htmlstr($get_hosts_res["name"]) . '</option>';
															}
													}

													echo '</select>';
							echo '</div>';

							echo '<div class="input">Theme: ';

							$get_themes_stmt = $db->prepare("SELECT * from themes where event_ID=:id");
							$get_themes_stmt->bindValue(":id",$event_id);
							$get_themes_stmt->execute();

							echo '<select name="category[' . attrstr($get_schedule_res["sequential_ID"]) . ']">';
							
							echo '<option value="">'."No Theme".'</option>';

							while($get_theme_res = $get_themes_stmt->fetch(PDO::FETCH_ASSOC)) {
								if($get_theme_res['ID'] == $get_schedule_res['category']){
									echo '<option selected value = ' . attrstr($get_theme_res["ID"]) . '>' . htmlstr($get_theme_res['theme_name']) . '</option>';
								} else {
																	echo '<option value = ' . attrstr($get_theme_res["ID"]) . '>' . htmlstr($get_theme_res['theme_name']) . '</option>';
								}
							}

							echo '</select></div>';
							echo '</div>';		// wrapper
							echo '</div>';		// detail
							echo '</div>';		// schedule item card
							
							$cur_item++;
						}	
						
						
						echo '</div>';			// day card
						echo '</div>';			// wrapper
					}
					?>	
				</div>
				

                                <p>Please save before navigating to a new page.</p>
				<div class="btn" onclick="addScheduleItem()">+ Add Schedule </div>
				<div class="btn" id="save" onclick="save()">Save</div>
			</form>
		</section>
	</body>

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

		function expandCollapse(div) {
			if (div.nextSibling.nextSibling.style.display == 'block') {
				div.nextSibling.nextSibling.style.display = 'none';
				div.innerHTML = '>';
			} else {
				div.nextSibling.nextSibling.style.display = 'block';
				div.innerHTML = 'V';
			}
		}
		
		// in the future, modify this function to include a pop-up to select a date value; otherwise, use CURDATE() (Line 82)
		// https://www.aspsnippets.com/Articles/Pass-value-from-child-popup-window-to-parent-page-window-using-JavaScript.aspx

		function copyDay(theDay) {
			document.forms['updateForm']['action'].value = "copyDay";
			document.forms['updateForm']['day'].value = theDay;
			$("#updateForm").submit();
		}
		
		// in the future, modify this function to delete the day card
		/*
		function deleteDay(sequential_id) {
			document.forms['updateForm']['action'].value = "deleteItem";
			document.forms['updateForm']['sequence'].value = sequential_id;
			$("#updateForm").submit();
		}
		*/

		// in the future, modify this function to expand/collapse the day card
		/* 
		function expandCollapseDay(div) {
			if (div.nextSibling.nextSibling.style.display == 'block') {
				div.nextSibling.nextSibling.style.display = 'none';
				div.innerHTML = '>';
			} else {
				div.nextSibling.nextSibling.style.display = 'block';
				div.innerHTML = 'V';
			}
		}
		*/
		
	</script>
</html>