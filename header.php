<?php
$coursesMenu = getCourses(NULL, $dbh);
?>

<!-- header -->
<header class="header_sticky">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-6">
                <div id="logo_home">
                    <h1><a href="index.php" title="Findoctor">DuoRace</a></h1>
                </div>
            </div>
            <nav class="col-lg-9 col-6">
                <a class="cmn-toggle-switch cmn-toggle-switch__htx open_close" href="#0"><span>Menu mobile</span></a>
                <!-- <ul id="top_access">
                        <li><a href="login.html"><i class="pe-7s-user"></i></a></li>
                        <li><a href="register-doctor.html"><i class="pe-7s-add-user"></i></a></li>
                    </ul>-->
                <div class="main-menu">
                    <ul>
                        <!--
                            <li class="submenu">
                                <a href="#0" class="show-submenu">Home<i class="icon-down-open-mini"></i></a>
                                <ul>
                                    <li><a href="index.html">Home Default</a></li>
                                    <li><a href="index-2.html">Home Version 2</a></li>
                                    <li><a href="index-3.html">Home Version 3</a></li>
                                    <li><a href="index-4.html">Home Version 4</a></li>
                                    <li><a href="index-7.html">Home with Map</a></li>
                                    <li><a href="index-6.html">Revolution Slider</a></li>
                                    <li><a href="index-5.html">With Cookie Bar (EU law)</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#0" class="show-submenu">Pages<i class="icon-down-open-mini"></i></a>
                                <ul>
                                    <li class="third-level"><a href="#0">List pages</a>
                                        <ul>
                                            <li><a href="list.html">List page</a></li>
                                            <li><a href="grid-list.html">List grid page</a></li>
                                            <li><a href="list-map.html">List map page</a></li>
                                        </ul>
                                    </li>
                                    <li class="third-level"><a href="#0">Detail pages</a>
                                        <ul>
                                            <li><a href="detail-page.html">Detail page 1</a></li>
                                            <li><a href="detail-page-2.html">Detail page 2</a></li>
                                            <li><a href="detail-page-3.html">Detail page 3</a></li>
                                            <li><a href="detail-page-working-booking.html">Detail working booking</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="submit-review.html">Submit Review</a></li>
                                    <li><a href="blog-1.html">Blog</a></li>
                                    <li><a href="badges.html">Badges</a></li>
                                    <li><a href="login.html">Login</a></li>
                                    <li><a href="login-2.html">Login 2</a></li>
                                    <li><a href="register-doctor.html">Register Doctor</a></li>
                                    <li><a href="register-doctor-working.html">Working doctor register</a></li>
                                    <li><a href="register.html">Register</a></li>
                                    <li><a href="about.html">About Us</a></li>
                                    <li><a href="contacts.html">Contacts</a></li>
                                </ul>
                            </li>
                            <li class="submenu">
                                <a href="#0" class="show-submenu">Extra Elements<i class="icon-down-open-mini"></i></a>
                                <ul>
                                    <li><a href="booking-page.html">Booking page</a></li>
                                    <li><a href="confirm.html">Confirm page</a></li>
                                    <li><a href="faq.html">Faq page</a></li>
                                    <li><a href="coming_soon/index.html">Coming soon</a></li>
                                    <li class="third-level"><a href="#0">Pricing tables</a>
                                        <ul>
                                            <li><a href="pricing-tables-3.html">Pricing tables 1</a></li>
                                            <li><a href="pricing-tables.html">Pricing tables 2</a></li>
                                            <li><a href="pricing-tables-2.html">Pricing tables 3</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="icon-pack-1.html">Icon pack 1</a></li>
                                    <li><a href="icon-pack-2.html">Icon pack 2</a></li>
                                    <li><a href="icon-pack-3.html">Icon pack 3</a></li>
                                    <li><a href="404.html">404 page</a></li>
                                </ul>
                            </li>
-->
                        <li><a href="index.php">Accueil</a></li>

                        <li class="submenu">
                            <a href="#0" class="show-submenu">L'évènement<i class="icon-down-open-mini"></i></a>
                            <ul>
                                <li><a href="valeurs.php">Nos valeurs</a></li>
                                <li><a href="territoire.php">Le territoire</a></li>
                                <li><a href="partenaires.php">Partenaires</a></li>
                                <li><a href="benevoles.php">Devenir bénévole</a></li>
                            </ul>
                        </li>


                        <li class="submenu">
                            <a href="#0" class="show-submenu">Courses<i class="icon-down-open-mini"></i></a>
                            <ul>
                                <?php
                                foreach ($coursesMenu as $course) {
                                ?><li><a
                                            href="detailcourse.php?idCourse=<?php echo $course['idCourse']; ?>"><?php echo $course['libelleCourse'] . " " . $course['distanceCourse'] . "km"; ?></a>
                                    </li><?php
                                        }
                                            ?>
                            </ul>
                        </li>


                        <li class="submenu">
                            <a href="#0" class="show-submenu">Inscriptions<i class="icon-down-open-mini"></i></a>
                            <ul>
                                <li><a href="inscriptions.php">Les courses en duo</a></li>
                                <li><a href="inscriptionRando.php">La Randonnée</a></li>

                            </ul>
                        </li>




                        <li class="submenu">
                            <a href="#0" class="show-submenu">Infos pratiques<i class="icon-down-open-mini"></i></a>
                            <ul>
                                <li><a href="reglement.php" target="_blank">Règlement</a></li>
                                <li><a href="programme.php">Programme</a></li>
                                <li><a href="transports.php">Les transports</a></li>
                                <li><a href="hebergements.php">Les hébergements</a></li>
                            </ul>
                        </li>



                        <li class="submenu">
                            <a href="#0" class="show-submenu">Résultats<i class="icon-down-open-mini"></i></a>
                            <ul>
                                <li><a href="https://altichrono.fr/resultats/2024_champignons/"
                                        target="_blank">Résultats altichrono</a>
                                </li>
                                <li><a href="resultats/trail_champignons_2024.pdf" target="_blank">Résultats 2024
                                        PDF</a></li>
                            </ul>
                        </li>


                        <li class="submenu">
                            <a href="#0" class="show-submenu">Editions<i class="icon-down-open-mini"></i></a>
                            <ul>
                                <li><a href="edition2024.php">Edition 2024</a></li>
                            </ul>
                        </li>








                    </ul>
                </div>
                <!-- /main-menu -->
            </nav>
        </div>
    </div>
    <!-- /container -->
</header>
<!-- /header -->