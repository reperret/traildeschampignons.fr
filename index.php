<?php
try {

    include 'api/bdd.php';
    include 'api/fonctions.php';

    $categorie = "";
    $courses = getCourses(NULL, $dbh);

    // Calculer le nombre total d'équipes inscrites
    $totalEquipes = count(getEquipes(NULL, $dbh));
    $maxEquipes = 100;

    // Calculer le nombre total de participants à la randonnée
    $inscriptionsRando = getInscriptionsRando($dbh);
    $totalParticipantsRando = 0;
    foreach ($inscriptionsRando as $rando) {
        $participants = json_decode($rando['participantsRando'], true);
        $totalParticipantsRando += count($participants);
    }
    $maxParticipantsRando = 100;



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Trail des Champignons - Participer au trail en Ardèche</title>
    <meta name="description"
        content="Inscrivez-vous dès maintenant au Trail des Champignons en Ardèche. Rejoignez-nous pour une aventure sportive et conviviale à travers des sentiers escarpés et des panoramas exceptionnels.">
    <meta name="keywords"
        content="inscriptionTrail des Champignons, trail Ardèche inscription, course nature Ardèche, participer trail Ardèche">
    <meta name="author" content="Trail des Champignons">
    <link rel="canonical" href="https://traildeschampignons.fr">

    <!-- Favicons-->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <link rel="apple-touch-icon" type="image/x-icon" href="img/apple-touch-icon-57x57-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="72x72" href="img/apple-touch-icon-72x72-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="114x114"
        href="img/apple-touch-icon-114x114-precomposed.png">
    <link rel="apple-touch-icon" type="image/x-icon" sizes="144x144"
        href="img/apple-touch-icon-144x144-precomposed.png">

    <!-- GOOGLE WEB FONT -->
    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800" rel="stylesheet">



    <!-- BASE CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="css/menu.css" rel="stylesheet">
    <link href="css/vendors.css" rel="stylesheet">
    <link href="css/icon_fonts/css/all_icons_min.css" rel="stylesheet">

    <!-- REVOLUTION STYLE SHEETS -->
    <link href="rev-slider-files/css/settings.css" rel="stylesheet">

    <!-- YOUR CUSTOM CSS -->
    <link href="css/custom.css" rel="stylesheet">

    <style>
    .btn-marron {
        background-color: #69381C !important;
        color: white !important;
        border: 2px solid #4f2b18 !important;
        /* Ajoute une bordure marron foncé */
        border-radius: 50px;
        font-weight: bold;
        animation: clignote 1s infinite;
    }

    @keyframes clignote {
        0% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }

        100% {
            opacity: 1;
        }
    }


    .progress {
        height: 30px;
        background-color: #f3f3f3;
        border-radius: 25px;
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 20px;
    }



    .progress-bar {
        height: 100%;
        line-height: 30px;
        color: #fff;
        font-weight: bold;
        transition: width 0.6s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        background-image: linear-gradient(45deg, #4caf50, #81c784);
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

    <?php include "header.php"; ?>

    <?php
        $date = new DateTime();
        $start = new DateTime('2024-11-02 06:00:00');
        $end = new DateTime('2025-02-03 20:00:00');

        if ($date >= $start && $date <= $end) {
        ?>
    <!-- Message manuscrit et boutons "Résultats", "Photos", "Vidéo" -->
    <div style="background-color: #69381C; text-align: center; padding: 20px;">

        <div class="main_title">
            <h2 style="color:#FFF;margin-bottom:14px">Merci à tous pour cette édition 2024, à l'année prochaine !</h2>
        </div>
        <a href="https://altichrono.fr/resultats/2024_champignons/" target="_blank" class="btn btn-lg btn-marron">
            Résultats
        </a>
        <a href="https://photos.app.goo.gl/7QDrX1vLVPGPAN3o7" target="_blank" class="btn btn-lg btn-marron">
            Photos
        </a>
        <a href="https://www.youtube.com/watch?v=Vp8QarlKY-I" target="_blank" class="btn btn-lg btn-marron">
            Vidéo
        </a>
    </div>
    <?php
        }
        ?>


    <main>
        <div id="rev_slider_72_1_wrapper" class="rev_slider_wrapper fullwidthbanner-container"
            data-alias="doctor_slider_1" data-source="gallery"
            style="margin:0px auto;background:transparent;padding:0px;margin-top:0px;margin-bottom:0px;">
            <div id="rev_slider_72_1" class="rev_slider fullwidthabanner" style="display:none;" data-version="5.4.1">
                <ul>
                    <!-- SLIDE  -->
                    <li data-index="rs-188" data-transition="fade" data-slotamount="default" data-hideafterloop="0"
                        data-hideslideonmobile="off" data-easein="default" data-easeout="default"
                        data-masterspeed="default" data-thumb="assets/100x50_b9dee-42512210_ml.jpg" data-delay="5150"
                        data-rotate="0" data-saveperformance="off" data-title="Slide" data-param1="" data-param2=""
                        data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8=""
                        data-param9="" data-param10="" data-description="">
                        <!-- MAIN IMAGE -->
                        <img src="rev-slider-files/assets/b9dee-42512210_ml.jpg" alt="" data-bgposition="center center"
                            data-kenburns="on" data-duration="10000" data-ease="Linear.easeNone" data-scalestart="100"
                            data-scaleend="110" data-rotatestart="0" data-rotateend="0" data-blurstart="0"
                            data-blurend="0" data-offsetstart="0 0" data-offsetend="0 0" class="rev-slidebg"
                            data-no-retina>
                        <!-- LAYERS -->

                        <!-- LAYER NR. 1 -->




                        <div class="tp-caption tp-resizeme" id="slide-188-layer-1" data-x="111" data-y="259"
                            data-width="['auto']" data-height="['auto']" data-type="text" data-responsive_offset="on"
                            data-frames='[{"delay":510,"speed":800,"frame":"0","from":"x:50px;opacity:0;","to":"o:1;","ease":"Power4.easeOut"},{"delay":"+3260","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                            data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]"
                            data-paddingright="[0,0,0,0]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]"
                            style="z-index: 5; white-space: nowrap; font-size: 60px; 
                        text-shadow:
                        -4px -4px 0 #704124,
                        4px -4px 0 #704124,
                        -4px 4px 0 #704124,
                        4px 4px 0 #704124;
            line-height: 80px; /* Augmenté pour espace entre lignes */
            font-weight: 700; color: #FFF; 
            
            font-family:Poppins; text-transform:uppercase;
            background-color:rgba(0, 122, 255, 0);">
                            <div>2 NOVEMBRE 2024</div>
                            <div style="margin-top: 20px;">1ère EDITION</div>
                            <div style="margin-top: 20px;">AVEC L'AUTOMNAL GOURMAND</div>
                        </div>

                        <div class="tp-caption tp-resizeme" id="slide-188-layer-2" data-x="111" data-y="259"
                            data-width="['auto']" data-height="['auto']" data-type="text" data-responsive_offset="on"
                            data-frames='[{"delay":5000,"speed":800,"frame":"0","from":"x:50px;opacity:0;","to":"o:1;","ease":"Power4.easeOut"},{"delay":"+3260","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                            data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]"
                            data-paddingright="[0,0,0,0]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]"
                            style="z-index: 5; white-space: nowrap; font-size: 60px; 
                          text-shadow:
        -3px -3px 0 #704124,  
        -3px  3px 0 #704124,
         3px -3px 0 #704124,
         3px  3px 0 #704124,
        -3px  0px 0 #704124,
         3px  0px 0 #704124,
         0px -3px 0 #704124,
         0px  3px 0 #704124;
            line-height: 80px; /* Augmenté pour espace entre lignes */
            font-weight: 700; color: #FFF; 
            font-family:Poppins; text-transform:uppercase;
            background-color:rgba(0, 122, 255, 0);">
                            <div>2 NOVEMBRE 2024</div>
                            <div style="margin-top: 20px;">1ère EDITION</div>
                            <div style="margin-top: 20px;">AVEC L'AUTOMNAL GOURMAND</div>
                        </div>





                    </li>
                    <!-- SLIDE  -->
                    <li data-index="rs-189" data-transition="crossfade" data-slotamount="default" data-hideafterloop="0"
                        data-hideslideonmobile="off" data-easein="default" data-easeout="default"
                        data-masterspeed="default" data-thumb="assets/100x50_4e36b-42512211_ml.jpg" data-delay="5150"
                        data-rotate="0" data-saveperformance="off" data-title="Slide" data-param1="" data-param2=""
                        data-param3="" data-param4="" data-param5="" data-param6="" data-param7="" data-param8=""
                        data-param9="" data-param10="" data-description="">
                        <!-- MAIN IMAGE -->
                        <img src="rev-slider-files/assets/4e36b-42512211_ml.jpg" alt="" data-bgposition="center center"
                            data-kenburns="on" data-duration="10000" data-ease="Linear.easeNone" data-scalestart="100"
                            data-scaleend="110" data-rotatestart="0" data-rotateend="0" data-blurstart="0"
                            data-blurend="0" data-offsetstart="0 0" data-offsetend="0 0" class="rev-slidebg"
                            data-no-retina>
                        <!-- LAYERS -->

                        <!-- LAYER NR. 3 -->
                        <div class="tp-caption   tp-resizeme" id="slide-189-layer-1" data-x="161" data-y="center"
                            data-voffset="-31" data-width="['auto']" data-height="['auto']" data-type="text"
                            data-responsive_offset="on"
                            data-frames='[{"delay":520,"speed":800,"frame":"0","from":"x:-50px;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"+3160","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                            data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]"
                            data-paddingright="[0,0,0,0]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]"
                            style="z-index: 5; white-space: nowrap; font-size: 60px; line-height: 22px; font-weight: 700; color: #4f3b1c;font-family:Poppins;text-transform:uppercase;">
                        </div>

                        <!-- LAYER NR. 4 -->
                        <div class="tp-caption   tp-resizeme" id="slide-189-layer-2" data-x="165" data-y="318"
                            data-width="['auto']" data-height="['auto']" data-type="text" data-responsive_offset="on"
                            data-frames='[{"delay":1080,"speed":800,"frame":"0","from":"x:-50px;opacity:0;","to":"o:1;","ease":"Power3.easeInOut"},{"delay":"+2800","speed":300,"frame":"999","to":"opacity:0;","ease":"Power3.easeInOut"}]'
                            data-textAlign="['inherit','inherit','inherit','inherit']" data-paddingtop="[0,0,0,0]"
                            data-paddingright="[0,0,0,0]" data-paddingbottom="[0,0,0,0]" data-paddingleft="[0,0,0,0]"
                            style="z-index: 6; white-space: nowrap; font-size: 52px; line-height: 22px; font-weight: 700; color: #4f3b1c;font-family:Poppins;text-transform:uppercase;">
                        </div>
                    </li>
                    <!-- SLIDE  -->

                </ul>
                <div class="tp-bannertimer tp-bottom" style="visibility: hidden !important;"></div>
            </div>
        </div>
        <!-- /REVOLUTION SLIDER -->


        <div class="bg_color_marron">

            <div class="container margin_120_95 ">

                <div class="main_title">
                    <h2 style="color:#FFF;margin-bottom:14px">Revivez le Spin off de l'édition 2024</h2>
                    <iframe width="900" height="500" src="https://www.youtube.com/embed/Vp8QarlKY-I?si=J852oiyB0lXOnuf6"
                        title="YouTube video player" frameborder="0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                </div>


            </div>
            <!-- /carousel -->
        </div>

        <!--

    <div class="container" style="margin-top: 30px;">
        <div class="row">
            <div class="col-md-6">
                <h6>Inscriptions aux Courses (max : <?php echo $maxEquipes; ?> équipes)</h6>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo ($totalEquipes / $maxEquipes) * 100; ?>%; background-image: linear-gradient(45deg, #3f51b5, #2196f3);" aria-valuenow="<?php echo $totalEquipes; ?>" aria-valuemin="0" aria-valuemax="<?php echo $maxEquipes; ?>">
                        <?php echo $totalEquipes; ?> / <?php echo $maxEquipes; ?> 
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h6>Inscriptions à la Randonnée (max : <?php echo $maxParticipantsRando; ?> participants)</h6>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo ($totalParticipantsRando / $maxParticipantsRando) * 100; ?>%; background-image: linear-gradient(45deg, #ff5722, #ff9800);" aria-valuenow="<?php echo $totalParticipantsRando; ?>" aria-valuemin="0" aria-valuemax="<?php echo $maxParticipantsRando; ?>">
                        <?php echo $totalParticipantsRando; ?> / <?php echo $maxParticipantsRando; ?> 
                    </div>
                </div>
            </div>
        </div>
    </div>
   -->




        <!-- 
            <div style="background-color: orange; text-align: center; padding: 20px;">
                <p style="color: white; font-size: 20px; margin: 0;">Vérifiez ou complétez votre inscription (certificat
                    médical/PPS)</p>
                <a href="uploadcertif.php" class="btn_1" style="margin-top: 10px;">VERIFIER/COMPLETER</a>
            </div>
           -->


        <div class="bg_color_1">
            <div class="container margin_120_95">
                <div class="main_title">
                    <h1><strong>2 novembre 2024</strong> - Saint André En Vivarais</h1>
                    <h2>Deux courses en <span class="highlight">Duo</span></h2>
                    <!--                      <a href="inscriptions.php" target="_blank" class="btn_1">S'inscrire</a>-->
                </div>
                <div class="row justify-content-between">
                    <div class="col-lg-6">
                        <figure class="add_bottom_30">
                            <img src="img/about_1.jpg" class="img-fluid" alt="">
                        </figure>
                    </div>
                    <div class="col-lg-6 text-justify">
                        <h4>Bienvenue sur le site officiel du TRAIL DES CHAMPIGNONS !</h4>
                        <br />
                        <p>Vous êtes à la recherche d’une aventure alliant <b>SPORT, NATURE, ET CONVIVIALITE ?</b></p>
                        <p>Cette année, venez découvrir nos deux parcours emblématiques !</p>
                        <p><b>LE CEPE (35 KM)</b> et <b>LA GIROLLE (15 KM)</b>, à réaliser en duo au cœur de sentiers
                            escarpés, de panoramas à couper le souffle et d’une halte aux ravitos gourmands.</p>

                        <p><b>LA RANDO MORILLE (10 KM)</b> comme son nom l'indique est un parcours à faire en randonnée,
                            non chronométré, en famille par exemple.</p>


                        <p>Vous souhaitez avoir une <b>RECUPERATION DE QUALITE, FESTIVE ET CHALEUREUSE</b> ?</p>
                        <p>Poursuivez votre aventure lors de <b>LA FOIRE AUX CHAMPIGNONS</b> organisée à Saint Bonnet le
                            Froid à l’occasion de l’Automnal Gourmand et découvrez les spécialités culinaires locales !
                        </p>
                        <p>Alors, que vous soyez coureurs expérimentés ou amateurs de nature, ce trail est fait pour
                            vous !</p>
                        <p><b> PRETS A LIER AMITIE, EFFORT,NATURE et GOURMANDISE?</b></p>
                        <p><a href="inscriptions.php">Inscrivez-vous dès maintenant au Trail des Champignons</a> et
                            venez célébrer avec nous l'esprit d'équipe au sein de l'incroyable beauté ardéchoise !
                        <p>
                    </div>
                </div>
                <!--/row-->
            </div>
            <!--/container-->
        </div>


        <div class="bg_color_beige">
            <div class="container margin_120_95">

                <div class="main_title">
                    <h2>Infos essentielles</h2>

                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="box_faq">
                            <i class="icon_pin_alt"></i>
                            <h4>Où</h4>
                            <p>A <a href="https://www.google.com/maps?q=45.12068607631963,4.4124457850904495"
                                    target="_blank">Saint-André-en-Vivarais</a>, en Ardèche </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box_faq">
                            <i class="icon_info_alt"></i>
                            <h4>Quoi ?</h4>
                            <p>Trail en Duo, avec plusieurs parcours : <?php echo getAffichageQuoiAccueil($dbh); ?></p>
                        </div>
                    </div>
                </div><!-- /row  -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="box_faq">
                            <i class="icon_calendar"></i>
                            <h4>Quand ?</h4>
                            <p>
                                2 novembre 2024
                                <br>
                                <?php echo getAffichageQuandAccueil($dbh); ?>
                            </p>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="box_faq">
                            <i class="icon_target"></i>
                            <h4>Comment ?</h4>
                            <p><a href="inscriptions.php">Inscriptions en ligne</a> et toutes les informations la
                                rubrique Infos pratiques</p>
                        </div>
                    </div>
                </div><!-- /row  -->
            </div>
        </div>








        <!-- /container -->
        </div>
        <!-- /white_bg -->






        <!--

        <div class="container margin_120_95">
            <div class="main_title">
                <h2>Les résultats en direct</h2>
                <br>
                <p>
                    <?php
                    foreach ($courses as $course) {
                    ?>
                    <a href="listingdepart.php?idCourse=<?php echo $course['idCourse']; ?>" class="btn btn-outline-secondary  btn-sm">Liste départ <?php echo $course['libelleCourse']; ?></a>
                    <?php
                    }
                    ?>
                </p>
            </div>
            <div class="row justify-content-center">
                <?php
                foreach ($courses as $course) {
                    //$classement=getClassement($course['idCourse'], $categorie, $dbh);
                    $classement = array();
                ?>
                <div class="col-xl-4 col-lg-5 col-md-6">
                    <div class="list_home">
                        <div class="list_title">
                            <h3><?php echo $course['libelleCourse']; ?></h3>
                        </div>
                        <ul>
                            <?php
                            $cl = 1;
                            $first = true;
                            $affichage = 0;
                            foreach ($classement as $coureur) {
                                $affichage++;
                                if ($affichage == 4) break;
                                if ($first) {
                                    $tempsPremier = $coureur['tempsCoureur'];
                                    $first = false;
                                }
                            ?>
                            <li><a href="resultats.php?idCourse=<?php echo $course['idCourse']; ?>"><strong><?php echo $cl; ?></strong> <?php echo $coureur['libelleCoureur']; ?></a></li>
                            <?php
                                $cl++;
                            }
                            ?>
                            <li><a href="resultats.php?idCourse=<?php echo $course['idCourse']; ?>"><strong>Voir la suite</strong></a></li>
                        </ul>
                    </div>
                </div>
                <?php
                }
                ?>
            </div>
         
        </div>



        -->





        <!--  <div class="container margin_120_95">
            <div class="main_title">
                <h2>Le Trail des <strong>Champignons</strong>, miam !</h2>
                <p>C'est là course où t'as juste envie de te faire une omelette aux champis, mettre tes tatanes et terminé bonsoir !</p>
            </div>
            <div class="row add_bottom_30">
                <div class="col-lg-4">
                    <div class="box_feat" id="icon_1">
                        <span></span>
                        <h3>Plusieurs formats</h3>
                        <p>Il y en aura pour tous les goûts, toutes les distances. Quelques kilomètres pour se dégourdir les jambes ou bien la longue qui va vous piquer les guibolles</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="box_feat" id="icon_2">

                        <span></span>
                        <h3>L'Ardèche </h3>
                        <p>Découvrez un coin hors du commun dans la montagne Ardéchoise. C'est pas la haute montagne, mais c'est beau, c'est "hArd" (échois...). Bref, que du bonheuyr</p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="box_feat" id="icon_3">
                        <h3>Ambiance</h3>
                        <p>Ici tout est possible : à la rigolade ou comme un mec de so trail un jeudi soir à la tête d'or, le couteau entre les temps, tu pourras t'exprimer comme tu le veux sur les terrains !</p>
                    </div>
                </div>
            </div>

            <p class="text-center"><a href="inscription.php" class="btn_1 medium">S'inscrire</a></p>

        </div>-->
        <!-- /container -->


        <!--
        <div id="app_section">
            <div class="container">
                <div class="row justify-content-around">
                    <div class="col-md-5">
                        <p><img src="img/app_img.svg" alt="" class="img-fluid" width="500" height="433"></p>
                    </div>
                    <div class="col-md-6">
                        <small>Application</small>
                        <h3>Download <strong>Findoctor App</strong> Now!</h3>
                        <p class="lead">Tota omittantur necessitatibus mei ei. Quo paulo perfecto eu, errem percipit ponderum no eos. Has eu mazim sensibus. Ad nonumes dissentiunt qui, ei menandri electram eos. Nam iisque consequuntur cu.</p>
                        <div class="app_buttons wow" data-wow-offset="100">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 43.1 85.9" style="enable-background:new 0 0 43.1 85.9;" xml:space="preserve">
                                <path stroke-linecap="round" stroke-linejoin="round" class="st0 draw-arrow" d="M11.3,2.5c-5.8,5-8.7,12.7-9,20.3s2,15.1,5.3,22c6.7,14,18,25.8,31.7,33.1" />
                                <path stroke-linecap="round" stroke-linejoin="round" class="draw-arrow tail-1" d="M40.6,78.1C39,71.3,37.2,64.6,35.2,58" />
                                <path stroke-linecap="round" stroke-linejoin="round" class="draw-arrow tail-2" d="M39.8,78.5c-7.2,1.7-14.3,3.3-21.5,4.9" />
                            </svg>
                            <a href="#0" class="fadeIn"><img src="img/apple_app.png" alt="" width="150" height="50" data-retina="true"></a>
                            <a href="#0" class="fadeIn"><img src="img/google_play_app.png" alt="" width="150" height="50" data-retina="true"></a>
                        </div>
                    </div>
                </div>
               
            </div>
          
        </div>
       
    </main>
    <!-- /main content -

-->
        <!--
        <footer>
            <div class="container margin_60_35">
                <div class="row">
                    <div class="col-lg-3 col-md-12">
                        <p>
                            <a href="index.html" title="Findoctor">
                                <img src="img/logo.png" data-retina="true" alt="" width="163" height="36" class="img-fluid">
                            </a>
                        </p>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <h5>About</h5>
                        <ul class="links">
                            <li><a href="#0">About us</a></li>
                            <li><a href="blog.html">Blog</a></li>
                            <li><a href="#0">FAQ</a></li>
                            <li><a href="login.html">Login</a></li>
                            <li><a href="register.html">Register</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <h5>Useful links</h5>
                        <ul class="links">
                            <li><a href="#0">Doctors</a></li>
                            <li><a href="#0">Clinics</a></li>
                            <li><a href="#0">Specialization</a></li>
                            <li><a href="#0">Join as a Doctor</a></li>
                            <li><a href="#0">Download App</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-md-4">
                        <h5>Contact with Us</h5>
                        <ul class="contacts">
                            <li><a href="tel://61280932400"><i class="icon_mobile"></i> + 61 23 8093 3400</a></li>
                            <li><a href="mailto:info@findoctor.com"><i class="icon_mail_alt"></i> help@findoctor.com</a></li>
                        </ul>
                        <div class="follow_us">
                            <h5>Follow us</h5>
                            <ul>
                                <li><a href="#0"><i class="social_facebook"></i></a></li>
                                <li><a href="#0"><i class="social_twitter"></i></a></li>
                                <li><a href="#0"><i class="social_linkedin"></i></a></li>
                                <li><a href="#0"><i class="social_instagram"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
          
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
   
    

    -->



        <div class="bg_color_1">
            <div class="container margin_120_95">

                <div class="main_title">
                    <h2>En partenariat avec</h2>

                </div>

                <div id="reccomended" class="owl-carousel owl-theme">


                    <?php
                        $listingPartenaires = getPartenaires($dbh);

                        // Affichage des partenaires par catégorie
                        foreach ($listingPartenaires as $partenaire) {
                        ?>
                    <div class="item">
                        <div class="title">
                            <a href="<?php echo htmlspecialchars($partenaire['lienPartenaire']); ?>" target="_blank">
                                <h4><?php echo htmlspecialchars($partenaire['libellePartenaire']); ?></h4>
                            </a>
                        </div>
                        <a href="<?php echo htmlspecialchars($partenaire['lienPartenaire']); ?>" target="_blank">
                            <img src="img/logosPartenaires/<?php echo htmlspecialchars($partenaire['logoPartenaire']); ?>"
                                alt="">
                        </a>
                    </div>
                    <?php
                        }
                        ?>



                </div>
                <!-- /carousel -->
            </div>
            <!-- /container -->
        </div>
        <!-- /white_bg -->



        <?php include 'footer.php'; ?>

        <div id="toTop"></div>
        <!-- Back to top button -->

        <!-- COMMON SCRIPTS -->
        <script src="js/jquery-2.2.4.min.js"></script>
        <script src="js/common_scripts.min.js"></script>
        <script src="js/functions.js"></script>

        <!-- REVOLUTION SLIDER SCRIPTS -->
        <script type="text/javascript" src="rev-slider-files/js/jquery.themepunch.tools.min.js"></script>
        <script type="text/javascript" src="rev-slider-files/js/jquery.themepunch.revolution.min.js"></script>
        <script type="text/javascript" src="rev-slider-files/js/extensions/revolution.extension.actions.min.js">
        </script>
        <script type="text/javascript" src="rev-slider-files/js/extensions/revolution.extension.carousel.min.js">
        </script>
        <script type="text/javascript" src="rev-slider-files/js/extensions/revolution.extension.kenburn.min.js">
        </script>
        <script type="text/javascript" src="rev-slider-files/js/extensions/revolution.extension.layeranimation.min.js">
        </script>
        <script type="text/javascript" src="rev-slider-files/js/extensions/revolution.extension.migration.min.js">
        </script>
        <script type="text/javascript" src="rev-slider-files/js/extensions/revolution.extension.navigation.min.js">
        </script>
        <script type="text/javascript" src="rev-slider-files/js/extensions/revolution.extension.parallax.min.js">
        </script>
        <script type="text/javascript" src="rev-slider-files/js/extensions/revolution.extension.slideanims.min.js">
        </script>
        <script type="text/javascript" src="rev-slider-files/js/extensions/revolution.extension.video.min.js"></script>
        <script type="text/javascript">
        var tpj = jQuery;
        var revapi72;
        tpj(document).ready(function() {
            if (tpj("#rev_slider_72_1").revolution == undefined) {
                revslider_showDoubleJqueryError("#rev_slider_72_1");
            } else {
                revapi72 = tpj("#rev_slider_72_1").show().revolution({
                    sliderType: "standard",
                    jsFileLocation: "rev-slider-files/js/",
                    sliderLayout: "auto",
                    dottedOverlay: "none",
                    delay: 9000,
                    navigation: {
                        keyboardNavigation: "off",
                        keyboard_direction: "horizontal",
                        mouseScrollNavigation: "off",
                        mouseScrollReverse: "default",
                        onHoverStop: "off",
                        touch: {
                            touchenabled: "on",
                            touchOnDesktop: "off",
                            swipe_threshold: 75,
                            swipe_min_touches: 1,
                            swipe_direction: "horizontal",
                            drag_block_vertical: false
                        },
                        arrows: {
                            style: "gyges",
                            enable: true,
                            hide_onmobile: true,
                            hide_under: 560,
                            hide_onleave: true,
                            hide_delay: 200,
                            hide_delay_mobile: 1200,
                            tmp: '',
                            left: {
                                h_align: "left",
                                v_align: "center",
                                h_offset: 20,
                                v_offset: 0
                            },
                            right: {
                                h_align: "right",
                                v_align: "center",
                                h_offset: 20,
                                v_offset: 0
                            }
                        }
                    },
                    visibilityLevels: [1240, 1024, 778, 480],
                    gridwidth: 1240,
                    gridheight: 600,
                    lazyType: "none",
                    shadow: 0,
                    spinner: "spinner0",
                    stopLoop: "off",
                    stopAfterLoops: -1,
                    stopAtSlide: -1,
                    shuffle: "off",
                    autoHeight: "off",
                    disableProgressBar: "on",
                    hideThumbsOnMobile: "off",
                    hideSliderAtLimit: 0,
                    hideCaptionAtLimit: 0,
                    hideAllCaptionAtLilmit: 0,
                    debugMode: false,
                    fallbacks: {
                        simplifyAll: "off",
                        nextSlideOnWindowFocus: "off",
                        disableFocusListener: false,
                    }
                });
            }
        }); /*ready*/
        </script>



</body>

</html>

<?php
} catch (Exception $e) {
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
?>