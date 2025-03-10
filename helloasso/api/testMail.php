<?php 
    //************ENVOI MAIL APPEL API REMYPERRET.ORG*************
    $message=NULL;
    $message="Votre inscription est confirmée";
        
    $postParameter = array(
        'emailExpediteur' => 'reperret@gmail.com',
        'nomExpediteur' => 'Trail des champignons',
        'emailDestinataire' => 'reperret@hotmail.com',
        'numeroTemplate' => 7,
        'tag_titre' => 'Félicitations !',
        'tag_contenu' => $message,
        'tag_lienbouton' => 'https://www.traildeschampignons.fr',
        'tag_libellebouton' => 'ACCEDER AU SITE WEB',
        'sujet' => 'inscription confirmée'
    );

    $curlHandle = curl_init('https://remyperret.org/api/sendmail/index.php');
    curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $postParameter);
    curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
    $curlResponse = curl_exec($curlHandle);
    curl_close($curlHandle);
?>
