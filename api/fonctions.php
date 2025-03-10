<?php

function getCoureursSansCertificat($dbh)
{
    $requete = "
    SELECT 
        C.idCoureur, C.nomCoureur, C.prenomCoureur, C.emailCoureur, 
        C.certificatCoureur, C.certificatValideCoureur,
        E.nomEquipe,
        CO.libelleCourse,
        COUNT(R.idCoureur) AS nbRelances, 
        GROUP_CONCAT(DATE_FORMAT(R.dateRelance, '%d/%m') ORDER BY R.dateRelance DESC SEPARATOR ', ') AS datesRelances
    FROM 
        coureur C
    INNER JOIN 
        equipe E ON C.idEquipe = E.idEquipe
    INNER JOIN 
        course CO ON E.idCourse = CO.idCourse
    LEFT JOIN 
        relance_certificat R ON C.idCoureur = R.idCoureur
    WHERE 
        (C.certificatCoureur IS NULL OR C.certificatCoureur = '' OR C.certificatValideCoureur = 0)
    GROUP BY 
        C.idCoureur
    ORDER BY 
        C.nomCoureur ASC";

    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $coureurs = $resultats->fetchAll(PDO::FETCH_ASSOC);
    /*
    // Bouchon pour les tests : ajout de deux coureurs fictifs
    $coureurs[] = [
        'idCoureur' => 1,
        'nomCoureur' => 'Teston',
        'prenomCoureur' => 'Pierre',
        'emailCoureur' => 'reperret@hotmail.com',
        'certificatCoureur' => '',
        'certificatValideCoureur' => 0,
        'nomEquipe' => 'Les Testeurs',
        'libelleCourse' => 'Course des Tests',
        'nbRelances' => 2,
        'datesRelances' => '15/10, 20/10'
    ];

    $coureurs[] = [
        'idCoureur' => 2,
        'nomCoureur' => 'Essai',
        'prenomCoureur' => 'Marie',
        'emailCoureur' => 'reperret@gmail.com',
        'certificatCoureur' => '',
        'certificatValideCoureur' => 0,
        'nomEquipe' => 'Les Explorateurs',
        'libelleCourse' => 'Course des Débuts',
        'nbRelances' => 1,
        'datesRelances' => '18/10'
    ];*/

    return $coureurs;
}



function enregistrerRelanceCertificat($idCoureur, $motifRelance, $dbh)
{
    try {
        $requete = $dbh->prepare("INSERT INTO relance_certificat (idCoureur, dateRelance, motifRelance) VALUES (:idCoureur, NOW(), :motifRelance)");
        $requete->bindParam(':idCoureur', $idCoureur, PDO::PARAM_INT);
        $requete->bindParam(':motifRelance', $motifRelance, PDO::PARAM_STR);

        if (!$requete->execute()) {
            $erreurs = $requete->errorInfo();
            throw new Exception("Erreur lors de l'insertion : " . $erreurs[2]);
        }
    } catch (Exception $e) {
        error_log("Erreur d'enregistrement de la relance : " . $e->getMessage());
    }
}



