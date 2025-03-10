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
    <meta name="description" content="Find easily a doctor and book online an appointment">
    <meta name="author" content="Ansonika">
    <title>COURSES - TRAIL DES CHAMPIGNONS</title>

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

    <!-- SPECIFIC CSS -->
    <link href="css/tables.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">

    <!-- Modernizr -->
    <script src="js/modernizr_tables.js"></script>

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
        <div id="breadcrumb">
            <div class="container">
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="lescourses.php">Courses</a></li>

                </ul>
            </div>
        </div>
        <!-- /breadcrumb -->
        <div class="margin_60_35">
            <div class="container">
                <div class="main_title">
                    <h1>Les courses 2023</h1>
                    <p>pour tous les goûts</p>
                </div>
            </div>

            <div class="pricing-container cd-has-margins">
                <ul class="pricing-list">
                    <li>
                        <ul class="pricing-wrapper">
                            <li class="is-visible">
                                <header class="pricing-header">
                                    <h2>La Girolle</h2>

                                    <div class="price">
                                        <span class="price-value">10</span>
                                        <span class="currency">€</span>
                                    </div>
                                </header>
                                <!-- /pricing-header -->
                                <div class="pricing-body">
                                    <ul class="pricing-features">
                                        <li><em>15</em> km</li>
                                        <li><em>400</em> D+</li>


                                    </ul>
                                </div>
                                <!-- /pricing-body -->
                                <footer class="pricing-footer">
                                    <a class="select-plan" href="https://www.openrunner.com/route-details/17904102" target="_blank">Voir le parcours</a>
                                </footer>
                            </li>
                        </ul>
                        <!-- /pricing-wrapper -->
                    </li>

                    <li>
                        <ul class="pricing-wrapper">
                            <li class="is-visible">
                                <header class="pricing-header">
                                    <h2>Le Cèpe</h2>
                                    <div class="price">

                                        <span class="price-value">10</span>
                                        <span class="currency">€</span>

                                    </div>
                                </header>
                                <!-- /pricing-header -->
                                <div class="pricing-body">
                                    <ul class="pricing-features">
                                        <li><em>35</em> km</li>
                                        <li><em>1150</em> D+</li>


                                    </ul>
                                </div>
                                <!-- /pricing-body -->
                                <footer class="pricing-footer">
                                    <a class="select-plan" href="https://www.strava.com/routes/3155496205493819656" target="_blank">Voir le pacours</a>
                                </footer>
                            </li>
                        </ul>
                        <!-- /pricing-wrapper -->
                    </li>
                </ul>
                <!-- /pricing-list -->
            </div>
            <!-- /pricing-container -->
        </div>
        <!-- /margin_60_35 -->


    </main>
    <!-- /main -->


    <?php include 'footer.php';?>


    <div id="toTop"></div>
    <!-- Back to top button -->

    <!-- COMMON SCRIPTS -->
    <script src="js/jquery-2.2.4.min.js"></script>
    <script src="js/common_scripts.min.js"></script>
    <script src="js/functions.js"></script>

    <!-- SPECIFIC SCRIPTS -->
    <script src="js/tables_func_2.js"></script>

</body>

</html>

<?php
} 
catch (Exception $e) 
{
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
?>
