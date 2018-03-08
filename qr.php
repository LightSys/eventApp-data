<!DOCTYPE html>
<html>
<body>

<?php

    include(phpqrcode/qrlib.php);

    $event_ID = (isset($_GET['id']) ? "?id=".$_GET['id'] : "");
    $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    $token = strtok($url, '/');

    $url = "";
    while ($token !== "qr.php") {
        $url .= $token."/";
        $token = strtok("/");
    }
    $url .= "getevent.php" . $event_ID;
    echo $url;
    QRcode::png($url);

?>

</body>
</html>

