<html>
	<?php include("../templates/head.php"); ?>
	
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
			<form id="informationPagesForm">
				<div id="informationCards">
				</div>
				<div class="btn" onclick="addPage()">+ Add Information Page</div>
				<input type="submit" value="Submit">
			</form>
		</section>

	</body>

	<script>
		var counter = 0;
		
		$(document).ready(function() {
			addPage();
		});

		function addPage() {
			var html = '<div class="contact-card">Navigation Name: <input type="text" name="name' + counter + '"><br>'
						+ 'Icon: <input type="file" name="logo' + counter + '"><br>'
						+ 'Title: <input type="text" name="title' + counter + '"><br>'
						+ 'Information: <textarea name="information' + counter + '"></textarea><br></div>';
			addFields(html, 'informationCards');
			counter++;
		}
	</script>
</html>
