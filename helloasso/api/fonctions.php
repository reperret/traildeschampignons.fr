<?php


    
function getPartenaires($dbh)
{
    $requete="SELECT * from partenaire";
    $partenaires = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $partenaires[$i]['logoPartenaire']=$colonne->logoPartenaire;
        $partenaires[$i]['libellePartenaire']=$colonne->libellePartenaire;
        $partenaires[$i]['lienPartenaire']=$colonne->lienPartenaire;
        $partenaires[$i]['categoriePartenaire']=$colonne->categoriePartenaire;
        $i++;
    }
    
    return $partenaires;
}

function getBaseUrl() {
    // Définir le chemin du répertoire de base en remontant de deux niveaux à partir de là où ce fichier est situé
    $baseDir = dirname(__FILE__, 2);

    // Obtenir le document root du serveur
    $docRoot = $_SERVER['DOCUMENT_ROOT'];

    // Assurer que le chemin est bien formé et uniforme
    $baseDir = realpath($baseDir);
    $docRoot = realpath($docRoot);

    // Trouver le chemin relatif en enlevant le document root du chemin de base
    $relativePath = str_replace($docRoot, '', $baseDir);

    // Obtenir le nom du serveur et le schéma
    $host = $_SERVER['HTTP_HOST'];
    $scheme = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'http';

    // Créer l'URL en combinant les composants
    $url = $scheme . '://' . $host . $relativePath."/";

    return $url;
}

function enregistrerCertificat($idEquipe, $idCoureur, $numCoureur, $dbh) 
{
    $targetDirectory = "/var/www/traildeschampignons.fr/test/certificats";
    $inputName = "certificatCoureur" . $numCoureur;

    if(isset($_FILES[$inputName])) {
        // Récupération de l'extension du fichier
        $fileExtension = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
        $fileName = "certif_" . $idEquipe . "_" . $idCoureur . "_" . date('YmdHis') . "." . $fileExtension;

        $tmpName = $_FILES[$inputName]['tmp_name'];
        $targetFilePath = $targetDirectory . "/" . $fileName;

        if(move_uploaded_file($tmpName, $targetFilePath)) {
            // Mise à jour de la base de données
            $sql = "UPDATE coureur SET certificatCoureur = :fileName WHERE idCoureur = :idCoureur";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':fileName', $fileName);
            $stmt->bindParam(':idCoureur', $idCoureur);
            $stmt->execute();

            return $fileName; // Retourne le nom du fichier en cas de succès
        } else {
            return false; // Échec de l'enregistrement du fichier
        }
    } else {
        return false; // Fichier non fourni
    }
}

function createEquipe($idCourse, $nomEquipe,$commentaireEquipe,$repasSuppEquipeCarne,$repasSuppEquipeVege, $dbh)
{    
    $idEquipe=-1;
    $dateInscriptionEquipe=date('Y-m-d H:i:s');
    $reqInsert = $dbh->prepare("INSERT INTO equipe (idCourse, nomEquipe, commentaireEquipe, repasSuppEquipeCarne, repasSuppEquipeVege, dateInscriptionEquipe) VALUES (?,?,?,?,?,?)");
    $reqInsert->bindParam(1, $idCourse);
    $reqInsert->bindParam(2, $nomEquipe);
    $reqInsert->bindParam(3, $commentaireEquipe);
    $reqInsert->bindParam(4, $repasSuppEquipeCarne);
    $reqInsert->bindParam(5, $repasSuppEquipeVege);
    $reqInsert->bindParam(6, $dateInscriptionEquipe);
    
    $return=$reqInsert->execute();
    if($return) $idEquipe=$dbh->lastInsertId();

    return $idEquipe;
}

