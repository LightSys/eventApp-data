<?php  
	session_start();

	include("../global.php");
	
	eventSecure();
	// If we are coming from the events page to create a new event
	if(isset($_POST['action']) && $_POST['action'] == 'newEvent') {
		// create a new event
		$new_event_stmt = $db->prepare("INSERT into event SET ID = UUID()");
		$new_event_stmt->execute();
		// get the id of that event
		$new_event_id_stmt = $db->prepare("SELECT * from event where internal_ID = (select MAX(internal_ID) from event)");

		$new_event_id_stmt->execute();
		$id;
		while($new_event_id = $new_event_id_stmt->fetch(PDO::FETCH_ASSOC)) {
			$id = $new_event_id['ID'];
		}
		// get the internal id
		$get_event_stmt = $db->prepare("SELECT internal_ID FROM event where ID=:id");
		$get_event_stmt->bindParam(":id",$id);
		$get_event_stmt->execute();
		$get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);
		if(count($get_event_res) != 1) {
			die(" Hi i am here error count != 1");
		}
		$get_event_res = $get_event_res[0];
		$internalEventID = $get_event_res["internal_ID"];
		// Create two blank contact page sections
		for($i = 0; $i < 2; $i++) {
			$new_contact_pages_stmt = $db->prepare("INSERT into contact_page_sections SET event_ID = :internalEventID, sequential_ID = (SELECT IFNULL(MAX(temp.sequential_ID),0)+1 from (select sequential_ID from contact_page_sections where event_ID=:internalEventID) as temp)");
			$new_contact_pages_stmt->bindValue('internalEventID',$internalEventID);
			$new_contact_pages_stmt->execute();
		}

		// Hard coded initial values.		
		$stmt = $db->prepare("UPDATE event SET refresh_rate = :refresh, admin = :admin, theme_dark = :themedark, theme_color = :themecolor, contact_nav = :contact_nav, sched_nav = :sched_nav, housing_nav = :housing_nav, prayer_nav = :prayer_nav, notif_nav = :notif_nav, contact_icon = :contact_icon, sched_icon = :sched_icon, housing_icon = :housing_icon, prayer_icon = :prayer_icon, notif_icon = :notif_icon, config_version = :config_ver, notif_version = :notif_ver WHERE internal_ID = :id");
		$stmt->bindValue(':admin', $_SESSION["username"]);
                $stmt->bindValue(':contact_nav', "Contacts");
                $stmt->bindValue(':sched_nav', "Schedule");
                $stmt->bindValue(':housing_nav', "Housing");
                $stmt->bindValue(':prayer_nav', "Prayer Partners");
                $stmt->bindValue(':notif_nav', "Notifications");
                $stmt->bindValue(':contact_icon', "ic_contact");
                $stmt->bindValue(':sched_icon', "ic_schedule");
                $stmt->bindValue(':housing_icon', "ic_house");
                $stmt->bindValue(':prayer_icon', "ic_group");
                $stmt->bindValue(':notif_icon', "ic_bell");
		$stmt->bindValue(':themedark', "#000000");
                $stmt->bindValue(':themecolor', "#0093FF");
		$stmt->bindValue(':id',$internalEventID);
		$stmt->bindValue(':config_ver',1);
		$stmt->bindValue(':notif_ver',1);
		$stmt->bindValue(':refresh',"auto");
		
		$stmt->execute();
		
		// reroute to this page with the new event id
		header("Location: ".full_url($_SERVER)."?id=".sanitize_id($id));
		die();
	}
	secure(full_url($_SERVER));

        $get_event_stmt = $db->prepare("SELECT custom_tz,view_remote,allow_qr_share,logo,name,time_zone,TZcatagory,welcome_message, visible,contact_nav,contact_icon,sched_nav,sched_icon,housing_nav,housing_icon,prayer_nav,prayer_icon,notif_nav,notif_icon FROM event WHERE ID =:id");
	
        $get_event_stmt->bindValue(":id", sanitize_id($_GET["id"]));
	
	$get_event_stmt->execute();
        $get_event_res = $get_event_stmt->fetchAll(PDO::FETCH_ASSOC);
        if(count($get_event_res) != 1) {
                die();
        }
        $get_event_res = $get_event_res[0];
        
    if(isset($_POST['name'])) {

		inc_config_ver();

		$stmt = $db->prepare("UPDATE event SET name = :name, time_zone = :time_zone,custom_tz= :custom, view_remote=:remote, allow_qr_share=:allow_qr_share, TZcatagory=:TZ_catagory, welcome_message = :welcome_message, visible = :visible, logo = :logo, contact_nav= :contact_nav,contact_icon= :contact_icon,sched_nav= :sched_nav,sched_icon= :sched_icon,housing_nav= :housing_nav,housing_icon= :housing_icon,prayer_nav= :prayer_nav,prayer_icon= :prayer_icon,notif_nav= :notif_nav,notif_icon= :notif_icon WHERE id = :id");
		$name = $_POST['name'];
		$timeZone = $_POST['time_zone'];
		$welcomeMessage = $_POST['welcome'];
		$visible = isset($_POST['visible'])?1:0;
		$custom = isset($_POST['custom'])?1:0;
		$remote = isset($_POST['remote'])?1:0;
		$allow_qr_share = isset($_POST['allow_qr_share'])?1:0;
		$id = $_POST["id"];

		$logo=null;
		// If the user specified a logo file
		
		if(isset($_FILES["logo"]["name"]) && strlen($_FILES['logo']['name']) > 0) {
			

			
			//die( "attempted upload");// we got this far
			// The directory to save the file to

			$uploaddir = '../temp/';
			// Get the full path to save the uploaded file to
			$uploadfile = $uploaddir . basename($_FILES['logo']['name']);
			// Try to upload the file
			$imageFileType = strtolower(pathinfo($uploadfile,PATHINFO_EXTENSION));
			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "svg") {
    					die ("Sorry, only SVG, JPG, JPEG, PNG, and GIF files are allowed.");
			}
			if($_FILES['logo']['size'] > 300000) {
				die ("Sorry, only logos below 300KB are allowed.");
			}
			else{
				if(move_uploaded_file($_FILES['logo']['tmp_name'], $uploadfile)) {
					$logo = base64_encode(file_get_contents($uploadfile));
					//die("encoding should be successful"); //failed by this point
					echo "<p>File succesfully uploaded</p>";
				} else {


				die("   did not move file"); //apparently there is a permission failure

					echo "<p>Error uploading file</p>";
				}
			}
		
			// Remove the contents of the temporary directory
			$files = glob($uploaddir); 	// get all file names
			foreach($files as $file) {  // iterate files
				if(is_file($file))
					unlink($file); 		// delete file
			}
		}
				
		$stmt->bindValue(':name', $name);
	        if($_POST["timeCatagory"]!=$get_event_res["TZcatagory"]){
			$stmt->bindValue(':time_zone', '');
                }
		else{
			$stmt->bindValue(':time_zone', $_POST["time_zone"]);
		}
		$stmt->bindValue(':welcome_message', $welcomeMessage);
		$stmt->bindValue(':id', $id);
		$stmt->bindValue(':visible', $visible);
		$stmt->bindValue(':custom', $custom);
		$stmt->bindValue(':remote', $remote);
		$stmt->bindValue(':allow_qr_share', $allow_qr_share);
		if ($_POST["saveLogo"] == "delete"){
			$stmt->bindValue(':logo',  base64_encode(""));
		}
		elseif (!is_null($logo)){
			$stmt->bindValue(':logo', $logo);
		}
		else{
			$stmt->bindValue(':logo',$get_event_res['logo']);	
		}
		$stmt->bindValue(":contact_nav", $_POST["contact_nav"]);
		$stmt->bindValue(":contact_icon", $_POST["contact_icon"]);
		$stmt->bindValue(":sched_nav", $_POST["sched_nav"]);
		$stmt->bindValue(":sched_icon", $_POST["sched_icon"]);
		$stmt->bindValue(":housing_nav", $_POST["housing_nav"]);
		$stmt->bindValue(":housing_icon", $_POST["housing_icon"]);
		$stmt->bindValue(":prayer_nav", $_POST["prayer_nav"]);
		$stmt->bindValue(":prayer_icon", $_POST["prayer_icon"]);
		$stmt->bindValue(":notif_nav", $_POST["notif_nav"]);
		$stmt->bindValue(":notif_icon", $_POST["notif_icon"]);
		$stmt->bindValue(":TZ_catagory", $_POST["timeCatagory"]);
		$stmt->execute();
		// reroute to this page with the new event id
		header("Location: general.php?id=" . sanitize_id($_POST['id']));
		die();
	}
	
