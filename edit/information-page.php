<?php session_start();
include("../connection.php");
include("../helper.php");

secure();

$event_id = getEventId();

if(isset($_POST['action'])) {
	if($_POST['action'] == "updateAll") {
		$stmt = $db->prepare("UPDATE info_page set nav=:name, icon=:icon where event_ID=:id and sequential_ID=:sequence");
		$stmt->bindValue(":id",$event_id);

		foreach ($_POST['name'] as $key => $value) {
			$stmt->bindValue(":sequence",$key);
			$stmt->bindValue(":name",$value);
			$stmt->bindValue(":icon",$_POST['icon'][$key]);
			$stmt->execute();

			$section_stmt = $db->prepare("UPDATE info_page_sections set header=:header, content=:content where info_page_ID=(select ID from info_page  where event_ID=:id and sequential_ID=:page_sequence) and sequential_ID=:section_sequence");
			$section_stmt->bindValue(":id",$event_id);
			$section_stmt->bindValue(":page_sequence",$key);

			foreach ($_POST['header'][$key] as $section_key => $section_value) {
				$section_stmt->bindValue(":section_sequence",$section_key);
				$section_stmt->bindValue(":header",$section_value);
				$section_stmt->bindValue(":content",$_POST['content'][$key][$section_key]);
				$section_stmt->execute();
			}

		}
	}
	else if($_POST['action'] == "addInfoPage") {
		$stmt = $db->prepare('INSERT into info_page(event_ID, sequential_ID, nav, icon) values (:id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from info_page where event_ID=:id) as temp), "", "")');
		$stmt->bindValue(":id",$event_id);
		$stmt->execute();

		$info_page_id = $db->lastInsertId();

		$stmt = $db->prepare('INSERT into info_page_sections(info_page_ID, sequential_ID, header, content) values (:id, 1, "", "")');
		$stmt->bindValue(":id",$info_page_id);
		$stmt->execute();
	}
	else if($_POST['action'] == "addSection") {
		$get_info_page_stmt = $db->prepare("SELECT (ID) FROM info_page where event_ID=:id and sequential_ID=:sequence");
		$get_info_page_stmt->bindValue(":id",$event_id);
		$get_info_page_stmt->bindValue(":sequence",$_POST['sequence']);
		$get_info_page_stmt->execute();

		if(!$get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
			echo "Error: Tried to add an information page section to a non-existent info page." . $event_id . " " . $_POST['sequence'];
			die();
		}

		$stmt = $db->prepare('INSERT into info_page_sections(info_page_ID, sequential_ID, header, content) values (:id, (SELECT MAX(temp.sequential_ID)+1 from (select sequential_ID from info_page_sections where info_page_ID=:id) as temp), "", "")');
		$stmt->bindValue(":id",$get_info_page_res["ID"]);
		$stmt->execute();
	}
	else if($_POST['action'] == "removeInfoPage") {
		$stmt = $db->prepare("DELETE from info_page where event_ID=:id and sequential_ID=:sequence");
		$stmt->bindValue(":id",$event_id);
		$stmt->bindValue(":sequence", $_POST['sequence']);
		$stmt->execute();
	}
	else if($_POST['action'] == "removeSection") {
		$get_info_page_stmt = $db->prepare("SELECT (ID) FROM info_page where event_ID=:id and sequential_ID=:sequence");
		$get_info_page_stmt->bindValue(":id",$event_id);
		$get_info_page_stmt->bindValue(":sequence",$_POST['page_sequence']);
		$get_info_page_stmt->execute();

		if(!$get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
			echo "Error: Tried to remove an information page section to a non-existent info page.";
			die();
		}

		$stmt = $db->prepare("DELETE from info_page_sections where info_page_ID=:id and sequential_ID=:sequence and (SELECT COUNT(*) FROM (select ID from info_page_sections where info_page_ID=:id) as temp) > 1");
		$stmt->bindValue(":id",$get_info_page_res["ID"]);
		$stmt->bindValue(":sequence", $_POST['section_sequence']);
		$stmt->execute();
	}
	else if($_POST['action'] == "movePage") {
		if($_POST['direction'] == "down") {
			$get_next_highest_stmt = $db->prepare("SELECT (sequential_ID) FROM info_page where event_ID=:id and sequential_ID>:sequence order by sequential_ID asc limit 1");
			$get_next_highest_stmt->bindValue(":id",$event_id);
			$get_next_highest_stmt->bindValue(":sequence",$_POST['sequence']);
			$get_next_highest_stmt->execute();

			if(!$get_next_highest_res = $get_next_highest_stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "Error: Tried to move lower than possible.";
				die();
			}

			$update_stmt = $db->prepare("UPDATE info_page as a inner join info_page as b on a.ID <> b.ID set a.sequential_ID = b.sequential_ID where a.event_ID = :id and b.event_ID=:id and a.sequential_ID in (:sequence,:sequence_next) and b.sequential_ID in (:sequence,:sequence_next)");
			$update_stmt->bindValue(":id",$event_id);
			$update_stmt->bindValue(":sequence",$_POST['sequence']);
			$update_stmt->bindValue(":sequence_next",$get_next_highest_res["sequential_ID"]);
			$update_stmt->execute();
		}
		else {
			$get_next_highest_stmt = $db->prepare("SELECT (sequential_ID) FROM info_page where event_ID=:id and sequential_ID<:sequence order by sequential_ID desc limit 1");
			$get_next_highest_stmt->bindValue(":id",$event_id);
			$get_next_highest_stmt->bindValue(":sequence",$_POST['sequence']);
			$get_next_highest_stmt->execute();

			if(!$get_next_highest_res = $get_next_highest_stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "Error: Tried to move higher than possible.";
				die();
			}

			$update_stmt = $db->prepare("UPDATE info_page as a inner join info_page as b on a.ID <> b.ID set a.sequential_ID = b.sequential_ID where a.event_ID = :id and b.event_ID=:id and a.sequential_ID in (:sequence,:sequence_next) and b.sequential_ID in (:sequence,:sequence_next)");
			$update_stmt->bindValue(":id",$event_id);
			$update_stmt->bindValue(":sequence",$_POST['sequence']);
			$update_stmt->bindValue(":sequence_next",$get_next_highest_res["sequential_ID"]);
			$update_stmt->execute();
		}
	}
	else if($_POST['action'] == "moveSection") {
		$get_info_page_stmt = $db->prepare("SELECT (ID) FROM info_page where event_ID=:id and sequential_ID=:sequence");
		$get_info_page_stmt->bindValue(":id",$event_id);
		$get_info_page_stmt->bindValue(":sequence",$_POST['page_sequence']);
		$get_info_page_stmt->execute();

		if(!$get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
			echo "Error: Tried to move an information page section of a non-existent info page.";
			die();
		}

		if($_POST['direction'] == "down") {
			$get_next_highest_stmt = $db->prepare("SELECT (sequential_ID) FROM info_page_sections where info_page_ID=:id and sequential_ID>:sequence order by sequential_ID asc limit 1");
			$get_next_highest_stmt->bindValue(":id",$get_info_page_res["ID"]);
			$get_next_highest_stmt->bindValue(":sequence",$_POST['section_sequence']);
			$get_next_highest_stmt->execute();

			if(!$get_next_highest_res = $get_next_highest_stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "Error: Tried to move lower than possible.";
				die();
			}

			$update_stmt = $db->prepare("UPDATE info_page_sections as a inner join info_page_sections as b on a.ID <> b.ID set a.sequential_ID = b.sequential_ID where a.info_page_ID = :id and b.info_page_ID=:id and a.sequential_ID in (:sequence,:sequence_next) and b.sequential_ID in (:sequence,:sequence_next)");
			$update_stmt->bindValue(":id",$get_info_page_res["ID"]);
			$update_stmt->bindValue(":sequence",$_POST['section_sequence']);
			$update_stmt->bindValue(":sequence_next",$get_next_highest_res["sequential_ID"]);
			$update_stmt->execute();
		}
		else {
			$get_next_highest_stmt = $db->prepare("SELECT (sequential_ID) FROM info_page_sections where info_page_ID=:id and sequential_ID<:sequence order by sequential_ID desc limit 1");
			$get_next_highest_stmt->bindValue(":id",$get_info_page_res["ID"]);
			$get_next_highest_stmt->bindValue(":sequence",$_POST['section_sequence']);
			$get_next_highest_stmt->execute();

			if(!$get_next_highest_res = $get_next_highest_stmt->fetch(PDO::FETCH_ASSOC)) {
				echo "Error: Tried to move higher than possible.";
				die();
			}

			$update_stmt = $db->prepare("UPDATE info_page_sections as a inner join info_page_sections as b on a.ID <> b.ID set a.sequential_ID = b.sequential_ID where a.info_page_ID = :id and b.info_page_ID=:id and a.sequential_ID in (:sequence,:sequence_next) and b.sequential_ID in (:sequence,:sequence_next)");
			$update_stmt->bindValue(":id",$get_info_page_res["ID"]);
			$update_stmt->bindValue(":sequence",$_POST['section_sequence']);
			$update_stmt->bindValue(":sequence_next",$get_next_highest_res["sequential_ID"]);
			$update_stmt->execute();
		}
	}

	header("Location: information-page.php?id=".$_POST['id']);
	die();

	 
        
        

}

