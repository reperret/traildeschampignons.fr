<?php
// Inclusion du fichier bdd.php pour la connexion à la base de données et les variables nécessaires
require_once 'api/bdd.php'; // Assure-toi que ce fichier est au bon emplacement
require_once 'api/fonctions.php'; // Inclusion des fonctions nécessaires

// Initialisation des variables
$email = '';
if(isset($_GET['eFromR']) && $_GET['eFromR']!='' ) $email = trim($_GET['eFromR']);

$jour = '';
$mois = '';
$annee = '';
$error = '';
$success = '';
$runnerFound = false;
$teamInfo = [];
$runner1 = [];
$runner2 = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Détermination de l'action
    $action = $_POST['action'] ?? 'search';

    if ($action == 'search') {
        // Traitement de l'email et de la date de naissance
        $email = strtolower(trim($_POST['email']));
        $jour = $_POST['jour'];
        $mois = $_POST['mois'];
        $annee = $_POST['annee'];
        $dateNaissance = "$annee-$mois-$jour";

        // Recherche du coureur correspondant
        $stmt = $dbh->prepare("SELECT idCoureur, idEquipe, LOWER(TRIM(emailCoureur)) as emailCoureur FROM coureur WHERE LOWER(TRIM(emailCoureur)) = :email AND ddnCoureur = :ddn");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':ddn', $dateNaissance);
        $stmt->execute();
        $coureur = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($coureur) {
            $idCoureur = $coureur['idCoureur'];
            $idEquipe = $coureur['idEquipe'];

            // Récupération des informations de l'équipe avec libelleCourse
            $stmt = $dbh->prepare("
                SELECT e.*, c.libelleCourse
                FROM equipe e
                JOIN course c ON e.idCourse = c.idCourse
                WHERE e.idEquipe = :idEquipe
            ");
            $stmt->bindParam(':idEquipe', $idEquipe);
            $stmt->execute();
            $teamInfo = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($teamInfo) {
                // Récupération des deux coureurs
                $stmt = $dbh->prepare("SELECT * FROM coureur WHERE idEquipe = :idEquipe ORDER BY numCoureur ASC");
                $stmt->bindParam(':idEquipe', $idEquipe);
                $stmt->execute();
                $runners = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (count($runners) == 2) {
                    $runner1 = $runners[0];
                    $runner2 = $runners[1];
                    $runnerFound = true;
                } else {
                    $error = "Impossible de trouver les deux coureurs de l'équipe.";
                }
            } else {
                $error = "Impossible de trouver l'équipe associée.";
            }
        } else {
            $error = "Aucun coureur trouvé avec cet email et cette date de naissance.";
        }
    } elseif ($action == 'upload_certif') {
        // Traitement de l'upload du certificat
        $idCoureur = $_POST['idCoureur'];
        $idEquipe = $_POST['idEquipe'];
        $numCoureur = $_POST['numCoureur']; // Doit être 1 ou 2

        // Traitement de l'upload du fichier
        $fileName = enregistrerCertificat($idEquipe, $idCoureur, $numCoureur, $dbh);

        if ($fileName) {
            $success = "Certificat mis à jour avec succès pour le coureur $numCoureur.";
        } else {
            $error = "Erreur lors de la mise à jour du certificat pour le coureur $numCoureur.";
        }

        // Récupération des informations mises à jour pour affichage
        $stmt = $dbh->prepare("
            SELECT e.*, c.libelleCourse
            FROM equipe e
            JOIN course c ON e.idCourse = c.idCourse
            WHERE e.idEquipe = :idEquipe
        ");
        $stmt->bindParam(':idEquipe', $idEquipe);
        $stmt->execute();
        $teamInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($teamInfo) {
            $stmt = $dbh->prepare("SELECT * FROM coureur WHERE idEquipe = :idEquipe ORDER BY numCoureur ASC");
            $stmt->bindParam(':idEquipe', $idEquipe);
            $stmt->execute();
            $runners = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($runners) == 2) {
                $runner1 = $runners[0];
                $runner2 = $runners[1];
                $runnerFound = true;
            } else {
                $error = "Impossible de trouver les deux coureurs de l'équipe.";
            }
        } else {
            $error = "Impossible de trouver l'équipe associée.";
        }
    } elseif ($action == 'delete_certif') {
        // Traitement de la suppression du certificat
        $idCoureur = $_POST['idCoureur'];
        $idEquipe = $_POST['idEquipe'];
        $numCoureur = $_POST['numCoureur']; // Doit être 1 ou 2

        // Récupérer le nom du fichier du certificat depuis la base de données
        $stmt = $dbh->prepare("SELECT certificatCoureur FROM coureur WHERE idCoureur = :idCoureur");
        $stmt->bindParam(':idCoureur', $idCoureur);
        $stmt->execute();
        $certif = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($certif && !empty($certif['certificatCoureur'])) {
            $filePath = 'certificats/' . $certif['certificatCoureur'];
            // Supprimer le fichier physique si il existe
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Mettre à jour la base de données en vidant le champ certificatCoureur
            $stmt = $dbh->prepare("UPDATE coureur SET certificatCoureur = NULL WHERE idCoureur = :idCoureur");
            $stmt->bindParam(':idCoureur', $idCoureur);
            $stmt->execute();

            $success = "Certificat supprimé avec succès pour le coureur $numCoureur.";
        } else {
            $error = "Aucun certificat trouvé pour le coureur $numCoureur.";
        }

        // Récupération des informations mises à jour pour affichage
        $stmt = $dbh->prepare("
            SELECT e.*, c.libelleCourse
            FROM equipe e
            JOIN course c ON e.idCourse = c.idCourse
            WHERE e.idEquipe = :idEquipe
        ");
        $stmt->bindParam(':idEquipe', $idEquipe);
        $stmt->execute();
        $teamInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($teamInfo) {
            $stmt = $dbh->prepare("SELECT * FROM coureur WHERE idEquipe = :idEquipe ORDER BY numCoureur ASC");
            $stmt->bindParam(':idEquipe', $idEquipe);
            $stmt->execute();
            $runners = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (count($runners) == 2) {
                $runner1 = $runners[0];
                $runner2 = $runners[1];
                $runnerFound = true;
            } else {
                $error = "Impossible de trouver les deux coureurs de l'équipe.";
            }
        } else {
            $error = "Impossible de trouver l'équipe associée.";
        }
    }
}

