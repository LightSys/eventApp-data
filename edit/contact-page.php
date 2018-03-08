<?php include("../templates/check-event-exists.php"); ?>

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
			var html = '<div class="card"><div class="input">Header: <input type="text" name="header' + counter + '"></div>'
						+ '<div class="input">Content: <textarea name="content' + counter + '"></textarea></div>'
						+ '<div class="input">Contacts: <div id="contacts' + counter + '"></div><br><br>'
						+ '<div class="btn" onclick="addContact(' + counter + ')">Add Contact</div></div>';
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
