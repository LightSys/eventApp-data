<?php include("../templates/check-event-exists.php"); ?>
<?php	
	include("../connection.php");
    if( isset($_POST['name']) )
	{
		foreach($_POST['name'] as $key => $name) {
			if (!($stmt = $db->prepare("INSERT into contacts (name, address, phone, event_ID) values(:name, :address, :phone, :event_id)"))) {
				die(0);
			}
			
			$address = $_POST['address'][$key];
			$phone = $_POST['phone'][$key];
			$id = "c24343ee-218a-11e8-9e9c-525400bb1e83";
			
			if (!($stmt->bindValue(':name', $name))) {
				die(1);
			}
			if (!($stmt->bindValue(':address', $address))) {
				die(2);
			}
			if (!($stmt->bindValue(':phone', $phone))) {
				die(3);
			}
			if (!($stmt->bindValue(':event_id', $id))) {
				die(4);
			}
			if(!($stmt->execute())) {
				die(5);
			}
		}
	}
?>

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
			<form id="contactForm" action = "contacts.php" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<div id="contactCards">
				</div>
				<div class="btn" onclick="addContact()">+ Add Contact</div>
				<input type="submit" value="Submit">
			</form>
		</section>

	</body>

	<script>
		
		$(document).ready(function() {
			addContact();
		});

		function addContact() {
			var html = '<div class="card"><div class="input">Name: <input type="text" name="name[]"></div>'
						+ '<div class="input">Address: <input type="text" name="address[]"></div>'
						+ '<div class="input">Phone: <input type="text" name="phone[]"></div>';
			addFields(html, 'contactCards');
		}
	</script>
</html>

