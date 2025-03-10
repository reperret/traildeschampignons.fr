<?php
include '../api/bdd.php';
include '../api/fonctions.php';
include 'verifAdmin.php';


if (isset($_POST['updateRecupDossard'])) {
    $equipeId = $_POST['idEquipe'];

    if ($_POST['deleteRecupDossard'] == "1") {
        // Supprimer la date de récupération en BDD
        $update = $dbh->prepare("UPDATE equipe SET recupDossardEquipe = NULL WHERE idEquipe = ?");
        $update->execute([$equipeId]);
    } else {
        // Ajouter la date actuelle de récupération en BDD
        $dateRecup = date("Y-m-d H:i:s");
        $update = $dbh->prepare("UPDATE equipe SET recupDossardEquipe = ? WHERE idEquipe = ?");
        $update->execute([$dateRecup, $equipeId]);
    }
}




if (isset($_POST['updateCertificat'])) {
    $coureurId = $_POST['idCoureur'];
    $currentStatus = $_POST['certificatStatus'];
    $newStatus = ($currentStatus == 1) ? 0 : 1;
    $update = $dbh->prepare("UPDATE coureur SET certificatValideCoureur = ? WHERE idCoureur = ?");
    $update->execute([$newStatus, $coureurId]);
}

if (isset($_POST['exportInscriptions'])) {
    // Définir l'en-tête HTTP pour forcer le téléchargement du fichier CSV
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="inscriptions.csv"');

    // Ouvrir un flux de sortie pour écrire le CSV
    $output = fopen('php://output', 'w');

    // Ajouter le BOM UTF-8 pour corriger l'affichage des caractères spéciaux dans Excel
    fwrite($output, "\xEF\xBB\xBF");

    // Écrire l'en-tête du fichier CSV avec toutes les colonnes
    fputcsv($output, [
        'ID Commande',
        'ID Transaction',
        'Course',
        'Équipe',
        'Dossard',
        'Montant',
        'Paiement',
        'Date inscription',
        'Nom C1',
        'Prénom C1',
        'Email C1',
        'Certificat C1',
        'Certificat Validé C1',
        'Sexe C1',
        'Ddn C1',
        'Ville C1',
        'Nom C2',
        'Prénom C2',
        'Email C2',
        'Certificat C2',
        'Certificat Validé C2',
        'Sexe C2',
        'Ddn C2',
        'Ville C2',
        'Repas C1',
        'Repas C2',
        'Repas supp Carné',
        'Repas supp Végétarien',
        'Cadeau C1',
        'Cadeau C2',
        'Taille Tee-shirt C1',
        'Taille Tee-shirt C2',
        'Commentaire'
    ], ';'); // Utilisation du point-virgule comme séparateur

    // Récupérer les inscriptions
    $getInscriptions = getInscriptions($dbh);
    $listingInscriptions = $getInscriptions[0];

    // Écrire les données d'inscriptions avec toutes les variables
    foreach ($listingInscriptions as $inscription) {
        fputcsv($output, [
            $inscription['helloOrderidEquipe'],
            $inscription['helloTransactionEquipe'],
            $inscription['libelleCourse'],
            $inscription['nomEquipe'],
            $inscription['dossardEquipe'],
            $inscription['montantInscriptionEquipe'],
            $inscription['paiementEquipe'] ? 'OK' : 'KO',
            date('Y-m-d', strtotime($inscription['dateInscriptionEquipe'])),
            $inscription['NomCoureur1'],
            $inscription['PrenomCoureur1'],
            $inscription['EmailCoureur1'],
            $inscription['CertificatCoureur1'] ? 'Oui' : 'Non',
            $inscription['CertificatValideCoureur1'] ? 'Validé' : 'Non Validé',
            $inscription['SexeCoureur1'],
            $inscription['DdnCoureur1'],
            $inscription['VilleCoureur1'],
            $inscription['NomCoureur2'],
            $inscription['PrenomCoureur2'],
            $inscription['EmailCoureur2'],
            $inscription['CertificatCoureur2'] ? 'Oui' : 'Non',
            $inscription['CertificatValideCoureur2'] ? 'Validé' : 'Non Validé',
            $inscription['SexeCoureur2'],
            $inscription['DdnCoureur2'],
            $inscription['VilleCoureur2'],
            ucfirst($inscription['RepasCoureur1']),
            ucfirst($inscription['RepasCoureur2']),
            $inscription['repasSuppEquipeCarne'],
            $inscription['repasSuppEquipeVege'],
            $inscription['CadeauCoureur1'],
            $inscription['CadeauCoureur2'],
            $inscription['TailleTeeshirtCoureur1'],
            $inscription['TailleTeeshirtCoureur2'],
            $inscription['commentaireEquipe']
        ], ';'); // Utilisation du point-virgule comme séparateur
    }

    // Fermer le flux de sortie
    fclose($output);
    exit();
}

