<?php
// Inclusion du fichier bdd.php pour la connexion à la base de données et les variables nécessaires
require_once 'api/bdd.php'; // Assure-toi que ce fichier est au bon emplacement

// Page de vérification de l'inscription - resume_inscription.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $email = strtolower(trim($_POST['email'])); // Trim les espaces et convertir en minuscule
    $jour = $_POST['jour'];
    $mois = $_POST['mois'];
    $annee = $_POST['annee'];
    $dateNaissance = "$annee-$mois-$jour";

    // Connexion à la base de données via l'inclusion de bdd.php
    // $dbh est déjà défini dans bdd.php

    // Rechercher le coureur correspondant
    $stmt = $dbh->prepare("SELECT idCoureur, idEquipe FROM coureur WHERE LOWER(TRIM(emailCoureur)) = :email AND ddnCoureur = :ddn");
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':ddn', $dateNaissance);
    $stmt->execute();
    $coureur = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($coureur) {
        // Redirection vers la page de résumé d'inscription avec les paramètres
        header("Location: resume_inscription.php?idEquipe=" . $coureur['idEquipe']);
        exit;
    } else {
        $error = "Aucun coureur trouvé avec cet email et cette date de naissance.";
    }
}

// Page de résumé de l'inscription et upload du certificat - resume_inscription.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idCoureur'])) {
    $idCoureur = $_POST['idCoureur'];
    $inputName = "file";

    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] === UPLOAD_ERR_OK) {
        // Récupération de l'extension du fichier
        $fileExtension = pathinfo($_FILES[$inputName]['name'], PATHINFO_EXTENSION);
        $fileName = "certif_" . $idCoureur . "_" . date('YmdHis') . ".$fileExtension";
        $targetDirectory = "certificats";
        $targetFilePath = $targetDirectory . "/" . $fileName;

        if (move_uploaded_file($_FILES[$inputName]['tmp_name'], $targetFilePath)) {
            // Mise à jour de la base de données
            $stmt = $dbh->prepare("UPDATE coureur SET certificatCoureur = :fileName WHERE idCoureur = :idCoureur");
            $stmt->bindParam(':fileName', $fileName);
            $stmt->bindParam(':idCoureur', $idCoureur);
            if ($stmt->execute()) {
                $success = "Certificat mis à jour avec succès.";
            } else {
                $error = "Erreur lors de la mise à jour du certificat.";
            }
        } else {
            $error = "Échec de l'upload du fichier.";
        }
    } else {
        $error = "Veuillez sélectionner un fichier valide.";
    }
}

if (isset($_GET['idEquipe'])) {
    $idEquipe = $_GET['idEquipe'];

    // Récupération des informations de l'équipe et des coureurs
    $stmt = $dbh->prepare("SELECT * FROM equipe WHERE idEquipe = :idEquipe");
    $stmt->bindParam(':idEquipe', $idEquipe);
    $stmt->execute();
    $equipe = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare("SELECT * FROM coureur WHERE idEquipe = :idEquipe");
    $stmt->bindParam(':idEquipe', $idEquipe);
    $stmt->execute();
    $coureurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérifier son inscription / Résumé</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background: linear-gradient(to bottom, #F5F5F5, #D3A87A) fixed; /* Dégradé du blanc vers marron clair, fixe */
            background-repeat: no-repeat;
            font-family: 'Open Sans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }

        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        label { font-weight: bold; color: maroon; }

        .custom-btn {
            background-color: #A67C52; /* Marron clair */
            border-color: #A67C52; /* Même couleur pour la bordure */
            color: #fff; /* Couleur du texte en blanc */
            font-weight: 600; /* Pour rendre le texte un peu plus visible */
        }

        .custom-btn:hover {
            background-color: #8B5E34; /* Marron légèrement plus foncé au survol */
            border-color: #8B5E34; /* Même couleur au survol */
        }

        .alert {
            position: relative;
            text-align: center;
            z-index: 1000;
        }

        h3 {
            font-weight: 600;
        }

        .custom-file-label::after {
            content: "Parcourir";
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Logo centré en haut -->
    <div class="text-center mb-4">
        <img src="img/logo_2x.png" alt="Logo" style="max-width: 326px;">
    </div>

    <h3 class="text-center">Vérifier son inscription</h3><br><br>
    <form action="" method="POST">
        <div class="form-group">
            <label for="email">Email :</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
        </div>
        <div class="form-group">
            <label>Date de naissance :</label>
            <div class="form-row">
                <div class="col">
                    <label for="jour">Jour</label>
                    <select class="form-control" name="jour" required>
                        <?php for ($i = 1; $i <= 31; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if (($jour ?? '') == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col">
                    <label for="mois">Mois</label>
                    <select class="form-control" name="mois" required>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if (($mois ?? '') == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col">
                    <label for="annee">Année</label>
                    <select class="form-control" name="annee" required>
                        <?php for ($i = 1900; $i <= 2024; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php if (($annee ?? '') == $i) echo 'selected'; ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="text-center">
            <button type="submit" class="btn btn-primary custom-btn">Vérifier</button>
        </div>
    </form>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-4">
            <?php echo $error; ?>
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success mt-4">
            <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($equipe) && $equipe && $coureurs): ?>
        <div class="text-center mb-4">
            <img src="img/logo_2x.png" alt="Logo" style="max-width: 326px;">
        </div>
        <h3 class="text-center mt-5">Résumé de l'inscription</h3><br>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th colspan="2">Informations de l'équipe</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>Nom de l'équipe</th>
                <td><?php echo htmlspecialchars($equipe['nomEquipe']); ?></td>
            </tr>
            <tr>
                <th>Commentaire de l'équipe</th>
                <td><?php echo htmlspecialchars($equipe['commentaireEquipe']); ?></td>
            </tr>
            <!-- Autres informations de l'équipe -->
            </tbody>
        </table>

        <h3 class="text-center mt-5">Informations des coureurs</h3><br>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Certificat Médical</th>
                <th>Mettre à jour le certificat</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($coureurs as $index => $coureur): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($coureur['nomCoureur']); ?></td>
                    <td><?php echo htmlspecialchars($coureur['prenomCoureur']); ?></td>
                    <td>
                        <?php if (!empty($coureur['certificatCoureur'])): ?>
                            <a href="certificats/<?php echo htmlspecialchars($coureur['certificatCoureur']); ?>" target="_blank">Voir le certificat</a>
                        <?php else: ?>
                            Aucun certificat
                        <?php endif; ?>
                    </td>
                    <td>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="idCoureur" value="<?php echo $coureur['idCoureur']; ?>">
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="file" name="file" required>
                                    <label class="custom-file-label" for="file">Choisir un fichier</label>
                                </div>
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary custom-btn">Valider</button>
                                </div>
                            </div>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Afficher le nom du fichier uploadé
    $('.custom-file-input').on('change', function (event) {
        var inputFile = event.currentTarget;
        $(inputFile).parent().find('.custom-file-label').html(inputFile.files[0].name);
    });
</script>
</body>
</html>
