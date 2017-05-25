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
$email = $_POST["email"];
$fname = $_POST["fname"];
$lname = $_POST["lname"];
$role  = $_POST["role"];

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
      
      
      $stmt = $conn->prepare("SELECT count(*) tcount FROM user WHERE email = :email ");
      $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
  
      if($stmt->execute()) {
          $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
          foreach($rows as $row) {
              if($row['tcount'] == 0) {
                  
                  $stmt = $conn->prepare("INSERT INTO user (email,passw)         
                                          VALUES(:email,'')");   
                  $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
                  $stmt->execute();
                  $lastUserID = $conn->lastInsertId();

                  $stmt = $conn->prepare("INSERT INTO userinfo (firstname,lastname,email,userid)         
                                                      VALUES(:fname,:lname,:email,:userid)");   
                  $stmt->bindParam(':fname',$fname,PDO::PARAM_STR,50);
                  $stmt->bindParam(':lname',$lname,PDO::PARAM_STR,50);
                  $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
                  $stmt->bindParam(':userid',$lastUserID,PDO::PARAM_INT);
                  $stmt->execute();

                  $stmt = $conn->prepare("INSERT INTO userassignment (appid,userid,iduserroles)         
                                                      VALUES(1,:userid,:iduserroles)");   
                  $stmt->bindParam(':iduserroles',$role,PDO::PARAM_INT);
                  $stmt->bindParam(':userid',$lastUserID,PDO::PARAM_INT);
                  $stmt->execute();


                  if($lastUserID > 0) {
                      // Let's send the email invite...
                       
                      // BEGIN: Send email
                        $strSubjectline     = "ShareMeToo: User Registration";
                        $strBody            = "Dear ".$fname.",<br/><br/>";
                        $strBody            .= "To complete your registration, please click on the URL below to setup your new password.<br/><br/>";
                        $strBody            .= "<a href='".$server_root."#/registeruser/".$email."'>".$server_root."#/registeruser/".$email."</a>";
                        $strBody            .= "<br/><br/>Best regards,<br/>ShareMeToo Team";

                        $headers  = 'MIME-Version: 1.0' . "\r\n";
                        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
                        $headers .= "From: noreply@xoloosh.com" . "\r\n";
                        
                      if(mail($email,$strSubjectline, $strBody,$headers)) {
                           $app_list = array("status" => "200") + array("results" => "Invitation sent...");           
                       } else  {
                           $app_list = array("status" => "400") + array("results" => "Unable to send email invitation.");
                       }

                      // END: Send email
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


