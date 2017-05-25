<?php

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



    


 
 
    /* These are functions available in this api set. */
    $possible_url = array("user_info_list", "user_assignment", "authenticate_user","invite_user","get_user_roles","register_user","reset_user_password","all_users_exlude_friends");
	$value = "An error has occurred";

    
    

    /***********************************************************************
    * This function returns all users.
    ***********************************************************************/
    function get_user_info_list()
    {
      include "mysqlconnect.php";
      
        $stmt = $conn->prepare("SELECT idno,firstname,lastname,company,email,mobile,phone,userid FROM userinfo ");
        
          if($stmt->execute()) {
            $app_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $app_list = array("status" => "200") + array("results" => $app_list); 
          } else {
        	  //echo "Something went wrong...";
        	  $app_list = $stmt->errorInfo();
              $app_list = array("status" => "400") + array("results" => $app_list);
          }
    
      
      return $app_list;
    }
    


    
    /***********************************************************************
    * This function returns all user app assignments.
    ***********************************************************************/
    function get_user_assignment($email)
    {
      include "mysqlconnect.php";
      
        $stmt = $conn->prepare("SELECT 
                                        user.userid, 
                                        user.email,
                                        status,
                                        appid,
                                        imgurl,
                                        userassignment.iduserroles,
                                        description,
                                        assignedtoorg,
                                        userinfo.firstname,
                                        userinfo.lastname,
                                        company,
                                        mobile,
                                        phone
                                from user inner join userassignment on user.userid = userassignment.userid 
                                inner join userroles on userassignment.iduserroles = userroles.iduserroles
                                inner join userinfo on userinfo.userid = userassignment.userid
                                WHERE user.email = :email ");
        
        $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
        
          if($stmt->execute()) {
            $app_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $app_list = array("status" => "200") + array("results" => $app_list);
          } else {
        	  //echo "Something went wrong...";
        	  $app_list = $stmt->errorInfo();
              $app_list = array("status" => "400") + array("results" => $app_list);
          }
    
      
      return $app_list;
    }


    
    /***********************************************************************
    * This function authenticates user
    ***********************************************************************/
    function do_authenticate_user($email,$passw)
    {
      include "mysqlconnect.php";
      
        $s = base64_encode(hash_hmac('sha256', $passw, "saltfreeport", true));
        
        $passwordOK = "";
        $usernameOK = "";
        $qPass      = "";
        $app_list = array("status" => "400") + array("results" => "Incorrect login.");
        
        //1. First let's check if there user exist
        $stmt = $conn->prepare("SELECT userid,email,passw, imgurl FROM user WHERE email = :email ");
        $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
        
          if($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                foreach($rows as $row) {
                    $qEmail = $row['email'];
                    
                    // 2. If the user exist then let's check if the password is correct
                    if($qEmail == $email)
                    {
                        $qPass = $row['passw'];
                        
                        if ($qPass == $s)//$passw)
                        {
                            $arr1 = array();
                            $person = array("userid"=>$row['userid'], "email"=>$row['email']);
                            array_push($arr1, $person);
                            $app_list = array("status" => "200") + array("results" => $arr1);
                            
                            return $app_list;
                        } else {
                            $passwordOK = "Incorrect password. ";
                        }
                    } else {
                        $usernameOK = "Username does not exist. ";
                    }
                }
            
              if((strlen($usernameOK) > 0) || (strlen($passwordOK) > 0))
                $app_list = array("status" => "400") + array("results" => $usernameOK . $passwordOK);
              else
                $app_list = array("status" => "400") + array("results" => "Incorrect login.");  
              
          } else {
            //echo "Something went wrong...";
        	  $app_list = $stmt->errorInfo();
              $app_list = array("status" => "400") + array("results" => $app_list);
          }
        
      
      return $app_list;
    }


    function do_send_invite($email,$fname,$lname,$role) {
        include "mysqlconnect.php";
        
        $commit = true;
        
        
        try {
            $conn->beginTransaction();
            
            
            $stmt = $conn->prepare("SELECT count(*) tcount FROM user WHERE email = :email ");
            $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
        
            if($stmt->execute()) {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                foreach($rows as $row) {
                    if($row['tcount'] > 0) {
                        
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
                             $header = "MIME-Version: 1.0\r\n";
                             $header .= "Content-type: text/html\r\n";

                             $strBody          = "Freeport LNG: User Registration";
                             $strSubjectline   = "Please click on the link below to complete your registration<br/><br/>";
                             $strSubjectline   .= "<a href='http://".$_SERVER['SERVER_NAME']."/#/registeruser/'".$email."></a>";
                             $strSubjectline   .= "<br/><br/>Best regards,<br/>Freeport LNG Team";

                             $retVal = mail ($email, $strSubjectline, $strBody, $header);

                             if($retVal == true) {
                                 $app_list = array("status" => "200") + array("results" => "Invitation sent...");           
                             } else  {
                                 $app_list = array("status" => "400") + array("results" => "Unable to send email invitation.");
                             }
                            
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
        
        
      return $app_list;
    }





    function get_user_roles() {
        include "mysqlconnect.php";
      
        $stmt = $conn->prepare("SELECT * FROM userroles ");
        
          if($stmt->execute()) {
            $roles_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $roles_list = array("status" => "200") + array("results" => $roles_list); 
          } else {
        	  //echo "Something went wrong...";
        	  $roles_list = $stmt->errorInfo();
              $roles_list = array("status" => "400") + array("results" => $roles_list);
          }
    
      
      return $roles_list;
    }




    function do_register_user($email,$passw)
    {
        include "mysqlconnect.php";
        
        $s = base64_encode(hash_hmac('sha256', $passw, "saltfreeport", true));
        
        try {
            
            $stmt = $conn->prepare("update user set passw = :passw, status='Active' where email = :email ");   
            $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
            $stmt->bindParam(':passw',$s,PDO::PARAM_STR,155);
            $stmt->execute();
                
            /*if($stmt->execute())
            {
                $app_list = array("status" => "200") + array("results" => "Password updated..."); 
            } else {
                //echo "Something went wrong...";
        	  $app_list = $stmt->errorInfo();
              $app_list = array("status" => "400") + array("results" => $app_list);
            }*/
            
            $app_list = array("status" => "200") + array("results" => "Password updated...");
        } catch(PDOException $e){
                
            //echo "Something went wrong...";
            $app_list = $stmt->errorInfo();
            $app_list = array("status" => "400") + array("results" => $e->getMessage());    
        }
        
        return $app_list;
        
    }









    function do_reset_user_password($email,$passw) {
        
        include "mysqlconnect.php";
        
        $s = base64_encode(hash_hmac('sha256', $passw, "saltfreeport", true));
        
        try {
            
            
            $stmt1 = $conn->prepare("select count(*) as tcount from user where email = :email and status = 'Reset' ");   
            $stmt1->bindParam(':email',$email,PDO::PARAM_STR,255);
            
            if($stmt1->execute()) {
                $rows = $stmt1->fetchAll(PDO::FETCH_ASSOC);
      
                foreach($rows as $row) {
                    if($row['tcount'] > 0) {
                        $stmt = $conn->prepare("update user set passw = :passw, status='Active' where email = :email ");   
                        $stmt->bindParam(':email',$email,PDO::PARAM_STR,255);
                        $stmt->bindParam(':passw',$s,PDO::PARAM_STR,155);
                        $stmt->execute();
                        
                        $app_list = array("status" => "200") + array("results" => "Password updated...");
                    } else {
                        $app_list = array("status" => "200") + array("results" => "Password not updated...");
                    }
                }
            }
            
        } catch(PDOException $e){
                
            //echo "Something went wrong...";
            $app_list = $stmt->errorInfo();
            $app_list = array("status" => "400") + array("results" => $e->getMessage());    
        }
        
        return $app_list;
    }





    function GetAllUsersExcludeFriends($userid,$searchterms) {
        include "mysqlconnect.php";
      
        $stmt = $conn->prepare("select * from
                                user u inner join userinfo ui
                                on u.userid = ui.userid 
                                where 
                                (ui.firstname like '".$searchterms."%' or ui.lastname like '".$searchterms."%')
                                and
                                (u.userid not in (select useridconnection from userconnection where userid = :id)
                                and u.userid not in (select userid from userconnection where userid = :id))");

        $stmt->bindParam(':id',$userid,PDO::PARAM_STR,255);
        
          if($stmt->execute()) {
            $roles_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $roles_list = array("status" => "200") + array("results" => $roles_list); 
          } else {
              //echo "Something went wrong...";
              $roles_list = $stmt->errorInfo();
              $roles_list = array("status" => "400") + array("results" => $roles_list);
          }
      
      return $roles_list;
    }






    /***********************************************************************
    * MAIN BODY. Program entry point.
    ***********************************************************************/
    if (isset($_POST["action"]) && in_array($_POST["action"], $possible_url))
    {
      switch ($_POST["action"])
        {
          case "get_user_info_list":
            //users.php?action=get_user_info_list
            $value = get_user_info_list();
            break;
          case "user_assignment":
            //users.php?action=get_user_assignment&email=antonio.logarta@g2-is.com
            if (isset($_POST["email"]))
               $value = get_user_assignment($_POST["email"]);
            else
              $value = "Missing email.";
            
            break;
          case "authenticate_user":
            //users.php?action=authenticate_user&email=antonio.logarta@g2-is.com&passw=password
            if (isset($_POST["email"]) && isset($_POST["passw"]))
               $value = do_authenticate_user($_POST["email"], $_POST["passw"]);
            else
              $value = "Missing email or password.";
          
            break;
          case "invite_user":
            if (isset($_POST["email"]))
               $value = do_send_invite($_POST["email"],$_POST["fname"],$_POST["lname"],$_POST["role"]);
            else
              $value = "Missing parameter.";
            break;
          case "register_user":
            if (isset($_POST["email"]))
               $value = do_register_user($_POST["email"],$_POST["passw"]);
            else
              $value = "Missing parameter.";
            break;
          case "reset_user_password":
            if (isset($_POST["email"]))
               $value = do_reset_user_password($_POST["email"],$_POST["passw"]);
            else
              $value = "Missing parameter.";
            break;
          case "get_user_roles":            
            $value = get_user_roles();
            break;
          case "all_users_exlude_friends":
            if (isset($_POST["userid"])) {
                $value = GetAllUsersExcludeFriends($_POST["userid"],$_POST["searchterms"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;
        }
    }

    
    //return JSON array
    header('Content-type: application/json');
    if (is_array($value)) { 
        echo(json_encode($value));
    } else { 
        echo($value);
    }
	

?>


