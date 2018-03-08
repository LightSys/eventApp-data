<?php
include("../connection.php");
include("../helper.php");

$event_id = getEventId();

if(isset($_POST['action'])) {
	if($_POST['action'] == "updateAll") {
		$stmt = $db->prepare("UPDATE info_page set name=:name, icon=:icon where event_ID=:id and sequential_ID=:sequence");
		$stmt->bindValue(":id",$event_id);

		foreach ($POST['name'] as $key => $value) {
			$stmt->bindValue(":sequence",$key);
			$stmt->bindValue(":name",$value);
			$stmt->bindValue(":icon",$POST['icon'][$key]);
			$stmt->execute();
		}
	}
	else if($_POST['action'] == "addInfoPage") {
		$stmt = $db->prepare('INSERT into info_page(event_ID, sequential_ID, nav, icon) values (:id, (SELECT ISNULL(MAX(sequential_ID),0)+1 from info_page where event_ID=:id), "", "")');
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
			echo "Error: Tried to add an information page section to a non-existent info page.";
			die();
		}

		$stmt = $db->prepare('INSERT into info_page_sections(info_page_ID, sequential_ID, header, content) values (:id, (SELECT ISNULL(MAX(sequential_ID),0)+1 from info_page_sections where info_page_ID=:id),"","")');
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

		$stmt = $db->prepare("DELETE from info_page_sections where info_page_ID=:id and sequential_ID=:sequence");
		$stmt->bindValue(":id",$get_info_page_res["ID"]);
		$stmt->bindValue(":sequence", $_POST['section_sequence']);
		$stmt->execute();
	}

	echo "<meta http-equiv='refresh' content='0'>";
	die();
}

$get_info_page_stmt = $db->prepare("SELECT * FROM info_page where event_ID=:id");
$get_info_page_stmt->bindValue(":id",$event_id);
$get_info_page_stmt->execute();

function createCard($name, $icon, $counter, $sections) {
	
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
			<form id="updateForm" method="post">
				<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
				<input type = "hidden" name="action" value="updateAll">
				<div id="informationCards">
					<?php 
					$index = 0; 
					while($get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {

						echo '<div class="card">';
						echo '<div class="input">Navigation Name: <input type="text" name="name[' . $index . ']" value="'.$get_info_page_res["nav"].'"></div>';
						echo '<div class="input">Icon: <input type="text" name="icon[' . $index . ']" value="'.$get_info_page_res["icon"].'">';

						$get_sections_stmt = $db->prepare("SELECT * FROM info_page_sections where info_page_ID=:id order by sequential_ID asc");
						$get_sections_stmt->bindValue(":id",$get_info_page_res["ID"]);
						$get_sections_stmt->execute();

						while($get_section_res = $get_sections_stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="section">';
							echo '<div class="input">Header: <input type="text" name="header['. $index .'][' . $get_section_res["sequential_ID"] . ']" value="'.$get_section_res["header"].'"></div>';
							echo '<div class="input">Content: <textarea name="content[' . $index . ']['. $get_section_res["sequential_ID"] .']">'.$get_section_res["content"].'</textarea></div>';
							echo '</div>';
						}

						echo '</div>';

						$index++; 
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

	</body>

	<script>
		function addPage() {
			$("#addInfoPage").submit();
		}
		function save() {
			$("#updateForm").submit();
		}
		function addSection(sequential_id) {
			$("#addInfoPageSection > #sequence").value(sequential_ID);
			$("#addInfoPageSection").submit();
		}
	</script>
	<?php include("../templates/head.php"); ?>
</html>
