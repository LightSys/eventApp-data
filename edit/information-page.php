<html>
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
			<form id="form" method="post">
				<div id="informationCards">
				</div>
				<div class="btn" onclick="addPage()">+ Add Information Page</div>
				<div class="btn" id="save">Save</div>
			</form>
		</section>

	</body>
	<?php include("../templates/head.php"); ?>
	<script>
		var counter = 0;
		
		$(document).ready(function() {
			addPage();
		});

		function addPage() {
			var html = '<div class="card"><div class="input">Navigation Name: <input type="text" name="name' + counter + '"></div>'
						+ '<div class="input">Icon: <input type="file" name="logo' + counter + '"></div>'
						+ '<div class="input">Title: <input type="text" name="title' + counter + '"></div>'
						+ '<div class="input">Information: <textarea name="information' + counter + '"></textarea></div></div>';
			addFields(html, 'informationCards');
			counter++;
		}
	</script>
</html>
