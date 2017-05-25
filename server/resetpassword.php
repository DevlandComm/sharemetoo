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


$email = $_POST["email"];

$retVal = false;
$commit = true;
	

$base_dir  = __DIR__; // Absolute path to your installation, ex: /var/www/mywebsite
$doc_root  = preg_replace("!${_SERVER['SCRIPT_NAME']}$!", '', $_SERVER['SCRIPT_FILENAME']); # ex: /var/www
$base_url  = preg_replace("!^${doc_root}!", '', $base_dir); # ex: '' or '/mywebsite'
$protocol  = empty($_SERVER['HTTPS']) ? 'http' : 'https';
$port      = $_SERVER['SERVER_PORT'];
$disp_port = ($protocol == 'http' && $port == 80 || $protocol == 'https' && $port == 443) ? '' : ":$port";


$server_root = $protocol . "://" . $_SERVER['SERVER_NAME'] . "/sharemetoo/";


try {

      $conn->beginTransaction();
      
      
      $stmt = $conn->prepare("SELECT user.email, userinfo.firstname FROM user inner join userinfo on user.userid = userinfo.userid WHERE user.email = :email ");
      $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
  
      if($stmt->execute()) {
          $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
          foreach($rows as $row) {
              if(strlen($row['email']) > 0) {
                  
                  $fname = $row['firstname'];
                  
                  $stmt = $conn->prepare("update user set status = 'Reset'
                                    where email = :email ");   
                  $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
                  $stmt->execute();
                  

                  // BEGIN: Send email
                    $strSubjectline     = "ShareMeToo: Reset Password";
                    $strBody            = "Dear ".$fname.",<br/><br/>";
                    $strBody            .= "To reset your password, please click on the URL below.<br/><br/>";
                    $strBody            .= "<a href='".$server_root."#/forgotpassw/".$email."'>".$server_root."#/forgotpassw/".$email."</a>";
                    $strBody            .= "<br/><br/>Best regards,<br/>ShareMeToo Team";

                    $headers  = 'MIME-Version: 1.0' . "\r\n";
                    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                    $headers .= "From: noreply@xoloosh.com" . "\r\n";

                  if(mail($email,$strSubjectline, $strBody,$headers)) {
                       $app_list = array("status" => "200") + array("results" => "Password reset instruction sent...");           
                   } else  {
                       $app_list = array("status" => "400") + array("results" => "Unable to send email invitation.");
                   }

                  
              } else {
                  $app_list = array("status" => "400") + array("results" => "User already exists.");
              }
          }
      }
       
        
  } catch(PDOException $e){
          
      //echo "Something went wrong...";
      $app_list = $stmt->errorInfo();
      $app_list = array("status" => "400") + array("results" => $e->getMessage());    
      $commit = false;
  }


  if(!$commit)
  {
      $conn->rollback();
  } else {
      $conn->commit();
  }

    
    
    //return JSON array
    header('Content-type: application/json');
    if (is_array($app_list)) { 
        echo(json_encode($app_list));
    } else { 
        echo($app_list);
    }

?>