function getTransactionAmount($orderId)
{
    $grant_type = "client_credentials";
    global $clientIdHelloAsso;
    global $clientSecretHelloAsso;
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



function render($template, $data)
{
    foreach ($data as $key => $value) {
        $template = str_replace('{{ ' . $key . ' }}', $value, $template);
    }
    return $template;
}

function clean($chaine, $type)
{
    // Trim les espaces avant et après
    $chaine = trim($chaine);

    // Supprimer les espaces dans le mot
    $chaine = str_replace(' ', '', $chaine);

    if ($type === 'nom') {
        // Convertir en majuscules et supprimer les accents
        $chaine = strtoupper($chaine);
        $chaine = iconv('UTF-8', 'ASCII//TRANSLIT', $chaine);
    } elseif ($type === 'prenom') {
        // Convertir la chaîne en minuscules pour conserver les accents
        $chaine = mb_strtolower($chaine, 'UTF-8');

        // Mettre la première lettre en majuscule sans accents
        $premiereLettre = mb_strtoupper(mb_substr($chaine, 0, 1, 'UTF-8'), 'UTF-8');
        $premiereLettre = iconv('UTF-8', 'ASCII//TRANSLIT', $premiereLettre);

        // Combiner la première lettre majuscule sans accents avec le reste de la chaîne
        $chaine = $premiereLettre . mb_substr($chaine, 1, null, 'UTF-8');
    }

    return $chaine;
}

function getPartenaires($dbh)
{
    $requete = "SELECT * from partenaire";
    $partenaires = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($lignes as $colonne) {
        $partenaires[$i]['logoPartenaire'] = $colonne->logoPartenaire;
        $partenaires[$i]['libellePartenaire'] = $colonne->libellePartenaire;
        $partenaires[$i]['lienPartenaire'] = $colonne->lienPartenaire;
        $partenaires[$i]['categoriePartenaire'] = $colonne->categoriePartenaire;
        $i++;
    }

    return $partenaires;
}

function getBaseUrl()
{
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
    $url = $scheme . '://' . $host . $relativePath . "/";

    return $url;
}

function enregistrerCertificat($idEquipe, $idCoureur, $numCoureur, $dbh)
{
    global $cheminAbsoluLinuxCertificats;
    $targetDirectory = $cheminAbsoluLinuxCertificats;
    $inputName = "certificatCoureur" . $numCoureur;

    if (isset($_FILES[$inputName])) {
        // Récupération de l'extension du fichier
        $fileExtension = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
        $fileName = "certif_" . $idEquipe . "_" . $idCoureur . "_" . date('YmdHis') . "." . $fileExtension;

        $tmpName = $_FILES[$inputName]['tmp_name'];
        $targetFilePath = $targetDirectory . "/" . $fileName;

        if (move_uploaded_file($tmpName, $targetFilePath)) {
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

function createEquipe($idCourse, $nomEquipe, $commentaireEquipe, $repasSuppEquipeCarne, $repasSuppEquipeVege, $dbh)
{
    $idEquipe = -1;
    $dateInscriptionEquipe = date('Y-m-d H:i:s');
    $reqInsert = $dbh->prepare("INSERT INTO equipe (idCourse, nomEquipe, commentaireEquipe, repasSuppEquipeCarne, repasSuppEquipeVege, dateInscriptionEquipe) VALUES (?,?,?,?,?,?)");
    $reqInsert->bindParam(1, $idCourse);
    $reqInsert->bindParam(2, $nomEquipe);
    $reqInsert->bindParam(3, $commentaireEquipe);
    $reqInsert->bindParam(4, $repasSuppEquipeCarne);
    $reqInsert->bindParam(5, $repasSuppEquipeVege);
    $reqInsert->bindParam(6, $dateInscriptionEquipe);

    $return = $reqInsert->execute();
    if ($return) $idEquipe = $dbh->lastInsertId();

    return $idEquipe;
}

function createCoureur($idEquipe, $infosPost, $numCoureur, $dbh)
{
    $idCoureur = -1;

    $tailleTeeshirtCoureur = NULL;
    if ($infosPost['tailleTeeshirtCoureur' . $numCoureur] != "NC") $tailleTeeshirtCoureur = $infosPost['tailleTeeshirtCoureur' . $numCoureur];
    $refusResultatsCoureur = isset($infosPost['refusResultatsCoureur' . $numCoureur]) ? 1 : 0;

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
    $reqInsert->bindParam(3,  clean($infosPost['nomCoureur' . $numCoureur], "nom"));
    $reqInsert->bindParam(4,  clean($infosPost['prenomCoureur' . $numCoureur], "prenom"));
    $reqInsert->bindParam(5,  $infosPost['sexeCoureur' . $numCoureur]);
    $reqInsert->bindParam(6,  $infosPost['ddnCoureur' . $numCoureur]);
    $reqInsert->bindParam(7,  $infosPost['emailCoureur' . $numCoureur]);
    $reqInsert->bindParam(8,  $infosPost['telephoneCoureur' . $numCoureur]);
    $reqInsert->bindParam(9,  $infosPost['adresseCoureur' . $numCoureur]);
    $reqInsert->bindParam(10, $infosPost['cpCoureur' . $numCoureur]);
    $reqInsert->bindParam(11, clean($infosPost['villeCoureur' . $numCoureur], "nom"));
    $reqInsert->bindParam(12, $infosPost['certificatCoureur' . $numCoureur]);
    $reqInsert->bindParam(13, $infosPost['clubCoureur' . $numCoureur]);
    $reqInsert->bindParam(14, $infosPost['licenceCoureur' . $numCoureur]);
    $reqInsert->bindParam(15, $infosPost['cadeauCoureur' . $numCoureur]);
    $reqInsert->bindParam(16, $tailleTeeshirtCoureur);
    $reqInsert->bindParam(17, $infosPost['repasCoureur' . $numCoureur]);
    $reqInsert->bindParam(18, $infosPost['allergiesCoureur' . $numCoureur]);
    $reqInsert->bindParam(19, $infosPost['urgenceCoureur' . $numCoureur]);
    $reqInsert->bindParam(20, $infosPost['numfideliteCoureur' . $numCoureur]);
    $reqInsert->bindParam(21, $refusResultatsCoureur);
    $reqInsert->bindParam(22, $infosPost['locomotionCoureur' . $numCoureur]);


    $return = $reqInsert->execute();

    if ($return) {
        $idCoureur = $dbh->lastInsertId();
    }

    return $idCoureur;
}

function participantsToJson($postData)
{
    $participants = [];

    // Nombre présumé de participants basé sur les noms ou prénoms postés
    $numberOfParticipants = count($postData['participantPrenom']);
    $numberOfParticipantsAdultes = 0;
    $numberOfParticipantsEnfants = 0;

    // Boucle sur chaque participant et association du nom avec le prénom
    for ($i = 0; $i < $numberOfParticipants; $i++) {
        if (!empty($postData['participantPrenom'][$i]) && !empty($postData['participantNom'][$i])) {
            $participants[] = [
                'prenom' => clean($postData['participantPrenom'][$i], "prenom"),
                'nom' => clean($postData['participantNom'][$i], "nom"),
                'type' => $postData['participantType'][$i]
            ];

            if ($postData['participantType'][$i] == "Adulte") $numberOfParticipantsAdultes++;
            if ($postData['participantType'][$i] == "Enfant") $numberOfParticipantsEnfants++;
        }
    }

    // Convertir l'array en JSON
    return array(json_encode($participants, JSON_UNESCAPED_UNICODE), $numberOfParticipants, $numberOfParticipantsAdultes, $numberOfParticipantsEnfants);
}

function getMontantInscriptionCourse($idCourse, $dbh)
{
    $isEarlyAdopters = earlyAdoptersAvailable($idCourse, $dbh);

    $montantInscriptionCourse = 0;
    $reductionEarlyAdoptersCourse = 0;

    $requete = "SELECT montantInscriptionCourse, reductionEarlyAdoptersCourse from course where idCourse=" . $idCourse;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne) {
        $montantInscriptionCourse = $colonne->montantInscriptionCourse;
        $reductionEarlyAdoptersCourse = $colonne->reductionEarlyAdoptersCourse;
    }

    $montantFinal = $montantInscriptionCourse;
    if ($isEarlyAdopters) {
        $montantFinal = $montantInscriptionCourse - $reductionEarlyAdoptersCourse;
    }

    return $montantFinal;
}

function getMontantTotalInscription($typeCourse, $infosPost, $dbh, $codePromo = null)
{
    global $montantInscriptionRandoAdulte;
    global $montantInscriptionRandoEnfant;
    global $montantRepasRando;
    global $montantRepasCourse;

    $montantFinal = 0;

    if ($typeCourse == "course") {
        $montantInscriptionCourse = getMontantInscriptionCourse($infosPost['idCourse'], $dbh);
        $montantFinal = $montantInscriptionCourse;
        if ($infosPost['repasCoureur1'] != "Non") $montantFinal = $montantFinal + $montantRepasCourse;
        if ($infosPost['repasCoureur2'] != "Non") $montantFinal = $montantFinal + $montantRepasCourse;
        if ($infosPost['repasSuppEquipeCarne'] > 0)   $montantFinal = $montantFinal + ($infosPost['repasSuppEquipeCarne'] * $montantRepasCourse);
        if ($infosPost['repasSuppEquipeVege'] > 0)    $montantFinal = $montantFinal + ($infosPost['repasSuppEquipeVege'] * $montantRepasCourse);
    } else {
        $jsonParticipant = participantsToJson($infosPost);
        $nbParticipantsAdultes = $jsonParticipant[2];
        $nbParticipantsEnfants = $jsonParticipant[3];
        $montantFinal = ($nbParticipantsAdultes * $montantInscriptionRandoAdulte) + ($nbParticipantsEnfants * $montantInscriptionRandoEnfant);
        if ($infosPost['nbRepasCarneRando'] > 0)   $montantFinal = $montantFinal + ($infosPost['nbRepasCarneRando'] * $montantRepasRando);
        if ($infosPost['nbRepasVegeRando'] > 0)    $montantFinal = $montantFinal + ($infosPost['nbRepasVegeRando'] * $montantRepasRando);
    }

    // Application du code promo si présent
    $reduction = 0;
    if ($codePromo) {
        $promo = validerCodePromo($codePromo, $dbh); // Utilisation de la fonction centralisée
        if ($promo) {
            $reduction = $promo['typeReduction'] == 'pourcentage' ? ($montantFinal * $promo['valeurReduction']) / 100 : $promo['valeurReduction'];
            $montantFinal -= $reduction;
        }
    }

    return array($montantFinal, $reduction);
}


function validerCodePromo($codePromo, $dbh)
{
    if (!$codePromo) {
        return false;
    }
    $stmt = $dbh->prepare("SELECT * FROM code_promotion WHERE code = ? AND dateDebut <= NOW() AND dateFin >= NOW()");
    $stmt->execute([$codePromo]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}




function createRando($infosPost, $dbh)
{
    $jsonParticipant = participantsToJson($infosPost);
    $jsonParticipant = $jsonParticipant[0];
    $reqInsert = $dbh->prepare("INSERT INTO rando 
    (

        emailRando,
        telephoneRando,
        adresseRando,
        cpRando,
        villeRando,
        nbRepasCarneRando,
        nbRepasVegeRando,
        commentaireRando,
        participantsRando
    ) 
    VALUES (?,?,?,?,?,?,?,?,?)");


    $reqInsert->bindParam(1,  $infosPost['emailRando']);
    $reqInsert->bindParam(2,  $infosPost['telephoneRando']);
    $reqInsert->bindParam(3,  $infosPost['adresseRando']);
    $reqInsert->bindParam(4,  $infosPost['cpRando']);
    $reqInsert->bindParam(5,  clean($infosPost['villeRando'], "nom"));
    $reqInsert->bindParam(6,  $infosPost['nbRepasCarneRando']);
    $reqInsert->bindParam(7,  $infosPost['nbRepasVegeRando']);
    $reqInsert->bindParam(8,  $infosPost['commentaireRando']);
    $reqInsert->bindParam(9,  $jsonParticipant);

    $return = $reqInsert->execute();

    if ($return) {
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
    $listeFichiers = array();
    if (is_dir($repertoire)) {
        if ($dh = opendir($repertoire)) {
            while (($fichier = readdir($dh)) !== false) {
                if ($fichier != "." && $fichier != "..") {
                    $listeFichiers[] = $fichier;
                }
            }
            closedir($dh);
        }
    } else {
        $listeFichiers = NULL;
    }
    sort($listeFichiers);

    return $listeFichiers;
}

function truncateArrivees($dbh)
{
    $return = false;
    $reqDelete = $dbh->prepare("truncate table passage");
    $return = $reqDelete->execute();

    return $return;
}

function getArriveesVideoATraiter($dbh)
{
    $now = new DateTime();
    $now->sub(new DateInterval('PT2M'));


    $requete = "SELECT * from passage where videoPassage IS NULL and heurePassage < '" . $now->format('Y-m-d H:i:s') . "'";
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($lignes as $colonne) {
        $input_date = $colonne->heurePassage;
        $date = new DateTime($input_date);
        $heurePassageFormat = $date->format("YmdHis");

        $array[$i]['idPassage'] = $colonne->idPassage;
        $array[$i]['heurePassage'] = $heurePassageFormat;
        $array[$i]['dossardPassage'] = $colonne->dossardPassage;
        $i++;
    }

    return $array;
}

function sendMail($email, $titre, $contenu, $libelleBouton, $lienBouton, $libelleBouton2, $lienBouton2, $libelleBouton3, $lienBouton3, $template, $sujet)
{
    //*********************************************************************
    // ENVOI EMAIL
    //*********************************************************************
    $ch = curl_init();
    $params = array(
        "emailExpediteur" => "sotrailexperience@gmail.com",
        "nomExpediteur" => "So Trail Experience",
        "emailDestinataire" => $email,
        "numeroTemplate" => $template,
        "tag_titre" => $titre,
        "tag_contenu" => $contenu,
        "tag_lienbouton" => $lienBouton,
        "tag_libellebouton" => $libelleBouton,
        "tag_lienbouton2" => $lienBouton2,
        "tag_libellebouton2" => $libelleBouton2,
        "tag_lienbouton3" => $lienBouton3,
        "tag_libellebouton3" => $libelleBouton3,
        "sujet" => $sujet
    );

    try {

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

        if (curl_errno($ch)) {
            echo curl_error($ch);
            die();
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code == intval(200)) {
            $messageConfirmation = true;
        } else {
            $messageConfirmation = false;
        }
    } catch (\Throwable $th) {
        throw $th;
    } finally {
        curl_close($ch);
    }

    return $messageConfirmation;
}

function deleteArrivee($idPassage, $dbh)
{
    $return = false;
    $reqDelete = $dbh->prepare("DELETE FROM passage where idPassage=?");
    $reqDelete->bindParam(1, $idPassage);
    $return = $reqDelete->execute();

    return $return;
}

function getCoureurs($idCourse, $dbh)
{
    $requete = "SELECT idCoureur, libelleCoureur, dossardCoureur, equipeCoureur, categorieCoureur from coureur where idCourse=" . $idCourse;
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($lignes as $colonne) {
        $array[$i]['idCoureur'] = $colonne->idCoureur;
        $array[$i]['libelleCoureur'] = $colonne->libelleCoureur;
        $array[$i]['dossardCoureur'] = $colonne->dossardCoureur;
        $array[$i]['equipeCoureur'] = $colonne->equipeCoureur;
        $array[$i]['categorieCoureur'] = $colonne->categorieCoureur;
        $i++;
    }

    return $array;
}

function getEquipes($idCourse, $dbh)
{
    $whereIDCOURSE = NULL;
    if ($idCourse != "") $whereIDCOURSE = " where idCourse=" . $idCourse;
    $requete = "SELECT idEquipe, nomEquipe, commentaireEquipe, dateInscriptionEquipe, paiementEquipe, helloTransactionEquipe from equipe " . $whereIDCOURSE;
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($lignes as $colonne) {
        $array[$i]['idEquipe'] = $colonne->idEquipe;
        $array[$i]['nomEquipe'] = $colonne->nomEquipe;
        $array[$i]['commentaireEquipe'] = $colonne->commentaireEquipe;
        $array[$i]['dateInscriptionEquipe'] = $colonne->dateInscriptionEquipe;
        $array[$i]['paiementEquipe'] = $colonne->paiementEquipe;
        $array[$i]['helloTransactionEquipe'] = $colonne->helloTransactionEquipe;
        $i++;
    }

    return $array;
}

function getInscriptionsRando($dbh)
{
    $requete = "SELECT * FROM rando ORDER BY idRando ASC";
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    return $resultats->fetchAll(PDO::FETCH_ASSOC);
}




function getInscriptions($dbh)
{
    $requete = "SELECT 
        C.libelleCourse, 
        E.*, 
        CONCAT(C1.nomCoureur, ' ', C1.prenomCoureur) AS Coureur1, 
        C1.nomCoureur AS NomCoureur1, 
        C1.prenomCoureur AS PrenomCoureur1,
        C1.sexeCoureur AS SexeCoureur1,
        C1.ddnCoureur AS DdnCoureur1,
        C1.telephoneCoureur AS TelephoneCoureur1,
        C1.villeCoureur AS VilleCoureur1,
        C1.cpCoureur AS CpCoureur1,
        C1.repasCoureur AS RepasCoureur1, 
        C1.emailCoureur AS EmailCoureur1, 
        C1.cadeauCoureur AS CadeauCoureur1, 
        C1.tailleTeeshirtCoureur AS TailleTeeshirtCoureur1,
        C1.certificatCoureur AS CertificatCoureur1,
        C1.certificatValideCoureur AS CertificatValideCoureur1,
        C1.idCoureur as idCoureur1,

        CONCAT(C2.nomCoureur, ' ', C2.prenomCoureur) AS Coureur2, 
        C2.nomCoureur AS NomCoureur2, 
        C2.prenomCoureur AS PrenomCoureur2,
        C2.sexeCoureur AS SexeCoureur2,
        C2.ddnCoureur AS DdnCoureur2,
        C2.telephoneCoureur AS TelephoneCoureur2,
        C2.villeCoureur AS VilleCoureur2,
        C2.cpCoureur AS CpCoureur2,
        C2.repasCoureur AS RepasCoureur2, 
        C2.emailCoureur AS EmailCoureur2,
        C2.cadeauCoureur AS CadeauCoureur2, 
        C2.tailleTeeshirtCoureur AS TailleTeeshirtCoureur2,
        C2.certificatCoureur AS CertificatCoureur2,
        C2.certificatValideCoureur AS CertificatValideCoureur2,
        C2.idCoureur as idCoureur2


    FROM 
        course C
    INNER JOIN 
        equipe E ON E.idCourse = C.idCourse
    LEFT JOIN 
        coureur C1 ON C1.idEquipe = E.idEquipe AND C1.idCoureur = (
            SELECT idCoureur FROM coureur 
            WHERE idEquipe = E.idEquipe 
            ORDER BY idCoureur ASC 
            LIMIT 1 OFFSET 0
        )
    LEFT JOIN 
        coureur C2 ON C2.idEquipe = E.idEquipe AND C2.idCoureur = (
            SELECT idCoureur FROM coureur 
            WHERE idEquipe = E.idEquipe 
            ORDER BY idCoureur ASC 
            LIMIT 1 OFFSET 1
        )
    ORDER BY 
        E.dateInscriptionEquipe";

    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    $totalRepasCarne = 0;
    $totalRepasVege = 0;
    $totalGourmand = 0;
    $totalTextile = 0;
    $xs = 0;
    $s = 0;
    $m = 0;
    $l = 0;
    $xl = 0;
    $masculin = 0;
    $feminin = 0;
    $cepe = 0;
    $girolle = 0;

    foreach ($lignes as $colonne) {
        $array[$i]['idEquipe'] = $colonne->idEquipe;
        $array[$i]['dossardEquipe'] = $colonne->dossardEquipe;
        $array[$i]['recupDossardEquipe'] = $colonne->recupDossardEquipe;
        $array[$i]['idCoureur1'] = $colonne->idCoureur1;
        $array[$i]['idCoureur2'] = $colonne->idCoureur2;
        $array[$i]['montantInscriptionEquipe'] = $colonne->montantInscriptionEquipe;
        $array[$i]['helloOrderidEquipe'] = $colonne->helloOrderidEquipe;
        $array[$i]['idCourse'] = $colonne->idCourse;
        $array[$i]['libelleCourse'] = $colonne->libelleCourse;
        $array[$i]['nomEquipe'] = $colonne->nomEquipe;
        $array[$i]['commentaireEquipe'] = $colonne->commentaireEquipe;
        $array[$i]['dateInscriptionEquipe'] = $colonne->dateInscriptionEquipe;
        $array[$i]['paiementEquipe'] = $colonne->paiementEquipe;
        $array[$i]['helloTransactionEquipe'] = $colonne->helloTransactionEquipe;
        $array[$i]['repasSuppEquipeCarne'] = $colonne->repasSuppEquipeCarne;
        $array[$i]['repasSuppEquipeVege'] = $colonne->repasSuppEquipeVege;
        $array[$i]['RepasCoureur1'] = $colonne->RepasCoureur1;
        $array[$i]['RepasCoureur2'] = $colonne->RepasCoureur2;
        $array[$i]['Coureur1'] = $colonne->Coureur1;
        $array[$i]['Coureur2'] = $colonne->Coureur2;
        $array[$i]['NomCoureur1'] = $colonne->NomCoureur1;
        $array[$i]['PrenomCoureur1'] = $colonne->PrenomCoureur1;
        $array[$i]['NomCoureur2'] = $colonne->NomCoureur2;
        $array[$i]['PrenomCoureur2'] = $colonne->PrenomCoureur2;
        $array[$i]['SexeCoureur1'] = $colonne->SexeCoureur1;
        $array[$i]['SexeCoureur2'] = $colonne->SexeCoureur2;
        $array[$i]['EmailCoureur1'] = $colonne->EmailCoureur1;
        $array[$i]['EmailCoureur2'] = $colonne->EmailCoureur2;
        $array[$i]['CadeauCoureur1'] = $colonne->CadeauCoureur1;
        $array[$i]['CadeauCoureur2'] = $colonne->CadeauCoureur2;
        $array[$i]['TailleTeeshirtCoureur1'] = $colonne->TailleTeeshirtCoureur1;
        $array[$i]['TailleTeeshirtCoureur2'] = $colonne->TailleTeeshirtCoureur2;
        $array[$i]['CertificatCoureur1'] = $colonne->CertificatCoureur1;
        $array[$i]['CertificatCoureur2'] = $colonne->CertificatCoureur2;
        $array[$i]['CertificatValideCoureur1'] = $colonne->CertificatValideCoureur1;
        $array[$i]['CertificatValideCoureur2'] = $colonne->CertificatValideCoureur2;
        $array[$i]['DdnCoureur1'] = $colonne->DdnCoureur1;
        $array[$i]['DdnCoureur2'] = $colonne->DdnCoureur2;
        $array[$i]['VilleCoureur1'] = $colonne->VilleCoureur1;
        $array[$i]['VilleCoureur2'] = $colonne->VilleCoureur2;



        // Comptage des cadeaux
        if ($colonne->CadeauCoureur1 == "G") $totalGourmand++;
        if ($colonne->CadeauCoureur2 == "G") $totalGourmand++;
        if ($colonne->CadeauCoureur1 == "T") $totalTextile++;
        if ($colonne->CadeauCoureur2 == "T") $totalTextile++;

        // Comptage des repas
        if ($colonne->RepasCoureur1 == "Vege") $totalRepasVege++;
        if ($colonne->RepasCoureur1 == "Carne") $totalRepasCarne++;
        if ($colonne->RepasCoureur2 == "Vege") $totalRepasVege++;
        if ($colonne->RepasCoureur2 == "Carne") $totalRepasCarne++;
        if ($colonne->repasSuppEquipeCarne > 0) $totalRepasCarne += $colonne->repasSuppEquipeCarne;
        if ($colonne->repasSuppEquipeVege > 0) $totalRepasVege += $colonne->repasSuppEquipeVege;

        // Comptage des tailles de t-shirts
        if ($colonne->TailleTeeshirtCoureur1 == "XS") $xs++;
        if ($colonne->TailleTeeshirtCoureur1 == "S") $s++;
        if ($colonne->TailleTeeshirtCoureur1 == "M") $m++;
        if ($colonne->TailleTeeshirtCoureur1 == "L") $l++;
        if ($colonne->TailleTeeshirtCoureur1 == "XL") $xl++;

        if ($colonne->TailleTeeshirtCoureur2 == "XS") $xs++;
        if ($colonne->TailleTeeshirtCoureur2 == "S") $s++;
        if ($colonne->TailleTeeshirtCoureur2 == "M") $m++;
        if ($colonne->TailleTeeshirtCoureur2 == "L") $l++;
        if ($colonne->TailleTeeshirtCoureur2 == "XL") $xl++;

        // Comptage des courses
        if ($colonne->idCourse == 1) $cepe++;
        if ($colonne->idCourse == 2) $girolle++;

        // Comptage des sexes
        if ($colonne->SexeCoureur1 == "M") $masculin++;
        if ($colonne->SexeCoureur2 == "M") $masculin++;
        if ($colonne->SexeCoureur1 == "F") $feminin++;
        if ($colonne->SexeCoureur2 == "F") $feminin++;

        $i++;
    }

    return array($array, $totalRepasCarne, $totalRepasVege, $totalGourmand, $totalTextile, $xs, $s, $m, $l, $xl, $cepe, $girolle, $masculin, $feminin);
}



function verifierDossardExiste($dossard, $dbh)
{

    $return = false;
    $requete = "SELECT dossardCoureur from coureur where dossardCoureur=" . $dossard;
    $idCoureur = NULL;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne) {
        $idCoureur = $colonne->dossardCoureur;
    }
    if ($idCoureur != NULL) $return = true;

    return $return;
}

function verifierDoublonPassage($dossard, $dbh)
{

    $return = false;
    $requete = "SELECT idPassage from passage where dossardPassage=" . $dossard;
    $idPassage = NULL;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne) {
        $idPassage = $colonne->idPassage;
    }
    if ($idPassage != NULL) $return = true;

    return $return;
}

function createPassage($dossardPassage, $dbh)
{
    $return = NULL;
    if (verifierDoublonPassage($dossardPassage, $dbh)) {
        $return = "Dossard " . $dossardPassage . " déjà enregistré";
    } elseif (!verifierDossardExiste($dossardPassage, $dbh)) {
        $return = "Dossard " . $dossardPassage . " n'existe pas";
    } else {
        $heurePassage = date('Y-m-d H:i:s');
        $reqInsert = $dbh->prepare("INSERT INTO passage (dossardPassage, heurePassage) VALUES (?,?)");
        $reqInsert->bindParam(1, $dossardPassage);
        $reqInsert->bindParam(2, $heurePassage);
        $return = $reqInsert->execute();
        $return = "Dossard " . $dossardPassage . "=> arrivée à : " . $heurePassage;
    }

    return $return;
}

function getHeureDepartCourse($idCourse, $dbh)
{
    $requete = "SELECT heureDepartCourse from course where idCourse=" . $idCourse;
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne) {
        $heureDepartCourse = $colonne->heureDepartCourse;
    }

    return $heureDepartCourse;
}

function getClassement($idCourse, $categorie, $dbh)
{
    $whereCategorie = NULL;
    if ($categorie == "ALL" || $categorie == NULL || $categorie == "") {
        $whereCategorie = NULL;
    } else {
        $whereCategorie = " and C.categorieCoureur='" . $categorie . "' ";
    }
    $requete = "SELECT P.idPassage, P.heurePassage, C.idCoureur, C.libelleCoureur, C.dossardCoureur, C.equipeCoureur, C.categorieCoureur , CO.heureDepartCourse,
    time_format(timediff(P.heurePassage,CO.heureDepartCourse),'%H:%i:%s') as tempsCoureur
    from coureur C inner join passage P on P.dossardPassage=C.dossardCoureur 
    inner join course CO on CO.idCourse=C.idCourse WHERE C.idCourse=" . $idCourse . " " . $whereCategorie . " order by tempsCoureur";

    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($lignes as $colonne) {
        $array[$i]['idPassage'] = $colonne->idPassage;
        $array[$i]['heurePassage'] = $colonne->heurePassage;
        $array[$i]['idCoureur'] = $colonne->idCoureur;
        $array[$i]['libelleCoureur'] = $colonne->libelleCoureur;
        $array[$i]['equipeCoureur'] = $colonne->equipeCoureur;
        $array[$i]['dossardCoureur'] = $colonne->dossardCoureur;
        $array[$i]['tempsCoureur'] = $colonne->tempsCoureur;
        $array[$i]['categorieCoureur'] = $colonne->categorieCoureur;
        $i++;
    }

    return $array;
}

function getPassages($idCourse, $dbh)
{
    $requete = "
    SELECT P.idPassage, P.dossardPassage, P.heurePassage , C.libelleCoureur, C.equipeCoureur
    from passage P inner join coureur C on C.dossardCoureur=P.dossardPassage
    WHERE C.idCourse=" . $idCourse;

    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($lignes as $colonne) {
        $array[$i]['idPassage'] = $colonne->idPassage;
        $array[$i]['dossardPassage'] = $colonne->dossardPassage;
        $array[$i]['libelleCoureur'] = $colonne->libelleCoureur;
        $array[$i]['equipeCoureur'] = $colonne->equipeCoureur;
        $array[$i]['heurePassage'] = $colonne->heurePassage;
        $i++;
    }

    return $array;
}

function ecartPremierTemps($tempsPassage, $tempsPremier)
{
    //****VARIABLES FINALES*********
    $jours = NULL;
    $heures = NULL;
    $minutes = NULL;
    $secondes = NULL .

        //****DECOUPAGE DES TEMPS DES COUREURS*********
        //****Coureur*****
        $heurePa = explode(":", $tempsPassage);
    $tempsSecondesPassage = intval($heurePa[0]) * 3600 + intval($heurePa[1]) * 60 + intval($heurePa[2]);
    //****Premier*****
    $heurePr = explode(":", $tempsPremier);
    $tempsSecondesPremier = intval($heurePr[0]) * 3600 + intval($heurePr[1]) * 60 + intval($heurePr[2]);

    //****CALCUL ECART EN SECONDES*********
    $seconds = $tempsSecondesPassage - $tempsSecondesPremier;

    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    if ($dtF->diff($dtT)->format('%a') != 0) $jours = $dtF->diff($dtT)->format('%a') . "j ";
    return $jours . $dtF->diff($dtT)->format('%H:%I:%S');
}


function getAffichageQuoiAccueil($dbh)
{
    $return = NULL;
    $requete = "SELECT * FROM course";
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($lignes as $colonne) {
        $return .= '<a href="detailcourse.php?idCourse=' . $colonne->idCourse . '">' . $colonne->libelleCourse . '</a> (' . $colonne->distanceCourse . 'km), ';
        $i++;
    }

    return substr($return, 0, -2);
}

function getAffichageQuandAccueil($dbh)
{
    $return = NULL;
    $requete = "SELECT * FROM course";
    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($lignes as $colonne) {
        $heureDepart = (new DateTime($colonne->heureDepartTheoriqueCourse))->format('G\hi');
        $return .= '<a href="detailcourse.php?idCourse=' . $colonne->idCourse . '">' . $colonne->libelleCourse . '</a> (Départ ' . $heureDepart . ')<br>';
        $i++;
    }

    return $return;
}




function earlyAdoptersAvailable($idCourse, $dbh)
{
    $isEarlyAdopters = false;
    $nbEquipes = sizeof(getEquipes($idCourse, $dbh));

    $nbEarlyAdoptersCourse = 0;

    $requete = "SELECT nbEarlyAdoptersCourse FROM course where idCourse=" . $idCourse;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne) {
        $nbEarlyAdoptersCourse = $colonne->nbEarlyAdoptersCourse;
    }

    if ($nbEquipes <= $nbEarlyAdoptersCourse) $isEarlyAdopters = true;

    return $isEarlyAdopters;
}

function afficherTarif($idCourse, $dbh)
{
    global $montantInscriptionRandoAdulte;
    global $montantInscriptionRandoEnfant;
    global $idCourseRando;
    $modeRando = false;

    if ($idCourseRando == $idCourse) $modeRando = true;


    if ($idCourse != NULL || $idCourse != "") {
        $stmt = $dbh->prepare("SELECT idCourse, reductionEarlyAdoptersCourse, montantInscriptionCourse, nbEarlyAdoptersCourse, libelleCourse FROM course WHERE idCourse = ?");
        $stmt->execute([$idCourse]);
    } else {
        $stmt = $dbh->query("SELECT idCourse, reductionEarlyAdoptersCourse, montantInscriptionCourse, nbEarlyAdoptersCourse, libelleCourse FROM course where idCourse<3");
    }

    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $affichage = '';

    foreach ($resultats as $course) {
        // Utilisation de la fonction pour vérifier si la réduction est toujours disponible
        $earlyAdoptersStillAvailable = earlyAdoptersAvailable($course['idCourse'], $dbh);

        if ($earlyAdoptersStillAvailable) {
            $prixReduced = $course['montantInscriptionCourse'] - $course['reductionEarlyAdoptersCourse'];
            $affichage .= "Tarif pour le duo " . $course['libelleCourse'] . " : <span class=\"rouge\"> " . $prixReduced . "€ </span>" . " pour les " . $course['nbEarlyAdoptersCourse'] . " premiers duos inscrits, puis <span class=\"rouge\">" . $course['montantInscriptionCourse'] . "€</span> ensuite<br/>";

            $affichage .= "<span class=\"promotion\">Inscrivez vous vite, il ne reste que quelques dossards à tarif réduit !</span>";
        } else {
            // Afficher les tarifs sans réduction
            $affichage .= "Tarif pour le duo " . $course['libelleCourse'] . " : <span class=\"rouge\">" . $course['montantInscriptionCourse'] . "€ </span>" .  "<br/>";
        }
    }

    if ($modeRando) $affichage = "Tarif Rando : <span class=\"rouge\"> " . $montantInscriptionRandoAdulte . "</span>€ pour un adulte / <span class=\"rouge\">" . $montantInscriptionRandoEnfant . "</span>€ pour un enfant";

    return $affichage;
}



function getCourses($idCourse, $dbh)
{
    $whereIDCOURSE = NULL;
    if ($idCourse != "") $whereIDCOURSE = " where idCourse=" . $idCourse;
    $requete = "SELECT * FROM course " . $whereIDCOURSE;

    $array = array();
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    $i = 0;
    foreach ($lignes as $colonne) {
        $array[$i]['idCourse'] = $colonne->idCourse;
        $array[$i]['libelleCourse'] = $colonne->libelleCourse;
        $array[$i]['distanceCourse'] = $colonne->distanceCourse;
        $array[$i]['dplusCourse'] = $colonne->dplusCourse;
        $array[$i]['heureDepartCourse'] = $colonne->heureDepartCourse;
        $array[$i]['heureDepartTheoriqueCourse'] = $colonne->heureDepartTheoriqueCourse;
        $array[$i]['lienParcoursCourse'] = $colonne->lienParcoursCourse;
        $array[$i]['descriptionCourse'] = $colonne->descriptionCourse;
        $array[$i]['tempsMaxCourse'] = $colonne->tempsMaxCourse;
        $array[$i]['nbRavCourse'] = $colonne->nbRavCourse;
        $array[$i]['nbMaxCoureursCourse'] = $colonne->nbMaxCoureursCourse;
        $array[$i]['montantInscriptionCourse'] = $colonne->montantInscriptionCourse;
        $array[$i]['reductionEarlyAdoptersCourse'] = $colonne->reductionEarlyAdoptersCourse;
        $array[$i]['montantRepasCourse'] = $colonne->montantRepasCourse;
        $i++;
    }

    if ($idCourse != "") $array = $array[0];

    return $array;
}

function getInfosUtilesMailArrivee($idPassage, $dbh)
{
    $infosUtilesMail = array();
    $mails = NULL;
    $nomEquipe = NULL;
    $nomCoureurs = NULL;
    $heureArrivee = NULL;

    //********RECUPERATION DES MAILS************
    $requete = "
        SELECT mailsCoureur, equipeCoureur, libelleCoureur, P.heurePassage
        from coureur C inner join passage P on C.dossardCoureur=P.dossardPassage
        where P.idPassage=" . $idPassage;

    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne) {
        $mails = explode("/", $colonne->mailsCoureur);
        $nomEquipe = $colonne->equipeCoureur;
        $nomCoureurs = $colonne->libelleCoureur;
        $heureArrivee = $colonne->heurePassage;
    }

    $infosUtilesMail['mails'] = $mails;
    $infosUtilesMail['nomEquipe'] = $nomEquipe;
    $infosUtilesMail['nomCoureurs'] = $nomCoureurs;
    $infosUtilesMail['heureArrivee'] = $heureArrivee;


    //********RECUPERATION ID COURSE************
    $requete = "  select C.idCourse from passage P 
                inner join coureur C on C.dossardCoureur=P.dossardPassage
                inner join course CO on CO.idCourse=C.idCourse
                where idPassage=" . $idPassage;
    $idCourse = NULL;
    $resultats = $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query($requete);
    $lignes = $resultats->fetchAll(PDO::FETCH_OBJ);
    foreach ($lignes as $colonne) {
        $idCourse = $colonne->idCourse;
    }
    $infosUtilesMail['idCourse'] = $idCourse;

    return $infosUtilesMail;
}

function updatePassage($idPassage, $dbh)
{
    $infosUtilesMail = getInfosUtilesMailArrivee($idPassage, $dbh);

    $dateTraitement = date('Y-m-d H:i:s');
    $array = array();
    $reqUpdate = $dbh->prepare("UPDATE passage set videoPassage=? where idPassage=?");
    $reqUpdate->bindParam(1, $dateTraitement);
    $reqUpdate->bindParam(2, $idPassage);
    $etatExecution = $reqUpdate->execute();

    if ($etatExecution) {
        $array['idPassage'] = $idPassage;
        $array['dateTraitement'] = $dateTraitement;
        $array['return'] = true;
        $array['returnDetail'] = "La mise à jour a bien été effectuée";
    } else {
        $array['idPassage'] = $idPassage;
        $array['dateTraitement'] = NULL;
        $array['return'] = false;
        $array['returnDetail'] = "Erreur dans la mise à jour en base de donnée";
    }

    //**************ENVOI MAIL COUREURS********************************
    //$mails=$infosCoursesUtiles[0];
    $titre = "Bien joué " . $infosUtilesMail['nomCoureurs'];

    $contenu = "Bravo, t'es arrivé.e, avec ton poto, et c'est bien ! Maintenant retrouve ton résultat sur le site et regarde dès maintenant ta vidéo d'arrivée et les photos du week end en cliquant ci dessous";

    $libelleBouton = "VOIR MA VIDEO";
    $lienBouton = "https://traildeschampignons.sotrail.fr/videosArrivee/" . $idPassage . ".mp4";

    $libelleBouton2 = "VOIR LES RESULTATS";
    $lienBouton2 = "https://traildeschampignons.sotrail.fr/resultats.php?idCourse=" . $infosUtilesMail['idCourse'];

    $libelleBouton3 = "VOIR LES PHOTOS";
    $lienBouton3 = "https://traildeschampignons.sotrail.fr/galerie";


    $template = 7;
    $sujet = "résultat Trail des champignons équipe " . $infosUtilesMail['nomEquipe'];
    foreach ($infosUtilesMail['mails'] as $mail) {
        sendMail($mail, $titre, $contenu, $libelleBouton, $lienBouton,  $libelleBouton2, $lienBouton2, $libelleBouton3, $lienBouton3, $template, $sujet);
    }


    return $array;
}

function cleanString($chaine, $maj)
{
    if (trim($chaine) == '' || $chaine == NULL) {
        return NULL;
    } else {
        //SUPPRIMER TOUT ACCENT OU CARACTERE CHELOU SUR UNE LETTRE
        $a = array('À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð', 'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'ß', 'à', 'á', 'â', 'ã', 'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'ÿ', 'Ā', 'ā', 'Ă', 'ă', 'Ą', 'ą', 'Ć', 'ć', 'Ĉ', 'ĉ', 'Ċ', 'ċ', 'Č', 'č', 'Ď', 'ď', 'Đ', 'đ', 'Ē', 'ē', 'Ĕ', 'ĕ', 'Ė', 'ė', 'Ę', 'ę', 'Ě', 'ě', 'Ĝ', 'ĝ', 'Ğ', 'ğ', 'Ġ', 'ġ', 'Ģ', 'ģ', 'Ĥ', 'ĥ', 'Ħ', 'ħ', 'Ĩ', 'ĩ', 'Ī', 'ī', 'Ĭ', 'ĭ', 'Į', 'į', 'İ', 'ı', 'Ĳ', 'ĳ', 'Ĵ', 'ĵ', 'Ķ', 'ķ', 'Ĺ', 'ĺ', 'Ļ', 'ļ', 'Ľ', 'ľ', 'Ŀ', 'ŀ', 'Ł', 'ł', 'Ń', 'ń', 'Ņ', 'ņ', 'Ň', 'ň', 'ŉ', 'Ō', 'ō', 'Ŏ', 'ŏ', 'Ő', 'ő', 'Œ', 'œ', 'Ŕ', 'ŕ', 'Ŗ', 'ŗ', 'Ř', 'ř', 'Ś', 'ś', 'Ŝ', 'ŝ', 'Ş', 'ş', 'Š', 'š', 'Ţ', 'ţ', 'Ť', 'ť', 'Ŧ', 'ŧ', 'Ũ', 'ũ', 'Ū', 'ū', 'Ŭ', 'ŭ', 'Ů', 'ů', 'Ű', 'ű', 'Ų', 'ų', 'Ŵ', 'ŵ', 'Ŷ', 'ŷ', 'Ÿ', 'Ź', 'ź', 'Ż', 'ż', 'Ž', 'ž', 'ſ', 'ƒ', 'Ơ', 'ơ', 'Ư', 'ư', 'Ǎ', 'ǎ', 'Ǐ', 'ǐ', 'Ǒ', 'ǒ', 'Ǔ', 'ǔ', 'Ǖ', 'ǖ', 'Ǘ', 'ǘ', 'Ǚ', 'ǚ', 'Ǜ', 'ǜ', 'Ǻ', 'ǻ', 'Ǽ', 'ǽ', 'Ǿ', 'ǿ');
        $b = array('A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
        $chaine = str_replace($a, $b, $chaine);

        //ON MET TOUT EN MINUSCULE
        $chaine = strtolower($chaine);

        //ON NE GARDE QUE DES LETTRES DE A 0 Z
        $email = NULL;
        if ($maj == 2) $regex = '0-9@\.\-_';
        if ($maj == 3) $regex = '0-9\'';
        $chaine = preg_replace("/[^a-z" . $regex . "]+/", " ", $chaine);

        //SUPPRESSION ESPACES EN TROP
        $chaine = trim($chaine);
        if ($maj == 1 || $maj == 3) {
            $chaine = strtoupper($chaine);
        } else {
            $chaine = ucwords($chaine);
        }
        if ($maj == 2) {
            $chaine = strtolower($chaine);
        }
        return $chaine;
    }
}
