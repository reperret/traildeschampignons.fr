<?php
try
{
    
include '../api/bdd.php';
include '../api/fonctions.php';
$aucunEvenement=false;
$message=NULL;
$repertoire="/var/www/traildeschampignons.sotrail.fr/photosArrivee/";
$listePhotos=listerVideosArriveesDisponibles($repertoire);

?>
<!doctype html>
<html class="">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Photographer - Responsive HTML5 Template">
    <meta name="keywords" content="blog, gallery, photography, html5, portfolio">
    <meta name="author" content="Pixelwars">
    <title>PHOTOS - TRAIL DES CHAMPIGNONS</title>
    <!-- FAV and TOUCH ICONS -->
    <link rel="shortcut icon" href="images/ico/favicon.ico">
    <link rel="apple-touch-icon" href="images/ico/apple-touch-icon.png" />
    <!-- FONTS -->
    <link rel="stylesheet" type="text/css" href="css/fonts/montserrat/montserrat.css">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
    <!-- STYLES -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel='stylesheet' href='https://themes.pixelwars.org/photographer-test/wp-content/plugins/woocommerce/assets/css/woocommerce.css?ver=3.3.5' type='text/css' media='all' />
    <link rel="stylesheet" type="text/css" href="css/fonts/fontello/css/fontello.css">
    <link rel="stylesheet" type="text/css" href="js/jquery.uniform/uniform.default.css">
    <link rel="stylesheet" type="text/css" href="js/jquery.fluidbox/fluidbox.css">
    <link rel="stylesheet" type="text/css" href="js/owl-carousel/owl.carousel.css">
    <link rel="stylesheet" type="text/css" href="js/photo-swipe/photoswipe.css">
    <link rel="stylesheet" type="text/css" href="js/photo-swipe/default-skin/default-skin.css">
    <link rel="stylesheet" type="text/css" href="js/jquery.magnific-popup/magnific-popup.css">
    <link rel="stylesheet" type="text/css" href="js/slippry/slippry.css">
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <link rel="stylesheet" type="text/css" href="css/768.css">
    <link rel="stylesheet" type="text/css" href="css/992.css">
    <!-- INITIAL SCRIPTS -->
    <script src="js/jquery-1.11.2.min.js"></script>
    <script src="js/jquery-migrate-1.2.1.min.js"></script>
    <script src="js/modernizr.min.js"></script>
    <!-- InstanceBeginEditable name="head" -->
    <!-- InstanceEndEditable -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Neucha&display=swap" rel="stylesheet">
    <style>
        .titre {
            font-weight: bold;
            font-size: 2.5em;
            color: #4f4f4f;
            text-align: center;
            display: block;
            font-family: 'Neucha', cursive;
        }

        .date {
            font-weight: bold;
            font-size: 1.3em;
            color: #c3a53d;
            text-align: center;
            display: block
        }

        #haut {
            background-color: #FFF;
            margin: auto;
            text-align: center;
            padding: 5px
        }

    </style>
</head>

