<?php
// Generates QR code for event that is used to obtain JSON files for app

    include('phpqrcode/qrlib.php');
    include('helper.php');

    $event_ID = (isset($_GET['id']) ? "?id=".$_GET['id'] : "");
    $url = stripFileName();
    $url .= "getevent.php" . $event_ID;
    QRcode::png($url);
    
?>