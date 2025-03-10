<?php

//*************DEFINITION DES SECRETS*******************************
//$client_id=urlencode("59a4ac3334474f9ca7b3801e2a099a14");
//$client_secret=urlencode("HWLusxldn87F4O24yyyXN7uW4md31Bbg");

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
if (curl_errno($ch)) 
{
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);

$infos=json_decode($result,true);
$accessToken=$infos['access_token'];


//************PREPARATION DES INFOS JSON PAIEMENT***************************
$idAdhesion='XXXXX';

$jsonClient=array();
$jsonClient['totalAmount']=100;
$jsonClient['initialAmount']=100;
$jsonClient['itemName']="Adhesion ".$idAdhesion;
$jsonClient['backUrl']="https://www.amis-de-megeve.com";
$jsonClient['errorUrl']="https://www.amis-de-megeve.com/helloasso/returnUrl.php?type=return";
$jsonClient['returnUrl']="https://www.amis-de-megeve.com/helloasso/returnUrl.php?type=return";
$jsonClient['containsDonation']=false;

$payer=array();
$payer['firstName']="PERRET";
$payer['lastName']="Rémy";
$payer['email']="reperret@hotmail.com";
$payer['dateOfBirth']="1989-08-24";
$payer['address']="13 rue commandant Caroline Aigle";
$payer['city']="LYON";
$payer['zipCode']="69008";
$jsonClient['payer']=$payer;

$metadata=array();
$metadata['idAdhesion']=12345;
$jsonClient['metadata']=$metadata;


//*************CREATION D'UN LIEN DE PAIEMENT*******************************
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
  CURLOPT_POSTFIELDS =>json_encode($jsonClient,true),
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$accessToken,
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
curl_close($curl);
$lien=json_decode($response,true);

//*************RECUPERATION DU LIEN ET ID DE PAIEMENT*******************************
echo $lienPaiement=$lien['redirectUrl'];
echo "<br>";
echo $idPaiement=$lien['id'];
//header('Location : '.$lienPaiement);
echo "fin";

?>