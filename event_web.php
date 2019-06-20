<?php
	include("global.php");
	$event_id = getEventId();
	mysqli_report(MYSQLI_REPORT_ALL); 
	
	//Query to retreive color
	$body_color = '';
	$get_col_stmt= $db->prepare("SELECT theme_color, theme_dark, theme_medium FROM event where ID=:id");
	$get_col_stmt->bindValue(":id",$_GET['id']);
	$get_col_stmt->execute();
	while($get_col_res = $get_col_stmt->fetch(PDO::FETCH_ASSOC)) {
		
		$col_thm= $get_col_res['theme_color'];
		$col_med = $get_col_res['theme_dark'];
		$col_drk = $get_col_res['theme_medium'];

		$body_color = '

		<style>
		body{
			color: '.$col_thm.'; 
			min-height: 100%; 
			margin: 0px;
			padding: 0px; 
			background: linear-gradient(180deg,'.$col_med.' 11%, '.$col_drk.' 100%);
		}
		.nav-tabs > li > a {
			color: '.$col_thm.'; 
		}
	
		</style>
		';
	}

	//Query to retrieve schedulerwith event ID
	$get_schedule_stmt = $db->prepare("SELECT * FROM schedule_items where event_ID=:id order by date,start_time asc");
	$get_schedule_stmt->bindValue(":id",$event_id);
	$get_schedule_stmt->execute();

	$tab_menu = '';
	$tab_content = '';  
	$count = 0; 		
	$last_id = ''; 
	$sched_array = array(); 
	
	//save data into a json array
	while($get_schedule_res = $get_schedule_stmt->fetch(PDO::FETCH_ASSOC)) {

		$day = date("D M jS",strtotime($get_schedule_res["date"])); 	
		$loc = $get_schedule_res["location"];  
		$last_id = ($get_schedule_res["ID"]); 
		$colonTime = substr_replace ($get_schedule_res["start_time"],":",strlen($get_schedule_res["start_time"]) - 2,0);
		if (strlen($colonTime) == 4)
			$colonTime = '0' . $colonTime;
		$desc = $get_schedule_res["description"];
		$leng = ($get_schedule_res["length"]);  
		
		$get_loc_stmt = $db->prepare("SELECT * FROM contacts where ID=:id");
		$get_loc_stmt->bindValue(":id",$loc);
		$get_loc_stmt->execute();
		$addr = ''; 
		while($get_loc_res = $get_loc_stmt->fetch(PDO::FETCH_ASSOC)) {
		
			$addr = $get_loc_res['name']; 
		}

		$sched_array[$day]['ID'] = $last_id;
		$sched_array[$day][$colonTime] = array(
			
			'description' => $desc,
			'location' => $addr,
			'length' => $leng  
		); 
	}	

	//echo json_encode($sched_array); 
	$count = 0; 
	//unpact the data and save into table for the tabs. 
	foreach($sched_array as $date => $time){

		$ID = $time['ID']; 
		if($count == 0){
				$tab_menu .= '<li role = "presentation" class = "active"><a href="#'. htmlstr($ID) . '" data-toggle="tab">' . $date . '</a></li>'; 
				$tab_content .= '<div role = "tabpanel" id = "'.$ID.'" class = "tab-pane active">'; 
		}
		else{
				$tab_menu .= '<li role ="presentation"><a href="#'.$ID. '" data-toggle="tab">' . $date . '</a></li>'; 
				$tab_content .= '<div role = "tabpanel"  id = "'.$ID.'" class = "tab-pane">';  
		}
		$time_array = array_shift($time); 

		$tab_content .='
				<table class = "table"> 
					<thead> 
						<tr> 
							<th scope = "col"> Start </th>
							<th scope = "col"> Event </th> 
							<th scope = "col"> Length </th> 
							<th scope = "col"> Room </th> 
						</tr> 
					</thead> 
				<tbody id = "myTable">	
		'; 

		foreach($time as $index => $key){

			$desc = $key['description']; 
			$leng = $key['length']; 
			$loc = ($key['location']); 
			$tab_content .= '
				
 
					<tr>
						<td> '.$index.'  </td> 
						<td>'.$desc.'</td> 
						<td>'.$leng.' m </td> 
						<td>'.$loc.' </td>
					</tr>


			'; 
		}		
		$tab_content .= '</tbody> </table> </div>'; 
		$count++; 
	}

?>
<!DOCTYPE html>
	<head>

	   <!-- Required meta tags 
		-->

  		<meta charset="utf-8">
  		<meta name="viewport" content="width=device-width, initial-scale=1">
    	

		<!-- Latest compiled and minified CSS -->
  		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
  		<link rel ="stylesheet" type ="text/css" media="screen" href = "webapp.css"> 
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
	  	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
		<link href="https://fonts.googleapis.com/css?family=Montserrat&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Quicksand&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css?family=Staatliches&display=swap" rel="stylesheet">

	</head>

	<body>
			<?php echo $body_color?>
			<div class="event-btn">
					<div class = "btn-contents"> 
							<?php
								// Get all the events from the database and add them to a selector
								$event_id = $_GET['id']; 
								$get_events_stmt = $db->prepare("SELECT * from event where ID=:id");
								$get_events_stmt->bindValue(":id",$event_id);
								$get_events_stmt->execute();

								while($get_events_res = $get_events_stmt->fetch(PDO::FETCH_ASSOC)) {
									echo '<h2 id= "sel"> ' . htmlstr($get_events_res['name']) . '</h2>';
								}
							?>
					</div> 
					<div id ="logo"> 
						<img src="logo_data.php?id=<?php echo (sanitize_id($_GET['id']));?>" class = "center-block img-responsive"> 
					</div> 
			</div>

			<div id = "s-cont"> 
				<section id="main">
						<input type = "hidden" name="id" value="<?php echo sanitize_id($_GET['id']); ?>">
						<input type = "hidden" name="action">
						<input type = "hidden" name="sequence">
						
						
						<ul class="nav nav-tabs nav-justified" role ="tablist" id="main-tab">
								<?php echo $tab_menu?> 				
						</ul>
						<div class="text-center">
							  <input class="form-control" placeholder="Search" name="srch-term" id="srch-term" type="text">
						</div>
						<div class = "tab-content"> 
							<?php echo $tab_content?>
						</div> 
				</section>
			</div>
			
			<script>
				var tabLinks = $('.nav > li'),
				 tabsContent = $('.tab-content > div > table >tbody'),
				 tabContent = [],
				 string,
				 i,
				 j;

				for (i = 0; i < tabsContent.length; i++) {
				  tabContent[i] = tabsContent.eq(i).text().toLowerCase();
				}
				$('#srch-term').on('input', function() {
				  string = $(this).val().toLowerCase();
				  for (j = 0; j < tabsContent.length; j++) {
					if (tabContent[j].indexOf(string) > -1) {
					  tabLinks.eq(j).show();
					  tabLinks.eq(j).find('a').tab('show');
					} else {
					  tabLinks.eq(j).hide();
					}
				  }
				});	
			</script>	

	</body>
</html>


