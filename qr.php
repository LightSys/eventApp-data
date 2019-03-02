<?php
// Generates QR code for event that is used to obtain JSON files for app

    session_start();

    include('phpqrcode/qrlib.php');
    include('global.php');

    secure();

    $event_ID = (isset($_GET['id']) ? ("?id=". sanitize_id($_GET['id'])) : "");
    $url = stripFileName();
    $url .= "get.php" . $event_ID;
    // echo $url;
    QRcode::png($url);
    
?>
