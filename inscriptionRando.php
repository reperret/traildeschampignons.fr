<?php 
try 
{
include 'api/bdd.php';
include 'api/fonctions.php';

$inscriptionsRando = getInscriptionsRando($dbh);
$totalParticipantsRando = 0;
foreach ($inscriptionsRando as $rando) {
    $participants = json_decode($rando['participantsRando'], true);
    $totalParticipantsRando += count($participants);
}
$maxParticipantsRando = 100;

if ($totalParticipantsRando >= $maxParticipantsRando) {
    header('Location: inscriptionsFermees.php?j=r');
    exit();
}

$manqueParametres = NULL;
    
if(
    isset($_POST['emailRando']) && $_POST['emailRando'] != '' 
    && isset($_POST['telephoneRando']) && $_POST['telephoneRando'] != '' 
    && isset($_POST['adresseRando']) && $_POST['adresseRando'] != ''
    && isset($_POST['villeRando']) && $_POST['villeRando'] != ''
    && isset($_POST['cpRando']) && $_POST['cpRando'] != ''
    && !empty($_POST['participantPrenom']) // Vérifie qu'il y a au moins un participant
) {
    $idRando = createRando($_POST, $dbh);

    $client_id = urlencode($clientIdHelloAsso);
    $client_secret = urlencode($clientSecretHelloAsso);
    $grant_type = "client_credentials";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlTokenHelloAsso);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=".$grant_type."&client_id=".$client_id."&client_secret=".$client_secret);
    curl_setopt($ch, CURLOPT_POST, 1);
    $headers = array('Content-Type: application/x-www-form-urlencoded');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }
    curl_close($ch);

    $infos = json_decode($result, true);
    $accessToken = $infos['access_token'];

    list($montantInscription, $reduction) = getMontantTotalInscription("rando", $_POST, $dbh);

    $stmt = $dbh->prepare("UPDATE rando SET montantInscriptionRando = ? WHERE idRando = ?");
    $stmt->execute([$montantInscription, $idRando]);

    $montantInscription = intval($montantInscription) * 100;
    $idPaiement = NULL;

    $prenom = $_POST['prenomCoureur1'];
    $nom = $_POST['nomCoureur1'];
    $email = $_POST['emailRando'];
    $adresse = $_POST['adresseRando'];
    $ville = $_POST['villeRando'];
    $code_postal = $_POST['cpRando'];
    $nomEquipeRando = $_POST['nomEquipeRando'];

    $jsonClient = [
        'totalAmount' => $montantInscription,
        'initialAmount' => $montantInscription,
        'itemName' => "Paiement rando ".$idRando,
        'backUrl' => $domaine.'inscriptionRando.php',
        'errorUrl' => $domaine.'confirmation.php?type=error',
        'returnUrl' => $domaine.'confirmation.php?type=return',
        'containsDonation' => false,
        'payer' => [
            'firstName' => $prenom,
            'lastName' => $nom,
            'email' => $email,
            'dateOfBirth' => "1950-01-01",
            'address' => $adresse,
            'city' => $ville,
            'zipCode' => $code_postal,
        ],
        'metadata' => [
            'idRando' => $idRando,
            'nomEquipeRando' => $nomEquipeRando,
            'emailRando' => $email,
        ]
    ];

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $checkoutIntentUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => json_encode($jsonClient, true),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer '.$accessToken,
            'Content-Type: application/json'
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    $lien = json_decode($response, true);
    header('Location: '.$lien['redirectUrl']);
    exit(); 
}
elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $manqueParametres = "Veuillez remplir tous les champs et ajouter au moins un participant !";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>TDC - INSCRIPTION RANDO</title>

    <!-- Favicons -->
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

    <script src="js/jquery-2.2.4.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('participantContainer');
            const form = document.getElementById('monFormulaire');

            function updateParticipantLabels() {
                const participantDivs = container.querySelectorAll('.participantDiv');
                participantDivs.forEach((div, index) => {
                    const labels = div.querySelectorAll('label');
                    labels[0].textContent = `Prénom ${index + 1}`;
                    labels[1].textContent = `Nom ${index + 1}`;
                    labels[2].textContent = `Type ${index + 1}`;
                });
            }

            function addParticipant() {
                const participantNumber = container.children.length + 1;
                const participantDiv = document.createElement('div');
                participantDiv.classList.add('participantDiv');
                participantDiv.innerHTML = `
                    <div class="row">
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <label class="colorChampsInscription">Prénom ${participantNumber}</label>
                                <input type="text" class="form-control" name="participantPrenom[]" placeholder="Prénom">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <label class="colorChampsInscription">Nom ${participantNumber}</label>
                                <input type="text" class="form-control" name="participantNom[]" placeholder="Nom">
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-4">
                            <div class="form-group">
                                <label class="colorChampsInscription">Type ${participantNumber}</label>
                                <select class="form-control" name="participantType[]">
                                    <option value="Adulte">Adulte</option>
                                    <option value="Enfant">Enfant</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="deleteBtn btn_1 gray">Supprimer</button>
                `;

                participantDiv.querySelector('.deleteBtn').addEventListener('click', function() {
                    participantDiv.remove();
                    updateParticipantLabels();
                });

                container.appendChild(participantDiv);
                updateParticipantLabels();
            }

            document.getElementById('addParticipant').addEventListener('click', addParticipant);

            form.addEventListener('submit', function(event) {
                if (container.children.length === 0) {
                    event.preventDefault();
                    alert("Vous devez ajouter au moins un participant !");
                }
            });
        });
    </script>

    <style>
        .participantDiv {
            margin-bottom: 10px;
        }

        .gray {
            background-color: red !important;
        }

        .green {
            background-color: green !important;
        }
    </style>

