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

    #This retrieves the image from the database to display at the top of the navigation.
    $img_stmt=$db->prepare("SELECT logo FROM event WHERE ID= :id");
    $img_stmt->bindValue(":id",$id);
    $img_stmt->execute();

    //store the blob in img
    $getImage=$img_stmt->fetch(PDO::FETCH_ASSOC);
    $img=$getImage['logo'];
#    $img='iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==';
    $img='"data:image/png;base64,'.$img.'"';

    echo ( 
        '<nav style="background-color: ' . $color . ';">'
        . '<ol>'
	.     '<img src='.$img.' alt="Logo not set." style="width:120px;height:120px;">'
        .     '<a href="general.php?id=' . $id . '"><li id="general">General</li></a>'
        .     '<a href="advanced.php?id=' . $id . '"><li id="advanced">Advanced</li></a>'
        .     '<a href="contacts.php?id=' . $id . '"><li id="contacts">Contacts</li></a>'
        .     '<a href="contact-page.php?id=' . $id . '"><li id="contact-page-sections">Contact Page Sections</li></a>'
	.     '<a href="attendees.php?id=' . $id . '"><li id="attendees">Attendees</li></a>'
        .     '<a href="themes.php?id=' . $id . '"><li id="themes">Schedule Themes</li></a>'	
        .     '<a href="schedule.php?id=' . $id . '"><li id="schedule">Schedule</li></a>'
        .     '<a href="information-page.php?id=' . $id . '"><li id="information-pages">Information Pages</li></a>'
        .     '<a href="housing.php?id=' . $id . '"><li id="housing">Housing</li></a>'
        .     '<a href="prayer-partners.php?id=' . $id . '"><li id="prayer-partners">Prayer Partners</li></a>'
        .     '<a href="notifications.php?id=' . $id . '"><li id="notifications">Notifications</li></a>'
        .     '<a href="../events.php"><li>Select Another Event</li></a>'
        .     '<a href="../templates/logout.php"><li id="Logout">Logout</li></a>'
	.'</ol>'
        . '</nav>');

    

    echo (
        '<style>
            .btn {
                background-color: ' . $color . ';
            }

            .btn:hover {
                border: 1px solid ' . $color . ';
                color: ' . $color . ';
            }
            
        </style>'
    )
?>
