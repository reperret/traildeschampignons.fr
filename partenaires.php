<?php
try
{

include 'api/bdd.php';
include 'api/fonctions.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>TDC - PARTENAIRES</title>

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

    <!-- YOUR CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">

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
                <p class="title-big-brown">NOS &nbsp;</p>
                <p class="title-big-orange">PARTENAIRES</p>
            </div>
        </div>
        <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="row">

                    <div class="col-md-12">

                        <p class="text-justify">

                            <?php
                        $listingPartenaires = getPartenaires($dbh);

                        // Tri des partenaires par catégorie
                        $partenairesParCategorie = [];
                        foreach ($listingPartenaires as $partenaire) {
                            $categorie = $partenaire['categoriePartenaire'];
                            if (!isset($partenairesParCategorie[$categorie])) {
                                $partenairesParCategorie[$categorie] = [];
                            }
                            $partenairesParCategorie[$categorie][] = $partenaire;
                        }

                        // Affichage des partenaires par catégorie
                        foreach ($partenairesParCategorie as $categorie => $partenaires) {
                            echo "<br><br><h4 class=\"title-section\">" . htmlspecialchars($categorie) . "</h1><br><br>"; // Utiliser htmlspecialchars pour éviter les failles XSS
                            foreach ($partenaires as $partenaire) {
                                ?>
                            <a href="<?php echo htmlspecialchars($partenaire['lienPartenaire']); ?>" target="_blank">
                                <img src="img/logosPartenaires/<?php echo htmlspecialchars($partenaire['logoPartenaire']); ?>">
                            </a>
                            <?php
    }
}
?>
                        </p>
                    </div>
                </div>
            </div>
        </div>





    </main>

    <?php include 'footer.php';?>

    <div id="toTop"></div>
    <!-- Back to top button -->

    <!-- COMMON SCRIPTS -->
    <script src="js/jquery-2.2.4.min.js"></script>
    <script src="js/common_scripts.min.js"></script>
    <script src="js/functions.js"></script>

    <script>
        const container = document.querySelector('.image-container-values-members');

        for (let i = 0; i < 16; i++) {
            const img = document.createElement('img');
            img.src = 'img/members/' + i + '.jpg';
            img.alt = 'Description de l\'image';
            container.appendChild(img);
        }

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
