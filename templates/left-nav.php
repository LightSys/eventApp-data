<?php 
    $id = $_GET['id'];

    // include the database connection
    include("../connection.php");
    
    $stmt = $db->prepare('SELECT theme_color FROM event where ID = :id');
    $stmt->bindValue(":id", $id);
    $stmt->execute();

    $color;
    $stmt_res = $stmt->fetch(PDO::FETCH_ASSOC);
    if($stmt_res['theme_color'] != '') {
        $color = $stmt_res['theme_color'];
    } else {
        $color = "#3E52A9";
    }

    $red = hexdec($color[1].$color[2]);
    $green = hexdec($color[3].$color[4]);
    $blue = hexdec($color[5].$color[6]);

    if((max($red,$green,$blue) + min($red,$green,$blue))/510 > .7) {
	$textColor = "black";
    } else {
        $textColor = "white";
    }

    #This retrieves the image from the database to display at the top of the navigation.
    $img_stmt=$db->prepare("SELECT logo FROM event WHERE ID= :id");
    $img_stmt->bindValue(":id",$id);
    $img_stmt->execute();

    //store the blob in img
    $getImage=$img_stmt->fetch(PDO::FETCH_ASSOC);
    $img=$getImage['logo'];
    $img='"data:image/png;base64,'.$img.'"';

    echo ( 
        '<nav style="background-color:' . $color . ';">'
        . '<ol>'
	.     '<img src='.$img.' alt="Logo not set." style="width:120px;height:120px;">'
        .     '<a style="color:' . $textColor . ';" href="general.php?id=' . $id . '"><li id="general">General</li></a>'
        .     '<a style="color:' . $textColor . ';" href="advanced.php?id=' . $id . '"><li id="advanced">Advanced</li></a>'
        .     '<a style="color:' . $textColor . ';" href="contacts.php?id=' . $id . '"><li id="contacts">Contacts</li></a>'
        .     '<a style="color:' . $textColor . ';" href="contact-page.php?id=' . $id . '"><li id="contact-page-sections">Contact Page Sections</li></a>'
	.     '<a style="color:' . $textColor . ';" href="attendees.php?id=' . $id . '"><li id="attendees">Attendees</li></a>'
        .     '<a style="color:' . $textColor . ';" href="themes.php?id=' . $id . '"><li id="themes">Schedule Themes</li></a>'	
        .     '<a style="color:' . $textColor . ';" href="schedule.php?id=' . $id . '"><li id="schedule">Schedule</li></a>'
        .     '<a style="color:' . $textColor . ';" href="information-page.php?id=' . $id . '"><li id="information-pages">Information Pages</li></a>'
        .     '<a style="color:' . $textColor . ';" href="housing.php?id=' . $id . '"><li id="housing">Housing</li></a>'
        .     '<a style="color:' . $textColor . ';" href="prayer-partners.php?id=' . $id . '"><li id="prayer-partners">Prayer Partners</li></a>'
        .     '<a style="color:' . $textColor . ';" href="notifications.php?id=' . $id . '"><li id="notifications">Notifications</li></a>'
        .     '<a style="color:' . $textColor . ';" href="../events.php"><li>Select Another Event</li></a>'
        .     '<a style="color:' . $textColor . ';" href="../templates/logout.php"><li id="Logout">Logout</li></a>'
	.'</ol>'
        . '</nav>');

    

    echo (
        '<style>
            .btn {
                background-color: ' . $color . ';
		color: ' . $textColor . ';
            }

            .btn:hover {
                border: 1px solid ' . $color . ';
                color: ' . $textColor . ';
            }
            
        </style>'
    )
?>
