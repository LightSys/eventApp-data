<?php

    include('phpqrcode/qrlib.php');
    include('helper.php');

    $event_ID = (isset($_GET['id']) ? "?id=".$_GET['id'] : "");
    $url = stripFileName();
    $url .= "getevent.php" . $event_ID;
    QRcode::png($url);
    
?>