<?php   session_start();
	include("../connection.php");
	include("../helper.php");
	$event_id = getEventId();	
	secure();	
	if( isset($_POST['action']) )
	{      
	 
		if($_POST['action'] == 'updateEvent') {
	 
			//update event record
			$stmt = $db->prepare("UPDATE event set refresh = :refresh, refresh_expire = :refreshExpire, 
			theme_dark = :themeDark, theme_medium = :themeMedium, theme_color = :themeColor where ID=:event_id");		
			$refresh = $_POST['refresh'];
			$refreshExpire = $_POST['refreshExpire'];
			$themeDark = $_POST['themeDark'];
			$themeColor = $_POST['themeColor'];
			$themeMedium = $_POST['themeMedium'];
			
			$stmt->bindValue(':refresh', $refresh);
			$stmt->bindValue(':refreshExpire', $refreshExpire);
			$stmt->bindValue(':themeDark', "#".$themeDark);
			$stmt->bindValue(':themeMedium', "#".$themeMedium);
			$stmt->bindValue(':themeColor', "#".$themeColor);
			$stmt->bindValue(':event_id', $_POST["id"]);
			$stmt->execute();
				
	
			
		}		
		
		// Redirect to the original address with parameters intact since they are dropped on form submit.
		// The records just added or updated will be added to the page
		header("Location: advanced.php?id=".$_POST['id']);
		die();
	}
	include("../templates/check-event-exists.php");

	
      

             
 
?>

<html>

	<?php include("../templates/head.php"); ?>

	<head>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script type="text/javascript" src="../scripts/jscolor.js"></script>
	</head>
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#advanced {
				background-color: grey;
				color: white;
			}
		</style>
		
		<section id="main">
			<h1>Advanced</h1>
			<p>This page contains advanced settings that are used to determine the "feel" of the app.</p>
			<form id="form" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updateEvent">
				<div class="card">
					<?php			
						$id = $_GET["id"];
						$get_contact_stmt = $db->prepare("SELECT * FROM event where ID=:id");
						$get_contact_stmt->bindValue(":id",$id);
						$get_contact_stmt->execute();
						$get_contact_res = $get_contact_stmt->fetch(PDO::FETCH_ASSOC);
						
						//populate page from database 
						//echo '<div class="input">Refresh: <input type="number" name="refresh" value="'.$get_contact_res['refresh'].'"></div>';

						echo '<div class="input">Default Notification Refresh Time: <select name="refresh" >';
							$times = array(

								0 => "1",
								1 => "5",
								2 => "15",
								3 => "30",
								4 => "60",
								5 => "never",
								6 => "auto",

							);

						for($i=0; $i<sizeof($times); $i++) {
							if ($get_contact_res['refresh'] == $times[$i]) {
								echo '<option selected>' . $times[$i] . '</option>';
							} else {
								echo '<option>' . $times[$i] . '</option>';
							}
						}
						echo '</select>';
						echo '</div>';

						echo '<div class="input">Refresh Notifications Expiration: <input type="date" name="refreshExpire" value="'.$get_contact_res['refresh_expire'].'"></div>';
						echo '<p>This is used with white text, be sure to set it dark enough.</p>';
						echo '<div class = "input">Gradient Theme Dark: <input class="jscolor {closable:true,closeText:"Close"}" name = "themeDark" 
								maxlength="7" value="'.str_replace("#", "", $get_contact_res['theme_dark']).'"></div>';
						echo '<p>This color is mainly used as an accent.</p>';
						echo '<div class = "input">Gradient Theme Medium: <input class="jscolor {closable:true,closeText:"Close"}" name = "themeMedium" 
								maxlength="7" value="'.str_replace("#", "", $get_contact_res['theme_medium']).'"></div>';
						echo '<p>This color is used the most, and also with white text.</p>';
						echo '<div class = "input">App Theme Color: <input class="jscolor {closable:true,closeText:"Close"}" name = "themeColor" 
								maxlength="7" value="'.str_replace("#", "", $get_contact_res['theme_color']).'"></div>';
					?>

				</div>
				<br>
				<div class="btn" id="save" onclick="updateEvent()">Save</div>
			</form>
		</section>
	</body>
	<script>		
		function updateEvent() {
			$("#form").submit();
		}
	</script>

</html>
