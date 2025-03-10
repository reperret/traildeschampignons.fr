<?php 
try 
{

include 'api/bdd.php';
include 'api/fonctions.php';
    
$totalEquipes = count(getEquipes(NULL, $dbh));
$maxEquipes = 100;

if ($totalEquipes >= $maxEquipes) {
    header('Location: inscriptionsFermees.php?j=c');
    exit();
}

$courses=getCourses(NULL,$dbh);
$manqueParametres=NULL;


    
if(
        isset($_POST['idCourse']) && $_POST['idCourse']!='' 
    &&  isset($_POST['nomCoureur1']) && $_POST['nomCoureur1']!='' 
    &&  isset($_POST['prenomCoureur1']) && $_POST['prenomCoureur1']!='' 
    &&  isset($_POST['emailCoureur1']) && $_POST['emailCoureur1']!='' 
    &&  isset($_POST['telephoneCoureur1']) && $_POST['telephoneCoureur1']!=''
    &&  isset($_POST['ddnCoureur1']) && $_POST['ddnCoureur1']!=''
    &&  isset($_POST['sexeCoureur1']) && $_POST['sexeCoureur1']!=''
    &&  isset($_POST['adresseCoureur1']) && $_POST['adresseCoureur1']!=''
    &&  isset($_POST['cpCoureur1']) && $_POST['cpCoureur1']!=''
    &&  isset($_POST['villeCoureur1']) && $_POST['villeCoureur1']!=''
    &&  isset($_POST['cadeauCoureur1']) && $_POST['cadeauCoureur1']!=''
    &&  isset($_POST['tailleTeeshirtCoureur1']) && $_POST['tailleTeeshirtCoureur1']!=''
   // &&  isset($_POST['certificatCoureur1']) && $_POST['certificatCoureur1']!=''
    
    &&  isset($_POST['nomCoureur2']) && $_POST['nomCoureur2']!='' 
    &&  isset($_POST['prenomCoureur2']) && $_POST['prenomCoureur2']!='' 
    &&  isset($_POST['emailCoureur2']) && $_POST['emailCoureur2']!='' 
    &&  isset($_POST['telephoneCoureur2']) && $_POST['telephoneCoureur2']!=''
    &&  isset($_POST['ddnCoureur2']) && $_POST['ddnCoureur2']!=''
    &&  isset($_POST['sexeCoureur2']) && $_POST['sexeCoureur2']!=''
    &&  isset($_POST['adresseCoureur2']) && $_POST['adresseCoureur2']!=''
    &&  isset($_POST['cpCoureur2']) && $_POST['cpCoureur2']!=''
    &&  isset($_POST['villeCoureur2']) && $_POST['villeCoureur2']!=''
    &&  isset($_POST['cadeauCoureur2']) && $_POST['cadeauCoureur2']!=''
    &&  isset($_POST['tailleTeeshirtCoureur2']) && $_POST['tailleTeeshirtCoureur2']!=''
   // &&  isset($_POST['certificatCoureur2']) && $_POST['certificatCoureur2']!=''
    
    &&  isset($_POST['nomEquipe']) && $_POST['nomEquipe']!=''
    &&  isset($_POST['accepteReglement']) && $_POST['accepteReglement'] == 'accepte'
)
{





    
    //********** CREATE EQUIPE***********
    $idEquipe=createEquipe($_POST['idCourse'],trim($_POST['nomEquipe']),trim($_POST['commentaireEquipe']) ,trim($_POST['repasSuppEquipeCarne']),trim($_POST['repasSuppEquipeVege']), $dbh);
    
    //********** CREATE COUREUR 1***********
    $idCoureur1=createCoureur($idEquipe,$_POST,1,$dbh);
    enregistrerCertificat($idEquipe, $idCoureur1, 1, $dbh);

    //********** CREATE COUREUR 2***********
    $idCoureur2=createCoureur($idEquipe,$_POST,2,$dbh);
    enregistrerCertificat($idEquipe, $idCoureur2, 2, $dbh);





    
    //****************************PAIEMENT HELLO ASSO**************************************
    //*************DEFINITION DES SECRETS*******************************
    $client_id=urlencode($clientIdHelloAsso);
    $client_secret=urlencode($clientSecretHelloAsso);
            
    $grant_type="client_credentials";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlTokenHelloAsso);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=".$grant_type."&client_id=".$client_id."&client_secret=".$client_secret);
    curl_setopt($ch, CURLOPT_POST, 1);
    $headers = array();
    $headers[] = 'Content-Type: application/x-www-form-urlencoded';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch))
    {
    echo 'Error:' . curl_error($ch);
    }
    curl_close ($ch);

    $infos=json_decode($result,true);
    $accessToken=$infos['access_token'];

    //************PREPARATION DES INFOS JSON PAIEMENT***************************
    
    //****CALCUL MONTANT INSCRIPTION*****
    $codePromo = isset($_POST['codePromotion']) ? trim($_POST['codePromotion']) : null;
    list($montantInscription, $reduction) = getMontantTotalInscription("course", $_POST, $dbh, $codePromo);
    
    // Vérifier si un code promo a été appliqué et mettre à jour les informations de l'équipe
    if ($codePromo) 
    {
        $stmt = $dbh->prepare("UPDATE equipe SET codePromoEquipe = ?, reductionEquipe = ? WHERE idEquipe = ?");
        $stmt->execute([$codePromo, $reduction, $idEquipe]);
    }


    //****ENREGISTRER MONTANT INSCRIPTION EQUIPE*****
    $stmt = $dbh->prepare("UPDATE equipe SET montantInscriptionEquipe = ? WHERE idEquipe = ?");
    $stmt->execute([$montantInscription, $idEquipe]);


    // Vérification si code promo total
    if($montantInscription<=0)
    {
        header('Location: confirmation.php?cp=full');
        exit(); 
    }

    //$montantInscription=1; //en €
    $montantInscription=intval($montantInscription)*100; // en centimes d'€
    $idPaiement=NULL;

    $prenom=$_POST['prenomCoureur1'];
    $nom=$_POST['nomCoureur1'];
    $email=$_POST['emailCoureur1'];
    $adresse=$_POST['adresseCoureur1'];
    $ville=$_POST['villeCoureur1'];
    $code_postal=$_POST['cpCoureur1'];
    $emails=$_POST['emailCoureur1'].";".$_POST['emailCoureur2'];
    $ddn=$_POST['ddnCoureur1'];
    $nomEquipe=$_POST['nomEquipe'];

    $jsonClient=array();
    $jsonClient['totalAmount']=$montantInscription;
    $jsonClient['initialAmount']=$montantInscription;

    $jsonClient['itemName']="Paiement équipe ".$idEquipe;
    $jsonClient['backUrl']=$domaine.'inscriptions.php';
    $jsonClient['errorUrl']=$domaine.'confirmation.php?type=error';
    $jsonClient['returnUrl']=$domaine.'confirmation.php?type=return';
    $jsonClient['containsDonation']=false;

    $payer=array();
    $payer['firstName']=$prenom;
    $payer['lastName']=$nom;
    $payer['email']=$email;
    $payer['dateOfBirth']=$ddn;
    $payer['address']=$adresse;
    $payer['city']=$ville;
    $payer['zipCode']=$code_postal;
    $jsonClient['payer']=$payer;

    $metadata=array();
    $metadata['idEquipe']=$idEquipe;
    $metadata['nomEquipe']=$nomEquipe;
    $metadata['emailsEquipe']=$emails;

    $jsonClient['metadata']=$metadata;


    //*************CREATION D'UN LIEN DE PAIEMENT*******************************
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $checkoutIntentUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>json_encode($jsonClient,true),
    CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer '.$accessToken,
    'Content-Type: application/json'
    ),
    ));

    $response = curl_exec($curl);

    print_r($response);
    curl_close($curl);
    $lien=json_decode($response,true);
    //echo $lien['redirectUrl'];
    //echo $_POST['ddnCoureur1'];
    header('Location: '.$lien['redirectUrl']);
    exit(); 
    
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST')
{
    $manqueParametres="Veuillez remplir tous les champs obligatoire (*) ! " ;
}
    
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>TDC - S'INSCRIRE</title>

    <!-- Favicons-->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/apple-touch-icon-144x144-precomposed.png">

    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">

    <!-- BASE CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/menu.css" rel="stylesheet">
    <link href="css/vendors.css" rel="stylesheet">
    <link href="css/icon_fonts/css/all_icons_min.css" rel="stylesheet">

    <!-- SPECIFIC CSS -->
    <link href="css/tables.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">

    <!-- Modernizr -->
    <script src="js/modernizr_tables.js"></script>
    <script>
        // Fonction qui gère la redirection
        function redirectUser() {
            var select = document.getElementById("idCourse"); // Obtention de l'élément select
            var value = select.options[select.selectedIndex].value; // Obtention de la valeur sélectionnée

            if (value == "<?php echo $idCourseRando;?>") { // Vérifier si une valeur est sélectionnée
                window.location.href = "inscriptionRando.php"; // Redirection vers l'URL sélectionnée
            }
        }

        window.addEventListener("pageshow", function(event) {
            if (event.persisted) {
                // La page est chargée à partir du cache, probablement via le bouton précédent
                window.location.reload(true); // Force le rechargement de la page depuis le serveur
            }
        });

    </script>


