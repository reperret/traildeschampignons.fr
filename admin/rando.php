<?php
include '../api/bdd.php';
include '../api/fonctions.php';
include 'verifAdmin.php';

if (isset($_POST['exportInscriptions'])) {
    // Définir l'en-tête HTTP pour forcer le téléchargement du fichier CSV
    // Définir l'en-tête HTTP pour forcer le téléchargement du fichier CSV
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="inscriptions_rando.csv"');

    // Ouvrir un flux de sortie pour écrire le CSV
    $output = fopen('php://output', 'w');

    // Ajouter le BOM UTF-8 pour corriger l'affichage des caractères spéciaux dans Excel
    fwrite($output, "\xEF\xBB\xBF");

    // Écrire l'en-tête du fichier CSV avec toutes les colonnes
    fputcsv($output, [
        'ID Rando', 'Montant', 'Nb Participants', 'Email Responsable', 'Téléphone', 'Adresse', 'Code Postal', 'Ville',
        'Nombre de Repas Carnés', 'Nombre de Repas Végétariens', 'Commentaire', 'Participants'
    ], ';'); // Utilisation du point-virgule comme séparateur

    // Récupérer les inscriptions
    $getInscriptionsRando = getInscriptionsRando($dbh);
    
    // Écrire les données d'inscriptions
    foreach ($getInscriptionsRando as $inscription) {
        $nbPart = 0;
        $participants = json_decode($inscription['participantsRando'], true);
        $participantsStr = '';
        
        if ($participants) {
            $nbPart = count($participants); // Compte le nombre de participants
            $participantsArray = array_map(function($participant) {
                return $participant['prenom'] . ' ' . $participant['nom'] . ' (' . $participant['type'] . ')';
            }, $participants);
            $participantsStr = implode(' | ', $participantsArray);
        }
        

        fputcsv($output, [
            $inscription['idRando'],
            $inscription['montantInscriptionRando'],
            $nbPart,
            $inscription['emailRando'],
            $inscription['telephoneRando'],
            $inscription['adresseRando'],
            $inscription['cpRando'],
            $inscription['villeRando'],
            $inscription['nbRepasCarneRando'],
            $inscription['nbRepasVegeRando'],
            $inscription['commentaireRando'],
            $participantsStr
        ], ';');
    }

    // Fermer le flux de sortie
    fclose($output);
    exit();
}

$getInscriptionsRando = getInscriptionsRando($dbh);

$totalInscrits = 0;
$totalRepasCarne = 0;
$totalRepasVege = 0;
$totalGroupes = count($getInscriptionsRando);

foreach ($getInscriptionsRando as $inscription) {
    $participants = json_decode($inscription['participantsRando'], true);
    if ($participants) {
        $totalInscrits += count($participants);
    }
    $totalRepasCarne += $inscription['nbRepasCarneRando'];
    $totalRepasVege += $inscription['nbRepasVegeRando'];
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Suivi Inscriptions Randonnée</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/admin.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body class="fixed-nav sticky-footer" id="page-top">

    <?php include 'nav.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-fw fa-user"></i> Inscriptions Randonnée
                    <?php echo sizeof($getInscriptionsRando) . " groupes inscrits"; ?>
                </div>
                <br>
                <div class="card-body">
                    <form action="" method="post" style="text-align: right;">
                        <button type="submit" name="exportInscriptions" class="btn btn-primary">
                            <i class="fa fa-download"></i> Exporter les inscriptions
                        </button>
                    </form>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-bordered" style="font-size:0.8em" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Email Responsable</th>
                                    <th>Téléphone</th>
                                    <th>Adresse</th>
                                    <th>Code Postal</th>
                                    <th>Ville</th>
                                    <th>Repas Carnés</th>
                                    <th>Repas Végétariens</th>
                                    <th>Commentaire</th>
                                    <th>Participants</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($getInscriptionsRando as $inscription) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($inscription['emailRando']); ?></td>
                                        <td><?php echo htmlspecialchars($inscription['telephoneRando']); ?></td>
                                        <td><?php echo htmlspecialchars($inscription['adresseRando']); ?></td>
                                        <td><?php echo htmlspecialchars($inscription['cpRando']); ?></td>
                                        <td><?php echo htmlspecialchars($inscription['villeRando']); ?></td>
                                        <td><?php echo htmlspecialchars($inscription['nbRepasCarneRando']); ?></td>
                                        <td><?php echo htmlspecialchars($inscription['nbRepasVegeRando']); ?></td>
                                        <td><?php echo htmlspecialchars($inscription['commentaireRando']); ?></td>
                                        <td>
                                            <?php 
                                                $participants = json_decode($inscription['participantsRando'], true);
                                                if ($participants) {
                                                    foreach ($participants as $participant) {
                                                        echo htmlspecialchars($participant['prenom']) . " " . htmlspecialchars($participant['nom']) . " (" . htmlspecialchars($participant['type']) . ")<br>";
                                                    }
                                                }
                                            ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Tableau récapitulatif en bas de la page -->
                    <div class="mt-4">
                        <h5>Récapitulatif des Totaux</h5>
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Total Groupes</th>
                                    <th>Total Inscrits</th>
                                    <th>Repas Carnés</th>
                                    <th>Repas Végétariens</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo $totalGroupes; ?></td>
                                    <td><?php echo $totalInscrits; ?></td>
                                    <td><?php echo $totalRepasCarne; ?></td>
                                    <td><?php echo $totalRepasVege; ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
    <script src="vendor/datatables/jquery.dataTables.js"></script>
    <script src="vendor/datatables/dataTables.bootstrap4.js"></script>
    <script src="js/admin.js"></script>

    <script>
        $('#dataTable').dataTable({
            "order": [
                [3, "desc"]
            ],
            "iDisplayLength": 200
        });
    </script>
</body>
</html>