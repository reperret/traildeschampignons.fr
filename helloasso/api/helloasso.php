<?php

include 'bdd.php';

//*************DEFINITION DES SECRETS*******************************
$client_id=urlencode($clientIdHelloAsso);
$client_secret=urlencode($clientSecretHelloAsso);
$grant_type="client_credentials";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $urlTokenHelloAsso);
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
$montantInscription=1;  //en €
$montantInscription=intval($montantInscription)*100; // en centimes d'€
$idPaiement=NULL;
$idEquipe=1;
$prenom="Rémy";
$nom="PERRET";
$email="reperret@hotmail.com";
$adresse="Test adresse";
$ville="MAVILLE";
$code_postal="69008";
$emails="premier@mail.com;second@mail.com";
$ddn="1960-01-02";
$nomEquipe="Mon nom d'équipe";

$jsonClient=array();
$jsonClient['totalAmount']=$montantInscription;
$jsonClient['initialAmount']=$montantInscription;

$jsonClient['itemName']="Paiement équipe ".$idEquipe;
$jsonClient['backUrl']=$domaine.'inscriptions.php';
$jsonClient['errorUrl']=$domaine.'confirmation.php?type=error';
$jsonClient['returnUrl']=$domaine.'confirmation.php?type=return';
$jsonClient['containsDonation']=false;

$payer=array();
$payer['firstName']=$prenom;
$payer['lastName']=$nom;
$payer['email']=$email;
$payer['dateOfBirth']=$ddn;
$payer['address']=$adresse;
$payer['city']=$ville;
$payer['zipCode']=$code_postal;
$jsonClient['payer']=$payer;

$metadata=array();
$metadata['idEquipe']=$idEquipe;
$metadata['nomEquipe']=$nomEquipe;
$metadata['emailsEquipe']=$emails;
          
$jsonClient['metadata']=$metadata;


//*************CREATION D'UN LIEN DE PAIEMENT*******************************
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $checkoutIntentUrl,
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

print_r($response);
curl_close($curl);
$lien=json_decode($response,true);
echo $lien['redirectUrl'];

//*************RECUPERATION DU LIEN *******************************
//$redirect='Location: '.$lien['redirectUrl'];

//**************************************************************************
//**************************************************************************
//**************************************************************************
//**************************HELLO ASSO PAIEMENT FIN*************************
?>
