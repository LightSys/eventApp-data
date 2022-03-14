<?php


    include("../global.php");

   


    $get_tokens = $db->prepare("SELECT token FROM device_tokens d JOIN event e on d.event_ID = e.ID where e.internal_ID=:id");
    $get_tokens->bindValue(":id",$_POST['id']);
    $get_tokens->execute();


    


  

    while($get_notification_res = $get_tokens->fetch(PDO::FETCH_ASSOC)){


       $token = $get_notification_res['token'];

       $var = json_encode($_POST);
       
       $ch = curl_init();
   
       curl_setopt($ch, CURLOPT_URL, 'https://api.sandbox.push.apple.com/3/device/'. $token);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, "{
                                                \"aps\": {
                                                    \"alert\": {
                                                              \"title\": \"".$_POST["title"]."\",
                                                              \"body\": \"".$_POST["body"]."\"
                                                            },
                                                    \"sound\": \"bingbong.aiff\"
                                                }
                                            }");
       curl_setopt($ch, CURLOPT_SSLCERT, "apn-2022.crt");
       curl_setopt($ch, CURLOPT_SSLKEY, "apn-2022.key");
       curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2);
       curl_setopt($ch, CURLOPT_VERBOSE, true);
   
       $headers = array();
       $headers[] = 'Apns-Topic: org.LightSys.iOSEventAppLS88';
       $headers[] = 'Content-Type: application/x-www-form-urlencoded';
       curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
       
   
   
       $result = curl_exec($ch);
       if (curl_errno($ch)) {
           echo 'Error:' . curl_error($ch);
       }
       curl_close($ch);

    }

?>