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

$infos=json_decode($result,true);
$accessToken=$infos['access_token'];



$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.helloasso.com/v5/organizations/vivre-megeve/checkout-intents',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'
  {
    "totalAmount": 100,
    "initialAmount": 100,
    "itemName": "Adhesion ID 12345",
    "backUrl": "https://www.vivremegeve.fr/helloasso/backUrl.php",
    "errorUrl": "https://www.vivremegeve.fr/helloasso/errorUrl.php",
    "returnUrl": "https://www.vivremegeve.fr/helloasso/returnUrl.php",
    "containsDonation": false,
    "payer": 
    {
        "firstName": "PERRET",
        "lastName": "Rémy",
        "email": "reperret@hotmail.com",
        "dateOfBirth": "1989-08-24",
        "address": "13 rue commandant Caroline Aigle",
        "city": "LYON",
        "zipCode": "69008"
    },
    "metadata": 
    {
        "idAdhesion": 12345
    }
}',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$accessToken,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);

curl_close($curl);

$lien=json_decode($response,true);

echo $accessToken=$lien['redirectUrl'];
echo "<br>";
echo $idPaiement=$lien['id'];
?>