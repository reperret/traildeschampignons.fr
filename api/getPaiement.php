<?php

include 'bdd.php';


$orderId = "46188142";


function getTransactionAmount($orderId)
{
    $grant_type = "client_credentials";
    global $clientIdHelloAsso ;
    global $clientSecretHelloAsso ;
    if (!$orderId) {
        return "Erreur identifiant Paiement. Aucune information disponible";
    }

    // Étape 1: Obtenir le jeton d'accès
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.helloasso.com/oauth2/token');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=$grant_type&client_id=" . urlencode($clientIdHelloAsso) . "&client_secret=" . urlencode($clientSecretHelloAsso));
    curl_setopt($ch, CURLOPT_POST, 1);
    $headers = array('Content-Type: application/x-www-form-urlencoded');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return 'Erreur : ' . curl_error($ch);
    }
    curl_close($ch);

    $infos = json_decode($result, true);
    $accessToken = $infos['access_token'] ?? null;

    if (!$accessToken) {
        return "Erreur lors de l'obtention du jeton d'accès.";
    }

    // Étape 2: Utiliser le jeton d'accès pour obtenir les détails du paiement
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.helloasso.com/v5/payments/' . $orderId,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        curl_close($curl);
        return 'Erreur : ' . curl_error($curl);
    }

    curl_close($curl);

    $paymentDetails = json_decode($response, true);

    // Vérifier si le montant est disponible
    $amountInCents = $paymentDetails['amount'] ?? null;

    if ($amountInCents === null) {
        return "Erreur : Montant non disponible.";
    }

    // Convertir le montant en euros
    $amountInEuros = $amountInCents / 100;

    return $amountInEuros;
}

// Appel de la fonction et affichage du montant
$amount = getTransactionAmount($orderId);
echo $amount;
?>
