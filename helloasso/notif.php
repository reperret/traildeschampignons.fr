<?php
//****************RECEPTION DU JSON************************
$input = file_get_contents('php://input');
include '../connexion.php';


//****************ON LOGUE LE JSON RECU************************
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$reqInsert = $dbh->prepare("INSERT INTO logPaiement (logPaiementContenu) VALUES (?)");
$reqInsert->bindParam(1, $input);
$reqInsert->execute();


//****************ON TRAITE LE PAIEMENT************************














header("HTTP/1.1 200 OK") ;

?>