function createCoureur($idEquipe, $infosPost, $numCoureur, $dbh) 
{
    $idCoureur = -1;
        
    $tailleTeeshirtCoureur=NULL;
    if($infosPost['tailleTeeshirtCoureur'.$numCoureur]!="NC") $tailleTeeshirtCoureur=$infosPost['tailleTeeshirtCoureur'.$numCoureur];
    $refusResultatsCoureur = isset($infosPost['refusResultatsCoureur'.$numCoureur]) ? 1 : 0;
        
    $reqInsert = $dbh->prepare("INSERT INTO coureur 
    (
    idEquipe, 
    numCoureur, 
    nomCoureur, 
    prenomCoureur, 
    sexeCoureur, 
    ddnCoureur, 
    emailCoureur, 
    telephoneCoureur, 
    adresseCoureur, 
    cpCoureur, 
    villeCoureur, 
    certificatCoureur,
    clubCoureur,
    licenceCoureur,
    cadeauCoureur,
    tailleTeeshirtCoureur,
    repasCoureur,
    allergiesCoureur,
    urgenceCoureur,
    numfideliteCoureur,
    refusResultatsCoureur,
    locomotionCoureur
    ) 
    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    
    $reqInsert->bindParam(1,  $idEquipe);
    $reqInsert->bindParam(2,  $numCoureur);
    $reqInsert->bindParam(3,  $infosPost['nomCoureur'.$numCoureur]);
    $reqInsert->bindParam(4,  $infosPost['prenomCoureur'.$numCoureur]);
    $reqInsert->bindParam(5,  $infosPost['sexeCoureur'.$numCoureur]);
    $reqInsert->bindParam(6,  $infosPost['ddnCoureur'.$numCoureur]);
    $reqInsert->bindParam(7,  $infosPost['emailCoureur'.$numCoureur]);
    $reqInsert->bindParam(8,  $infosPost['telephoneCoureur'.$numCoureur]);
    $reqInsert->bindParam(9,  $infosPost['adresseCoureur'.$numCoureur]);
    $reqInsert->bindParam(10, $infosPost['cpCoureur'.$numCoureur]);
    $reqInsert->bindParam(11, $infosPost['villeCoureur'.$numCoureur]);
    $reqInsert->bindParam(12, $infosPost['certificatCoureur'.$numCoureur]);
    $reqInsert->bindParam(13, $infosPost['clubCoureur'.$numCoureur]);
    $reqInsert->bindParam(14, $infosPost['licenceCoureur'.$numCoureur]);
    $reqInsert->bindParam(15, $infosPost['cadeauCoureur'.$numCoureur]);
    $reqInsert->bindParam(16, $tailleTeeshirtCoureur);
    $reqInsert->bindParam(17, $infosPost['repasCoureur'.$numCoureur]);
    $reqInsert->bindParam(18, $infosPost['allergiesCoureur'.$numCoureur]);
    $reqInsert->bindParam(19, $infosPost['urgenceCoureur'.$numCoureur]);
    $reqInsert->bindParam(20, $infosPost['numfideliteCoureur'.$numCoureur]);
    $reqInsert->bindParam(21, $refusResultatsCoureur);
    $reqInsert->bindParam(22, $infosPost['locomotionCoureur'.$numCoureur]);
    

    $return = $reqInsert->execute();

    if ($return) 
    {
        $idCoureur = $dbh->lastInsertId();
    }
    
    return $idCoureur;
}

function participantsToJson($postData) 
{
    $participants = [];
    
    // Nombre présumé de participants basé sur les noms ou prénoms postés
    $numberOfParticipants = count($postData['participantPrenom']);
    
    // Boucle sur chaque participant et association du nom avec le prénom
    for ($i = 0; $i < $numberOfParticipants; $i++) {
        if (!empty($postData['participantPrenom'][$i]) && !empty($postData['participantNom'][$i])) {
            $participants[] = [
                'prenom' => $postData['participantPrenom'][$i],
                'nom' => $postData['participantNom'][$i]
            ];
        }
    }
    
    // Convertir l'array en JSON
    return array(json_encode($participants,JSON_UNESCAPED_UNICODE), $numberOfParticipants);
}

function getMontantInscriptionCourse($idCourse,$dbh)
{
    $isEarlyAdopters=earlyAdoptersAvailable($idCourse,$dbh);
    
    $montantInscriptionCourse=0;
    $reductionEarlyAdoptersCourse=0;
    
    $requete="SELECT montantInscriptionCourse, reductionEarlyAdoptersCourse from course where idCourse=".$idCourse;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne)
    {
        $montantInscriptionCourse=$colonne->montantInscriptionCourse;
        $reductionEarlyAdoptersCourse=$colonne->reductionEarlyAdoptersCourse;
    }

    $montantFinal=$montantInscriptionCourse;
    if($isEarlyAdopters)
    {
        $montantFinal=$montantInscriptionCourse-$reductionEarlyAdoptersCourse;
    }
    
    return $montantFinal;
}

