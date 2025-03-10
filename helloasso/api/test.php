<?php 
include 'bdd.php';
include 'fonctions.php';
echo "DEBUT<br>";
$email="reperret@hotmail.com";
$titre="Félicitations !";
$contenu="Coucou les gens";
$libelleBouton="ACCEDER AU SITE";
$lienBouton="https://www.traildeschampignons.fr";
$libelleBouton2="";
$lienBouton2="";
$libelleBouton3="";
$lienBouton3="";
$template=7;
$sujet="inscription confirmée";
echo sendMail($email,$titre ,$contenu, $libelleBouton,$lienBouton, $libelleBouton2,$lienBouton2, $libelleBouton3,$lienBouton3,$template, $sujet);
echo "<br>FIN";
?>