</head>

<body>

    <div class="layer"></div>

    <div id="preloader">
        <div data-loader="circle-side"></div>
    </div>

    <?php include "header.php";?>

    <main>
        <div id="breadcrumb">
            <div class="container">
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="lescourses.php">Courses</a></li>
                </ul>
            </div>
        </div>

        <div class="container margin_60_35">
            <center>
                <h1>Inscriptions Randonnée</h1>
                <p>2 novembre 2024 - Saint-André-en-Vivarais (07690)</p>
                <p>
                    <span class="mentionPrixInscriptionRando"><?php echo $montantInscriptionRandoAdulte;?>€ par adulte<br>
                        <?php echo round($montantInscriptionRandoEnfant,2);?>€ par enfant (< 15 ans) <br>
                            (comprenant ravitaillement sur le parcours et à l'arrivée)
                    </span>
                </p>
                <span class="mentionPrixInscriptionRando">Vous pouvez tout à fait vous inscrire en famille sur cette rando</span>
                <?php if($manqueParametres != NULL) { ?>
                <div class="alerte-rouge-clair"><?php echo $manqueParametres;?></div>
                <?php } ?>
                <br>
            </center>

            <form action="inscriptionRando.php" method="POST" id="monFormulaire">

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="box_general_3 write_review">

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" id="emailRando" name="emailRando" class="form-control" value="<?php echo $_POST['emailRando'];?>" required>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Téléphone</label>
                                        <input type="text" id="telephoneRando" name="telephoneRando" class="form-control" value="<?php echo $_POST['telephoneRando'];?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Adresse</label>
                                <input type="text" id="adresseRando" name="adresseRando" class="form-control" value="<?php echo $_POST['adresseRando'];?>" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Code postal</label>
                                        <input type="text" id="cpRando" name="cpRando" class="form-control" value="<?php echo $_POST['cpRando'];?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-6">
                                    <div class="form-group">
                                        <label>Ville</label>
                                        <input type="text" id="villeRando" name="villeRando" class="form-control" value="<?php echo $_POST['villeRando'];?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Nombre de repas Carné à <?php echo $montantRepasRando;?>€</label>
                                        <select class="form-control" name="nbRepasCarneRando" id="nbRepasCarneRando">
                                            <?php for($i=0;$i<50;$i++) { ?>
                                            <option value="<?php echo $i; ?>" <?php if($_POST['nbRepasCarneRando']==$i) echo " selected";?>>
                                                <?php echo $i; ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Nombre de repas Végé à <?php echo $montantRepasRando;?>€</label>
                                        <select class="form-control" name="nbRepasVegeRando" id="nbRepasVegeRando">
                                            <?php for($i=0;$i<50;$i++) { ?>
                                            <option value="<?php echo $i; ?>" <?php if($_POST['nbRepasVegeRando']==$i) echo " selected";?>>
                                                <?php echo $i; ?>
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 col-sm-12">
                                    <div class="form-group">
                                        <div id="participantContainer"></div>
                                        <br>
                                        <button type="button" id="addParticipant" class="btn_1 green">Ajouter un participant</button>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="form-group">
                                <label>Commentaire</label>
                                <textarea class="form-control" name="commentaireRando" style="height: 60px;" placeholder="commentaire ou demande particulière..."><?php echo $_POST['commentaireRando'];?></textarea>
                            </div>

                            <center><input type="submit" class="btn_1 medium" value="VALIDER ET PAYER"></center>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <?php include 'footer.php';?>

    <div id="toTop"></div>

    <!-- COMMON SCRIPTS -->
    <script src="js/common_scripts.min.js"></script>
    <script src="js/functions.js"></script>
    <script src="js/tables_func_2.js"></script>

</body>
</html>

<?php
} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
?>
