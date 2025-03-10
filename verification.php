<?php
// Inclusion du fichier bdd.php pour la connexion à la base de données et les variables nécessaires
require_once 'api/bdd.php'; // Assure-toi que ce fichier est au bon emplacement

// Page de vérification de l'inscription - verification.php
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérifier son inscription</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
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
            <button type="submit" class="btn btn-primary">Vérifier</button>
        </div>
    </form>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mt-4">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>