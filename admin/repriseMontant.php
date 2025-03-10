<?php
include '../api/bdd.php';
include '../api/fonctions.php';

// Récupération de la liste des inscriptions
$listingInscriptions = getInscriptions($dbh)[0];

// Affichage des requêtes SQL à exécuter pour chaque inscription
foreach ($listingInscriptions as $inscription) {
    $idEquipe = $inscription['idEquipe'];
    $amount = getTransactionAmount($inscription['helloOrderidEquipe']);

    // Création de la requête SQL de mise à jour
    $sql = "UPDATE equipe SET montantInscriptionEquipe = $amount WHERE idEquipe = $idEquipe;";

    // Affichage de la requête SQL
    echo $sql . "<br>";
}
?>
