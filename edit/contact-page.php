<?php 
	include("../templates/check-event-exists.php"); 
	include("../helper.php");

	$event_id = getEventId();
	
	include("../connection.php");
	if( isset($_POST['action'])) {
		
		if(isset($_POST['insertSection'])) {
			
		}
		else if(isset($_POST['insertContact'])) {
			
		}
		else if(isset($_POST['updateSection'])) {
			
			$stmt = $db->prepare("update contact_page_sections(event_ID, header, content) VALUES (:event_ID, :header, :content)");
			foreach($_POST['header'] as $key => $header) {
				
				$event_ID = $_GET["id"];
				$content = $_POST["content"][$key];
				
				$stmt->bindValue(':event_ID', $event_ID);
				$stmt->bindValue(':header', $header);
				$stmt->bindValue(':content', $content);
				$stmt->execute();
			}
		}
		else if(isset($_POST['updateSection'])) {
			
		}
	}
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
				<div id="contactCards">
					<?php			
						$id = $_GET["id"];
						$get_info_page_stmt = $db->prepare("SELECT * FROM contact_page_sections where event_ID=:id order by sequential_ID asc");
						$get_info_page_stmt->bindValue(":id",$event_id);
						$get_info_page_stmt->execute();

						while($get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
							echo '<div class="card"><div class="input">Name: <input type="text" name="name['.$get_info_page_res["sequential_ID"].']" 
								value = \''.$get_info_page_res["name"].'\'></div>';
							echo '<div class="input">Address: <input type="text" name="address['.$get_info_page_res["sequential_ID"].']" 
								value = \''.$get_info_page_res["address"].'\'></div>';
							echo '<div class="input">Phone: <input type="text" name="phone['.$get_info_page_res["sequential_ID"].']" 
								value = \''.$get_info_page_res["phone"].'\'></div></div>';
								
							echo '<div class="card"><div class="input">Header: <input type="text" name="header['.$get_info_page_res["sequential_ID"].']"></div>';
							echo '<div class="input">Content: <textarea name="content['.$get_info_page_res["sequential_ID"].']"></textarea></div>';
							echo '<div class="input">Contacts: <div id="contacts['.$get_info_page_res["sequential_ID"].']"></div><br><br>';
							echo '<div class="btn" onclick="addContact()">Add Contact</div></div>';
						}
					?>
				</div>
				<div class="btn" onclick="addSection()">+ Add Contact Page Section</div>
				<div class="btn" id="save">Save</div>
			</form>
			<form id = "addSection" action = "contact-page.php" method="post">	
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "addSection">
			</form>
			<form id = "addContact" action = "contact-page.php" method="post">	
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "addContact">
			</form>
		</section>

	</body>

	<?php include("../templates/head.php"); ?>
	<script>
		var counter = 0;
		var contactCounters = [];
		
		$(document).ready(function() {
			addSection();
		});

		function addSection() {
			//contactCounters[counter] = 0;
			//var html = '<div class="card"><div class="input">Header: <input type="text" name="header[]"></div>'
				//		+ '<div class="input">Content: <textarea name="content[]"></textarea></div>'
				//		+ '<div class="input">Contacts: <div id="contacts[]"></div><br><br>'
				//		+ '<div class="btn" onclick="addContact([])">Add Contact</div></div>';
			//addFields(html, 'sectionCards');
			//counter++;
		}

		function addContact(num) {
			var html = '<select id="contact' + contactCounters[num] + '"><option>contact name</option></select>';
			addFields(html, "contacts" + num);
			contactCounters[num]++;
		}
	</script>
</html>
