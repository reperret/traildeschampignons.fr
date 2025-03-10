<?php 
include '../api/bdd.php';
include '../api/fonctionsUtiles.php';

$listingExemplaires=getExemplaire($idLudotheque,NULL,NULL,$dbh);
$nbResultats=sizeof($listingExemplaires);
   
?>

<!doctype html>
<html lang="en" class="no-js">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">


      <link href="https://fonts.googleapis.com/css?family=Comfortaa&display=swap" rel="stylesheet">

	<link rel="stylesheet" href="css/reset.css"> <!-- CSS reset -->
	<link rel="stylesheet" href="css/style.css"> <!-- Resource style -->
	<script src="js/modernizr.js"></script> <!-- Modernizr -->
  	
	<title>Cabane à jeux</title>
    
    <style>
    
            h1{ font-size: 1.5em !important; font-weight: bold !important; }
        h2{ font-size: 1.1em !important; font-weight: bold !important; }
    td{border:0 !important; margin-top: 5px !important; padding: 5px !important; text-align:center; vertical-align: middle;}
      .titrePartie{ font-size: 0.7em;  color:#3b3b3b; margin-top: 25px; margin-bottom: 2px;}
    </style>
    
</head>
<body>
	<!--<header class="cd-header">
		<h1><img src="../img/logo.png"></h1>
	</header>-->

	<main class="cd-main-content">
		<div class="cd-tab-filter-wrapper">
			<div class="cd-tab-filter">
				<ul class="cd-filters">
					<li class="placeholder"> 
						<a data-type="all" href="#0">TOUS</a> <!-- selected option on mobile -->
					</li> 
					<li class="filter"><a class="selected" href="#0" data-type="all">TOUS</a></li>
					<li class="filter" data-filter=".Initiation"><a href="#0" data-type="Initiation">Initiation</a></li>
                    <li class="filter" data-filter=".Familial"><a href="#0" data-type="Familial">Familial</a></li>
                    <li class="filter" data-filter=".Amateur"><a href="#0" data-type="Amateur">Amateur</a></li>
                    <li class="filter" data-filter=".Expert"><a href="#0" data-type="Expert">Expert</a></li>
                    
                    
				</ul> <!-- cd-filters -->
			</div> <!-- cd-tab-filter -->
            
           
		</div> <!-- cd-tab-filter-wrapper -->

		<section class="cd-gallery">
			<ul>
<?php
    for($i=0;$i<sizeof($listingExemplaires);$i++)
    {
        ?>
                <li class="mix <?php echo $listingExemplaires[$i]['typeJeu'] ;?> litteratie<?php echo $listingExemplaires[$i]['litteratieJeu'] ;?> <?php echo $listingExemplaires[$i]['statutExemplaire'] ;?> <?php echo $listingExemplaires[$i]['libelleJeu'] ;?> <?php echo $listingExemplaires[$i]['difficulteJeu'] ;?> <?php echo getFilterAge($listingExemplaires[$i]['ageMinimumJeu']) ;?>">
                    
                    
                    <a href="detailJeu.php?idE=<?php echo $listingExemplaires[$i]['idExemplaire'] ;?>">
                    
                    <span style="font-size: 1.5em !important; font-weight: bolder !important;color: #<?php echo getCouleurJeu($listingExemplaires[$i]['difficulteJeu']);?>;padding: 0px; margin :0px; ">
                        <?php echo $listingExemplaires[$i]['libelleJeu'];?>
                    </span>
                    
                    </a>
                    
                    
                
                
                
                    
                    
                    
    
                    
                   <div class="maDiv">	
  
           
           <center>

     
             <br>
                <table>



        <tr>
            <td><img src="../img/picto1.png" width="25px"></td>
            <td><img src="../img/picto2.png" width="25px"></td>
            <td><img src="../img/picto3.png" width="25px"></td>
            <td rowspan="2"><img src="../img/litteratie<?php echo $listingExemplaires[$i]['litteratieJeu'];?>.png" width="36" height="36" ></td>
            <td rowspan="2"> <img src="../img/<?php echo getTypePicto($listingExemplaires[$i]['typeJeu']);?>.png" width="36" height="36"></td>

        </tr>

        <tr>
            <td><?php echo $listingExemplaires[$i]['ageMinimumJeu']."+";?></td>
            <td><?php echo $listingExemplaires[$i]['joueursMinJeu']." - ".$listingExemplaires[$i]['joueursMaxJeu'];?></td>
            <td>
                <?php 
                if($listingExemplaires[$i]['dureeJeu']!='')
                {
                    echo $listingExemplaires[$i]['dureeJeu']."'";
                }
                else
                {
                    echo "libre";
                }
                
                ?>
            </td>
      
        </tr>


</table>
           
               
               
           </center>

     
          <p class="titrePartie"> <?php echo $listingExemplaires[$i]['descriptionJeu'];?></p>
           
          <br><br>
           
          
           <?php 
                        if($listingExemplaires[$i]['reglesJeu']!='')
                        {
                        ?>
                        -> <a href="<?php echo $listingExemplaires[$i]['reglesJeu'];?>"  data-effect="mfp-zoom-in" class="btn_1 gray" target="_blank"> Voir les règles</a>
                        <!-- Reply to review popup -->
                        
                        <?php
                        }
                        ?>
           
		</div>
          
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                
                
                
                
                
                
                
                
                </li>
                
        <?php
        
    }
?>

				<li class="gap"></li>
				<li class="gap"></li>
				<li class="gap"></li>
			</ul>
			<div class="cd-fail-message">Aucun jeu trouvé</div>
		</section> <!-- cd-gallery -->

		<div class="cd-filter">
			<form>
				<div class="cd-filter-block">
					<h4>Rechercher</h4>
					
					<div class="cd-filter-content">
						<input type="search" placeholder="nom du jeu...">
					</div> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->

                
                	<div class="cd-filter-block">
					<h4>Age</h4>
					
					<div class="cd-filter-content">
						<div class="cd-select cd-filters">
							<select class="filter" name="selectThis" id="selectThis">
								<option value="">Choisir un age</option>
                                <?php
                                for($i=1;$i<18;$i++)
                                {
                                    ?><option value="<?php echo ".age".$i ;?>"><?php echo $i;?></option><?php
                                }
                                ?>
							</select>
						</div> <!-- cd-select -->
					</div> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->
                
                
                
                
                
				<div class="cd-filter-block">
					<h4>Littératie</h4>

					<ul class="cd-filter-content cd-filters list">
                        
                        	<li>
							<input class="filter" data-filter=".litteratie0" type="checkbox" id="idLitteratie0">
			    			<label class="checkbox-label" for="idLitteratie0">Pas de texte</label>
						</li>

						<li>
							<input class="filter" data-filter=".litteratie1" type="checkbox" id="idLitteratie1">
			    			<label class="checkbox-label" for="idLitteratie1">Lecture simple</label>
						</li>
                        
                        <li>
							<input class="filter" data-filter=".litteratie2" type="checkbox" id="idLitteratie2">
			    			<label class="checkbox-label" for="idLitteratie2">Lecture avancée</label>
						</li>
                        
                        <li>
							<input class="filter" data-filter=".litteratie3" type="checkbox" id="idLitteratie3">
			    			<label class="checkbox-label" for="idLitteratie3">Ecriture</label>
						</li>
                        
                    
					</ul> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->
                
                
                				<div class="cd-filter-block">
					<h4>Type</h4>

					<ul class="cd-filter-content cd-filters list">
                        

                         <?php
    $typesJeu=getTypesJeu($dbh);
    for($i=0;$i<sizeof($typesJeu);$i++)
    {  
        ?>
            <li>
            <input class="filter" data-filter=".<?php echo $typesJeu[$i];?>" type="checkbox" id="<?php echo $typesJeu[$i];?>">
            <label class="checkbox-label" for="<?php echo $typesJeu[$i];?>"><?php echo $typesJeu[$i];?></label>
            </li>            
        <?php
    }
    ?>
                        
                    
					</ul> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->
                
                

			

				<div class="cd-filter-block">
					<h4>Statut</h4>

					<ul class="cd-filter-content cd-filters list">
						<li>
							<input class="filter" data-filter="" type="radio" name="statut" id="statutTous" checked>
							<label class="radio-label" for="statutTous">Tous</label>
						</li>

						<li>
							<input class="filter" data-filter=".Empruntable .Consultable" type="radio" name="statut" id="Empruntable">
							<label class="radio-label" for="Empruntable">Empruntable</label>
						</li>

						<li>
							<input class="filter" data-filter=".Consultable" type="radio" name="statut" id="Consultable">
							<label class="radio-label" for="Consultable">Consultable</label>
						</li>
					</ul> <!-- cd-filter-content -->
				</div> <!-- cd-filter-block -->
			</form>

			<a href="#0" class="cd-close">Fermer</a>
		</div> <!-- cd-filter -->

		<a href="#0" class="cd-filter-trigger">Filtres</a>
	</main> <!-- cd-main-content -->
<script src="js/jquery-2.1.1.js"></script>
<script src="js/jquery.mixitup.min.js"></script>
<script src="js/main.js"></script> <!-- Resource jQuery -->
</body>
</html>