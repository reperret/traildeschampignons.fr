<?php 
try 
{

include 'api/bdd.php';
include 'api/fonctions.php';
if(isset($_GET['idCourse']) && $_GET['idCourse']!='' )     $idCourse=$_GET['idCourse'];  
if(isset($_POST['idCourse']) && $_POST['idCourse']!='' )     $idCourse=$_POST['idCourse']; 
  
$categorie="ALL";
if(isset($_POST['categorie']) && $_POST['categorie']!='' )     $categorie=$_POST['categorie']; 

$classement=getClassement($idCourse, $categorie, $dbh);
$MaCourse=getCourses($idCourse,$dbh);

$repertoireVideos = "/var/www/traildeschampignons.sotrail.fr/videosArrivee";
$listingVideosDisponibles=listerVideosArriveesDisponibles($repertoireVideos);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Find easily a doctor and book online an appointment">
    <meta name="author" content="Ansonika">
    <title>RESULTATS - TRAIL DES CHAMPIGNONS</title>

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

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
                    <h1>Résultats</h1>
                    <h2><?php echo $MaCourse['libelleCourse'];?></h2>
                    <p>Départ : <?php echo  date('d/m/Y H:i:s', strtotime($MaCourse['heureDepartCourse'])); ;?></p>


                </div>
                <div class="row justify-content-between">


                    <form method="post" action="resultats.php" id="classementForm">


                        <div class="row">

                            <div class="form-group">
                                <select name="categorie" id="categorie" class="form-control">
                                    <option value="ALL" <?php if($categorie=="ALL") echo " selected";?>>Classement scratch</option>
                                    <option value="M" <?php if($categorie=="M")   echo " selected";?>>Equipes mixtes</option>
                                    <option value="F" <?php if($categorie=="F")   echo " selected";?>>Equipes féminines</option>
                                    <option value="H" <?php if($categorie=="H")   echo " selected";?>>Equipes masculines</option>
                                </select>
                            </div>
                        </div>


                        <input type="hidden" name="idCourse" value="<?php echo $idCourse;?>">
                    </form>


                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Cl</th>
                                    <th>Vidéo</th>
                                    <th>Dossard</th>
                                    <th>Coureurs</th>
                                    <th>Equipe</th>
                                    <th>Cat.</th>
                                    <th>Temps</th>
                                    <th>Arrivée</th>
                                    <th>Ecart</th>

                                </tr>
                            </thead>
                            <tbody>


                                <?php 
            $cl=1;
            $first=true;
            foreach($classement as $coureur)
            {
                if($first)
                {
                    $tempsPremier=$coureur['tempsCoureur'];
                    $first=false;
                }
            ?>
                                <tr>
                                    <td><?php echo $cl;?></td>
                                    <td>
                                        <?php
                                        if(in_array($coureur['idPassage'].".mp4",$listingVideosDisponibles))
                                        {
                                            ?><a href="videosArrivee/<?php echo $coureur['idPassage'];?>.mp4" target="_blank"><i class="fa-solid fa-video"></i></a><?php
                                        }
                                        ?>
                                    </td>


                                    <td><span class="badge badge-pill badge-dark"><?php echo $coureur['dossardCoureur'];?></span></td>
                                    <td><?php echo $coureur['libelleCoureur'];?></td>
                                    <td><?php echo $coureur['equipeCoureur'];?></td>
                                    <td><?php echo formatCategorie($coureur['categorieCoureur']);?></td>
                                    <td><?php echo $coureur['tempsCoureur'];?></td>
                                    <td><?php 
                                        $now = new DateTime($coureur['heurePassage']);
                                        echo $now->format('H:i:s');
                                        ?></td>
                                    <td>+<?php echo ecartPremierTemps($coureur['tempsCoureur'], $tempsPremier);?></td>

                                </tr>
                                <?php
                $cl++;
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

    <?php include 'footer.php';?>

    <div id="toTop"></div>
    <!-- Back to top button -->

    <!-- COMMON SCRIPTS -->
    <script src="js/jquery-2.2.4.min.js"></script>
    <script src="js/common_scripts.min.js"></script>
    <script src="js/functions.js"></script>


    <script>
        // Récupérez la liste déroulante et le formulaire
        const selectElement = document.getElementById("categorie");
        const formElement = document.getElementById("classementForm");

        // Ajoutez un gestionnaire d'événement de changement à la liste déroulante
        selectElement.addEventListener("change", function() {
            // Soumettez automatiquement le formulaire lorsque la valeur change
            formElement.submit();
        });

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