function getMontantTotalInscription($typeCourse, $infosPost, $dbh)
{
    global $montantInscriptionRando;
    global $montantRepasRando;
    global $montantRepasCourse;
    
    echo "START";
    
    $montantFinal=0;
    
    if($typeCourse=="course")
    {
        $montantInscriptionCourse=getMontantInscriptionCourse($infosPost['idCourse'],$dbh);
        $montantFinal=$montantInscriptionCourse;
        if($infosPost['repasCoureur1']!="Non") $montantFinal=$montantFinal+$montantRepasCourse;
        if($infosPost['repasCoureur2']=="Non") $montantFinal=$montantFinal+$montantRepasCourse;
        if($infosPost['repasSuppEquipeCarne']>0)   $montantFinal=$montantFinal+($infosPost['repasSuppEquipeCarne']*$montantRepasCourse);
        if($infosPost['repasSuppEquipeVege']>0)    $montantFinal=$montantFinal+($infosPost['repasSuppEquipeVege']*$montantRepasCourse);
    }
    else
    {
        $jsonParticipant=participantsToJson($infosPost);
        $nbParticipants=$jsonParticipant[1];
        $montantFinal=$nbParticipants*$montantInscriptionRando;
        if($infosPost['nbRepasRando']>0)   $montantFinal=$montantFinal+($infosPost['nbRepasRando']*$montantRepasRando);
    }

    echo "END";

    return $montantFinal;
}

function createRando($infosPost, $dbh) 
{
    $jsonParticipant=participantsToJson($infosPost);
    $jsonParticipant=$jsonParticipant[0];
    $reqInsert = $dbh->prepare("INSERT INTO rando 
    (

        emailRando,
        telephoneRando,
        adresseRando,
        cpRando,
        villeRando,
        nbRepasRando,
        commentaireRando,
        participantsRando
    ) 
    VALUES (?,?,?,?,?,?,?,?)");
    

    $reqInsert->bindParam(1,  $infosPost['emailRando']);
    $reqInsert->bindParam(2,  $infosPost['telephoneRando']);
    $reqInsert->bindParam(3,  $infosPost['adresseRando']);
    $reqInsert->bindParam(4,  $infosPost['cpRando']);
    $reqInsert->bindParam(5,  $infosPost['villeRando']);
    $reqInsert->bindParam(6,  $infosPost['nbRepasRando']);
    $reqInsert->bindParam(7,  $infosPost['commentaireRando']);
    $reqInsert->bindParam(8,  $jsonParticipant);

    $return = $reqInsert->execute();

    if ($return) 
    {
        $idRando = $dbh->lastInsertId();
    }
    
    return $idRando;
}

function formatCategorie($categorie) 
{
    $result = '';

    if ($categorie == 'H') {
        $result = "<span style=\"color:blue; font-weight:bold\">H</span>";
    } elseif ($categorie == 'F') {
        $result = "<span style=\"color:pink; font-weight:bold\">F</span>";
    } elseif ($categorie == 'M') {
        $result = "<span style=\"color:black;font-weight:bold;\">M</span>";
    }

    return $result;
}

function listerVideosArriveesDisponibles($repertoire)
{
    $listeFichiers=array();
    if (is_dir($repertoire)) 
    {
        if ($dh = opendir($repertoire)) 
        {
            while (($fichier = readdir($dh)) !== false) 
            {
                if ($fichier != "." && $fichier != "..") 
                {
                    $listeFichiers[]= $fichier;
                }
            }
            closedir($dh);
        }
    } 
    else
    {
        $listeFichiers=NULL;
    }
     sort($listeFichiers);
    
    return $listeFichiers;
}

function truncateArrivees($dbh)
{
    $return=false;    
    $reqDelete = $dbh->prepare("truncate table passage");
    $return=$reqDelete->execute();
    
    return $return;
}

function getArriveesVideoATraiter($dbh)
{
    $now = new DateTime();
    $now->sub(new DateInterval('PT2M'));
    
    
    $requete="SELECT * from passage where videoPassage IS NULL and heurePassage < '".$now->format('Y-m-d H:i:s')."'";
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $input_date = $colonne->heurePassage;
        $date = new DateTime($input_date);
        $heurePassageFormat = $date->format("YmdHis");
        
        $array[$i]['idPassage']=$colonne->idPassage;
        $array[$i]['heurePassage']=$heurePassageFormat;
        $array[$i]['dossardPassage']=$colonne->dossardPassage;
        $i++;
    }
    
    return $array;
    
}

