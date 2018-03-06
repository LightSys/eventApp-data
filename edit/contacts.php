<html>
	
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
			<form id="form" method="post">
				<div id="contactCards">
				</div>
				<div class="btn" onclick="addContact()">+ Add Contact</div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>

	</body>
	<?php include("../templates/head.php"); ?>
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

