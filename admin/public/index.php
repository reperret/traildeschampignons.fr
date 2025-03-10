<?php 
include '../api/bdd.php';
include '../api/fonctionsUtiles.php';

//*****Gestion filtrage

$age=-1;
if(isset($_POST['age']) &&  $_POST['age']!=-1) $age=$_POST['age'];

$litteratie=-1;
if(isset($_POST['litteratie']) &&  $_POST['litteratie']!=-1) $litteratie=$_POST['litteratie'];

$statut=-1;
if(isset($_POST['statut']) &&  $_POST['statut']!=-1) $statut=$_POST['statut'];

$type=-1;
if(isset($_POST['type']) &&  $_POST['type']!=-1) $type=$_POST['type'];

$difficulte=-1;
if(isset($_POST['difficulte']) &&  $_POST['difficulte']!=-1) $difficulte=$_POST['difficulte'];




$listingExemplaires=getExemplaire2($idLudotheque,NULL,NULL,$age,$litteratie,$statut,$type,$difficulte,-1,$dbh);
$nbResultats=sizeof($listingExemplaires);
   
?>
<!DOCTYPE html>
<html>
<head>
      <meta charset="utf-8">
  <title>Cabane à jeux</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
  box-sizing: border-box;
}

    body{padding: 5px; 
font-family: 'Comfortaa', cursive;}
    
    
    
    .pure-button-primary{ background: rgb(49, 93, 47) !important; /* this is an orange */}
#myInput {
  background-image: url('searchicon.png');
  background-position: 10px 10px;
  background-repeat: no-repeat;
  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}
    
    
#select {


  width: 100%;
  font-size: 16px;
  padding: 12px 20px 12px 40px;
  border: 1px solid #ddd;
  margin-bottom: 12px;
}

#myTable {
  border-collapse: collapse;
  width: 100%;
  border: 1px solid #ddd;
  font-size: 18px;
}

#myTable th, #myTable td {
  text-align: left;
  padding: 12px;
}
    .allwidth{width: 100%; margin:3px !important}

#myTable tr {
  border-bottom: 1px solid #ddd;
}

#myTable tr.header, #myTable tr:hover {
  background-color: #f1f1f1;
}
    
    #bandeau{text-align: center; margin: 0; background-color: #56351c; padding: 6px; margin-bottom: 10px;}
</style>
    <link href="https://fonts.googleapis.com/css?family=Comfortaa&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.1/build/pure-min.css" integrity="sha384-oAOxQR6DkCoMliIh8yFnu25d7Eq/PHS21PClpwjOTeU2jRSq11vu66rf90/cZr47" crossorigin="anonymous">
</head>
        <div id="bandeau"><img src="logo.png"></div>
<body>
    


    <form action="index.php" method="POST">
<input type="text" id="myInput" onkeyup="myFunction()" placeholder="Rechercher par nom" title="Rechercher par nom">
    
<select name="litteratie" id="select">
    <option value="-1" <?php if($litteratie==-1) echo " selected"; ?>>Littératie</option>
    <option value="0"  <?php if($litteratie==0) echo " selected"; ?>>Pas de texte</option>
     <option value="1" <?php if($litteratie==1) echo " selected"; ?>>Lecture simple</option>
     <option value="2" <?php if($litteratie==2) echo " selected"; ?>>Lecture avancée</option>
    <option value="3"  <?php if($litteratie==3) echo " selected"; ?>>Ecriture</option>
</select> 
    
<select name="age" id="select">
    <option value="-1" <?php if($age==-1) echo " selected"; ?>>Age minimum</option>
    <?php
    for($i=0;$i<18;$i++)
    {
    ?><option value="<?php echo $i;?>" <?php if($age==$i) echo " selected"; ?>><?php echo $i;?></option><?php
    }
    ?>
        

</select> 
    
<select name="statut" id="select">
    <option value="-1" <?php if($statut==-1) echo " selected"; ?>>Statut</option>
     <option value="Empruntable" <?php if($statut=='Empruntable') echo " selected"; ?>>Empruntable</option>
     <option value="Consultable" <?php if($statut=='Consultable') echo " selected"; ?>>Consultable</option>
</select> 
    
    <select name="type" id="select">
    <option value="-1" <?php if($type==-1) echo " selected"; ?>>Type</option>
         <?php
    $typesJeu=getTypesJeu($dbh);
    for($i=0;$i<sizeof($typesJeu);$i++)
    {
        ?><option value="<?php echo $typesJeu[$i];?>" <?php if($type==$typesJeu[$i]) echo " selected"; ?>><?php echo $typesJeu[$i];?></option><?php
    }
    ?>
</select> 
    
 <select name="difficulte" id="select">
    <option value="-1">Difficulté</option>
         <?php
    $difficultesJeu=getDifficultesJeu($dbh);
    for($i=0;$i<sizeof($difficultesJeu);$i++)
    {
        ?><option value="<?php echo $difficultesJeu[$i];?>" <?php if($difficulte==$difficultesJeu[$i]) echo " selected"; ?>><?php echo $difficultesJeu[$i];?></option><?php
    }
    ?>
</select>
  
        <button class="pure-button pure-button-primary allwidth" type="submit">RECHERCHER</button>
        <br><a href="index.php" class="pure-button allwidth" >EFFACER LA RECHERCHE</a>
</form>
    <br><br>
   
    <center><h2><?php echo "Nb jeux : ".$nbResultats;?></h2></center>
<table id="myTable">
  <tr class="header">
    <th style="width:20%;">Jeu</th>
    <th style="width:80%;">Country</th>
  </tr>
  
 <?php
  
      for($i=0;$i<sizeof($listingExemplaires);$i++)
    {
        ?>
      <tr>
      <td><a href="detailJeu.php?idE=<?php echo $listingExemplaires[$i]['idExemplaire'] ;?>"><img src="searchicon.png"></a></td>
      <td><?php echo $listingExemplaires[$i]['libelleJeu'] ;?></td>
    </tr>
      <?php
    }
      ?>
</table>
        

<script>
function myFunction() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[1];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }       
  }
}
</script>

</body>
</html>