function sendMail($email,$titre ,$contenu, $libelleBouton,$lienBouton, $libelleBouton2,$lienBouton2, $libelleBouton3,$lienBouton3,$template, $sujet)
{
    //*********************************************************************
    // ENVOI EMAIL
    //*********************************************************************
    $ch = curl_init();
    $params=array(
    "emailExpediteur" =>"sotrailexperience@gmail.com",
    "nomExpediteur" =>"So Trail Experience",
    "emailDestinataire" =>$email,
    "numeroTemplate" =>$template,
    "tag_titre" =>$titre,
    "tag_contenu" =>$contenu,
    "tag_lienbouton" =>$lienBouton,
    "tag_libellebouton" =>$libelleBouton,
    "tag_lienbouton2" =>$lienBouton2,
    "tag_libellebouton2" =>$libelleBouton2,
    "tag_lienbouton3" =>$lienBouton3,
    "tag_libellebouton3" =>$libelleBouton3,
    "sujet" =>$sujet
    );

    try
    {
        
        curl_setopt($ch, CURLOPT_URL, "https://remyperret.org/api/sendmail/");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch))
        {
            echo curl_error($ch);
            die();
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if($http_code == intval(200))
        {
            $messageConfirmation=true;
        }
        else
        {
            $messageConfirmation=false;
        }
    }
    catch (\Throwable $th)
    {
        throw $th;
    }
    finally
    {
        curl_close($ch);
    }

    return $messageConfirmation;
}

function deleteArrivee($idPassage,$dbh)
{
    $return=false;    
    $reqDelete = $dbh->prepare("DELETE FROM passage where idPassage=?");
    $reqDelete->bindParam(1,$idPassage);
    $return=$reqDelete->execute();
    
    return $return;
}

function getCoureurs($idCourse, $dbh)
{
    $requete="SELECT idCoureur, libelleCoureur, dossardCoureur, equipeCoureur, categorieCoureur from coureur where idCourse=".$idCourse;
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $array[$i]['idCoureur']=$colonne->idCoureur;
        $array[$i]['libelleCoureur']=$colonne->libelleCoureur;
        $array[$i]['dossardCoureur']=$colonne->dossardCoureur;
        $array[$i]['equipeCoureur']=$colonne->equipeCoureur;
        $array[$i]['categorieCoureur']=$colonne->categorieCoureur;
        $i++;
    }
    
    return $array;
    
}