</head>

<body>

    <div class="layer"></div>
    <!-- Mobile menu overlay mask -->

    <div id="preloader">
        <div data-loader="circle-side"></div>
    </div>
    <!-- End Preload -->

    <?php include "header.php";?>

    <main>
        <div class="image-container-values">
            <div class="overlay-values">
                <p class="title-big-brown">INSCRIPTIONS</p>

            </div>
        </div>


        <div class="container margin_60_35">
            <center>

                <h2>2 novembre 2024</h2>
                <h3>Saint André-en-Vivarais (07690)</h3>
                <?php 
                if($manqueParametres!=NULL)
                {
                ?>
                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        <?php if ($manqueParametres != NULL) { ?>
                        alert("Veuillez remplir tous les champs obligatoire (*) !");
                        <?php } ?>
                    });

                </script>


                <div class="alerte-rouge-clair">
                    <?php echo $manqueParametres;?>
                </div>
                <?php
                }
                ?>
                <br>

            </center>

            <form action="inscriptions.php" method="POST" enctype="multipart/form-data" id="monFormulaire">

                <div class="row justify-content-center">

                    <div class="col-lg-8">
                        <div class="box_general_3 write_review">




                            <div class="title course-title">Course</div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <select class="form-control" name="idCourse" id="idCourse" onchange="redirectUser()">
                                            <?php
                $first = true; // Assurez-vous d'initialiser $first
                foreach ($courses as $course) 
                {
                    $selected = '';
                    if (isset($_POST['idCourse']) && $course['idCourse'] == $_POST['idCourse']) 
                    {
                        $selected = ' selected';
                    } elseif (!isset($_POST['idCourse']) && $first) 
                    {
                        // Si aucune course n'est encore sélectionnée via POST et que c'est le premier élément
                        $selected = ' selected';
                        $first = false;
                    }

                    // Ajout de l'attribut data-montant pour stocker le montant d'inscription
                    echo '<option value="' . $course['idCourse'] . '"' . $selected . '>';
                    echo htmlspecialchars($course['libelleCourse'] . " " . $course['distanceCourse'] . "km");
                    echo '</option>';
                }
                ?>
                                        </select>
                                    </div>
                                    <label><?php echo afficherTarif(NULL, $dbh);?>

                                    </label>
                                </div>
                            </div>





                            <div class="title course-title">Coureur 1</div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="colorChampsInscription">Nom*</label>
                                        <input type="text" id="nomCoureur1" name="nomCoureur1" class="form-control" value="<?php echo $_POST['nomCoureur1'];?>" required>

                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Prénom*</label>
                                        <input type="text" id="prenomCoureur1" name="prenomCoureur1" class="form-control" value="<?php echo $_POST['prenomCoureur1'];?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Email*</label>
                                        <input type="email" id="emailCoureur1" name="emailCoureur1" class="form-control" value="<?php echo $_POST['emailCoureur1'];?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Téléphone*</label>
                                        <input type="text" id="telephoneCoureur1" name="telephoneCoureur1" class="form-control" value="<?php echo $_POST['telephoneCoureur1'];?>" required>

                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Date naissance*</label>
                                        <input type="date" id="ddnCoureur1" name="ddnCoureur1" class="form-control" value="<?php echo $_POST['ddnCoureur1'];?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">

                                    <div class="form-group">
                                        <label class="d-block">Sexe*</label>


                                        <div class="radio-container">
                                            <input type="radio" name="sexeCoureur1" id="sexeMasculin" class="radio-input" value="M" <?php if(isset($_POST['sexeCoureur1']) && $_POST['sexeCoureur1']=="M") echo "checked"; ?>>
                                            <label for="sexeMasculin">Masculin</label>
                                        </div>
                                        <div class="radio-container">
                                            <input type="radio" name="sexeCoureur1" id="sexeFeminin" class="radio-input" value="F" <?php if(isset($_POST['sexeCoureur1']) && $_POST['sexeCoureur1']=="F") echo "checked"; ?>>
                                            <label for="sexeFeminin">Féminin</label>
                                        </div>



                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label>Adresse*</label>
                                <input type="text" id="adresseCoureur1" name="adresseCoureur1" class="form-control" value="<?php echo $_POST['adresseCoureur1'];?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Code postal*</label>
                                        <input type="text" id="cpCoureur1" name="cpCoureur1" class="form-control" value="<?php echo $_POST['cpCoureur1'];?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Ville*</label>
                                        <input type="text" id="villeCoureur1" name="villeCoureur1" class="form-control" value="<?php echo $_POST['villeCoureur1'];?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Club</label>
                                        <input type="text" id="clubCoureur1" name="clubCoureur1" class="form-control" value="<?php echo $_POST['clubCoureur1'];?>">
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Contact d'urgence</label>
                                        <input type="text" id="urgenceCoureur1" name="urgenceCoureur1" class="form-control" value="<?php echo $_POST['urgenceCoureur1'];?>" placeholder="nom, téléphone...">
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="d-block">Choix du cadeau*</label>

                                        <select name="cadeauCoureur1" class="form-control">
                                            <option value="" <?php if($_POST['cadeauCoureur1']=="") echo " selected";?> disabled>choisir...</option>
                                            <option value="G" <?php if($_POST['cadeauCoureur1']=="G") echo " selected";?>>Gourmand</option>
                                            <option value="T" disabled <?php if($_POST['cadeauCoureur1']=="T") echo " selected";?>>Textile (épuisé)</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="d-block">Taille de vêtement*</label>
                                        <select name="tailleTeeshirtCoureur1" class="form-control">
                                            <option value="" <?php if($_POST['tailleTeeshirtCoureur1']=="") echo " selected";?> disabled>choisir...</option>
                                            <option value="XS">XS</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">


                                    <div class="form-group">
                                        <label class="d-block">Option Repas à <?php echo $montantRepasCourse;?>€ </label>

                                        <div class="radio-container">
                                            <input type="radio" name="repasCoureur1" id="repasCoureur1" class="radio-input" value="Non" <?php if(isset($_POST['repasCoureur1']) && $_POST['repasCoureur1']=="Non") echo "checked"; ?> checked>
                                            <label for="repasCoureur1">Pas de repas</label>
                                        </div>

                                        <div class="radio-container">
                                            <input type="radio" name="repasCoureur1" id="repasCoureur1" class="radio-input" value="Vege" <?php if(isset($_POST['repasCoureur1']) && $_POST['repasCoureur1']=="Vege") echo "checked"; ?>>
                                            <label for="repasCoureur1">Végétarien</label>
                                        </div>

                                        <div class="radio-container">
                                            <input type="radio" name="repasCoureur1" id="repasCoureur1" class="radio-input" value="Carne" <?php if(isset($_POST['repasCoureur1']) && $_POST['repasCoureur1']=="Carne") echo "checked"; ?>>
                                            <label for="repasCoureur1">Carné</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Allergies</label>
                                        <input type="text" id="allergiesCoureur1" name="allergiesCoureur1" class="form-control" value="<?php echo $_POST['allergiesCoureur1'];?>">

                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Numéro fidélité (jesuisuncoureur.com)</label>
                                        <input type="text" id="numfideliteCoureur1" name="numfideliteCoureur1" class="form-control" value="<?php echo $_POST['numfideliteCoureur1'];?>">

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>N° licence FFA (ou pass’ J’aime courir FFA)</label>
                                        <input type="text" id="licenceCoureur1" name="licenceCoureur1" class="form-control" value="<?php echo $_POST['licenceCoureur1'];?>">

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Certificat médical ou PPS</label><br>

                                        <div class="fileupload"><input type="file" name="certificatCoureur1" accept="*/*"></div>
                                        <span class="mentionInscriptionRefus">Vous pouvez fournir un <a href="https://pps.athle.fr/" target="_blank">PPS</a> ou Certifcat médical de non-contre-indication à la pratique de l'athlétisme en compétition ou
                                            du sport en compétition ou de la course à pied en compétition</span>
                                    </div>
                                </div>
                            </div>
                            
                            
                         

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Moyen de locomotion</label><br>

                                        <select name="locomotionCoureur2" class="form-control">
                                            <option value="" <?php if($_POST['locomotionCoureur2']=="") echo " selected";?> disabled>choisir...</option>
                                            <option value="VELO" <?php if($_POST['locomotionCoureur2']=="VELO") echo " selected";?>>Vélo</option>
                                            <option value="TRAIN" <?php if($_POST['locomotionCoureur2']=="TRAIN") echo " selected";?>>Train</option>
                                            <option value="VOITURE" <?php if($_POST['locomotionCoureur2']=="VOITURE") echo " selected";?>>Voiture covoiturage</option>
                                            <option value="COVOIT" <?php if($_POST['locomotionCoureur2']=="COVOIT") echo " selected";?>>Voiture solo</option>
                                            <option value="AUTRE" <?php if($_POST['locomotionCoureur2']=="AUTRE") echo " selected";?>>Autre</option>
                                        </select>

                                    </div>
                                </div>
                            </div>


                            <br><br>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <div class="checkboxes add_bottom_30 add_top_15">
                                            <label class="container_check">

                                                <span class="mentionInscriptionRefus"> En cochant cette case, je demande expressément qu'il ne soit pas fait mention de mon nom dans les résultats paraissant sur les sites Internet de l'organisation ni sur ceux des ses éventueIs prestataires et/ou partenaires. Pour toute opposition à la publication de mes résultats sur le site de la FFA, je reconnais devoir expressément l’en informer à l’adresse suivante : dpo@athle.fr
                                                </span>


                                                <input name="refusResultatsCoureur1" type="checkbox" value="refusResultatsCoureur1" <?php if (isset($_POST['refusResultatsCoureur1']) && $_POST['refusResultatsCoureur1'] == 'refusResultatsCoureur1') echo 'checked'; ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>


                                    </div>
                                </div>


                            </div>





                            <hr>





                            <div class="title course-title">Coureur 2</div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="colorChampsInscription">Nom*</label>
                                        <input type="text" id="nomCoureur2" name="nomCoureur2" class="form-control" value="<?php echo $_POST['nomCoureur2'];?>" required>

                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Prénom*</label>
                                        <input type="text" id="prenomCoureur2" name="prenomCoureur2" class="form-control" value="<?php echo $_POST['prenomCoureur2'];?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Email*</label>
                                        <input type="email" id="emailCoureur2" name="emailCoureur2" class="form-control" value="<?php echo $_POST['emailCoureur2'];?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Téléphone*</label>
                                        <input type="text" id="telephoneCoureur2" name="telephoneCoureur2" class="form-control" value="<?php echo $_POST['telephoneCoureur2'];?>" required>

                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Date naissance*</label>
                                        <input type="date" id="ddnCoureur2" name="ddnCoureur2" class="form-control" value="<?php echo $_POST['ddnCoureur2'];?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">

                                    <div class="form-group">
                                        <label class="d-block">Sexe*</label>


                                        <div class="radio-container">
                                            <input type="radio" name="sexeCoureur2" id="sexeMasculin" class="radio-input" value="M" <?php if(isset($_POST['sexeCoureur2']) && $_POST['sexeCoureur2']=="M") echo "checked"; ?>>
                                            <label for="sexeMasculin">Masculin</label>
                                        </div>
                                        <div class="radio-container">
                                            <input type="radio" name="sexeCoureur2" id="sexeFeminin" class="radio-input" value="F" <?php if(isset($_POST['sexeCoureur2']) && $_POST['sexeCoureur2']=="F") echo "checked"; ?>>
                                            <label for="sexeFeminin">Féminin</label>
                                        </div>



                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <label>Adresse*</label>
                                <input type="text" id="adresseCoureur2" name="adresseCoureur2" class="form-control" value="<?php echo $_POST['adresseCoureur2'];?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Code postal*</label>
                                        <input type="text" id="cpCoureur2" name="cpCoureur2" class="form-control" value="<?php echo $_POST['cpCoureur2'];?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Ville*</label>
                                        <input type="text" id="villeCoureur2" name="villeCoureur2" class="form-control" value="<?php echo $_POST['villeCoureur2'];?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Club FFA</label>
                                        <input type="text" id="clubCoureur2" name="clubCoureur2" class="form-control" value="<?php echo $_POST['clubCoureur2'];?>">
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Contact d'urgence</label>
                                        <input type="text" id="urgenceCoureur2" name="urgenceCoureur2" class="form-control" value="<?php echo $_POST['urgenceCoureur2'];?>" placeholder="nom, téléphone...">
                                    </div>
                                </div>


                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="d-block">Choix du cadeau*</label>

                                        <select name="cadeauCoureur2" class="form-control">
                                            <option value="" <?php if($_POST['cadeauCoureur2']=="") echo " selected";?> disabled>choisir...</option>
                                            <option value="G" <?php if($_POST['cadeauCoureur2']=="G") echo " selected";?>>Gourmand</option>
                                            <option value="T" disabled <?php if($_POST['cadeauCoureur2']=="T") echo " selected";?>>Textile (épuisé)</option>
                                        </select>

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label class="d-block">Taille de vêtement*</label>
                                        <select name="tailleTeeshirtCoureur2" class="form-control">
                                            <option value="" <?php if($_POST['tailleTeeshirtCoureur2']=="") echo " selected";?> disabled>choisir...</option>
                                            <option value="XS">XS</option>
                                            <option value="S">S</option>
                                            <option value="M">M</option>
                                            <option value="L">L</option>
                                            <option value="XL">XL</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">


                                    <div class="form-group">
                                        <label class="d-block">Option Repas à <?php echo $montantRepasCourse;?>€ </label>

                                        <div class="radio-container">
                                            <input type="radio" name="repasCoureur2" id="repasCoureur2" class="radio-input" value="Non" <?php if(isset($_POST['repasCoureur2']) && $_POST['repasCoureur2']=="Non") echo "checked"; ?> checked>
                                            <label for="repasCoureur2">Pas de repas</label>
                                        </div>

                                        <div class="radio-container">
                                            <input type="radio" name="repasCoureur2" id="repasCoureur2" class="radio-input" value="Vege" <?php if(isset($_POST['repasCoureur2']) && $_POST['repasCoureur2']=="Vege") echo "checked"; ?>>
                                            <label for="repasCoureur2">Végétarien</label>
                                        </div>

                                        <div class="radio-container">
                                            <input type="radio" name="repasCoureur2" id="repasCoureur2" class="radio-input" value="Carne" <?php if(isset($_POST['repasCoureur2']) && $_POST['repasCoureur2']=="Carne") echo "checked"; ?>>
                                            <label for="repasCoureur2">Carné</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Allergies</label>
                                        <input type="text" id="allergiesCoureur2" name="allergiesCoureur2" class="form-control" value="<?php echo $_POST['allergiesCoureur2'];?>">

                                    </div>
                                </div>
                            </div>

                            <div class="row">

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Numéro fidélité (jesuisuncoureur.com)</label>
                                        <input type="text" id="numfideliteCoureur2" name="numfideliteCoureur2" class="form-control" value="<?php echo $_POST['numfideliteCoureur2'];?>">

                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>N° licence FFA (ou pass’ J’aime courir FFA)</label>
                                        <input type="text" id="licenceCoureur2" name="licenceCoureur2" class="form-control" value="<?php echo $_POST['licenceCoureur2'];?>">

                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Certificat médical ou PPS</label><br>

                                        <div class="fileupload"><input type="file" name="certificatCoureur2" accept="*/*"></div>
                                        <span class="mentionInscriptionRefus">Vous pouvez fournir un <a href="https://pps.athle.fr/" target="_blank">PPS</a> ou Certifcat médical de non-contre-indication à la pratique de l'athlétisme en compétition ou
                                            du sport en compétition ou de la course à pied en compétition</span>
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Moyen de locomotion</label><br>

                                        <select name="locomotionCoureur1" class="form-control">
                                            <option value="" <?php if($_POST['locomotionCoureur1']=="") echo " selected";?> disabled>choisir...</option>
                                            <option value="VELO" <?php if($_POST['locomotionCoureur1']=="VELO") echo " selected";?>>Vélo</option>
                                            <option value="TRAIN" <?php if($_POST['locomotionCoureur1']=="TRAIN") echo " selected";?>>Train</option>
                                            <option value="VOITURE" <?php if($_POST['locomotionCoureur1']=="VOITURE") echo " selected";?>>Voiture covoiturage</option>
                                            <option value="COVOIT" <?php if($_POST['locomotionCoureur1']=="COVOIT") echo " selected";?>>Voiture solo</option>
                                            <option value="AUTRE" <?php if($_POST['locomotionCoureur1']=="AUTRE") echo " selected";?>>Autre</option>
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <br><br>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <div class="checkboxes add_bottom_30 add_top_15">
                                            <label class="container_check">

                                                <span class="mentionInscriptionRefus"> En cochant cette case, je demande expressément qu'il ne soit pas fait mention de mon nom dans les résultats paraissant sur les sites Internet de l'organisation ni sur ceux des ses éventueIs prestataires et/ou partenaires. Pour toute opposition à la publication de mes résultats sur le site de la FFA, je reconnais devoir expressément l’en informer à l’adresse suivante : dpo@athle.fr
                                                </span>


                                                <input name="refusResultatsCoureur2" type="checkbox" value="refusResultatsCoureur2" <?php if (isset($_POST['refusResultatsCoureur2']) && $_POST['refusResultatsCoureur2'] == 'refusResultatsCoureur2') echo 'checked'; ?>>
                                                <span class="checkmark"></span>
                                            </label>
                                        </div>


                                    </div>
                                </div>


                            </div>






                            <hr>

                            <div class="form-group">
                                <label>Nom d'équipe*</label>
                                <input type="text" id="nomEquipe" name="nomEquipe" class="form-control" value="<?php echo $_POST['nomEquipe'];?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Repas carnés pour personnes accompagnantes supplémentaires (<?php echo $montantRepasCourse;?>€)</label><br>


                                        <select class="form-control" name="repasSuppEquipeCarne" id="repasSuppEquipeCarne">
                                            <?php
                                   
                                            for($i=0;$i<50;$i++) 
                                            {
                                                ?><option value="<?php echo $i; ?>" <?php if($_POST['repasSuppEquipeCarne']==$i) echo " selected";?>>
                                                <?php echo $i; ?>
                                            </option><?php
                                            }
                                            ?>
                                        </select>

                                    </div>

                                    <div class="form-group">
                                        <label>Repas végétariens pour personnes accompagnantes supplémentaires (<?php echo $montantRepasCourse;?>€)</label><br>


                                        <select class="form-control" name="repasSuppEquipeVege" id="repasSuppEquipeVege">
                                            <?php
                                   
                                            for($i=0;$i<50;$i++) 
                                            {
                                                ?><option value="<?php echo $i; ?>" <?php if($_POST['repasSuppEquipeVege']==$i) echo " selected";?>>
                                                <?php echo $i; ?>
                                            </option><?php
                                            }
                                            ?>
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <!-- Ajout du champ Code Promotionnel -->
                            <div class="form-group">
                                <label>Code promotionnel</label>
                                <input type="text" id="codePromotion" name="codePromotion" class="form-control" placeholder="Entrez votre code promo">
                            </div>


                            <div class="form-group">
                                <label>Commentaire</label>
                                <textarea class="form-control" name="commentaireEquipe" style="height: 60px;" placeholder="commentaire ou demande particulière..."><?php echo $_POST['commentaireEquipe'];?></textarea>
                            </div>

                            <br><br>
                            <div class="form-group">
                                <div class="checkboxes add_bottom_30 add_top_15">
                                    <label class="container_check">

                                        * Par la présente inscription, Les 2 coureurs reconnaissent avoir pris connaissance du <a href="reglement.php" target="_blank">réglement</a> de la compétition, et déclarent l'accepter sans aucune restriction. Ils s'engagent à être en possession du matérieI de sécurité requis dans le règlement de l'épreuve durant la durée de celle-ci

                                        <input name="accepteReglement" id="accepteReglement" value="accepte" type="checkbox" <?php if (isset($_POST['accepteReglement']) && $_POST['accepteReglement'] == 'accepte') echo 'checked'; ?>>

                                        <span class="checkmark"></span>
                                    </label>
                                </div>


                            </div>
                            <br><br>
                            <center><input type="submit" class="btn_1 medium" value="VALIDER ET PAYER"></center>
                        </div>
                    </div>
                </div>
                <!-- /row -->
            </form>
        </div>
        <!-- /container -->





    </main>
    <!-- /main -->


    <?php include 'footer.php';?>


    <div id="toTop"></div>
    <!-- Back to top button -->

    <!-- COMMON SCRIPTS -->
    <script src="js/jquery-2.2.4.min.js"></script>
    <script src="js/common_scripts.min.js"></script>
    <script src="js/functions.js"></script>

    <!-- SPECIFIC SCRIPTS -->
    <script src="js/tables_func_2.js"></script>


    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
        $(function() {
            $("#birthdate").datepicker({
                changeYear: true,
                changeMonth: true,
                yearRange: "-100:+0", // Permet à l'utilisateur de sélectionner des années jusqu'à 100 ans dans le passé
                dateFormat: "yy-mm-dd" // Format de date ISO-8601
            });
        });

    </script>



</body>

</html>

<?php
} 
catch (Exception $e) 
{
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
?>
