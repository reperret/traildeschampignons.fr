<?php
$coursesMenu = getCourses(NULL, $dbh);
?>

<header>
    <div class="menu-container">
        <a href="index.php" class="logo">
            <img src="img/logo_2x.png" alt="Logo Trail des Champignons" />
        </a>

        <button id="menu-toggle" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav id="main-menu">
            <ul class="menu">
                <li><a href="index.php">Accueil</a></li>

                <li class="has-submenu">
                    <a href="#">L'évènement</a>
                    <ul class="submenu">
                        <li><a href="valeurs.php">Nos valeurs</a></li>
                        <li><a href="territoire.php">Le territoire</a></li>
                        <li><a href="partenaires.php">Partenaires</a></li>
                        <li><a href="benevoles.php">Devenir bénévole</a></li>
                    </ul>
                </li>

                <li class="has-submenu">
                    <a href="#">Courses</a>
                    <ul class="submenu">
                        <?php foreach ($coursesMenu as $course) { ?>
                            <li>
                                <a href="detailcourse.php?idCourse=<?= $course['idCourse']; ?>">
                                    <?= htmlspecialchars($course['libelleCourse']) . " " . $course['distanceCourse'] . "km"; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </li>

                <li class="has-submenu">
                    <a href="#">Inscriptions</a>
                    <ul class="submenu">
                        <li><a href="inscriptions.php">Les courses en duo</a></li>
                        <li><a href="inscriptionRando.php">La Randonnée</a></li>
                    </ul>
                </li>

                <li class="has-submenu">
                    <a href="#">Infos pratiques</a>
                    <ul class="submenu">
                        <li><a href="reglement.php">Règlement</a></li>
                        <li><a href="programme.php">Programme</a></li>
                        <li><a href="transports.php">Les transports</a></li>
                        <li><a href="hebergements.php">Les hébergements</a></li>
                    </ul>
                </li>

                <li class="has-submenu">
                    <a href="#">Résultats</a>
                    <ul class="submenu">
                        <li><a href="https://altichrono.fr/resultats/2024_champignons/" target="_blank">Altichrono</a>
                        </li>
                        <li><a href="resultats/trail_champignons_2024.pdf" target="_blank">Résultats 2024 PDF</a></li>
                    </ul>
                </li>

                <li class="has-submenu">
                    <a href="#">Éditions</a>
                    <ul class="submenu">
                        <li><a href="edition2024.php">Édition 2024</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
    </div>

    <div class="menu-backdrop" id="menu-layer"></div>
</header>