function getEquipes($idCourse, $dbh)
{
    $whereIDCOURSE=NULL;
    if($idCourse!="") $whereIDCOURSE=" where idCourse=".$idCourse;
    $requete="SELECT idEquipe, nomEquipe, commentaireEquipe, dateInscriptionEquipe, paiementEquipe, helloTransactionEquipe from equipe ".$whereIDCOURSE;
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $array[$i]['idEquipe']=$colonne->idEquipe;
        $array[$i]['nomEquipe']=$colonne->nomEquipe;
        $array[$i]['commentaireEquipe']=$colonne->commentaireEquipe;
        $array[$i]['dateInscriptionEquipe']=$colonne->dateInscriptionEquipe;
        $array[$i]['paiementEquipe']=$colonne->paiementEquipe;
        $array[$i]['helloTransactionEquipe']=$colonne->helloTransactionEquipe;
        $i++;
    }
    
    return $array;
    
}

function verifierDossardExiste($dossard,$dbh)
{
    
    $return=false;
    $requete="SELECT dossardCoureur from coureur where dossardCoureur=".$dossard;
    $idCoureur = NULL;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne)
    {
       $idCoureur=$colonne->dossardCoureur;
    }
    if($idCoureur!=NULL) $return=true;
    
    return $return;
}

function verifierDoublonPassage($dossard,$dbh)
{
    
    $return=false;
    $requete="SELECT idPassage from passage where dossardPassage=".$dossard;
    $idPassage = NULL;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne)
    {
       $idPassage=$colonne->idPassage;
    }
    if($idPassage!=NULL) $return=true;
    
    return $return;
}

function createPassage($dossardPassage,$dbh)
{    
    $return=NULL;
    if(verifierDoublonPassage($dossardPassage,$dbh))
    {
        $return="Dossard ".$dossardPassage." déjà enregistré";
    }
    elseif(!verifierDossardExiste($dossardPassage,$dbh))
    {
        $return="Dossard ".$dossardPassage." n'existe pas";
    }
    else
    {
        $heurePassage=date('Y-m-d H:i:s');
        $reqInsert = $dbh->prepare("INSERT INTO passage (dossardPassage, heurePassage) VALUES (?,?)");
        $reqInsert->bindParam(1, $dossardPassage);
        $reqInsert->bindParam(2, $heurePassage);
        $return=$reqInsert->execute();
        $return="Dossard ".$dossardPassage. "=> arrivée à : ".$heurePassage;
    }

    return $return;
}

function getHeureDepartCourse($idCourse, $dbh)
{
    $requete="SELECT heureDepartCourse from course where idCourse=".$idCourse;
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne)
    {
        $heureDepartCourse=$colonne->heureDepartCourse;
    }
    
    return $heureDepartCourse;
    
}
    
function getClassement($idCourse, $categorie, $dbh)
{
    $whereCategorie=NULL;
    if($categorie=="ALL" || $categorie==NULL || $categorie=="")
    {
        $whereCategorie=NULL;
    }
    else
    {
       $whereCategorie=" and C.categorieCoureur='".$categorie."' "; 
    }
    $requete="SELECT P.idPassage, P.heurePassage, C.idCoureur, C.libelleCoureur, C.dossardCoureur, C.equipeCoureur, C.categorieCoureur , CO.heureDepartCourse,
    time_format(timediff(P.heurePassage,CO.heureDepartCourse),'%H:%i:%s') as tempsCoureur
    from coureur C inner join passage P on P.dossardPassage=C.dossardCoureur 
    inner join course CO on CO.idCourse=C.idCourse WHERE C.idCourse=".$idCourse." ".$whereCategorie." order by tempsCoureur";

    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $array[$i]['idPassage']=$colonne->idPassage;
        $array[$i]['heurePassage']=$colonne->heurePassage;
        $array[$i]['idCoureur']=$colonne->idCoureur;
        $array[$i]['libelleCoureur']=$colonne->libelleCoureur;
        $array[$i]['equipeCoureur']=$colonne->equipeCoureur;
        $array[$i]['dossardCoureur']=$colonne->dossardCoureur;
        $array[$i]['tempsCoureur']=$colonne->tempsCoureur;
        $array[$i]['categorieCoureur']=$colonne->categorieCoureur;
        $i++;
    }
    
    return $array;
    
}

