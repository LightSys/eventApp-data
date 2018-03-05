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

				<div id="contactForm">
					<!-- Name: <input type="text" name="name"><br>
					Address: <input type="text" name="address"><br>
					Phone: <input type="text" name="phone"><br> -->
				</div>

				<div class="btn" onclick="addContact()">+ Add Contact</div>
				<input type="submit" value="Submit">
			</form>
		</section>

	</body>

	<script>
		var contactCounter = 0;
		
		$(document).ready(function() {
			addContact();
		});

		function addContact() {
			var html = '<div class="contact-card">Name: <input type="text" name="name' + contactCounter + '"><br>'
						+ 'Address: <input type="text" name="address' + contactCounter + '"><br>'
						+ 'Phone: <input type="text" name="phone' + contactCounter + '"><br></div>'
			addFields(html, 'contactForm');
			contactCounter++;
		}
	</script>
</html>

