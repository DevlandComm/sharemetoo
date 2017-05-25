<?php

include "mysqlconnect.php";

	  if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400');    // cache for 1 day
    }
 
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
 
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
 
        exit(0);
    }



/*$email = "antonio.logarta@g2-is.com"; //$_GET["email"];
$fname = "Antonio"; //$_GET["fname"];
$lname = "Logarta"; //$_GET["lname"];
$role  = "6"; //$_GET["role"];*/
$email  = $_POST["email"];
$userid = $_POST["userid"];

define('SENDER', 'aclogarta@yahoo.com');   
define('RECIPIENT', $email);

define('USERNAME','AKIAI5RHHDYPRHKX7JBA'); 
define('PASSWORD','AtcXmzIHO4JilQ9iBhlAU5o573TuCAk05P4B0rHJJ01U'); 

	
$base_dir  = __DIR__; // Absolute path to your installation, ex: /var/www/mywebsite
$doc_root  = preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']); # ex: /var/www
$base_url  = preg_replace("!^${doc_root}!", '', $base_dir); # ex: '' or '/mywebsite'
$protocol  = empty($_SERVER['HTTPS']) ? 'http' : 'https';
$port      = $_SERVER['SERVER_PORT'];
$disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";


$server_root = $protocol . "://" . $_SERVER['SERVER_NAME'];


try {

      $strSubjectline     = "ShareMeToo: User Registration";
      $strBody            = "Dear ".$email.",<br/><br/>";
      $strBody            .= "To complete your registration, please click on the URL below to setup your new password.<br/><br/>";
      $strBody            .= "<a href='".$server_root."#/registeruser/".$email."'>".$server_root."#/registeruser/".$email."</a>";
      $strBody            .= "<br/><br/>Best regards,<br/>ShareMeToo Team";

      define('HOST', 'email-smtp.us-west-2.amazonaws.com');
      define('PORT', '587');
      define('SUBJECT', $strSubjectline);
      define('BODY', $strBody);

      require_once 'Mail.php';

      $headers = array (
        'From' => SENDER,
        'To' => RECIPIENT,
        'Subject' => SUBJECT);

      $smtpParams = array (
        'host' => HOST,
        'port' => PORT,
        'auth' => true,
        'username' => USERNAME,
        'password' => PASSWORD
      );

       // Create an SMTP client.
      $mail = Mail::factory('smtp', $smtpParams);


      // Send the email.
      $result = $mail->send(RECIPIENT, $headers, BODY);

      if (PEAR::isError($result)) {
        $app_list = array("status" => "400") + array("results" => "Unable to send email invitation. " . $result->getMessage());
      } else {
        $app_list = array("status" => "200") + array("results" => "Invitation sent...");           
      }
       
        
  } catch(Exception $e){
          
      //echo "Something went wrong...";
      $app_list = array("status" => "400") + array("results" => $e->getMessage());    
  }

    
    //return JSON array
    header('Content-type: application/json');
    if (is_array($app_list)) { 
        echo(json_encode($app_list));
    } else { 
        echo($app_list);
    }

?>