$totalAmount = 0;
$courses = getCourses(NULL, $dbh);
$idCourse = NULL;
if (isset($_POST['idCourse']) && $_POST['idCourse'] != '' && $_POST['idCourse'] != 'all') {
    $idCourse = trim($_POST['idCourse']);
}
$getInscriptions = getInscriptions($dbh);
$listingInscriptions = $getInscriptions[0];
$totalRepasCarne = $getInscriptions[1];
$totalRepasVege = $getInscriptions[2];
$totalGourmand = $getInscriptions[3];
$totalTextile = $getInscriptions[4];
$xs = $getInscriptions[5];
$s = $getInscriptions[6];
$m = $getInscriptions[7];
$l = $getInscriptions[8];
$xl = $getInscriptions[9];
$cepe = $getInscriptions[10];
$girolle = $getInscriptions[11];
$masculin = $getInscriptions[12];
$feminin = $getInscriptions[13];

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Suivi Inscriptions Trail des Champignons</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="css/admin.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        .badge.px-3.py-2 {
            font-size: 1.5em;
            /* Augmente davantage la taille pour plus de visibilité */
            padding: 0.5em 1em;
            /* Ajuste le padding pour rendre le badge plus grand */
        }
    </style>
</head>

<body class="fixed-nav sticky-footer" id="page-top">

    <?php include 'nav.php'; ?>

    <div class="content-wrapper">
        <div class="container-fluid">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fa fa-fw fa-user"></i> Inscriptions
                    <?php echo sizeof($listingInscriptions) . " équipes inscrites"; ?>
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
                        <form action="" method="post">
                            <table class="table table-bordered" style="font-size:0.8em" id="dataTable" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Course</th>
                                        <th>Équipe</th>
                                        <th>Dossard</th>
                                        <th>Montant</th>
                                        <th>Paiement</th>
                                        <th>Date inscription</th>
                                        <th>Coureur 1</th>
                                        <th>Coureur 2</th>
                                        <th>Rep C1</th>
                                        <th>Rep C2</th>
                                        <th>Repas supp Carné</th>
                                        <th>Repas supp Végétarien</th>
                                        <th>Cadeau C1</th>
                                        <th>Cadeau C2</th>
                                        <th>Taille C1</th>
                                        <th>Taille C2</th>
                                        <th>Commentaire</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listingInscriptions as $inscription) { ?>
                                        <tr>
                                            <td><?php echo $inscription['libelleCourse']; ?></td>
                                            <td><?php echo $inscription['nomEquipe']; ?></td>
                                            <td>
                                                <form method="post" action="index.php" style="display: inline;"
                                                    onsubmit="return confirmRecupDossard(<?php echo $inscription['idEquipe']; ?>, <?php echo is_null($inscription['recupDossardEquipe']) ? 'false' : 'true'; ?>);">
                                                    <input type="hidden" name="idEquipe"
                                                        value="<?php echo $inscription['idEquipe']; ?>">
                                                    <input type="hidden" name="updateRecupDossard" value="1">
                                                    <input type="hidden" name="deleteRecupDossard" value="0"
                                                        id="deleteRecup_<?php echo $inscription['idEquipe']; ?>">

                                                    <?php
                                                    $dossardStyle = is_null($inscription['recupDossardEquipe']) ? 'bg-light text-dark' : 'bg-success text-white';
                                                    $tooltipText = is_null($inscription['recupDossardEquipe']) ? '' : 'title="' . $inscription['recupDossardEquipe'] . '"';
                                                    ?>

                                                    <button type="submit"
                                                        class="badge px-3 py-2 <?php echo $dossardStyle; ?>"
                                                        style="border: none; background: none; cursor: pointer;"
                                                        <?php echo $tooltipText; ?>>
                                                        <?php echo $inscription['dossardEquipe']; ?>
                                                    </button>
                                                </form>

                                            </td>



                                            <td><?php echo $inscription['montantInscriptionEquipe'] . "€"; ?></td>
                                            <td><span
                                                    class="badge badge-<?php echo $inscription['paiementEquipe'] ? 'success' : 'danger'; ?>"><?php echo $inscription['paiementEquipe'] ? 'OK' : 'KO'; ?></span>
                                            </td>
                                            <td><?php echo date('Y-m-d', strtotime($inscription['dateInscriptionEquipe'])); ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (empty($inscription['CertificatCoureur1'])) {
                                                    $iconeCoureur1 = '<i class="fa fa-circle" style="color:#B0B0B0;" title="Aucun certificat"></i>';
                                                    $certificatStatus1 = 0;
                                                } elseif ($inscription['CertificatValideCoureur1'] == 1) {
                                                    $iconeCoureur1 = '<i class="fa fa-circle" style="color:#28A745;" title="Certificat validé"></i>';
                                                    $certificatStatus1 = 1;
                                                } else {
                                                    $iconeCoureur1 = '<i class="fa fa-circle" style="color:#FFC107;" title="Certificat en attente de validation"></i>';
                                                    $certificatStatus1 = 0;
                                                }
                                                ?>
                                                <form method="post" style="display:inline;" action="index.php">
                                                    <input type="hidden" name="idCoureur"
                                                        value="<?php echo $inscription['idCoureur1']; ?>">
                                                    <input type="hidden" name="certificatStatus"
                                                        value="<?php echo $certificatStatus1; ?>">
                                                    <input type="hidden" name="updateCertificat" value="1">
                                                    <button type="submit"
                                                        style="border: none; background: none; cursor: pointer;">
                                                        <?php echo $iconeCoureur1; ?>
                                                    </button>
                                                </form>
                                                <?php if (!empty($inscription['CertificatCoureur1'])) { ?>
                                                    <a href="../certificats/<?php echo $inscription['CertificatCoureur1']; ?>"
                                                        target="_blank">
                                                        <?php echo $inscription['Coureur1']; ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <?php echo $inscription['Coureur1']; ?>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (empty($inscription['CertificatCoureur2'])) {
                                                    $iconeCoureur2 = '<i class="fa fa-circle" style="color:#B0B0B0;" title="Aucun certificat"></i>';
                                                    $certificatStatus2 = 0;
                                                } elseif ($inscription['CertificatValideCoureur2'] == 1) {
                                                    $iconeCoureur2 = '<i class="fa fa-circle" style="color:#28A745;" title="Certificat validé"></i>';
                                                    $certificatStatus2 = 1;
                                                } else {
                                                    $iconeCoureur2 = '<i class="fa fa-circle" style="color:#FFC107;" title="Certificat en attente de validation"></i>';
                                                    $certificatStatus2 = 0;
                                                }
                                                ?>
                                                <form method="post" style="display:inline;" action="index.php">
                                                    <input type="hidden" name="idCoureur"
                                                        value="<?php echo $inscription['idCoureur2']; ?>">
                                                    <input type="hidden" name="certificatStatus"
                                                        value="<?php echo $certificatStatus2; ?>">
                                                    <input type="hidden" name="updateCertificat" value="1">
                                                    <button type="submit"
                                                        style="border: none; background: none; cursor: pointer;">
                                                        <?php echo $iconeCoureur2; ?>
                                                    </button>
                                                </form>
                                                <?php if (!empty($inscription['CertificatCoureur2'])) { ?>
                                                    <a href="../certificats/<?php echo $inscription['CertificatCoureur2']; ?>"
                                                        target="_blank">
                                                        <?php echo $inscription['Coureur2']; ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <?php echo $inscription['Coureur2']; ?>
                                                <?php } ?>
                                            </td>
                                            <td><?php echo $inscription['RepasCoureur1']; ?></td>
                                            <td><?php echo $inscription['RepasCoureur2']; ?></td>
                                            <td><?php echo $inscription['repasSuppEquipeCarne']; ?></td>
                                            <td><?php echo $inscription['repasSuppEquipeVege']; ?></td>
                                            <td><?php echo $inscription['CadeauCoureur1']; ?></td>
                                            <td><?php echo $inscription['CadeauCoureur2']; ?></td>
                                            <td><?php echo $inscription['TailleTeeshirtCoureur1']; ?></td>
                                            <td><?php echo $inscription['TailleTeeshirtCoureur2']; ?></td>
                                            <td><?php echo $inscription['commentaireEquipe']; ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </form>
                    </div>

                    <!-- Tableau récapitulatif en bas de la page -->
                    <div class="mt-4">
                        <h5>Récapitulatif des Totaux</h5>
                        <table class="table table-bordered">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Repas Carnés</th>
                                    <th>Repas Végétariens</th>
                                    <th>KDO Gourmands</th>
                                    <th>KDO Textiles</th>
                                    <th>XS</th>
                                    <th>S</th>
                                    <th>M</th>
                                    <th>L</th>
                                    <th>XL</th>
                                    <th>Cepe</th>
                                    <th>Girolle</th>
                                    <th>Masculin</th>
                                    <th>Féminin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><?php echo $totalRepasCarne; ?></td>
                                    <td><?php echo $totalRepasVege; ?></td>
                                    <td><?php echo $totalGourmand; ?></td>
                                    <td><?php echo $totalTextile; ?></td>
                                    <td><?php echo $xs; ?></td>
                                    <td><?php echo $s; ?></td>
                                    <td><?php echo $m; ?></td>
                                    <td><?php echo $l; ?></td>
                                    <td><?php echo $xl; ?></td>
                                    <td><?php echo $cepe; ?></td>
                                    <td><?php echo $girolle; ?></td>
                                    <td><?php echo $masculin; ?></td>
                                    <td><?php echo $feminin; ?></td>
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
                [2, "asc"]
            ], // La troisième colonne a un index de 2 (0, 1, 2)
            "iDisplayLength": 200
        });
    </script>


    <script>
        function confirmRecupDossard(idEquipe, isRecupere) {
            if (isRecupere) {
                // Affiche la confirmation uniquement si le dossard est récupéré
                if (confirm("Voulez-vous vraiment annuler l'enregistrement du dossard ?")) {
                    // Indique qu'on veut supprimer la date de récupération
                    document.getElementById("deleteRecup_" + idEquipe).value = "1";
                    return true;
                } else {
                    return false; // Annule la soumission si l'utilisateur choisit "Non"
                }
            }
            // Si le dossard n'est pas encore récupéré, continue sans confirmation
            return true;
        }
    </script>





</body>

</html>