function getPassages($idCourse,$dbh)
{
    $requete="
    SELECT P.idPassage, P.dossardPassage, P.heurePassage , C.libelleCoureur, C.equipeCoureur
    from passage P inner join coureur C on C.dossardCoureur=P.dossardPassage
    WHERE C.idCourse=".$idCourse;

    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $array[$i]['idPassage']=$colonne->idPassage;
        $array[$i]['dossardPassage']=$colonne->dossardPassage;
        $array[$i]['libelleCoureur']=$colonne->libelleCoureur;
        $array[$i]['equipeCoureur']=$colonne->equipeCoureur;
        $array[$i]['heurePassage']=$colonne->heurePassage;
        $i++;
    }
    
    return $array;
    
}

function ecartPremierTemps($tempsPassage, $tempsPremier)
{
    //****VARIABLES FINALES*********
	$jours=NULL;
    $heures=NULL;
    $minutes=NULL;
    $secondes=NULL.

    //****DECOUPAGE DES TEMPS DES COUREURS*********
    //****Coureur*****
	$heurePa= explode(":", $tempsPassage);
	$tempsSecondesPassage=intval($heurePa[0])*3600+intval($heurePa[1])*60+intval($heurePa[2]);
	//****Premier*****
	$heurePr= explode(":", $tempsPremier);
	$tempsSecondesPremier=intval($heurePr[0])*3600+intval($heurePr[1])*60+intval($heurePr[2]);

    //****CALCUL ECART EN SECONDES*********
	$seconds=$tempsSecondesPassage-$tempsSecondesPremier;

	$dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
	if($dtF->diff($dtT)->format('%a')!=0) $jours=$dtF->diff($dtT)->format('%a')."j ";
    return $jours.$dtF->diff($dtT)->format('%H:%I:%S');
}


function getAffichageQuoiAccueil($dbh)
{
    $return=NULL;
    $requete="SELECT * FROM course";
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $return.='<a href="detailcourse.php?idCourse='.$colonne->idCourse.'">'.$colonne->libelleCourse.'</a> ('.$colonne->distanceCourse.'km), ';
        $i++;
    }
        
    return substr($return, 0, -2);
    
}    

function getAffichageQuandAccueil($dbh)
{
    $return=NULL;
    $requete="SELECT * FROM course";
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $heureDepart=(new DateTime($colonne->heureDepartTheoriqueCourse))->format('G\hi');
        $return.='<a href="detailcourse.php?idCourse='.$colonne->idCourse.'">'.$colonne->libelleCourse.'</a> (Départ '.$heureDepart.')<br>';
        $i++;
    }

    return $return;

}
    
function afficherTarif($idCourse, $dbh) {
    // Initialisation de la variable de retour
    $variableAAfficher = '';

    // Requête pour obtenir les informations de la course
    $requete = "SELECT montantInscriptionCourse FROM course WHERE idCourse = :idCourse";
    $stmt = $dbh->prepare($requete);
    $stmt->bindParam(':idCourse', $idCourse, PDO::PARAM_INT);
    $stmt->execute();
    $resultat = $stmt->fetch(PDO::FETCH_OBJ);

    // Vérifier si un résultat a été trouvé
    if ($resultat) {
        // Récupérer le JSON des tarifs
        $jsonTarifs = $resultat->montantInscriptionCourse;

        // Convertir le JSON en tableau associatif
        $tarifs = json_decode($jsonTarifs, true);

        // Parcourir les périodes tarifaires et construire la chaîne à afficher
        foreach ($tarifs as $tarif) {
            $date_debut = date('d/m/Y', strtotime($tarif['date_debut']));
            $date_fin = date('d/m/Y', strtotime($tarif['date_fin']));
            $montant = number_format($tarif['montant'], 0, ',', ' ') . ' €';
            $variableAAfficher .= "Entre le $date_debut et le $date_fin : <strong>$montant</strong><br>";
        }
    } else {
        $variableAAfficher = "Aucun tarif trouvé pour cette course.";
    }

    return $variableAAfficher;
}


