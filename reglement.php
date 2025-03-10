<?php
include 'api/bdd.php';
include 'api/fonctions.php';

// Construire l'URL de redirection
$redirectUrl = getBaseUrl() . "/documents/reglementTrailChampignons2024.pdf";

// Rediriger vers l'URL
header("Location: $redirectUrl");
exit; // Assure que le script PHP s'arrête après la redirection

?>
