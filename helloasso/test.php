<?php

$serverURL = "https://api.helloasso.com";
$token = auth();
var_dump($token);

/*
if ($token === null)
   exit;

$cl = curl_init();
curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
/* uncomment the next line in case you don't have the required SSL certificates */
// curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false);

/* show the token and add it to our future requests*/
/*
var_dump($token);
curl_setopt($cl, CURLOPT_HTTPHEADER, array("Authorization: Bearer " . $token['access_token']));
*/


/**
 * Get an authentication token
 */
function auth()
{
   global $serverURL;
   $cl = curl_init();
   curl_setopt($cl, CURLOPT_RETURNTRANSFER, true);
   curl_setopt($cl, CURLOPT_URL, "$serverURL/oauth2/token");
   curl_setopt($cl, CURLOPT_POST, true);
   /* uncomment this line if you don't have the required SSL certificates */
   // curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, false);
   curl_setopt($cl, CURLOPT_POSTFIELDS, array(
     "grant_type" => "client_credentials",
     "client_id" => "2d88f2b681d94f919159a193feadc7cc",
     "client_secret" => "3B4do33plp6ZkHZg6ypd+FC+QX+bOIoX"
   ));
   $auth_response = curl_exec($cl);
   if ($auth_response === false)
   {
      echo "Failed to authenticate\n";
      var_dump(curl_getinfo($cl));
      curl_close($cl);
      return NULL;
   }
   curl_close($cl);
   return json_decode($auth_response, true);
}