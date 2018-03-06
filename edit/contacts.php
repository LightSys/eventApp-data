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
			var html = '<div class="contact-card">Name: <input type="text" name="name' + counter + '"><br>'
						+ 'Address: <input type="text" name="address' + counter + '"><br>'
						+ 'Phone: <input type="text" name="phone' + counter + '"><br></div>';
			addFields(html, 'contactCards');
			counter++;
		}
	</script>
</html>

