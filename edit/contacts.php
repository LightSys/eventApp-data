<?php include("../templates/check-event-exists.php"); ?>
<?php	
	include("../connection.php");
	include("../helper.php");

	$event_id = getEventId();
    if( isset($_POST['action']) )
	{
		if($_POST['action'] == 'addContact') {
			$stmt = $db->prepare("INSERT into contacts(event_id) values(:event_id)");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();
			
			header("Location: ".$_SERVER['REQUEST_URI']."?id=".$_POST['id']);
			die();
		}		
		else if ($_POST['action'] == 'updateContact') {		
			$stmt = $db->prepare("UPDATE contacts set name = :name, address = :address, phone = :phone, 
				event_ID = :event_id where event_ID=:id and sequential_ID=:sequence");
		
			foreach($_POST['name'] as $key => $name) {		
				$address = $_POST['address'][$key];
				$phone = $_POST['phone'][$key];
				
				$stmt->bindValue(":sequence",$key);
				$stmt->bindValue(':name', $name);
				$stmt->bindValue(':address', $address);
				$stmt->bindValue(':phone', $phone);
				$stmt->bindValue(':event_id', $event_id);
				$stmt->execute();
			}
		}
		else if ($_POST['action'] == 'deleteContact') {
			
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
				<input type="hidden" name="action" value = "updateContact">
				<div id="contactCards">
				<?php			
					$event_id = $_GET["id"];
					$get_info_page_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id");
					$get_info_page_stmt->bindValue(":id",$event_id);
					$get_info_page_stmt->execute();

					while($get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="card"><div class="input">Name: <input type="text" name="name[]" value = \''.$get_info_page_res["name"].'\'></div>';
						echo '<div class="input">Address: <input type="text" name="address[]" value = \''.$get_info_page_res["address"].'\'></div>';
						echo '<div class="input">Phone: <input type="text" name="phone[]" value = \''.$get_info_page_res["phone"].'\'></div>';
					}
				?>
				</div></br>
				<div class="btn" onclick="addContact()">+ Add Contact</div>
				<input type="submit" value="Submit">
			</form>
		</section>
		<form id = "addContact" action = "contacts.php" method="post">	
			<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
			<input type="hidden" name="action" value = "addContact">
		</form>

	</body>

	<script>		
		function addContact() {
			//var html = '<div class="card"><div class="input">Name: <input type="text" name="name[]"></div>'
			//			+ '<div class="input">Address: <input type="text" name="address[]"></div>'
			//			+ '<div class="input">Phone: <input type="text" name="phone[]"></div>';
			//addFields(html, 'contactCards');
			$("#addContact").submit();
		}
	</script>
</html>

