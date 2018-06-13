<?php	session_start();
	include("../connection.php");
	include("../helper.php");
	secure();
	$event_id = getEventId();
    if( isset($_POST['action']) )
	{

                //update all notification records in the event
                $stmt = $db->prepare("UPDATE notifications set title = :title, body = :body, date=TIMESTAMP(:date, :time), refresh = :refresh where event_ID=:event_id and sequential_ID=:sequence");
                $stmt->bindValue(':event_id', $event_id);
                foreach($_POST['title'] as $key => $value) {
                        $stmt->bindValue(":sequence",$key);
                        $stmt->bindValue(':title', $value);
                        $stmt->bindValue(':body', $_POST['body'][$key]);
                        $stmt->bindValue(':date', $_POST['date'][$key]);
                        $stmt->bindValue(':time', $_POST['time'][$key]);

                        // echo isset($_POST['refresh'][$key]);
                        // die();

                        $stmt->bindValue(':refresh', isset($_POST['refresh'][$key]));
                        $stmt->execute();
                }

		if($_POST['action'] == 'addNotification') {
			//add a blank notification record
			$stmt = $db->prepare('INSERT into notifications(event_ID, sequential_ID,title,body,date,refresh) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from notifications where event_ID=:event_id) as temp), "","", :datetime, 0)');
			$stmt->bindValue(':datetime', date("Y-m-d H:i:s"));
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();

		}		

		else if ($_POST['action'] == 'deleteNotification') {
			//delete notification record
			$stmt = $db->prepare("DELETE from notifications where event_ID=:id and sequential_ID=:sequence");
			$stmt->bindValue(":id",$event_id);
			$stmt->bindValue(":sequence", $_POST['sequence']);
			$stmt->execute();
		}
		
		// Redirect to the original address with parameters intact since they are dropped on form submit.
		// The records just added or updated will be added to the page
		header("Location: notifications.php?id=".$_POST['id']);
		die();
	}
	
       
?>

<html>
	<?php include("../templates/head.php"); ?>
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#notifications {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Notifications</h1>
			<p>This page determines what notifications are sent to the user's phone. For instance it could be used to remind users of important activities.</p>
			<form id="notificationForm"  method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action">
				<input type="hidden" name="sequence">
				<div id="notificationCards">
				<?php			
					$id = $_GET["id"];
					$get_notification_stmt = $db->prepare("SELECT * FROM notifications where event_ID=:id order by date asc");
					$get_notification_stmt->bindValue(":id",$event_id);
					$get_notification_stmt->execute();
					
					//populate the form with the event notifications 
					while($get_notification_res = $get_notification_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="card">';
						echo '<div class="btn" onclick="deleteNotification('.$get_notification_res["sequential_ID"].')">X</div>';
						echo '<div class="input">Subject: <input type="text" name="title['.$get_notification_res["sequential_ID"].']" maxlength="100" value = \''.$get_notification_res["title"].'\'></div>';
						echo '<div class="input">Message: <textarea name="body['. $get_notification_res["sequential_ID"] .']">'.$get_notification_res["body"].'</textarea></div>';
						echo '<div class="input">Date: <input type="date" name="date['.$get_notification_res["sequential_ID"].']" value="'. date("Y-m-d",strtotime($get_notification_res["date"])).'"></div>';
						echo '<div class="input">Time: <input type="time" name="time['.$get_notification_res["sequential_ID"].']" value="'. date("H:i",strtotime($get_notification_res["date"])).'"></div>';
						echo '<div name="refresh['.$get_notification_res["sequential_ID"].']" type="hidden" name="visible" value="true" ' . (($get_notification_res["refresh"]) ? "checked" : "") .'></div>';
						echo '</div>';
					}
				?>
				</div>
				<div class="btn" onclick="addNotification()">+ Add Notifications</div>
				<div class="btn" id="save" onclick="save()">Save</div>
			</form>
		</section>
	</body>

	<script>		
		function addNotification() {
			document.forms['notificationForm']['action'].value = "addNotification";
			$("#notificationForm").submit();
		}

		function deleteNotification(sequential_id) {
                        document.forms['notificationForm']['action'].value = "deleteNotification";
                        document.forms['notificationForm']['sequence'].value = sequential_id;
			$("#notificationForm").submit();
		}
		function save(){
                        document.forms['notificationForm']['action'].value = "save";
			$("#notificationForm").submit();
		}
	</script>
</html>

