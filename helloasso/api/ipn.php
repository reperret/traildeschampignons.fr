<?php
$input = file_get_contents('php://input');
include 'bdd.php';
include('fonctions.php');


//**************RECUPERATION DES ELEMENTS HELLO ASSO**************
$dataPaiementHello=json_decode($input,true);

//MODE RANDO
$idRando =          $dataPaiementHello['metadata']['idRando'];
$emailRando =       $dataPaiementHello['metadata']['emailRando'];
$nomEquipeRando =   $dataPaiementHello['metadata']['nomEquipeRando'];
    
    

//MODE COURSE
$idEquipe = $dataPaiementHello['metadata']['idEquipe'];
$nomEquipe = $dataPaiementHello['metadata']['nomEquipe'];
$emailsEquipe = $dataPaiementHello['metadata']['emailsEquipe'];


$payment_id=$dataPaiementHello['data']['order']['id'];
$orderId=$dataPaiementHello['data']['id'];
$payment_date=$dataPaiementHello['data']['date'];

$eventState=$dataPaiementHello['data']['state'];
$eventType=$dataPaiementHello['eventType'];
$itemState=$dataPaiementHello['data']['items'][0]['state'];
$logPaiementEvent=$eventType." ".$eventState." ".$itemState;


//****************ON LOGUE LE JSON RECU ET POUR EVITER DOUBLON************************
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$reqInsert = $dbh->prepare("INSERT INTO logPaiement (logPaiementContenu,logPaiementIdHello,logPaiementEvent,logPaiementDateReellePaiement, logPaiementOrderId) VALUES (?,?,?,?,?)");
$reqInsert->bindParam(1, $input);
$reqInsert->bindParam(2, $payment_id);
$reqInsert->bindParam(3, $logPaiementEvent);
$reqInsert->bindParam(4, $payment_date);
$reqInsert->bindParam(5, $orderId);

$reqInsert->execute();


//****************SI PAIEMENT OK************************
if($eventType=='Payment' && $eventState=='Authorized' && $itemState=='Processed')
{

    if($idRando!="")
    {
        //***********UPDATE PAIEMENT ********************
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $paiementRando=1;
        $reqUpdate = $dbh->prepare("UPDATE rando set helloTransactionRando=?, paiementRando=? where idRando=?");
        $reqUpdate->bindParam(1,$payment_id);
        $reqUpdate->bindParam(2,$paiementRando);
        $reqUpdate->bindParam(3,$idRando);
        $reqUpdate->execute();
        
        //******************** ENVOI MAIL ************************
        $titre="Inscription randonnée confirmée !";
        $contenu="Vous êtes maintenant inscrit à la randonnée du Trail des Champignons, votre paiement a bien été pris en compte. Retrouvez l'ensemble des informations utiles sur notre site web ! <br><br>A très bientôt !";
        $libelleBouton="ACCEDER AU SITE";
        $lienBouton="https://www.traildeschampignons.fr";
        $libelleBouton2="";
        $lienBouton2="";
        $libelleBouton3="";
        $lienBouton3="";
        $template=7;
        $sujet="confirmation inscription randonnée";
        
        sendMail(trim($emailRando),$titre ,$contenu, $libelleBouton,$lienBouton, $libelleBouton2,$lienBouton2, $libelleBouton3,$lienBouton3,$template, $sujet);
        //******************** FIN GENERATION EMAIL CONFIRMATION CLIENT************************
        
    }
    else
    {
        //***********UPDATE PAIEMENT ********************
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $paiementEquipe=1;
        $reqUpdate = $dbh->prepare("UPDATE equipe set helloTransactionEquipe=?, paiementEquipe=? where idEquipe=?");
        $reqUpdate->bindParam(1,$payment_id);
        $reqUpdate->bindParam(2,$paiementEquipe);
        $reqUpdate->bindParam(3,$idEquipe);
        $reqUpdate->execute();
        
        //******************** ENVOI MAILS ************************
        $emails = explode(';', $emailsEquipe);
        $titre="Inscriptions confirmée !";
        $contenu="Vous êtes maintenant inscrit au trail des champignons, votre paiement a bien été pris en compte. Retrouvez l'ensemble des informations utiles sur notre site web ! <br><br>A très bientôt !";
        $libelleBouton="ACCEDER AU SITE";
        $lienBouton="https://www.traildeschampignons.fr";
        $libelleBouton2="";
        $lienBouton2="";
        $libelleBouton3="";
        $lienBouton3="";
        $template=7;
        $sujet="confirmation inscription";

        sendMail(trim($emails[0]),$titre ,$contenu, $libelleBouton,$lienBouton, $libelleBouton2,$lienBouton2, $libelleBouton3,$lienBouton3,$template, $sujet);
        sendMail(trim($emails[1]),$titre ,$contenu, $libelleBouton,$lienBouton, $libelleBouton2,$lienBouton2, $libelleBouton3,$lienBouton3,$template, $sujet);
        //******************** FIN GENERATION EMAIL CONFIRMATION CLIENT************************
        
    }

    


}

?>
