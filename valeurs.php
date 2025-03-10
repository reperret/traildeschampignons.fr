<?php
try {
    include 'api/bdd.php';
    include 'api/fonctions.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>TDC - NOS VALEURS</title>
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114" href="img/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144" href="img/apple-touch-icon-144x144-precomposed.png">
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/menu.css" rel="stylesheet">
    <link href="css/vendors.css" rel="stylesheet">
    <link href="css/icon_fonts/css/all_icons_min.css" rel="stylesheet">
    <link href="css/custom.css" rel="stylesheet">
</head>

<body>
    <div class="layer"></div>
    <div id="preloader">
        <div data-loader="circle-side"></div>
    </div>

    <?php include "header.php";?>

    <main>
        <div class="image-container-values">
            <div class="overlay-values">
                <p class="title-big-brown">NOS &nbsp;</p>
                <p class="title-big-orange">VALEURS</p>
            </div>
        </div>
        <div class="bg_color_1">
            <div class="container margin_70_60">
                <div class="row">
                    <div class="col-md-6 centered-in-col">
                        <img src="img/assets/logo-ste.jpg" width="30%" alt="Logo STE" class="img-fluid">
                    </div>
                    <div class="col-md-6 text-justify">
                        <h4 class="title-section">LA GENÈSE</h4>
                        <p>Enchanté, nous sommes une petite tribu de <strong>16 membres</strong> !</p>
                        <p>Chacun avec son propre parcours, son propre niveau et sa propre pratique.</p>
                        <p>Animés par le désir de retrouver l'essence même de ce sport, se déplacer au cœur de la nature sauvage en harmonie avec le territoire qui nous entoure, nous avons décidé de fonder une association en 2023 : <strong>SO TRAIL EXPERIENCE</strong>.</p>
                        <p>Portés par notre détermination commune, notre association vise à réunir les amoureux du trail autour de valeurs telles que le <strong>SPORT, L'AUTHENTICITE ET LE PARTAGE</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg_color_beige">
            <div class="container margin_70_60">
                <div class="row">
                    <div class="col-md-6 text-justify">
                        <h4 class="title-section">MISSION</h4>
                        <p>Notre quête principale est d’inspirer les adeptes du trail à réinventer leur passion en renouant avec l’origine de cette pratique.</p>
                        <p>Nous cherchons donc à éveiller les consciences sur l'empreinte que laisse notre activité sur <strong>L'ENVIRONNEMENT</strong>, ainsi que sur les liens humains tissés au fil des sentiers.</p>
                    </div>
                    <div class="col-md-6">
                        <div class="image-container-values-members"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg_color_1">
            <div class="container margin_70_60">
                <div class="row">
                    <div class="col-md-6 centered-in-col">
                        <img src="img/assets/mushroom.png" width="35%" alt="Logo STE" class="img-fluid">
                    </div>
                    <div class="col-md-6 text-justify">
                        <h4 class="title-section">VALEURS</h4>
                        <p>Nous souhaitons que le <strong>TRAIL DES CHAMPIGNONS</strong> incarne bien plus qu'une simple compétition sportive.</p>
                        <p>C'est un événement ancré dans <strong>L'AUTHENTICITE, LA VALORISATION DU TERROIR ET LE RESPECT DE LA NATURE</strong>, contribuant ainsi à faire briller le territoire de l'Ardèche.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg_color_beige">
            <div class="container margin_70_60">
                <div class="text-justify">
                    <h4 class="title-section">NOTRE EVENEMENT</h4>
                    <p>Dans l'écrin verdoyant de l'Ardèche se profile la toute première édition du Trail des Champignons.</p>
                    <p>À travers cette aventure, vous plongerez dans un univers où les saveurs locales se mêlent aux sentiers sinueux, où la course se vit en duo, propageant l'esprit de camaraderie.</p>
                    <p>Mais surtout, vous prendrez conscience de l'empreinte que laisse votre pratique sur ce territoire, et comment vous pouvez contribuer à la préserver.</p>
                </div>
            </div>
        </div>
    </main>

    <?php include 'footer.php';?>

    <div id="toTop"></div>
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
} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
?>
