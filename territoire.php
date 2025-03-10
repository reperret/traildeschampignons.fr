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

    <title>TDC - LE TERRITOIRE</title>

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
                <p class="title-big-brown">LE &nbsp;</p>
                <p class="title-big-orange">TERRITOIRE</p>
            </div>
        </div>
        <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="row">
                    <div class="col-md-6 centered-in-col">
                        <img src="img/assets/vivarais_plateau_image.png" width="50%" alt="vivarais_plateau_image" class="img-fluid">
                    </div>
                    <div class="col-md-6">
                        <h4 class="title-section">LE PLATEAU DU VIVARAIS</h4>
                        <p class="text-justify">Le plateau du Vivarais, également connu sous le nom de Massif du Vivarais, est une région emblématique située dans le sud-est de la France, principalement en Ardèche, mais également en partie dans la Haute-Loire. Ce plateau, caractérisé par ses paysages variés et sa géologie particulière, joue un rôle central dans l'identité et l'histoire de la région.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg_color_beige">
            <div class="container margin_120_95">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="title-section">HABITANTS</h4>
                        <p class="text-justify">Historiquement, le plateau du Vivarais a été habité depuis la préhistoire, comme en témoignent les nombreux sites archéologiques découverts dans la région. Le Vivarais est une région historique du sud-est de la France, à partir du milieu du Moyen-âge (xe siècle), rattachée au territoire du Saint-Empire romain germanique.</p>
                    </div>
                    <div class="col-md-6 centered-in-col">
                        <img src="img/assets/vivarais_habitants.png" width="50%" alt="vivarais_habitants" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
        <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="row">
                    <div class="col-md-6 centered-in-col">
                        <img src="img/assets/environmental_protection.png" width="50%" alt="environmental_protection" class="img-fluid">
                    </div>
                    <div class="col-md-6">
                        <h4 class="title-section">ENVIRONNEMENT</h4>
                        <p class="text-justify">
                            La préservation de l'environnement naturel et culturel du plateau du Vivarais est une préoccupation majeure pour les autorités locales et les habitants. Des efforts sont déployés pour protéger la biodiversité unique de la région, ainsi que pour promouvoir un tourisme durable et respectueux de l'environnement.
                            <br><br>En somme, cette région est bien plus qu'une simple étendue de terre ; c'est un territoire chargé d'histoire, de beauté naturelle et de traditions, qui continue d'inspirer et d'enchanter ceux qui le découvrent.
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