?>





<html>

	<?php include("../templates/head.php"); ?>	

	<body>

		<?php include("../templates/left-nav.php"); ?>

		<style>
			#general {
				background-color: grey;
				color: white;
			}
		</style>

		

		<section id="main">

			<h1>General</h1>

			<p>This page contains needed settings that determine the look and the use of the event app.</p>

			<form method = "post" enctype="multipart/form-data" id="form">

					<div class="card">
						<input type="hidden" name="id" value="<?php echo sanitize_id($_GET['id']); ?>">

						<input type="hidden" name="saveLogo">

						<input type="hidden" name="sched_icon" maxlength="100" value="ic_schedule">
					
						<input type="hidden" name="housing_icon" maxlength="100" value="ic_house">
					
						<input type="hidden" name="prayer_icon" maxlength="100" value="ic_group">						

						<input type="hidden" name="contact_icon" maxlength="100" value="ic_contact">

						<input type="hidden" name="notif_icon" maxlength="100" value="ic_bell">

                                                <div class="input" title="This distiguishes this event in a list of many events.">Event Name:<input type="text" title = "This distiguishes this event in a list of many events." name="name" maxlength="100" value="<?php echo attrstr($get_event_res["name"]); ?>"></div>

                                                <div class="input" title="This logo will be use in the app just above the navigation it will apear here just above general in the same way.">
							Event Logo:<input title="Keep logos below 300KB, png files are prefered." type="file" name="logo" >
							<div class='btn' id="delete" onclick="deleteLogo()">Delete Logo</div>
						</div>
						
						<!--<div class="input" title="Select the general area the event will be in, then a city that you know is in the same time zone.">Time Zone:
						<?php $currentTZ = $get_event_res["TZcatagory"] ?>
						<select name="timeCatagory" onchange="save()">
							<option value="Africa" <?php if($currentTZ=="Africa") { echo('selected = "selected"');} ?> >Africa</option>
							<option value="America" <?php if($currentTZ=="America") { echo('selected = "selected"');} ?> >America</option>
							<option value="Antarctica" <?php if($currentTZ=="Antarctica") { echo('selected = "selected"');} ?> >Antarctica</option>
							<option value="Arctic" <?php if($currentTZ=="Arctic") { echo('selected = "selected"');} ?> >Arctic</option>
                                                        <option value="Asia" <?php if($currentTZ=="Asia") { echo('selected = "selected"');} ?> >Asia</option>
                                                        <option value="Atlantic" <?php if($currentTZ=="Atlantic") { echo('selected = "selected"');} ?> >Atlantic</option>
                                                        <option value="Australia" <?php if($currentTZ=="Australia") { echo('selected = "selected"');} ?> >Australia</option>
                                                        <option value="Europe" <?php if($currentTZ=="Europe") { echo('selected = "selected"');} ?> >Europe</option>
                                                        <option value="Indian" <?php if($currentTZ=="Indian") { echo('selected = "selected"');} ?> >Indian</option>
                                                        <option value="Pacific" <?php if($currentTZ=="Pacific") { echo('selected = "selected"');} ?> >Pacific</option>
						</select>-->


						<!--This code displays the same options shown above with UTC as well, and nothing is hardcoaded so it is adaptible to the timezone list.
							The option above was chosen inorder to use the on change even so a user didn't have to hit the save button for the other list to change.-->
			
						<?php
							echo "<div class='input'>Time Zone:<select name='timeCatagory' onchange='save()'>";
							$TZ_Cats=array();
							$TZNames=DateTimeZone::ListIdentifiers();
							$tempmatch;
							for($i=0;$i<sizeof($TZNames);$i++){
								$tempmatch=preg_split('/[\W]+/',$TZNames[$i]);
								if(!in_array($tempmatch[0],$TZ_Cats)){
									if($tempmatch[0]==$get_event_res["TZcatagory"]){
										echo "<option selected>" . htmlstr($tempmatch[0]) ."</option>";
										array_push($TZ_Cats, $tempmatch[0]);
									} 
									else{
										array_push($TZ_Cats,$tempmatch[0]);
										echo "<option>" . htmlstr($tempmatch[0]) ."</option>";
									}
								}
							}
							echo "</select>";
							echo  "<select name='time_zone'>";
                                                        $TZNames=DateTimeZone::ListIdentifiers();
							for ($i=0;$i<sizeof($TZNames);$i++){
								$tempstring=preg_split('/[\W]+/',$TZNames[$i],2);
								if ($tempstring[0]==$get_event_res["TZcatagory"]){
								        if($tempstring[1]==$get_event_res["time_zone"]){
                                                                                echo "<option selected>" . htmlstr($tempstring[1]) ."</option>";
                                                                                
                                                                        }
                                                                        else{
                                                                                
                                                                                echo "<option>" . htmlstr($tempstring[1]) ."</option>";
                                                                        }
                                                                }
                                                        }
                                                        echo "</select>";	
							
								
							
						?>
						
						<!--<div class='btn' id="savetz" onclick="save()">confirm general area</div>       no longer needed, it automatically saves-->
						</div>
						
						

						<div class="input" title="This message is the first thing a user sees whenever they enter the app. The notificaitons are displayed immeadiatly below it.">Welcome Message:<input type="text" title="This message is the first thing a user sees whenever they enter the app. The notificaitons are displayed immeadiatly below it." name="welcome" maxlength="100" value="<?php echo attrstr($get_event_res["welcome_message"]); ?>"></div>

						<p>"Nav" stands for navigation. The following fields decide what each page is labled in the navigation on the left side off the app much like this page is labled general in the menu on your left.</p>	

						<div class="input">Contact Page Nav:<input type="text" name="contact_nav" maxlength="25" value="<?php echo attrstr($get_event_res["contact_nav"]); ?>"></div>

						<div class="input">Schedule Page Nav:<input type="text" name="sched_nav" maxlength="25" value="<?php echo attrstr($get_event_res["sched_nav"]); ?>"></div>

						<div class="input">Housing Page Nav:<input type="text" name="housing_nav" maxlength="25" value="<?php echo attrstr($get_event_res["housing_nav"]); ?>"></div>

						<div class="input">Prayer Partners Page Nav:<input type="text" name="prayer_nav" maxlength="25" value="<?php echo attrstr($get_event_res["prayer_nav"]); ?>"></div>

						<div class="input">Notification Page Nav:<input type="text" name="notif_nav" maxlength="25" value="<?php echo attrstr($get_event_res["notif_nav"]); ?>"></div>

                                                <div class="input" title="This option allows a user to enter what timezone they are in on the app so the schedule is displayed with those times.">Allow a User to Enter a Custom Timezone:<input autocomplete="off" type="checkbox" name="custom" value="true" <?php echo ($get_event_res["custom_tz"]) ? "checked" : ""; ?>></div>

                                                <div class="input" title="This option allows the app to display the schedule in the same timezone that the users divice reports.">Allow a User to Attend Remotely:<input autocomplete="off" type="checkbox" name="remote" value="true" <?php echo ($get_event_res["view_remote"]) ? "checked" : ""; ?>></div>

                                                <div class="input" title="This option allows the app to share the QR code without coming back to this web site">Allow a User to share a QR code:<input autocomplete="off" type="checkbox" name="allow_qr_share" value="true" <?php echo ($get_event_res["allow_qr_share"]) ? "checked" : ""; ?>></div>

						<div class="input" title="When this option is checked the app can scan the qr code to get the event data.">Event is Live:<input autocomplete="off" type="checkbox" name="visible" value="true" <?php echo ($get_event_res["visible"]) ? "checked" : ""; ?>></div>

						<p>This is the QR code associated with the app. Once the event app is downloaded it immediately lauches into the device camera in order to scan this code. Once this code is scaned the app has the information it needs and the app is set for the rest of the event. It is recommended to email or print this code for the attendees to scan.</p>

						<div><img src=<?php echo "'".getParentDir(2)."qr.php?id=" . sanitize_id($_GET['id']) . "'";?> alt="Mountain View">

						<div><a href="https://play.google.com/store/apps/details?id=org.lightsys.eventApp"><img src="google-play-badge.png" alt="This is a link to the event app in the android store." style="height:62.5;width:161.5;"></a>

					</div>

					<br>

					<p>Please save before navigating to a new page.</p>
			
					<div class="btn" id="save" onclick="save()">Save</div>

				</form>

		</section>

	</body>


	<script>

	function save(){
		document.forms['form']['saveLogo'].value = "save";
		$("#form").submit();
	}

	function deleteLogo(){
		document.forms['form']['saveLogo'].value = "delete";
		$("#form").submit();
	}

	</script>


</html>
