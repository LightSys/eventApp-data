<?php	
	include("../connection.php");
	include("../helper.php");

	$event_id = getEventId();
    if( isset($_POST['action']) )
	{
		if($_POST['action'] == 'addContact') {
			$stmt = $db->prepare("INSERT into contacts(event_ID, sequential_ID) values(:event_id, (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from info_page where event_ID=:event_id) as temp))");
			$stmt->bindValue(':event_id', $event_id);
			$stmt->execute();
		}		
		else if ($_POST['action'] == 'updateContact') {		
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
			
		}
		header("Location: ".$_SERVER['REQUEST_URI']."?id=".$_POST['id']);
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
			<form id="contactForm" action = "contacts.php" method="post">
				<input type="hidden" name="id" value = "<?php echo $_GET["id"]?>">
				<input type="hidden" name="action" value = "updateContact">
				<div id="contactCards">
				<?php			
					$id = $_GET["id"];
					$get_info_page_stmt = $db->prepare("SELECT * FROM contacts where event_ID=:id order by sequential_ID asc");
					$get_info_page_stmt->bindValue(":id",$event_id);
					$get_info_page_stmt->execute();

					while($get_info_page_res = $get_info_page_stmt->fetch(PDO::FETCH_ASSOC)) {
						echo '<div class="card"><div class="input">Name: <input type="text" name="name['.$get_info_page_res["sequential_ID"].']" 
							value = \''.$get_info_page_res["name"].'\'></div>';
						echo '<div class="input">Address: <input type="text" name="address['.$get_info_page_res["sequential_ID"].']" 
							value = \''.$get_info_page_res["address"].'\'></div>';
						echo '<div class="input">Phone: <input type="text" name="phone['.$get_info_page_res["sequential_ID"].']" 
							value = \''.$get_info_page_res["phone"].'\'></div></div>';
					}
				?>
				</div>
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