// Fonction pour convertir les noms de champs en labels plus lisibles
function formatFieldLabel($fieldName) {
    $fieldLabels = [
        // Champs de la table equipe
        // 'idEquipe' => 'ID Équipe', // Retiré selon les instructions
        'idCourse' => 'Course',
        'libelleCourse' => 'Course',
        'nomEquipe' => 'Nom de l\'équipe',
        'commentaireEquipe' => 'Commentaire',
        'dateInscriptionEquipe' => 'Date d\'inscription',
        'repasSuppEquipeCarne' => 'Repas supplémentaires (Carné)',
        'repasSuppEquipeVege' => 'Repas supplémentaires (Végé)',
        'paiementEquipe' => 'Paiement',
        // 'helloTransactionEquipe' => 'Transaction HelloAsso', // Retiré
        // 'helloOrderidEquipe' => 'Order ID HelloAsso', // Retiré
        'codePromoEquipe' => 'Code Promo',
        'montantInscriptionEquipe' => 'Montant Inscription',
        'reductionEquipe' => 'Réduction',

        // Champs de la table coureur
        // 'idCoureur' => 'ID Coureur', // Retiré
        // 'idEquipe' => 'ID Équipe', // Retiré
        // 'numCoureur' => 'Numéro Coureur', // Retiré
        'nomCoureur' => 'Nom',
        'prenomCoureur' => 'Prénom',
        'sexeCoureur' => 'Sexe',
        'ddnCoureur' => 'Date de Naissance',
        'emailCoureur' => 'Email',
        'telephoneCoureur' => 'Téléphone',
        'adresseCoureur' => 'Adresse',
        'cpCoureur' => 'Code Postal',
        'villeCoureur' => 'Ville',
        'certificatCoureur' => 'Certificat',
        'clubCoureur' => 'Club',
        'licenceCoureur' => 'Licence',
        'cadeauCoureur' => 'Cadeau',
        'tailleTeeshirtCoureur' => 'Taille T-Shirt',
        'repasCoureur' => 'Repas',
        'allergiesCoureur' => 'Allergies',
        'urgenceCoureur' => 'Personne à contacter en cas d\'urgence',
        // 'numfideliteCoureur' => 'Numéro de fidélité', // Retiré
        // 'certificatValideCoureur' => 'Certificat Valide', // Retiré (traité différemment)
        'refusResultatsCoureur' => 'Refus de publication des résultats',
        'locomotionCoureur' => 'Mode de locomotion',
    ];

    return $fieldLabels[$fieldName] ?? $fieldName;
}

