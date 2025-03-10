<?php
$client_id=urlencode("2d88f2b681d94f919159a193feadc7cc");
$client_secret=urlencode("3B4do33plp6ZkHZg6ypd+FC+QX+bOIoX");
$grant_type="client_credentials";



$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, 'https://api.helloasso.com/oauth2/token');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=".$grant_type."&client_id=".$client_id."&client_secret=".$client_secret);
curl_setopt($ch, CURLOPT_POST, 1);

$headers = array();
$headers[] = 'Content-Type: application/x-www-form-urlencoded';
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);




print_r($result);
?>