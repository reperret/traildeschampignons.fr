<?php
if (isset($_POST['connexion']) && $_POST['connexion'] == 'Connexion') 
{
	if ((isset($_POST['emailAdministrateur']) && !empty($_POST['emailAdministrateur'])) && (isset($_POST['passAdministrateur']) && !empty($_POST['passAdministrateur']))) 
	{
		$reqInsert = $dbh->prepare('SELECT idAdministrateur, passAdministrateur, profilAdministrateur,emailAdministrateur, count(*) AS nbre FROM administrateur WHERE emailAdministrateur=? AND passAdministrateur=?');
		$reqInsert->bindParam(1, $login);
		$reqInsert->bindParam(2, $passmd5);
		$login = $_POST['emailAdministrateur'];
		$passmd5 = md5($_POST['passAdministrateur']);
		$reqInsert->execute();
		$lignes=$reqInsert->fetchAll(PDO::FETCH_OBJ);
        
        foreach ($lignes as $colonne)
        {
            if ($colonne->nbre == 1) 
            {
                $some_name = session_name("loginAdmin");
                session_set_cookie_params(0, '/', $domaineCookie);
                session_start();
                $_SESSION['loginAdmin'] = $_POST['emailAdministrateur'];
                $value = $colonne->idAdministrateur.'---'.hash('sha512', $colonne->emailAdministrateur.'---'.$colonne->passAdministrateur."---_Admin");
                setcookie('authTrailDesChampignons', $value, time() + (7 * 24 * 3600) , "/", $domaineCookie);
                //echo "Cookie: ".$_COOKIE['authTrailDesChampignons'];
               //echo "Session: ".$_SESSION['loginAdmin'];
                $redirect="index.php"; 
                header('Location: '.$redirect);
                exit();
            }
            elseif ($colonne->nbre == 0) 
            {
                $erreur = 'Compte non reconnu.';
            }
            else 
            {
                $erreur = 'Une erreur fatale est intervenue. Veuillez contacter votre webmaster';
            }
        }
        $reqInsert->closeCursor(); 
	}
	else 
	{
		$erreur = 'Veuillez remplir tous les champs demandés';
	}
}
?>
