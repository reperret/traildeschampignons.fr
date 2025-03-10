<?php
//**************RECUPERATION DES DROITS**********************
	session_start();
    $emailAdministrateur=NULL;
    $passAdministrateur=NULL;
    $nomAdministrateur=NULL;
    $prenomAdministrateur=NULL;
    $profilAdministrateur=-1;
    $idAdministrateur=NULL;
    $redirect=NULL;
    $rentreDansCookie=false;
    $authExplode=NULL;
    $authExplodeNb=0;
    $hash=NULL;
    if(isset($_COOKIE['authTrailDesChampignons'])) 
    {
        $authExplode = explode('---', $_COOKIE['authTrailDesChampignons']);
        if (count($authExplode) === 2) 
        {
            $authExplodeNb=count($authExplode);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->query('SET NAMES UTF8');
            $resultats = $dbh->query('SELECT * FROM administrateur WHERE idAdministrateur='.$authExplode[0]);
            $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);

            foreach ($lignes as $colonne)
            {
                $idAdministrateur=$colonne->idAdministrateur;
                $emailAdministrateur=$colonne->emailAdministrateur;	
                $nomAdministrateur=$colonne->nomAdministrateur;
                $prenomAdministrateur=$colonne->prenomAdministrateur;	
                $passAdministrateur=$colonne->passAdministrateur;	
                $profilAdministrateur=$colonne->profilAdministrateur;	
            }	
            $resultats->closeCursor();	

            if ($lignes!=NULL && $authExplode[1] === hash('sha512', $emailAdministrateur.'---'.$passAdministrateur."---_Admin")) 
            {
                $_SESSION['loginAdmin'] = $emailAdministrateur;
                $hash=hash('sha512', $emailAdministrateur.'---'.$passAdministrateur."---_Admin");
            }
        }
    }
    elseif(isset($_SESSION['loginAdmin']) && $_SESSION['loginAdmin'] != NULL )
    {
        $emailAdministrateur=$_SESSION['loginAdmin'];
    }	

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $dbh->query('SET NAMES UTF8');
    $resultats = $dbh->query('SELECT idAdministrateur, profilAdministrateur, emailAdministrateur FROM administrateur WHERE emailAdministrateur LIKE "'.$emailAdministrateur.'"');
    $lignes=$resultats->fetchAll(PDO::FETCH_OBJ);

    foreach ($lignes as $colonne)
    {	
        $idAdministrateur=intval($colonne->idAdministrateur);	
        $profilAdministrateur=intval($colonne->profilAdministrateur);	
        $emailUtilisateur=$colonne->emailAdministrateur;	
    }	
    $resultats->closeCursor();	

    if($profilAdministrateur!=0)
    {
        $redirect="login.php?e=1";
        header('Location: '.$redirect);
        exit();
    }
   


?>