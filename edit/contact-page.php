<?php   session_start();
	include("../helper.php");
	include("../connection.php");
        secure();


	$event_id = getEventId();
	if( isset($_POST['action'])) {
		
		if($_POST['action'] =="insertSection") {
		}
		else if($_POST['action'] =="deleteSection") {
		}
		else if($_POST['action'] =="updateSection") {
			if(isset($_POST['header'])) {
				$stmt = $db->prepare("UPDATE contact_page_sections set header=:header, content=:content where event_ID=:id and sequential_ID=:sequence");
				$stmt->bindValue(':id', $event_id);

				foreach($_POST['header'] as $key => $header) {
					$content = $_POST["content"][$key];
					
					$stmt->bindValue(':sequence', $key);
					$stmt->bindValue(':header', $header);
					$stmt->bindValue(':content', $content);
					$stmt->execute();
				}
			}

			$contact_str="";
			$first = true;
			foreach($_POST['contact'] as $key => $name) {
				if($first) {
					$first = false;
				}
				else {
					$contact_str .= ":";
				}
				$contact_str .= $name;
			}

			$stmt = $db->prepare("UPDATE contact_page_sections set header=:header, content=:content where event_ID=:id order by sequential_ID desc limit 1");
			$stmt->bindValue(':id', $event_id);
			$stmt->bindValue(':header', $_POST["contacts_header"]);
			$stmt->bindValue(':content', $contact_str);
			$stmt->execute();
		}

		header("Location: contact-page.php?id=".$_POST['id']);
		die();
	}

	include("../templates/check-event-exists.php"); 
	 

?>

<html>
	
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#contact-page-sections {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Contact Page Sections</h1>
			<form id="form" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updateSection">

				<div id="contactCards">
					<?php			
						$id = $_GET["id"];
						$get_sections_stmt = $db->prepare("SELECT * FROM contact_page_sections where event_ID=:id and ID != (SELECT MAX(ID) FROM contact_page_sections where event_ID=:id) order by sequential_ID asc");
						$get_sections_stmt->bindValue(":id",$event_id);
						$get_sections_stmt->execute();

						while($get_sections_res = $get_sections_stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="card">'; 
							echo '<div class="input">Header: <input type="text" name="header['.$get_sections_res["sequential_ID"].']" maxlength="100" value="'.$get_sections_res["header"].'"></div>';
							echo '<div class="input">Content: <textarea name="content['.$get_sections_res["sequential_ID"].']">'.$get_sections_res["content"].'</textarea></div>';
							echo '</div>';
						}

						$get_last_section_stmt = $db->prepare("SELECT * FROM contact_page_sections where event_ID=:id order by sequential_ID desc limit 1");
						$get_last_section_stmt->bindValue(":id",$event_id);
						$get_last_section_stmt->execute();
						$get_last_section_res = $get_last_section_stmt->fetch(PDO::FETCH_ASSOC);

						$contacts = explode(":",$get_last_section_res["content"]);
						if($get_last_section_res["content"] == "") {
							$contacts = array();
						}

						echo '<div class="card">';
						echo '<div class="input">Header: <input type="text" name="contacts_header" maxlength="100"value="'.$get_last_section_res["header"].'"></div>';
						echo '<div class="input">Contacts: <div id="contact_list">'; 

						foreach($contacts as $contact) {

							echo '<select name="contact[]" autocomplete="off">';
							
							$get_contacts_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id");
							$get_contacts_stmt->bindValue(":id", $event_id);
							$get_contacts_stmt->execute();

							echo '<option value="remove">Remove</option>';

							while($get_contacts_res = $get_contacts_stmt->fetch(PDO::FETCH_ASSOC)) {
								if($get_contacts_res['name'] == $contact) {
									echo '<option value='. $get_contacts_res['sequential_ID'] .' selected>' . $get_contacts_res['name'] . '</option>';
								} else {
									echo '<option>' . $get_contacts_res['name'] . '</option>';
								}
							}

							echo '</select>';
						}

						echo'</div>';
						echo '<div class="btn" onclick="addContact()">Add Contact</div></div>';
						echo '</div>';
					?>
				</div>
				<!-- This is disabled for now. Once the app supports multiple sections, this should be added. -->
				<!-- <div class="btn" onclick="addSection()">+ Add Contact Page Section</div> -->
				<div class="btn" id="save">Save</div>
			</form>

			<form id = "addSection"  method="post">	
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "addSection">
			</form>

		</section>

	</body>

	<?php include("../templates/head.php"); ?>
	<script>

		function addSection() {
		
		}

		function addContact() {
			var html = '<select name="contact[]" autocomplete="off"><?php
					
					$get_contacts_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id");
					$get_contacts_stmt->bindValue(":id", $event_id);
					$get_contacts_stmt->execute();

					echo '<option value="remove">Remove</option>';
					while($get_contacts_res = $get_contacts_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<option value="'. $get_contacts_res['name'] .'">' . $get_contacts_res['name'] . '</option>';
					}
					?></select>';
			addFields(html, "contact_list");
		}
	</script>
</html>
