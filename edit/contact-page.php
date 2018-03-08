<?php include("../templates/check-event-exists.php"); ?>
<?php
	// FIXME: This is not functioning.

	include("../connection.php");
	if( isset($_POST['header'])) {
		foreach($_POST['header'] as $key => $header) {
			if (!($stmt = $db->prepare("INSERT into contact_page_sections(event_ID, header, content) VALUES (:event_ID, :header, :content)"))) {
				die(0);
			}
			
			$event_ID = $_GET["id"];
			$content = $_POST["content"][$key];
			
			if (!($stmt->bindValue(':event_ID', $event_ID))) {
				die(1);
			}
			if (!($stmt->bindValue(':header', $header))) {
				die(2);
			}
			if (!($stmt->bindValue(':content', $content))) {
				die(3);
			}
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
				<div id="sectionCards">
				</div>
				<div class="btn" onclick="addSection()">+ Add Contact Page Section</div>
				<div class="btn" id="save">Save</div>
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
			contactCounters[counter] = 0;
			var html = '<div class="card"><div class="input">Header: <input type="text" name="header[]"></div>'
						+ '<div class="input">Content: <textarea name="content[]"></textarea></div>'
						+ '<div class="input">Contacts: <div id="contacts[]"></div><br><br>'
						+ '<div class="btn" onclick="addContact([])">Add Contact</div></div>';
			addFields(html, 'sectionCards');
			counter++;
		}

		function addContact(num) {
			var html = '<select id="contact' + contactCounters[num] + '"><option>contact name</option></select>';
			addFields(html, "contacts" + num);
			contactCounters[num]++;
		}
	</script>
</html>
