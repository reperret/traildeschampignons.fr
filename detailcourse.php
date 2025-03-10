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
    <title>Inscription - Trail des Champignons - Participer au trail en Ardèche</title>
    <meta name="description" content="Inscrivez-vous dès maintenant au Trail des Champignons en Ardèche. Rejoignez-nous pour une aventure sportive et conviviale à travers des sentiers escarpés et des panoramas exceptionnels.">
    <meta name="keywords" content="inscription Trail des Champignons, trail Ardèche inscription, course nature Ardèche, participer trail Ardèche">
    <meta name="author" content="Trail des Champignons">
    <link rel="canonical" href="https://traildeschampignons.fr/detailcourse.php">

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

    <style>
        .race-table {
            width: 80%;
            /* Taille normale sur ordinateur */
            border-collapse: collapse;
            margin: auto;
        }

        .race-table th,
        .race-table td {
            padding: 5px;
            text-align: center;
            border: none;
            /* Déplacé de .race-table td, .race-table th */
        }

        .race-table th {
            font-weight: bold;
            background-color: #ffffff;
        }

        .race-table td {
            background-color: #ffffff;
        }

        .race-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .race-table td.highlight {
            background-color: #ffff00;
        }

        .responsive-image {
            max-width: 100%;
            height: auto;
        }

        /* Styles pour mobile */
        @media (max-width: 768px) {
            .race-table {
                width: 90%;
                /* 90% de la largeur sur mobile */
            }
        }

    </style>

</head>

<body>

    <div class="layer"></div>
    <!-- Mobile menu overlay mask -->

    <div id="preloader">
        <div data-loader="circle-side"></div>
    </div>
    <!-- End Preload -->

    <?php include "header.php";?>
    <?php
    $courses=getCourses($_GET['idCourse'],$dbh);
    $course=$courses;
    ?>

    <main>

        <div class="image-container-values">
            <div class="overlay-values">
                <p class="title-big-brown"><?php echo $course['libelleCourse'];?></p><br>

            </div>
        </div>



        <!-- /breadcrumb -->

        <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="main_title">
                    <strong>
                        <h2><?php echo $course['distanceCourse'];?>km / <?php echo $course['dplusCourse'];?>D+</h2>
                    </strong>
                    <br><br>
                    <?php
                    if($course['idCourse']==$idCourseRando)
                    {
                        ?><p> <a href="inscriptionRando.php" class="btn_1">S'INSCRIRE</a><?php
                    }
                    else{
                        ?>
                    <p> <a href="inscriptions.php?idCourse=<?php echo $course['idCourse'];?>" class="btn_1">S'INSCRIRE</a><?php
                    }
                    ?>
                        <a href="reglement.php" class="btn_1" target="_blank">REGLEMENT</a>
                    </p>

                </div>
                <div class="row justify-content-between margin_60_35">
                    <div class="col-lg-12">
                        <table class="race-table">
                            <tr>
                                <th>KM</th>
                                <th>D+</th>
                                <th>Durée max</th>
                                <th>Jauge limite</th>
                                <th>Départ</th>
                                <th>Ravitos</th>
                            </tr>
                            <tr>
                                <td><?php echo $course['distanceCourse'];?></td>
                                <td><?php echo $course['dplusCourse'];?></td>
                                <td><?php echo $course['tempsMaxCourse'];?></td>
                                <td><?php 
                                if( $course['nbMaxCoureursCourse']!=0) 
                                {
                                    echo $course['nbMaxCoureursCourse']." duos";
                                }
                                else
                                {
                                    echo "pas de limite";
                                }
                                    ?>
                                </td>
                                <td><?php echo date("d/m/Y H:i:s", strtotime($course['heureDepartTheoriqueCourse']));;?></td>
                                <td><?php echo $course['nbRavCourse'];?></td>
                            </tr>
                        </table>

                        <br><br>
                        <center>
                            <h4>Tarifs d'inscription</h4>

                            <?php echo afficherTarif($course['idCourse'], $dbh);?>


                        </center>

                    </div>



                </div>
                <div class="row justify-content-between margin_60_35">
                    <div class="col-lg-6">
                        <figure class="add_bottom_30">
                            <img src="img/about_1.jpg" class="img-fluid" alt="">
                        </figure>
                    </div>
                    <div class="col-lg-5">
                        <p><?php echo render($course['descriptionCourse'], $course); ?></p>
                    </div>


                </div>
                <!--/row-->
            </div>
            <!--/container-->
        </div>


        <div class="margin_60_35 center">


            <center>
                <p> <a href="<?php echo $course['lienParcoursCourse'];?>" target="_blank" class="btn_1">EXPLORER LE PARCOURS</a></p>
                <p> <a href="courses/<?php echo $course['idCourse'];?>.gpx" target="_blank">Télécharger le fichier GPX</a></p>
                <br><br>

                <a href="<?php echo $course['lienParcoursCourse'];?>" target="_blank"> <img src="courses/<?php echo $course['idCourse'];?>.png" class="responsive-image"></a>
            </center>
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
