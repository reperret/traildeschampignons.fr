<?php
include 'api/bdd.php';
include 'api/fonctions.php';

$coureursSansCertificat = getCoureursSansCertificat($dbh);

$message = ""; // Variable pour le message de confirmation ou d'erreur
$messageType = ""; // Type de message: 'success' ou 'danger'

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['relancerTous'])) {
    $relanceReussie = true;

    foreach ($coureursSansCertificat as $coureur) {
        $motif = empty($coureur['certificatCoureur']) ? 'certificat non présent' : 'certificat non validé';
        
        // Envoi de mail de relance (fonction sendMail)
        $titre = "Rappel : Certificat Médical ou PPS requis pour le Trail des Champignons";
        $contenu = "Bonjour {$coureur['prenomCoureur']} {$coureur['nomCoureur']}, <br><br>

Attention ! Si vous recevez ce mail c'est sûrement que nous n'avons pas reçu de certificat médical (< 1 an) ou de PPS (< 3 mois) associé à votre inscription ! Pour rappel, il est obligatoire de nous fournir un justificatif pour participer à la course le jour J. Si vous pensez avoir déjà fourni un justificatif mais que vous recevez ce mail, merci de suivre la procédure tout de même, et désolé par avance si c'est un râté de notre part.
<br><br>
À la semaine prochaine ! (d'ici là, mangez au moins 1 champignon par jour)
<br><br>
L'organisation";
        $lienBouton = "https://traildeschampignons.fr/uploadcertif.php?eFromR=".trim($coureur['emailCoureur']);
        $template = 7;
        
        if (sendMail($coureur['emailCoureur'], $titre, $contenu, "Ajouter mon certificat", $lienBouton, '', '', '', '', $template, "Relance Certificat")) {
            enregistrerRelanceCertificat($coureur['idCoureur'], $motif, $dbh);
        } else {
            $relanceReussie = false;
        }
    }

    if ($relanceReussie) {
        $message = "Les relances ont été envoyées avec succès.";
        $messageType = "success";
    } else {
        $message = "Une erreur est survenue lors de l'envoi des relances.";
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Relances Certificats Médicaux</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .badge-gray { background-color: #6c757d; color: white; }
        .badge-orange { background-color: #fd7e14; color: white; }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Relances pour Certificats Médicaux</h2>
    
    <!-- Affichage du message de confirmation ou d'erreur -->
    <?php if ($message): ?>
        <div class="alert alert-<?= $messageType ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="relancerTous" class="btn btn-danger mb-3">Relancer tous les certificats manquants</button>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Équipe</th>
                <th>Course</th>
                <th>Statut du Certificat</th>
                <th>Nombre de Relances</th>
                <th>Dates des Relances</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($coureursSansCertificat as $coureur): ?>
                <tr>
                    <td><?= htmlspecialchars($coureur['nomCoureur']) ?></td>
                    <td><?= htmlspecialchars($coureur['prenomCoureur']) ?></td>
                    <td><?= htmlspecialchars($coureur['emailCoureur']) ?></td>
                    <td><?= htmlspecialchars($coureur['nomEquipe']) ?></td>
                    <td><?= htmlspecialchars($coureur['libelleCourse']) ?></td>
                    <td>
                        <?php if (empty($coureur['certificatCoureur'])): ?>
                            <span class="badge badge-gray">Aucun certificat</span>
                        <?php else: ?>
                            <span class="badge badge-orange">Non validé</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($coureur['nbRelances']) ?></td>
                    <td><?= htmlspecialchars($coureur['datesRelances']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
