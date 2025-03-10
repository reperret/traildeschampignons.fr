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

    <title>TDC - HEBERGEMENTS</title>

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
                <p class="title-big-orange">HEBERGEMENTS</p>
            </div>
        </div>
        
               <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="row">
                    <div class="col-md-6 centered-in-col">
                        <img src="img/hebergement_illustration.jpg" width="50%" alt="vivarais_plateau_image" class="img-fluid">
                    </div>
                    <div class="col-md-6">
                        <h4 class="title-section">LA COLONIE</h4>
                        <p class="text-justify">
                        
                        Nous mettons à disposition un hébergement situé dans le village du départ de la course. Il s’agit d’un grand établissement sous forme de dortoirs qui comporte 112 places. Outre les personnes oragnisatrices du Trail ainsi que les bénévoles, nous souhaitons donner accès au plus grand nombre à cet hébergement. Nous proposons donc un tarif à 30€ pour deux nuits ou 25€ une nuit. Au-delà du côté pratique (à 5 minutes à pieds du départ), vous serez au coeur de l’ambiance qui, on l’espère, sera festive après la course ! <strong>Inscrivez vous avant le vendredi 18 octobre si vous souhaitez une place (vous ainsi que vos proches)</strong>

                           
                
                            <br><br>
                            <a href="https://www.helloasso.com/associations/so-trail-experience/evenements/trail-des-champignons" class="btn_1" target="_blank">RESERVER UNE PLACE</a>

                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        
        
        <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="row">
                    <div class="col-md-6 centered-in-col">
                        <img src="img/hebergement_illustration.jpg" width="50%" alt="vivarais_plateau_image" class="img-fluid">
                    </div>
                    <div class="col-md-6">
                        <h4 class="title-section">LE PLATEAU DU VIVARAIS</h4>
                        <p class="text-justify">Les terres Ardéchoises vous proposent une farandole d’hébergements, entre hôtels, gîtes, chambres d’hôtes, ou campings, faites votre choix pour trouver le meilleur endroit pour dormir et discuter avec Morphée de votre stratégie de course.Pensez à réserver rapidement votre logement, car la foire aux champignons attire énormément de monde (sûrement plus que les jeux olympiques). Retrouvez ci-dessous les liens des offices de tourisme de Saint André en Vivarais et d’Ardèches Hautes Vallées, afin de vous aiguiller vers les hébergements proches de la course.

                            <br><br>

                            <a href="https://www.saintandreenvivarais.fr/spip.php?article20" target="_blank"><img src="img/hebergement_saintandre.png"></a><br>
                            <a href="https://www.ardeche-hautes-vallees.fr/mon-sejour/hebergements/" target="_blank"><img src="img/hebergement_ardeche.png"></a>

                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg_color_beige">
            <div class="container margin_120_95">
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="title-section">EXEMPLE D'HEBERGEMENT AU RELAIS SAINT ANDRÉ </h4>
                        <p class="text-justify">Situé à 20 secondes de la ligne de départ (10 secondes en trottinant) le relais Saint-André vous accueille dans un cadre verdoyant. <u><strong>L'hébergement n'est pas compris dans le prix de l'inscription, nous vous fournissons uniquement ici des informations à titre indicatif</strong></u><br><br>
                        HEBERGEMENT PARTENAIRE : AU RELAIS SAINT ANDRÉ<br>   
                        Adresse : Le village , 07690 Saint André en Vivarais.<br>
                            Mail : <a href="mailto:lesaintandre07@orange.fr">lesaintandre07@orange.fr</a><br>
                            Téléphone : 04.75.30.03.72 <br>
                            Internet : <a href="https://www.au-relais-saint-andre.com">www.au-relais-saint-andre.com</a><b></b>
                        </p>
                    </div>
                    <div class="col-md-6 centered-in-col">
                        <img src="img/hebergement_relais.png" width="50%" alt="vivarais_habitants" class="img-fluid">
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
