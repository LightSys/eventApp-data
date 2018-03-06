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
			<form id="contactPageSectionForm">
				<div id="sectionCards">
				</div>
				<div class="btn" onclick="addSection()">+ Add Contact Page Section</div>
				<input type="submit" value="Submit">
			</form>
		</section>

	</body>

	<script>
		var counter = 0;
		
		$(document).ready(function() {
			addSection();
		});

		function addSection() {
			var html = '<div class="contact-card">Header: <input type="text" name="header' + counter + '"><br>'
						+ 'Content: <textarea name="content' + counter + '"></textarea><br></div>';
			addFields(html, 'sectionCards');
			counter++;
		}
	</script>
</html>
