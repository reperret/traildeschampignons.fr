<?php
//*******************VARIABLES BDD**************************
$serveur="localhost";
$user="root";
$pass="Deflagratione89";
$base = "folomilight";
//**********************************************************

//*******************CONNEXION BDD**************************
try
{
	$dbh = new PDO('mysql:dbname='.$base.';host='.$serveur, $user,$pass);
} 
catch (Exception $e) 
{
	die("Impossible de se connecter: " . $e->getMessage());
}
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//**********************************************************

//******************VARIABLE DOMAINE GENERAL***************
$domaine = "https://www.traildeschampignons.fr/test/";
//**********************************************************

//********************VARIABLES HELLOASSO******************
$nomCodeHelloAsso="so-trail-experience";
$clientIdHelloAsso="372d5f494bb044338e191371a0389a97";
$clientSecretHelloAsso="mXpTKsscQ4bgszfDN1n5kUw0+gg7r4ko";
$urlTokenHelloAsso='https://api.helloasso.com/oauth2/token';
$checkoutIntentUrl='https://api.helloasso.com/v5/organizations/'.$nomCodeHelloAsso.'/checkout-intents';
//**********************************************************

//**********RECUPERATION DES PRIX DES COURSES***************
$idCourseRando=3;

function getMontants($idCourse,$dbh)
{
    $requete="SELECT * FROM course ";
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $array[$i]['idCourse']=$colonne->idCourse;
        $array[$i]['montantInscriptionCourse']=$colonne->montantInscriptionCourse;
        $array[$i]['montantRepasCourse']=$colonne->montantRepasCourse;
        $i++;
    }

    return $array;
}

$montantsCourses=getMontants(NULL,$dbh);
$montantRepasRando=0;
$montantRepasCourse=0;
$montantInscriptionRando=0;

foreach($montantsCourses as $course)
{
    if($course['idCourse']==$idCourseRando)
    {
        $montantRepasRando=$course['montantRepasCourse'];
        $montantInscriptionRando=$course['montantInscriptionCourse'];
    }
    else
    {
        $montantRepasCourse=$course['montantRepasCourse'];
    }
}
//**********************************************************



//$nomCodeHelloAsso="remy-perret";
//$clientIdHelloAsso="b4f3c3f7ebae41e1b33a2b0a4ebd9b6f";
//$clientSecretHelloAsso="JObABcBTSh5muODZ+dY0L2Lb/9mvNZvp";
//$urlTokenHelloAsso='http://api.helloasso-sandbox.com.com/oauth2/token';
//$checkoutIntentUrl='http://api.helloasso-sandbox.com.com/v5/organizations/'.$nomCodeHelloAsso.'/checkout-intents';



?>
