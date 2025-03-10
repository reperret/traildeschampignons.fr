<?php
include 'bdd.php';
include 'fonctions.php';
$method=NULL;
$auth=NULL;

if(isset($_GET['method']) && $_GET['method']!='') $method=$_GET['method'];
if(isset($_POST['method']) && $_POST['method']!='') $method=$_POST['method'];
if(isset($_GET['auth']) && $_GET['auth']!='') $auth=$_GET['auth'];
if(isset($_POST['auth']) && $_POST['auth']!='') $auth=$_POST['auth'];

//*****************************************
//********RECEPTION DES APPEL*************
//*****************************************


//********VERIFICATION EMETTEUR*************
if($auth=="OHIAZEHRFJIIaeji8949afM")
{
    switch ($method)
    {
                  
        //***************************************************
        //*********getConfithons*****************
        //***************************************************
        case 'getConfiguration':
           if(
                   (isset($_GET['idAppareil']) && $_GET['idAppareil']!='')                
              )
            {
                echo json_encode(getConfiguration($_GET['idAppareil'],$dbh),JSON_BIGINT_AS_STRING);
            }
            else
            {
                $data = array();
                $data["codeErreur"]  = 'ERROR_MISSING_PARAMETERS';
                echo json_encode($data,JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
            }

            break;
      


        //***************************************************
        //*********uploadMontage*****************
        //***************************************************
       case 'updatePassage':
             if(
                (isset($_POST['idPassage']) && $_POST['idPassage']!='')
              )
            {
                echo json_encode(updatePassage($_POST['idPassage'],$dbh),JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
            }
            else
            {
                $data = array();
                $data["codeErreur"]  = 'ERROR_MISSING_PARAMETERS';
                echo json_encode($data,JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
            }

            break;
        


        //***************COMPORTEMENT PAR DEFAUT SI METHODE NON TROUVEE********************
        default:
            $data = array();
            $data["codeErreur"]  = 'UNKNOWN_METHOD';
            echo json_encode($data,JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
    }
}
else
{
    $data = array();
    $data["codeErreur"] ="ERROR_AUTH_FAILED";
    echo json_encode($data,JSON_FORCE_OBJECT|JSON_UNESCAPED_UNICODE);
}
?>