<body>
    <!-- page -->



    <div id="page" class="hfeed site">
        <!-- header -->


        <header id="masthead" class="site-header" role="banner">
            <div class="site-logo">

                <img src="../img/logo_2x.png" alt="logo">
                <span class="date">04/11/2023</span>
            </div>


        </header>

        <?php
    if(!$aucunEvenement)
        {
        ?>

        <!-- header -->
        <!-- site-main -->
        <div id="main" class="site-main">
            <!-- primary -->
            <div id="primary" class="content-area">
                <!-- site-content -->
                <div id="content" class="site-content" role="main">
                    <!-- InstanceBeginEditable name="FeaturedContent" -->
                    <!-- InstanceEndEditable -->
                    <!-- layout-full -->
                    <div class="layout-full">
                        <!-- InstanceBeginEditable name="LayoutFullContent" -->
                        <!-- .gallery-single -->
                        <div class="gallery-single">
                            <!-- entry-header -->

                            <!-- entry-header -->
                            <!-- .media-grid-wrap -->
                            <div class="media-grid-wrap">
                                <!-- .pw-gallery -->
                                <div class="pw-gallery pw-collage pw-collage-loading" data-gallery-style="minimal" data-share="true" data-fullscreen="true" data-bg-opacity="0.95" data-row-height="360" data-mobile-row-height="120" data-effect="effect-4">







                                    <?php
                                foreach($listePhotos as $image)
                                {
                                    $infosPhotos=getimagesize("../photosArrivee/".$image);
                                    $width=$infosPhotos[0];
                                    $height=$infosPhotos[1];
                                ?>


                                    <a href="../photosArrivee/<?php echo $image;?>" data-size="<?php echo $width;?>x<?php echo $height;?>" data-med="../photosArrivee/<?php echo $image;?>" data-med-size="<?php echo $width;?>x<?php echo $height;?>">
                                        <img src="../photosArrivee/<?php echo $image;?>" alt="image" width="1170" height="1770" />
                                        <figure>Date</figure>
                                    </a>
                                    <?php
                                }
                                ?>



                                </div>
                                <!-- .pw-gallery -->
                            </div>
                            <!-- .media-grid-wrap -->

                        </div>
                        <!-- .gallery-single -->
                        <!-- Root element of PhotoSwipe. Must have class pswp. -->
                        <div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="pswp__bg"></div>
                            <div class="pswp__scroll-wrap">
                                <div class="pswp__container">
                                    <div class="pswp__item"></div>
                                    <div class="pswp__item"></div>
                                    <div class="pswp__item"></div>
                                </div>
                                <div class="pswp__ui pswp__ui--hidden">
                                    <div class="pswp__top-bar">
                                        <div class="pswp__counter"></div>
                                        <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
                                        <button class="pswp__button pswp__button--share" title="Share"></button>
                                        <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
                                        <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
                                        <div class="pswp__preloader">
                                            <div class="pswp__preloader__icn">
                                                <div class="pswp__preloader__cut">
                                                    <div class="pswp__preloader__donut"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
                                        <div class="pswp__share-tooltip"></div>
                                    </div>
                                    <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)"></button>
                                    <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)"></button>
                                    <div class="pswp__caption">
                                        <div class="pswp__caption__center">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Root element of PhotoSwipe -->
                        <!-- InstanceEndEditable -->
                    </div>
                    <!-- layout-full -->
                </div>
                <!-- site-content -->
            </div>
            <!-- primary -->
        </div>
        <!-- site-main -->
        <!-- site-footer -->

        <?php
            }
        else
        {
            echo $message;
        }
        ?>
        <footer id="colophon" class="site-footer" role="contentinfo">

            <div class="layout-medium">


                <div class="site-info">
                    <p>Le Trail des Champignons</p>
                </div>
            </div>
        </footer>

        <!-- site-footer -->
    </div>
    <!-- page -->
    <!-- SCRIPTS -->
    <script src="js/fastclick.js"></script>
    <script src="js/jquery.fitvids.js"></script>
    <script src="js/jquery.validate.min.js"></script>
    <script src="js/jquery.uniform/jquery.uniform.min.js"></script>
    <script src="js/imagesloaded.pkgd.min.js"></script>
    <script src="js/jquery.fluidbox/jquery.fluidbox.min.js"></script>
    <script src="js/owl-carousel/owl.carousel.min.js"></script>
    <script src="js/socialstream.jquery.js"></script>
    <script src="js/jquery.collagePlus/jquery.collagePlus.min.js"></script>
    <script src="js/photo-swipe/photoswipe.min.js"></script>
    <script src="js/photo-swipe/photoswipe-ui-default.min.js"></script>
    <script src="js/photo-swipe/photoswipe-run.js"></script>
    <script src="js/jquery.gridrotator.js"></script>
    <script src="js/slippry/slippry.min.js"></script>
    <script src="js/jquery.magnific-popup/jquery.magnific-popup.min.js"></script>
    <script src="js/masonry.pkgd.min.js"></script>
    <script src="js/view.min.js"></script>
    <script src="js/main.js"></script>
</body>
<!-- InstanceEnd -->

</html>

<?php
} 
catch (Exception $e) 
{
    echo 'Exception reçue : ',  $e->getMessage(), "\n";
}
    
    ?>
