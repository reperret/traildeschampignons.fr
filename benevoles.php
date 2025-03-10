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

    <title>TDC - BENEVOLES</title>

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
                <p class="title-big-brown">DEVENIR &nbsp;</p>
                <p class="title-big-orange">BENEVOLE</p>
            </div>
        </div>
        <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="row">
                    <div class="col-md-6 centered-in-col">
                        <img src="img/benevoles.jpg" width="60%" alt="Logo STE" class="img-fluid">
                    </div>

                    <div class="col-md-6">
                        <h4 class="title-section">NOUS AVONS BESOIN DE TOI</h4>
                        <p class="text-justify">
                            Afin de préserver les coureurs de l'abandon<br>
                            Afin de rallier les trailers dans la bonne direction<br>
                            Afin de les nourrir jusqu'à la satiété<br>
                            Afin d'assurer leur sécurité<br>
                            Signaleurs<br>
                            Ravitailleurs<br>
                            Bénévoles plus motivés que jamais<br>
                            <strong> Rejoignez-nous !</strong>

                            <center>
                                <a href="https://wa.me/33634240620" target="_blank">
                                    <button style="background-color: #25D366; margin-top:20px; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 15px; display: inline-flex; align-items: center; box-shadow: 2px 2px 5px #ddd;">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" style="height: 24px; margin-right: 10px;">
                                        Contacter par WhatsApp
                                    </button>
                                </a>
                            </center>


                            <center>
                                <a href="mailto:contact@traildeschampignons.fr" target="_blank">
                                    <button style="background-color: white; margin-top:20px; color: black; padding: 10px 20px; border: none; cursor: pointer; border-radius: 15px; display: inline-flex; align-items: center; box-shadow: 2px 2px 5px #ddd;">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/e/ec/Circle-icons-mail.svg" alt="WhatsApp" style="height: 24px; margin-right: 10px;">
                                        Contacter par mail
                                    </button>
                                </a>
                            </center>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg_color_beige">
            <div class="container margin_120_95">
                <div class="row">

                    <div class="col-md-6">
                        <h4 class="title-section">MISSION</h4>
                        <p class="text-justify">

                            Le trail des Champignons a besoin de vous afin d'assurer une épreuvre dans les meilleurs conditions<br><br>
                            Que ce soit pour :


                        <ul>
                            <li>Accueillir coureurs et public</li>
                            <li>Remettre les dossards</li>
                            <li>Préparer la remise des récompenses</li>
                            <li>Préparer le service des repas</li>
                            <li>Ravitailler</li>
                            <li>Signaler sur les parcours</li>
                            <li>Baliser et débaliser les parcours</li>
                            <li>Ouvrir et fermer les parcours</li>
                        </ul>

                        <br><br>
                        <strong>Venez vivre de l'intérieur le plus champignonesque des trails ! </strong>

                        <center>
                            <a href="https://wa.me/33634240620" target="_blank">
                                <button style="background-color: #25D366; margin-top:20px; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 15px; display: inline-flex; align-items: center; box-shadow: 2px 2px 5px #ddd;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" style="height: 24px; margin-right: 10px;">
                                    Contacter par WhatsApp
                                </button>
                            </a>
                        </center>


                        <center>
                            <a href="mailto:contact@traildeschampignons.fr" target="_blank">
                                <button style="background-color: white; margin-top:20px; color: black; padding: 10px 20px; border: none; cursor: pointer; border-radius: 15px; display: inline-flex; align-items: center; box-shadow: 2px 2px 5px #ddd;">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/e/ec/Circle-icons-mail.svg" alt="WhatsApp" style="height: 24px; margin-right: 10px;">
                                    Contacter par mail
                                </button>
                            </a>
                        </center>



                        </p>
                    </div>

                    <div class="col-md-6 centered-in-col">
                        <img src="img/benevoles2.jpg" width="60%" alt="Logo STE" class="img-fluid">
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
