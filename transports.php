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

    <title>TDC - TRANSPORTS</title>

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
                <p class="title-big-brown">LES &nbsp;</p>
                <p class="title-big-orange">TRANSPORTS</p>
            </div>
        </div>
        <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="row">
                    <div class="col-md-6 centered-in-col">
                        <img src="img/transport.jpg" width="60%" alt="Togetzer" class="img-fluid">
                    </div>
                    <div class="col-md-6">
                        <h4 class="title-section">Minimisons l'impact du Trail des Champignons !</h4>
                        <p class="text-justify">
                            La commune de Saint André en Vivarais est située à environ 1h30 de Lyon et Valence en voiture. A la frontière entre l'Ardèche et la Haute-Loire.
                            <br><br>
                            Afin de limiter l’impact environnemental de l’évènement nous incitons les participant.es et accompagnant.es à favoriser le co-voiturage en se manifestant sur cette plateforme dédiée ci dessous :

                            <br><br>
                            <a href="https://togetzer.com/covoiturage-evenement/kbltiv" class="btn_1" target="_blank"> CHERCHER OU PROPOSER UN TRANSPORT</a>

                            <br><br>
                            Pour les plus motiv.ées, il est possible de venir en train + vélo :
                            Depuis Lyon : il y a un TER direct de Lyon (Part-dieu ou Jean Macé) jusque Saint Vallier sur Rhône (45min) et ensuite il reste environ 45-50 km et 1200 m de d+ :

                            <br><br>
                            <a href="https://www.komoot.com/fr-fr/tour/1575235082?share_token=ab1jzyAaUxGuQTmQELqcazPzl8hPqHwOPfGisoHOadNATineM7&ref=wtd" class="btn_1" target="_blank"> Lien Komoot</a>
                            <br><br>

                            Des récompenses/incitations sont prévues pour les personnes pouvant justifier d’un trajet en train + vélo.
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