function earlyAdoptersAvailable($idCourse,$dbh)
{
    $isEarlyAdopters=false;
    $nbEquipes=sizeof(getEquipes($idCourse, $dbh));
    
    $nbEarlyAdoptersCourse=0;
    
    $requete="SELECT nbEarlyAdoptersCourse FROM course where idCourse=".$idCourse;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne)
    {
        $nbEarlyAdoptersCourse=$colonne->nbEarlyAdoptersCourse;
    }
        
    if($nbEquipes<$nbEarlyAdoptersCourse) $isEarlyAdopters=true;
        
    return $isEarlyAdopters;
}


function getCourses($idCourse,$dbh)
{
    $whereIDCOURSE=NULL;
    if($idCourse!="") $whereIDCOURSE=" where idCourse=".$idCourse;
    $requete="SELECT * FROM course ".$whereIDCOURSE;

    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    $i=0;
    foreach ($lignes as $colonne)
    {
        $array[$i]['idCourse']=$colonne->idCourse;
        $array[$i]['libelleCourse']=$colonne->libelleCourse;
        $array[$i]['distanceCourse']=$colonne->distanceCourse;
        $array[$i]['dplusCourse']=$colonne->dplusCourse;
        $array[$i]['heureDepartCourse']=$colonne->heureDepartCourse;
        $array[$i]['heureDepartTheoriqueCourse']=$colonne->heureDepartTheoriqueCourse;
        $array[$i]['lienParcoursCourse']=$colonne->lienParcoursCourse;
        $array[$i]['descriptionCourse']=$colonne->descriptionCourse;
        $array[$i]['tempsMaxCourse']=$colonne->tempsMaxCourse;
        $array[$i]['nbRavCourse']=$colonne->nbRavCourse;
        $array[$i]['nbMaxCoureursCourse']=$colonne->nbMaxCoureursCourse;
        $array[$i]['montantInscriptionCourse']=$colonne->montantInscriptionCourse;
        $array[$i]['reductionEarlyAdoptersCourse']=$colonne->reductionEarlyAdoptersCourse;
        $array[$i]['montantRepasCourse']=$colonne->montantRepasCourse;
        $i++;
    }
    
    if($idCourse!="") $array=$array[0];
    
    return $array;
    
}

function getInfosUtilesMailArrivee($idPassage,$dbh)
{
    $infosUtilesMail=array();
    $mails=NULL;
    $nomEquipe=NULL;
    $nomCoureurs=NULL;
    $heureArrivee=NULL;
    
    //********RECUPERATION DES MAILS************
    $requete="
        SELECT mailsCoureur, equipeCoureur, libelleCoureur, P.heurePassage
        from coureur C inner join passage P on C.dossardCoureur=P.dossardPassage
        where P.idPassage=".$idPassage;
    
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne)
    {
        $mails = explode("/", $colonne->mailsCoureur);
        $nomEquipe = $colonne->equipeCoureur;
        $nomCoureurs = $colonne->libelleCoureur;
        $heureArrivee = $colonne->heurePassage;      
    }
     
    $infosUtilesMail['mails']=$mails;
    $infosUtilesMail['nomEquipe']=$nomEquipe;
    $infosUtilesMail['nomCoureurs']=$nomCoureurs;
    $infosUtilesMail['heureArrivee']=$heureArrivee;
    
    
    //********RECUPERATION ID COURSE************
    $requete="  select C.idCourse from passage P 
                inner join coureur C on C.dossardCoureur=P.dossardPassage
                inner join course CO on CO.idCourse=C.idCourse
                where idPassage=".$idPassage;
    $idCourse=NULL;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne)
    {
        $idCourse=$colonne->idCourse;
    }
    $infosUtilesMail['idCourse']=$idCourse;
    
    return $infosUtilesMail;
}

