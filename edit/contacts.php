<html>
	<?php include("../templates/head.php"); ?>
	<body>
		<?php include("../templates/left-nav.php"); ?>
		<style>
			#contacts {
				background-color: grey;
				color: white;
			}
		</style>

		<section id="main">
			<h1>Contacts</h1>
			<form id="contactForm">
				<div id="contactCards">
				</div>
				<div class="btn" onclick="addContact()">+ Add Contact</div>
				<input type="submit" value="Submit">
			</form>
		</section>

	</body>

	<script>
		var counter = 0;
		
		$(document).ready(function() {
			addContact();
		});

		function addContact() {
			var html = '<div class="card"><div class="input">Name: <input type="text" name="name' + counter + '"></div>'
						+ '<div class="input">Address: <input type="text" name="address' + counter + '"></div>'
						+ '<div class="input">Phone: <input type="text" name="phone' + counter + '"></div>';
			addFields(html, 'contactCards');
			counter++;
		}
	</script>
</html>