// Fonction pour afficher les valeurs personnalisées
function formatFieldValue($fieldName, $value) {
    if ($fieldName == 'reductionEquipe' && is_null($value)) {
        return '0';
    }
    if ($fieldName == 'cadeauCoureur') {
        if ($value == 'G') return 'Gourmand';
        if ($value == 'T') return 'Textile';
    }
    if ($fieldName == 'paiementEquipe') {
        if ($value == 1) 
        {
            return '<span class="badge badge-success"><i class="fa fa-check"></i> OK</span>';
        }
        else
        {
            return '<span class="badge badge-danger">Non validé</span>';
        }

    }
    if ($fieldName == 'repasCoureur') {
        if ($value == 'Vege') return 'Végétarien';
        if ($value == 'Carne') return 'Carné';
    }
    if ($fieldName == 'refusResultatsCoureur') {
        if ($value == 0) return 'Non';
        if ($value == 1) return 'Oui';
    }
    if ($fieldName == 'certificatValideCoureur') {
        if ($value == 1) {
            return '<span class="badge badge-success"><i class="fa fa-check"></i> OK</span>';
        } else {
            return '<span class="badge badge-danger">Non validé</span>';
        }
    }
    if ($fieldName == 'sexeCoureur') {
        if ($value == 'M') return 'Homme';
        if ($value == 'F') return 'Femme';
    }
    return $value;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Les mêmes balises head que précédemment -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Votre inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Ajout de Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        /* Styles existants */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(to bottom, #F5F5F5, #D3A87A);
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-size: cover;
            font-family: 'Open Sans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            overflow-y: auto;
            max-height: 90vh;
        }

        label{ font-weight: bold; color: maroon;}

        .custom-btn {
            background-color: #A67C52;
            border-color: #A67C52;
            color: #fff;
            font-weight: 600;
        }

        .custom-btn:hover {
            background-color: #8B5E34;
            border-color: #8B5E34;
        }

        .alert {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            text-align: center;
            z-index: 1000;
        }

        h2, h3, h4 {
            font-weight: 600;
        }

        .custom-file-label::after {
            content: "Parcourir";
        }

        table {
            width: 100%;
            margin-bottom: 20px;
        }

        th, td {
            padding: 8px;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }

        th {
            width: 30%;
            text-align: left;
            font-weight: bold;
            color: maroon;
        }

        .table-container {
            margin-bottom: 40px;
        }

        .btn-view {
            margin-bottom: 10px;
        }

        .badge-success {
            background-color: #28a745;
            color: #fff;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
        }

        .btn-delete {
            margin-left: 10px;
        }
    </style>
</head>
<body>

<!-- Affichage des messages de succès ou d'erreur tout en haut -->
<?php if (!empty($error)): ?>
    <div class="alert alert-danger">
        <?php echo $error; ?>
    </div>
<?php elseif (!empty($success)): ?>
    <div class="alert alert-success">
        <?php echo $success; ?>
    </div>
<?php endif; ?>

<div class="container">

    <!-- Logo centré en haut -->
    <div class="text-center mb-4">
        <img src="img/logo_2x.png" alt="Logo" style="max-width: 326px;">
    </div>

    <h3 class="text-center">Votre inscription</h3>
    <h4 class="text-center">Vous pouvez mettre à jour vos certificats médicaux</h4><br><br>

    <?php if (!$runnerFound): ?>
        <!-- Formulaire initial pour l'email et la date de naissance -->
        <form action="" method="POST">
            <input type="hidden" name="action" value="search">
            <!-- Champ Email -->
            <div class="form-group">
                <label for="email">Email choisi lors de l'inscription :</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>

            <!-- Champs Date de naissance -->
            <div class="form-group">
                <label>Date de naissance :</label>
                <div class="form-row">
                    <div class="col">
                        <label for="jour">Jour</label>
                        <select class="form-control" name="jour" required>
                            <?php for ($i = 1; $i <= 31; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php if ($jour == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col">
                        <label for="mois">Mois</label>
                        <select class="form-control" name="mois" required>
                            <?php for ($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php if ($mois == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col">
                        <label for="annee">Année</label>
                        <select class="form-control" name="annee" required>
                            <?php for ($i = 1900; $i <= 2024; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php if ($annee == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Bouton de validation -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary custom-btn">VALIDER</button>
            </div>
        </form>

    <?php else: ?>
        <!-- Affichage des informations de l'équipe et des coureurs -->
        <div class="table-container">
            <h3>Informations de l'équipe</h3>
            <table class="table">
                <?php foreach ($teamInfo as $field => $value): ?>
                    <?php
                    // Champs à exclure
                    if (in_array($field, ['idEquipe','idCourse', 'helloTransactionEquipe', 'helloOrderidEquipe'])) {
                        continue;
                    }
                    ?>
                    <tr>
                        <th><?php echo htmlspecialchars(formatFieldLabel($field)); ?></th>
                        <td>
                            <?php
                            if ($field == 'paiementEquipe') {
                                echo formatFieldValue($field, $value);
                            } else {
                                echo htmlspecialchars(formatFieldValue($field, $value));
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Coureur 1 -->
        <div class="table-container">
            <h4>Coureur 1</h4>
            <table class="table">
                <!-- Certificat en premier -->
                <tr>
                    <th><?php echo htmlspecialchars(formatFieldLabel('certificatCoureur')); ?></th>
                    <td>
                        <?php if (!empty($runner1['certificatCoureur'])): ?>
                            <a href="certificats/<?php echo htmlspecialchars($runner1['certificatCoureur']); ?>" target="_blank" class="btn btn-primary btn-view">Voir le certificat</a>
                            <!-- Bouton de suppression -->
                            <form action="" method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete_certif">
                                <input type="hidden" name="idEquipe" value="<?php echo $teamInfo['idEquipe']; ?>">
                                <input type="hidden" name="idCoureur" value="<?php echo $runner1['idCoureur']; ?>">
                                <input type="hidden" name="numCoureur" value="1">
                                <button type="submit" class="btn btn-danger btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce certificat ?');">Supprimer le certificat</button>
                            </form>
                        <?php else: ?>
                            Aucun certificat enregistré.
                        <?php endif; ?>

                        <!-- Formulaire d'upload -->
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload_certif">
                            <input type="hidden" name="idEquipe" value="<?php echo $teamInfo['idEquipe']; ?>">
                            <input type="hidden" name="idCoureur" value="<?php echo $runner1['idCoureur']; ?>">
                            <input type="hidden" name="numCoureur" value="1">

                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file1" name="certificatCoureur1" required>
                                    <label class="custom-file-label" for="file1">Choisir un fichier</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary custom-btn">ENVOYER</button>
                        </form>
                    </td>
                </tr>
                <?php foreach ($runner1 as $field => $value): ?>
                    <?php
                    // Champs à exclure
                    if (in_array($field, ['idCoureur', 'idCourse','idEquipe', 'numCoureur', 'numfideliteCoureur', 'certificatCoureur'])) {
                        continue;
                    }
                    ?>
                    <tr>
                        <th><?php echo htmlspecialchars(formatFieldLabel($field)); ?></th>
                        <td>
                            <?php
                            if ($field == 'certificatValideCoureur' || $field == 'paiementEquipe')  {
                                echo formatFieldValue($field, $value);
                            } else {
                                echo htmlspecialchars(formatFieldValue($field, $value));
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

        <!-- Coureur 2 -->
        <div class="table-container">
            <h4>Coureur 2</h4>
            <table class="table">
                <!-- Certificat en premier -->
                <tr>
                    <th><?php echo htmlspecialchars(formatFieldLabel('certificatCoureur')); ?></th>
                    <td>
                        <?php if (!empty($runner2['certificatCoureur'])): ?>
                            <a href="certificats/<?php echo htmlspecialchars($runner2['certificatCoureur']); ?>" target="_blank" class="btn btn-primary btn-view">Voir le certificat</a>
                            <!-- Bouton de suppression -->
                            <form action="" method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="delete_certif">
                                <input type="hidden" name="idEquipe" value="<?php echo $teamInfo['idEquipe']; ?>">
                                <input type="hidden" name="idCoureur" value="<?php echo $runner2['idCoureur']; ?>">
                                <input type="hidden" name="numCoureur" value="2">
                                <button type="submit" class="btn btn-danger btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce certificat ?');">Supprimer le certificat</button>
                            </form>
                        <?php else: ?>
                            Aucun certificat enregistré.
                        <?php endif; ?>

                        <!-- Formulaire d'upload -->
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="upload_certif">
                            <input type="hidden" name="idEquipe" value="<?php echo $teamInfo['idEquipe']; ?>">
                            <input type="hidden" name="idCoureur" value="<?php echo $runner2['idCoureur']; ?>">
                            <input type="hidden" name="numCoureur" value="2">

                            <div class="form-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file2" name="certificatCoureur2" required>
                                    <label class="custom-file-label" for="file2">Choisir un fichier</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary custom-btn">ENVOYER</button>
                        </form>
                    </td>
                </tr>
                <?php foreach ($runner2 as $field => $value): ?>
                    <?php
                    // Champs à exclure
                    if (in_array($field, ['idCoureur', 'idCourse','idEquipe', 'numCoureur', 'numfideliteCoureur', 'certificatCoureur'])) {
                        continue;
                    }
                    ?>
                    <tr>
                        <th><?php echo htmlspecialchars(formatFieldLabel($field)); ?></th>
                        <td>
                            <?php
                            if ($field == 'certificatValideCoureur' || $field == 'paiementEquipe' )  {
                                echo formatFieldValue($field, $value);
                            } else {
                                echo htmlspecialchars(formatFieldValue($field, $value));
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

    <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script>
    // Afficher le nom du fichier uploadé
    $('.custom-file-input').on('change', function (event) {
        var inputFile = event.currentTarget;
        $(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
    });
</script>
</body>
</html>
