<?php   
	session_start();

	include("../global.php");

        secure();

	$event_id = getEventId();
	if( isset($_POST['action'])) {

		inc_config_ver();
		
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
					$contact_str .= $name;
				}
				elseif($name=="remove"){}
				else {
					$contact_str .= ":";
					$contact_str .= $name;
				}
			}

			$stmt = $db->prepare("UPDATE contact_page_sections set header=:header, content=:content where event_ID=:id order by sequential_ID desc limit 1");
			$stmt->bindValue(':id', $event_id);
			$stmt->bindValue(':header', $_POST["contacts_header"]);
			$stmt->bindValue(':content', $contact_str);
			$stmt->execute();
		}

		header("Location: contact-page.php?id=" . sanitize_id($_POST['id']));
		die();
	}

	include("../templates/check-event-exists.php"); 
	 

?>

<html>
        <?php include("../templates/head.php"); ?>
	
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
			<p> This is to create a page which has some contacts (including phone numbers) and explianation on when they can be contacted. A prime example is emergency contacts.</p>
			<form id="form" method="post">
				<input type="hidden" name="id" value = "<?php echo sanitize_id($_GET["id"]); ?>">
				<input type="hidden" name="action" value = "updateSection">

				<div id="contactCards">
					<?php			
						$id = sanitize_id($_GET["id"]);
						$get_sections_stmt = $db->prepare("SELECT * FROM contact_page_sections where event_ID=:id and ID != (SELECT MAX(ID) FROM contact_page_sections where event_ID=:id) order by sequential_ID asc");
						$get_sections_stmt->bindValue(":id",$event_id);
						$get_sections_stmt->execute();

						while($get_sections_res = $get_sections_stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="card">'; 
							echo '<div class="input">Page Header: <input type="text" name="header[' . attrstr($get_sections_res["sequential_ID"]) . ']" value="' . attrstr($get_sections_res["header"]) . '"></div>';
							echo '<div class="input">Content: <textarea name="content[' . attrstr($get_sections_res["sequential_ID"]) . ']">' . htmlstr($get_sections_res["content"]) . '</textarea></div>';
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
						echo '<div class="input">Contact Header: <input type="text" name="contacts_header" value="' . attrstr($get_last_section_res["header"]) . '"></div>';
						echo '<div class="input">Contacts: <div id="contact_list">'; 

						foreach($contacts as $contact) {

							echo '<select name="contact[]" autocomplete="off">';
							
							$get_contacts_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id");
							$get_contacts_stmt->bindValue(":id", $event_id);
							$get_contacts_stmt->execute();

							echo '<option value="remove">Remove</option>';

							while($get_contacts_res = $get_contacts_stmt->fetch(PDO::FETCH_ASSOC)) {
								if($get_contacts_res['ID'] == $contact) {
									echo '<option value="' . attrstr($get_contacts_res["ID"]) .'" selected>' . htmlstr($get_contacts_res['name']) . '</option>';
								} else {
									echo '<option value="' . attrstr($get_contacts_res["ID"]) .'">' . htmlstr($get_contacts_res['name']) . '</option>';
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

                                <p>Please save before navigating to a new page.</p>

				<div class="btn" id="save" onclick="save()">Save</div>
			</form>

			<form id = "addSection"  method="post">	
				<input type="hidden" name="id" value = "<?php echo sanitize_id($_GET["id"]); ?>">
				<input type="hidden" name="action" value = "addSection">
			</form>

		</section>

	</body>

	<script>

		function addSection() {
		
		}

		function save() {
			document.forms["form"]["action"].value="updateSection";
			$("#form").submit();
		}

		function addContact() {
			var html = '<select name="contact[]" autocomplete="off"><?php
					
					$get_contacts_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id");
					$get_contacts_stmt->bindValue(":id", $event_id);
					$get_contacts_stmt->execute();

					while($get_contacts_res = $get_contacts_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<option value="'. attrstr($get_contacts_res['ID']) .'" selected>' . htmlstr($get_contacts_res['name']) . '</option>';
					}
					echo '<option value="remove">Remove</option>';

					?></select>';
			addFields(html, "contact_list");
                        document.forms["form"]["action"].value="updateSection";
                        $("#form").submit();

		}
	</script>
</html>