function updatePassage($idPassage,$dbh)
{
    $infosUtilesMail=getInfosUtilesMailArrivee($idPassage,$dbh);
    
    $dateTraitement=date('Y-m-d H:i:s');
    $array=array();
    $reqUpdate = $dbh->prepare("UPDATE passage set videoPassage=? where idPassage=?");
    $reqUpdate->bindParam(1, $dateTraitement);
    $reqUpdate->bindParam(2, $idPassage);   
    $etatExecution=$reqUpdate->execute();
    
    if($etatExecution)
    {
        $array['idPassage']=$idPassage;
        $array['dateTraitement']=$dateTraitement;
        $array['return']=true;
        $array['returnDetail']="La mise à jour a bien été effectuée";
    }
    else
    {
        $array['idPassage']=$idPassage;
        $array['dateTraitement']=NULL;
        $array['return']=false;
        $array['returnDetail']="Erreur dans la mise à jour en base de donnée";
    }
    
    //**************ENVOI MAIL COUREURS********************************
    $mails=$infosCoursesUtiles[0];
    $titre="Bien joué ".$infosUtilesMail['nomCoureurs'];
    
    $contenu="Bravo, t'es arrivé.e, avec ton poto, et c'est bien ! Maintenant retrouve ton résultat sur le site et regarde dès maintenant ta vidéo d'arrivée et les photos du week end en cliquant ci dessous";
    
    $libelleBouton="VOIR MA VIDEO";
    $lienBouton="https://traildeschampignons.sotrail.fr/videosArrivee/".$idPassage.".mp4";
    
    $libelleBouton2="VOIR LES RESULTATS";
    $lienBouton2="https://traildeschampignons.sotrail.fr/resultats.php?idCourse=".$infosUtilesMail['idCourse'];
    
    $libelleBouton3="VOIR LES PHOTOS";
    $lienBouton3="https://traildeschampignons.sotrail.fr/galerie";
    
    
    $template=7;
    $sujet="résultat Trail des champignons équipe ".$infosUtilesMail['nomEquipe'];
    foreach($infosUtilesMail['mails'] as $mail)
    {
         sendMail($mail,$titre ,$contenu, $libelleBouton,$lienBouton,  $libelleBouton2,$lienBouton2, $libelleBouton3,$lienBouton3,$template, $sujet); 
    }
     
                
    return $array;
}

function cleanString($chaine, $maj)
{
    if(trim($chaine)=='' || $chaine==NULL)
    {
        return NULL;
    }
    else
    {
        //SUPPRIMER TOUT ACCENT OU CARACTERE CHELOU SUR UNE LETTRE
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        $chaine=str_replace($a, $b, $chaine);

        //ON MET TOUT EN MINUSCULE
        $chaine=strtolower($chaine);

        //ON NE GARDE QUE DES LETTRES DE A 0 Z
        $email=NULL;
        if($maj==2) $regex='0-9@\.\-_';
        if($maj==3) $regex='0-9\'';
        $chaine = preg_replace("/[^a-z".$regex."]+/", " ", $chaine);

        //SUPPRESSION ESPACES EN TROP
        $chaine = trim($chaine);
        if($maj==1 || $maj==3){$chaine=strtoupper($chaine);}else{$chaine=ucwords($chaine);}
        if($maj==2){$chaine=strtolower($chaine);}
        return $chaine;
    }


}



?>
