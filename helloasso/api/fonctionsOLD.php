<?php

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
    "emailExpediteur" =>"reperret@gmail.com",
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

function getCourses($idCourse,$dbh)
{
    $whereIDCOURSE=NULL;
    if($idCourse!="") $whereIDCOURSE=" where idCourse=".$idCourse;
    $requete="SELECT idCourse, libelleCourse, distanceCourse, dplusCourse, heureDepartCourse FROM course ".$whereIDCOURSE;

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
