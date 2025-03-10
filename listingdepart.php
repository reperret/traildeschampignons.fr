<?php 
try 
{

include 'api/bdd.php';
include 'api/fonctions.php';
    
if(isset($_GET['idCourse']) && $_GET['idCourse']!='' )     $idCourse=$_GET['idCourse'];  

$MaCourse=getCourses($idCourse,$dbh);
$coureurs=getCoureurs($idCourse ,$dbh);
$heureDepartCourse=$MaCourse['heureDepartCourse'];
    
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Find easily a doctor and book online an appointment">
    <meta name="author" content="Ansonika">
    <title>LISTES DEPART - TRAIL DES CHAMPIGNONS</title>

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
        <div id="breadcrumb">
            <div class="container">
                <ul>
                    <li><a href="index.php">Accueil</a></li>
                    <li>Liste de départ</li>
                    <li><?php echo $MaCourse['libelleCourse'];?></li>
                </ul>
            </div>
        </div>
        <!-- /breadcrumb -->

        <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="main_title">
                    <h1>Start list</h1>
                    <h2><?php echo $MaCourse['libelleCourse'];?></h2>
                    <p>Départ : <?php echo  date('d/m/Y H:i:s', strtotime($MaCourse['heureDepartCourse'])); ;?></p>

                </div>
                <div class="row justify-content-between">


                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Dossard</th>
                                    <th>Coureurs</th>
                                    <th>Equipe</th>
                                    <th>Catégorie</th>
                                </tr>
                            </thead>
                            <tbody>


                                <?php 

            foreach($coureurs as $coureur)
            {
               
            ?>
                                <tr>
                                    <td><strong><?php echo $coureur['dossardCoureur'];?></strong></td>
                                    <td><?php echo $coureur['libelleCoureur'];?></td>
                                    <td><?php echo $coureur['equipeCoureur'];?></td>
                                    <td><?php echo formatCategorie($coureur['categorieCoureur']);?></td>
                                </tr>
                                <?php
            }
            ?>



                            </tbody>
                        </table>
                    </div>




                </div>
                <!--/row-->
            </div>
            <!--/container-->
        </div>
        <!--/bg_color_1-->



    </main>
    <!-- /main -->

    <footer>
        <div class="container margin_60_35">

            <hr>
            <div class="row">
                <div class="col-md-8">
                    <ul id="additional_links">
                        <li><a href="#0">Terms and conditions</a></li>
                        <li><a href="#0">Privacy</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <div id="copy">© 2017 Findoctor</div>
                </div>
            </div>
        </div>
    </footer>
    <!--/footer-->

    <div id="toTop"></div>
    <!-- Back to top button -->

    <!-- COMMON SCRIPTS -->
    <script src="js/jquery-2.2.4.min.js"></script>
    <script src="js/common_scripts.min.js"></script>
    <script src="js/functions.js"></script>

</body>

</html>


<?php
} 
catch (Exception $e) 
{
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
?>
