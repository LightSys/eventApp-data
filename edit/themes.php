<?php	session_start();
	include("../connection.php");
	include("../helper.php");
        secure();
	$event_id = getEventId();
    if( isset($_POST['action']) )
	{
			//update all theme records in the event 
                        $stmt = $db->prepare("UPDATE themes set theme_name = :themeName, theme_color = :themeColor
                                where event_ID=:event_id and sequential_ID=:sequence");
                        foreach($_POST['themeName'] as $key => $name) {
                                $themeColor = $_POST['themeColor'][$key];

                                $stmt->bindValue(":sequence", $key);
                                $stmt->bindValue(":themeColor", "#".$themeColor);
                                $stmt->bindValue(":themeName", $name);
                                $stmt->bindValue(':event_id', $event_id);
                                $stmt->execute();
                        }

		if($_POST['action'] == 'addTheme') {
			//add a blank theme record
			$stmt = $db->prepare("INSERT into themes(event_ID, sequential_ID) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from themes where event_ID=:event_id) as temp))");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();
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
		header("Location: themes.php?id=".$_POST['id']);
		die();
	}
  

        
?>

<html>
	<head>
		<script type="text/javascript" src="../scripts/jscolor.js"></script>
	</head>
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
			<h1>Schedule Themes</h1>
			<p>This is where you make custom theme colors. They can be selected in the schedule tab for different events. This is how you color code the app calendar.</p>
			<form id="themeForm" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action">
				<input type="hidden" name="sequence">
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
						echo '<div class="input">Theme Name: <input type="text" name="themeName['.$get_theme_res["sequential_ID"].']" 
								maxlength="50" value="'.$get_theme_res['theme_name'].'"></div>';
						echo '<div class = "input">Theme Color: <input class="jscolor {closable:true,closeText:"Close"}" 
								name="themeColor['.$get_theme_res["sequential_ID"].']" 
								maxlength="7" value="'.str_replace("#", "", $get_theme_res['theme_color']).'"></div></div>';
					}
				?>
				</div>
				<div class="btn" onclick="addTheme()">+ Add Theme</div>
				<div class="btn" id="save" onclick="save()">Save</div>
			</form>
		</section>

	</body>

	<script>		
		function addTheme() {
			document.forms["themeForm"]["action"].value = "addTheme";
			$("#themeForm").submit();
		}
		
		function save() {
			document.forms["themeForm"]["action"].value = "updateTheme";
			$("#themeForm").submit();
		}

		function deleteTheme(sequential_id) {
			document.forms["themeForm"]["action"].value = "deleteTheme";
			document.forms["themeForm"]["sequence"].value = sequential_id;
			$("#themeForm").submit();
		}
	</script>
</html>

