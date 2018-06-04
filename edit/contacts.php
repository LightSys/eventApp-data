<?php   session_start();	
	include("../connection.php");
	include("../helper.php");
	secure();	
	


	$event_id = getEventId();
    if( isset($_POST['action']) )
	{
		if($_POST['action'] == 'addContact') {
			//add a blank contact record
			$stmt = $db->prepare("INSERT into contacts(event_ID, sequential_ID) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from contacts where event_ID=:event_id) as temp))");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();
		}		
	
		else if ($_POST['action'] == 'updateContact') {	
		
			//update all contact records in the event 
			$stmt = $db->prepare("UPDATE contacts set name = :name, address = :address, phone = :phone 
				where event_ID=:event_id and sequential_ID=:sequence");
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
			//delete contact record
			$stmt = $db->prepare("DELETE from contacts where event_ID=:id and sequential_ID=:sequence");
			$stmt->bindValue(":id",$event_id);
			$stmt->bindValue(":sequence", $_POST['sequence']);
			$stmt->execute();
		}
		
		// Redirect to the original address with parameters intact since they are dropped on form submit.
		// The records just added or updated will be added to the page
		header("Location: contacts.php?id=".$_POST['id']);
		die();
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
			<form id="contactForm" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updateContact">
				<div id="contactCards">
				<?php			
					$id = $_GET["id"];
					$get_contact_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id order by sequential_ID asc");
					$get_contact_stmt->bindValue(":id",$event_id);
					$get_contact_stmt->execute();
					
					//populate the form with the event contacts 
					while($get_contact_res = $get_contact_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="card">';
						echo '<div class="btn" onclick="deleteContact('.$get_contact_res["sequential_ID"].')">X</div>';
						echo '<div class="input">Name: <input type="text" name="name['.$get_contact_res["sequential_ID"].']" 
							value = \''.$get_contact_res["name"].'\'></div>';
						echo '<div class="input">Address: <input type="text" name="address['.$get_contact_res["sequential_ID"].']" 
							value = \''.$get_contact_res["address"].'\'></div>';
						echo '<div class="input">Phone: <input type="text" name="phone['.$get_contact_res["sequential_ID"].']" 
							value = \''.$get_contact_res["phone"].'\'></div></div>';
					}
				?>
				</div>
				<div class="btn" onclick="addContact()">+ Add Contact</div>
				<div class="btn" id="save" onclick="save()">Save</div>
			</form>
		</section>
		<!--Form to be submitted when the add contact button is clicked.
			This allows the postinng of data-->
		<form id = "addContact" method="post">	
			<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
			<input type="hidden" name="action" value = "addContact">
		</form>
		
		<!--Form to be submitted when the delete contact button is clicked-->
		<form id="deleteContact" method="post">
			<input type = "hidden" name="id" value="<?php echo $_GET['id']; ?>">
			<input type = "hidden" name="action" value="deleteContact">
			<input type = "hidden" name="sequence" value="">
		</form>

	</body>

	<script>		
		function addContact() {
			$("#addContact").submit();
		}
		
		function save() {
			$("#contactForm").submit();
		}

		function deleteContact(sequential_id) {
			$('#deleteContact > input[name="sequence"]').val(sequential_id);
			$("#deleteContact").submit();
		}
	</script>
</html>

