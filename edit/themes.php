<?php	
	include("../connection.php");
	include("../helper.php");

	$event_id = getEventId();
    if( isset($_POST['action']) )
	{
		if($_POST['action'] == 'addTheme') {
			//add a blank theme record
			$stmt = $db->prepare("INSERT into themes(event_ID, sequential_ID) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from themes where event_ID=:event_id) as temp))");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();
		}		
	
		else if ($_POST['action'] == 'updateTheme') {	
		
			//update all theme records in the event 
			$stmt = $db->prepare("UPDATE themes set name = :name, address = :address, phone = :phone 
				where event_ID=:event_id and sequential_ID=:sequence");
			foreach($_POST['name'] as $key => $name) {		
				$address = $_POST['address'][$key];
				$phone = $_POST['phone'][$key];
				
				$stmt->bindValue(":sequence",$key);
				$stmt->bindValue(':name', $name);
				$stmt->bindValue(':address', $address);
				$stmt->bindValue(':phone', $phone);
				$stmt->bindValue(':event_id', $event_id);
				$stmt->execute();
			}
		}
		else if ($_POST['action'] == 'deleteTheme') {
			//delete theme record
			$stmt = $db->prepare("DELETE from themes where event_ID=:id and sequential_ID=:sequence");
			$stmt->bindValue(":id",$event_id);
			$stmt->bindValue(":sequence", $_POST['sequence']);
			$stmt->execute();
		}
		
		// Redirect to the original address with parameters intact since they are dropped on form submit.
		// The records just added or updated will be added to the page
		header("Location: ".full_url($_SERVER)."?id=".$_POST['id']);
		die();
	}
?>

<html>
	<?php include("../templates/head.php"); ?>
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#themes {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Themes</h1>
			<form id="themeForm" action = "themes.php" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updateTheme">
				<div id="themeCards">
				<?php			
					$id = $_GET["id"];
					$get_theme_stmt = $db->prepare("SELECT * FROM themes where event_ID=:id order by sequential_ID asc");
					$get_theme_stmt->bindValue(":id",$event_id);
					$get_theme_stmt->execute();
					
					//populate the form with the event themes 
					while($get_theme_res = $get_theme_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="card">';
						echo '<div class="btn" onclick="deleteTheme('.$get_theme_res["sequential_ID"].')">X</div>';
						echo '<div class="input">Name: <input type="text" name="name['.$get_theme_res["sequential_ID"].']" 
							value = \''.$get_theme_res["name"].'\'></div>';
						echo '<div class="input">Address: <input type="text" name="address['.$get_theme_res["sequential_ID"].']" 
							value = \''.$get_theme_res["address"].'\'></div>';
						echo '<div class="input">Phone: <input type="text" name="phone['.$get_theme_res["sequential_ID"].']" 
							value = \''.$get_theme_res["phone"].'\'></div></div>';
					}
				?>
				</div>
				<div class="btn" onclick="addTheme()">+ Add Theme</div>
				<input type="submit" value="Submit">
			</form>
		</section>
		<!--Form to be submitted when the add Theme button is clicked.
			This allows the postinng of data-->
		<form id = "addTheme" action = "themes.php" method="post">	
			<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
			<input type="hidden" name="action" value = "addTheme">
		</form>
		
		<!--Form to be submitted when the delete theme button is clicked-->
		<form id="deleteTheme" action="Themes.php" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="deleteTheme">
			<input type = "hidden" name="sequence" value="">
		</form>

	</body>

	<script>		
		function addTheme() {
			$("#addTheme").submit();
		}

		function deleteTheme(sequential_id) {
			$('#deleteTheme > input[name="sequence"]').val(sequential_id);
			$("#deleteTheme").submit();
		}
	</script>
</html>