include("../templates/check-event-exists.php");
 $get_event_stmt = $db->prepare("SELECT admin from event where ID=:id");
        $get_event_stmt->bindValue(":id", $_GET["id"]);

        $get_event_stmt->execute();



        $get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);




        if(count($get_event_res) != 1) {

                die();

        }



        $get_event_res = $get_event_res[0];
        if(!is_null($get_event_res['admin']) && (!isset($_SESSION["username"])||$get_event_res['admin']!=$_SESSION['username'])){
                header("Location: https://lightsys.org/");
				die();
        }

        
?>

<html>
	<body>
		<?php include("../templates/left-nav.php"); ?>

		<style>
			#information-pages {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Information Pages</h1>
			<form id="updateForm"  method="post">
				<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
				<input type = "hidden" name="action" value="updateAll">
				<div id="informationCards">
					<?php 
					$get_info_page_stmt = $db->prepare("SELECT * FROM info_page where event_ID=:id order by sequential_ID asc");
					$get_info_page_stmt->bindValue(":id",$event_id);
					$get_info_page_stmt->execute();

					while($get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {

						echo '<div class="card">';
						echo '<div class="btn" onclick="deletePage('.$get_info_page_res["sequential_ID"].')">X</div>';
						echo '<div class="btn" onclick="movePage('.$get_info_page_res["sequential_ID"].',\'up\')">Up</div>';
							echo '<div class="btn" onclick="movePage('.$get_info_page_res["sequential_ID"].',\'down\')">Down</div>';
						echo '<div class="input">Navigation Name: <input type="text" name="name[' . $get_info_page_res["sequential_ID"] . ']" value="'.$get_info_page_res["nav"].'"></div>';
						echo '<div class="input">Icon: <input type="text" name="icon[' . $get_info_page_res["sequential_ID"] . ']" value="'.$get_info_page_res["icon"].'"></div>';

						$get_sections_stmt = $db->prepare("SELECT * FROM info_page_sections where info_page_ID=:id order by sequential_ID asc");
						$get_sections_stmt->bindValue(":id",$get_info_page_res["ID"]);
						$get_sections_stmt->execute();

						while($get_section_res = $get_sections_stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="section">';
							echo '<div class="btn" onclick="deleteSection('.$get_info_page_res["sequential_ID"].', '.$get_section_res["sequential_ID"].')">X</div>';
							echo '<div class="btn" onclick="moveSection('.$get_info_page_res["sequential_ID"].', '.$get_section_res["sequential_ID"].',\'up\')">Up</div>';
							echo '<div class="btn" onclick="moveSection('.$get_info_page_res["sequential_ID"].', '.$get_section_res["sequential_ID"].',\'down\')">Down</div>';
							echo '<div class="input">Header: <input type="text" name="header['. $get_info_page_res["sequential_ID"] .'][' . $get_section_res["sequential_ID"] . ']" value="'.$get_section_res["header"].'"></div>';
							echo '<div class="input">Content: <textarea name="content[' . $get_info_page_res["sequential_ID"] . ']['. $get_section_res["sequential_ID"] .']">'.$get_section_res["content"].'</textarea></div>';
							echo '</div>';
						}

						echo '<div class="btn" onclick="addSection('.$get_info_page_res["sequential_ID"].')">+ Add Section</div>';
						echo '</div>';
					}
					?>
				</div>
				<div class="btn" onclick="addPage()">+ Add Information Page</div>
				<div class="btn" id="save" onclick="save()">Save</div>
			</form>
		</section>
		<form id="addInfoPage" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="addInfoPage">
		</form>

		<form id="addInfoPageSection" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="addSection">
			<input type = "hidden" name="sequence" value="">
		</form>

		<form id="deleteInfoPage" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="removeInfoPage">
			<input type = "hidden" name="sequence" value="">
		</form>

		<form id="deleteInfoPageSection" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="removeSection">
			<input type = "hidden" name="page_sequence" value="">
			<input type = "hidden" name="section_sequence" value="">
		</form>

		<form id="moveSection" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="moveSection">
			<input type = "hidden" name="page_sequence" value="">
			<input type = "hidden" name="section_sequence" value="">
			<input type = "hidden" name="direction" value="down">
		</form>

		<form id="movePage" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="movePage">
			<input type = "hidden" name="sequence" value="">
			<input type = "hidden" name="direction" value="down">
		</form>

	</body>

	<script>
		function addPage() {
			$("#addInfoPage").submit();
		}
		function save() {
			$("#updateForm").submit();
		}
		function addSection(sequential_id) {
			$('#addInfoPageSection > input[name="sequence"]').val(sequential_id);
			$("#addInfoPageSection").submit();
		}

		function deletePage(sequential_id) {
			$('#deleteInfoPage > input[name="sequence"]').val(sequential_id);
			$("#deleteInfoPage").submit();
		}

		function deleteSection(page_sequential_id, section_sequential_id) {
			$('#deleteInfoPageSection > input[name="page_sequence"]').val(page_sequential_id);
			$('#deleteInfoPageSection > input[name="section_sequence"]').val(section_sequential_id);
			$("#deleteInfoPageSection").submit();
		}

		function moveSection(page_sequential_id, section_sequential_id, dir) {
			$('#moveSection > input[name="page_sequence"]').val(page_sequential_id);
			$('#moveSection > input[name="section_sequence"]').val(section_sequential_id);
			$('#moveSection > input[name="direction"]').val(dir);
			$("#moveSection").submit();
		}

		function movePage(sequential_id,dir) {
			$('#movePage > input[name="sequence"]').val(sequential_id);
			$('#movePage > input[name="direction"]').val(dir);
			$("#movePage").submit();
		}

	</script>
	<?php include("../templates/head.php"); ?>
</html>
