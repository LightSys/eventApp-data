<!DOCTYPE html>
<html>
<body>

<?php
$string = "Hello world. Beautiful day today.";
 $token = strtok($string, " ");
 
while ($token !== false)
   {
   echo "$token<br>";
   $token = strtok(" ");
   }

    // include(./phpqrcode/qrlib.php);

    // $event_id = $_GET['id'];
    $url = full_url($_SERVER);
    $token = strtok($url, '/');
    // $url = strtok($url, '/');

    while ($token !== false) {
        echo "$token<br>";
        $token = strtok(" ");
    }
    //  . "?id=" . $_GET['id'];
    // QRcode::png($url);

?>

</body>
</html>

