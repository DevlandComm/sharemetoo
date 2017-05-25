<?php

    //  header("Access-Control-Allow-Origin: *");
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
 
    

    /*******************************************
    * API Key processing...
    *******************************************/
    function authenticateByAPI($params,$secretSignature,$deviceid) {
        include "mysqlconnect.php";
        
        $stmt = $conn->prepare("SELECT apikey FROM devices where deviceid = :deviceid ");
        $stmt->bindParam(':deviceid',$deviceid,PDO::PARAM_INT);
        
          if($stmt->execute()) {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
                foreach($rows as $row) {
                    $API_KEY = $row['apikey'];
                }
          }
            
        $s = hash_hmac('sha256', $params, $API_KEY, true);
        
        //$app_list = array("status" => "200") + array("results" => base64_encode($secretSignature)); 
        //return $app_list;
        
        if(base64_encode($s) == str_replace(" ", "+", $secretSignature)) {
            return true;
        } else {
            return false;
        }
        
    }
    

    
    /***********************************************************************
    * This function returns an app information based on its id.
    
        {"status":"200","results":[{"appid":"1","appname":"FORM252","version":"1.0.0"}]}
    ***********************************************************************/
    function get_app_by_id($id)
    {
      include "mysqlconnect.php";
      //normally this info would be pulled from a database.
      //build JSON array
      //$app_list = array(array("id" => 1, "name" => "Web Demo"), array("id" => 2, "name" => "Audio Countdown"), array("id" => 3, "name" => "The Tab Key"), array("id" => 4, "name" => "Music Sleep Timer")); 

        $stmt = $conn->prepare("SELECT appid, appname, version, description, enabled, imagepath FROM application WHERE appid = :appid ");
        
        $stmt->bindParam(':appid',$id,PDO::PARAM_INT);
        
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
    * This function returns a list of all the app stored in the database.
    
        {"status":"200","results":[{"appid":"1","appname":"FORM252","version":"1.0.0"},
                                    {"appid":"2","appname":"INVENTORY505","version":"1.0.0"}]}
    ***********************************************************************/
    function get_app_list()
    {
      include "mysqlconnect.php";
      
        $stmt = $conn->prepare("SELECT appid, appname, version, description, enabled, imagepath FROM application ");
        
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
    * This function returns the dynamic form based on app id
        {"status":"200","results":[{"appid":"1","appname":"FORM252","version":"1.0.0"}]}
    ***********************************************************************/
    function get_app_form($appid)
    {
      include "mysqlconnect.php";
      //normally this info would be pulled from a database.
      //build JSON array
      //$app_list = array(array("id" => 1, "name" => "Web Demo"), array("id" => 2, "name" => "Audio Countdown"), array("id" => 3, "name" => "The Tab Key"), array("id" => 4, "name" => "Music Sleep Timer")); 

        $stmt = $conn->prepare("SELECT appid, appname, version FROM application WHERE appid = :appid ");
        
        $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
        
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




    /*************************************************************************************
    * This function returns UI Controls with its field values.
        {"status":"200","results":[{"recid":"2","fieldid":"1","fieldvalue":"New","appid":"1","fieldname":"inspectiontype","fielddisplayname":"Inspection Type","controltype":"multiselect","logicaltablename":"form252"},
        {"recid":"2","fieldid":"2","fieldvalue":"12-20-2015","appid":"1","fieldname":"inspectiondate","fielddisplayname":"Inspection Date","controltype":"datefield","logicaltablename":"form252"}]}
    **************************************************************************************/
    function get_form_controls_and_values($appid,$recid)
    {
      include "mysqlconnect.php";

        
        $stmt = $conn->prepare("SELECT f.recid,f.fieldid,fieldvalue,appid,fieldname,fielddisplayname,controltype,logicaltablename FROM formfields f INNER JOIN reccounter r on f.recid = r.recid INNER JOIN formdictionary d on f.fieldid = d.fieldid WHERE appid = :appid AND f.recid = :recid ");
        
        $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
        $stmt->bindParam(':recid',$recid,PDO::PARAM_INT);
        
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



    /*************************************************************************************
    * This function returns UI Controls only.
        {"status":"200","results":[{"fieldid":"1","fieldname":"inspectiontype","fielddisplayname":"Inspection Type","fielddatatype":"nvarchar","controltype":"multiselect","logicaltablename":"form252","appid":"1"},
        {"fieldid":"2","fieldname":"inspectiondate","fielddisplayname":"Inspection Date","fielddatatype":"date","controltype":"datefield","logicaltablename":"form252","appid":"1"}]}
    **************************************************************************************/
    function get_form_controls($appid,$tablename)
    {
        include "mysqlconnect.php";

        //$stmt = $conn->prepare("SELECT DISTINCT d.fieldid,fieldname,fielddisplayname,fielddatatype,controltype,logicaltablename,a.appid FROM formdictionary d INNER JOIN formfields f on d.fieldid = f.fieldid INNER JOIN reccounter r on r.recid = f.recid INNER JOIN application a on a.appid = r.appid WHERE a.appid = :appid AND d.logicaltablename = :tablename");
        $stmt = $conn->prepare("SELECT DISTINCT fieldid,fieldname,fielddisplayname,fielddatatype,fieldlength,controltype,logicaltablename,appid FROM formdictionary WHERE appid = :appid AND logicaltablename = :tablename");
        
        $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
        $stmt->bindParam(':tablename',$tablename,PDO::PARAM_STR,50);
        
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



    
    /*************************************************************************************
    * This function returns all field values.
        {"status":"200","results":[{"recid":"1","fieldid":"1","fieldname":"inspectiontype","fieldvalue":"New","appid":"1","logicaltablename":"form252"},
        {"recid":"1","fieldid":"2","fieldname":"inspectiondate","fieldvalue":"12-20-2015","appid":"1","logicaltablename":"form252"}]}
    
    **************************************************************************************/
    function get_all_form_values($appid,$tablename)
    {
      include "mysqlconnect.php";
    
        $stmt = $conn->prepare("SELECT f.recid,f.fieldid,fieldname,fieldvalue,appid,logicaltablename FROM formfields f INNER JOIN reccounter r on f.recid = r.recid INNER JOIN formdictionary d on f.fieldid = d.fieldid WHERE appid = :appid AND d.logicaltablename = :tablename");
        
        $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
        $stmt->bindParam(':tablename',$tablename,PDO::PARAM_STR,50);
        
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





    /*************************************************************************************
    * This function returns all field values.
        {"status":"200","results":[{"recid":"1","fieldid":"1","fieldname":"inspectiontype","fieldvalue":"New","appid":"1","logicaltablename":"form252"},
        {"recid":"1","fieldid":"2","fieldname":"inspectiondate","fieldvalue":"12-20-2015","appid":"1","logicaltablename":"form252"}]}
    
    **************************************************************************************/
    function get_all_tables($appid)
    {
      include "mysqlconnect.php";
    
        //$stmt = $conn->prepare("SELECT DISTINCT logicaltablename FROM formfields f INNER JOIN reccounter r on f.recid = r.recid INNER JOIN formdictionary d on f.fieldid = d.fieldid WHERE d.appid = :appid ");
        $stmt = $conn->prepare("SELECT DISTINCT logicaltablename FROM formdictionary  WHERE appid = :appid ");
        
        $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
        
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






    /*************************************************************************************
    * This function returns all field values only (pivoted table).
        {"status":"200","results":[
            {"recid":"1","inspectiontype":"New","inspectiondate":"12-20-2015","datenotified":"12-21-2015","inspectedby":"Antonio C. Logarta","wrnumber":"WR-32143","developer":"Dev Man","projectname":"Project 1","location":"San Francisco","sleevecontractor":"Sleeve Company","contactname":"Jerome","phone":"7071234545","mobile":"","servicetrench":"OK","muletype":"Yes","shade":"Native"},
            {"recid":"2","inspectiontype":"New","inspectiondate":"12-22-2015","datenotified":"12-23-2015","inspectedby":"Jerome Longakit","wrnumber":"WR-32144","developer":"Dev Man","projectname":"Project 2","location":"Fairfield","sleevecontractor":"Sleeve Company","contactname":"Jerome","phone":"7071234545","mobile":"","servicetrench":"OK","muletype":"Not Needed","shade":"Sand"}
            ]}
    **************************************************************************************/
    function get_all_table_records($appid,$tablename)
    {
      include "mysqlconnect.php";
    
        $sql = "call get_records_from_table_and_pivot('".$appid."','".$tablename."')";
        
        $q = $conn->query($sql);
        
         if($q) {
            $app_list = $q->fetchAll(PDO::FETCH_ASSOC);
            $app_list = array("status" => "200") + array("results" => $app_list);    
          } else {
        	  //echo "Something went wrong...";
        	  $app_list = $stmt->errorInfo();
              $app_list = array("status" => "400") + array("results" => $app_list);
          }
    
      
      return $app_list;
        
    }



    /*************************************************************************************
    * This function returns filtered field values only (pivoted table).
        
    **************************************************************************************/
    function get_filtered_table_records($appid,$tablename, $where_clause_filter)
    {
      include "mysqlconnect.php";
    
        $sql = "call get_filtered_records_from_table_and_pivot('".$appid."','".$tablename."','".$where_clause_filter."')";
        
        $q = $conn->query($sql);
        
         if($q) {
            $app_list = $q->fetchAll(PDO::FETCH_ASSOC);
            $app_list = array("status" => "200") + array("results" => $app_list);    
          } else {
        	  //echo "Something went wrong...";
        	  $app_list = $stmt->errorInfo();
              $app_list = array("status" => "400") + array("results" => $app_list);
          }
    
      
      return $app_list;
    }


    

    /*************************************************************************************
    * This function returns all dropdowns, multiselect choices.
        {"status":"200","results":[{"idno":"1","fieldid":"1","referencevalue":"New"},
        {"idno":"2","fieldid":"1","referencevalue":"Random"},
        {"idno":"3","fieldid":"1","referencevalue":"Customer"},
        {"idno":"4","fieldid":"13","referencevalue":"OK"},
        {"idno":"5","fieldid":"13","referencevalue":"Rejected"},
        {"idno":"6","fieldid":"14","referencevalue":"Yes"},
        {"idno":"7","fieldid":"14","referencevalue":"No"},
        {"idno":"8","fieldid":"14","referencevalue":"Not Needed"},
        {"idno":"9","fieldid":"15","referencevalue":"Sand"},
        {"idno":"10","fieldid":"15","referencevalue":"Native"}]}
    
    **************************************************************************************/
    function get_field_choices($appid,$tablename)
    {
      include "mysqlconnect.php";
    
        $stmt = $conn->prepare("SELECT idno, fv.fieldid, referencevalue FROM fieldvaluechoicereference fv INNER JOIN formdictionary fd ON fv.fieldid = fd.fieldid WHERE appid = :appid AND logicaltablename = :tablename");
        
        $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
        $stmt->bindParam(':tablename',$tablename,PDO::PARAM_STR,50);
        
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



    
    /*******************************************************************************
    * Methods for updating the members_facebook table
        [
            {"fieldid":"1","fieldvalue":"This is the value"},
            {"fieldid":"1","fieldvalue":"This is the value"}
        ]
    ********************************************************************************/
    function InsertRecord($appid,$inputvalues) {
            include "mysqlconnect.php";

            $commit = true;
        
            $jsonFields = json_decode($inputvalues);
        
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $conn->beginTransaction();
                
                $stmt = $conn->prepare("INSERT INTO reccounter 
                                                           (
                                                            appid
                                                           ) 
                                                    VALUES( :APPID )");


                    $stmt->bindParam(':APPID',$appid,PDO::PARAM_INT);
                    $stmt->execute();
                    $lastId = $conn->lastInsertId();
                
                 $stmt = $conn->prepare("INSERT INTO formfields 
                                                (
                                                recid,
                                                fieldid,
                                                fieldvalue
                                               ) 
                                            VALUES ( 
                                                    :RECID,
                                                    :FIELDID,
                                                    :FIELDVALUE
                                                    )");
                
                    for($i=0; $i<count($jsonFields); $i++)
                    {
                        $stmt->bindParam(':RECID',$lastId,PDO::PARAM_INT);
                        $stmt->bindParam(':FIELDID',$jsonFields[$i]->fieldid,PDO::PARAM_INT);
                        $stmt->bindParam(':FIELDVALUE',$jsonFields[$i]->fieldvalue,PDO::PARAM_STR,150);
                        $stmt->execute();
                        $app_list = array("status" => "201") + array("results" => "Created");
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


    

    /*******************************************************************************
    * Methods for inserting new table into the database based on appid, tablename
    ********************************************************************************/
    function InsertNewTable($appid,$tablename,$physicaltable,$fields) {
            include "mysqlconnect.php";

            // Decode JSON containing fields that needs to be created
            $jsonFields = json_decode($fields);
            
            // Initialize error
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            $commit = true;
        
            // Try to create the new logical table
            try {
                
                $conn->beginTransaction();
                
                $stmt = $conn->prepare("INSERT INTO formdictionary (
                                                    fieldname,
                                                    fielddisplayname,
                                                    fielddatatype,
                                                    controltype,
                                                    fieldlength,
                                                    logicaltablename,
                                                    appid
                                                )         
                                                VALUES( 
                                                    :fieldname,
                                                    :fielddisplayname,
                                                    :fielddatatype,
                                                    :controltype,
                                                    :fieldlength,
                                                    :logicaltablename,
                                                    :appid
                                                )");
                
                    for($i=0; $i<count($jsonFields); $i++)
                    {
                        $stmt->bindParam(':fieldname',$jsonFields[$i]->FieldName,PDO::PARAM_STR,50);
                        $stmt->bindParam(':fielddisplayname',$jsonFields[$i]->DisplayName,PDO::PARAM_STR,50);
                        $stmt->bindParam(':fielddatatype',$jsonFields[$i]->DataType,PDO::PARAM_STR,50);
                        $stmt->bindParam(':controltype',$jsonFields[$i]->ControlType,PDO::PARAM_STR,50);
                        $stmt->bindParam(':fieldlength',$jsonFields[$i]->Length,PDO::PARAM_INT);
                        $stmt->bindParam(':logicaltablename',$tablename,PDO::PARAM_STR,50);
                        $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
                        $stmt->execute();
                        $app_list = array("status" => "201") + array("results" => "Created");    
                    }
                
                if($physicaltable == "1")
                {
                    //Try to create the actual table.
                    //$createStatement = "CREATE TABLE appid" . $appid . "." . $tablename;
                    $createStatement = "CREATE TABLE " . $tablename;
                    $createStatement = $createStatement . "(";
                    $primarykeys = "";

                    for($i=0; $i<count($jsonFields); $i++)
                    {
                        if(strtolower($jsonFields[$i]->DataType) == "nvarchar")
                            $createStatement = $createStatement . $jsonFields[$i]->FieldName . " NVARCHAR(" . $jsonFields[$i]->Length . "),";
                        else if(strtolower($jsonFields[$i]->DataType) == "int autoincrement") {
                            $createStatement = $createStatement . $jsonFields[$i]->FieldName . " MEDIUMINT NOT NULL AUTO_INCREMENT,";
                            $primarykeys = "PRIMARY KEY (" . $jsonFields[$i]->FieldName . "),";
                        }
                        else
                            $createStatement = $createStatement . $jsonFields[$i]->FieldName . " " . $jsonFields[$i]->DataType . ",";
                    }

                    $createStatement = $createStatement . $primarykeys;

                    $createStatement = rtrim($createStatement,",");

                    $createStatement = $createStatement . ")";

                    $stmt = $conn->prepare($createStatement);
                    $stmt->execute();
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






    /*******************************************************************************
    * Methods for inserting new field for a table into the database based on appid, tablename
    ********************************************************************************/
    function InsertNewField($appid,$tablename,$fieldname,$fielddisplayname,$fielddatatype,$controltype,$fieldlength) {
            include "mysqlconnect.php";
            
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $stmt = $conn->prepare("INSERT INTO formdictionary (
                                                    fieldname,
                                                    fielddisplayname,
                                                    fielddatatype,
                                                    controltype,
                                                    fieldlength,
                                                    logicaltablename,
                                                    appid
                                                )         
                                                VALUES( 
                                                    :fieldname,
                                                    :fielddisplayname,
                                                    :fielddatatype,
                                                    :controltype,
                                                    :fieldlength,
                                                    :logicaltablename,
                                                    :appid
                                                )");
                
                    $stmt->bindParam(':fieldname',$fieldname,PDO::PARAM_STR,50);
                    $stmt->bindParam(':fielddisplayname',$fielddisplayname,PDO::PARAM_STR,50);
                    $stmt->bindParam(':fielddatatype',$fielddatatype,PDO::PARAM_STR,50);
                    $stmt->bindParam(':controltype',$controltype,PDO::PARAM_STR,50);
                    $stmt->bindParam(':fieldlength',$fieldlength,PDO::PARAM_INT);
                    $stmt->bindParam(':logicaltablename',$tablename,PDO::PARAM_STR,50);
                    $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);                        
                    $stmt->execute();
                    
                
                $app_list = array("status" => "201") + array("results" => "Created");
                    
                
            } catch(PDOException $e){
                
                //echo "Something went wrong...";
                $app_list = $stmt->errorInfo();
                $app_list = array("status" => "400") + array("results" => $e->getMessage());    
            }
        
            return $app_list;

    }
    
    
    
    

    
    /*******************************************************************************
    * Methods for inserting new table into the database based on appid, tablename
    ********************************************************************************/
    function DeleteTable($appid,$tablename) {
            include "mysqlconnect.php";

            
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $stmt = $conn->prepare("DELETE FROM formdictionary WHERE
                                                   logicaltablename = :logicaltablename
                                                   AND
                                                   appid = :appid");
                
                    $stmt->bindParam(':logicaltablename',$tablename,PDO::PARAM_STR,50);
                    $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
                    $stmt->execute();
                    $app_list = array("status" => "200") + array("results" => "OK");    
                
                
            } catch(PDOException $e){
                
                
                //echo "Something went wrong...";
                $app_list = $stmt->errorInfo();
                $app_list = array("status" => "400") + array("results" => $e->getMessage());    
            }
        
            return $app_list;

    }





    function DeleteField($fieldid) {
            include "mysqlconnect.php";

            
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $stmt = $conn->prepare("DELETE FROM formdictionary WHERE
                                                   fieldid = :fieldid");
                
                    $stmt->bindParam(':fieldid',$fieldid,PDO::PARAM_INT);
                    $stmt->execute();
                    $app_list = array("status" => "200") + array("results" => "OK");    
                
                
            } catch(PDOException $e){
                
                
                //echo "Something went wrong...";
                $app_list = $stmt->errorInfo();
                $app_list = array("status" => "400") + array("results" => $e->getMessage());    
            }
        
            return $app_list;

    }
    


    
    /*******************************************************************************
    * Methods for inserting new application into the database
    ********************************************************************************/
    function InsertNewApp($appname,$appversion,$appdescription,$appicon) {
            include "mysqlconnect.php";

            $image_link = "http://".$_SERVER['HTTP_HOST']."/".substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],"application.php"))."/".$appicon;
            
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $stmt = $conn->prepare("INSERT INTO application (
                                                    appname,
                                                    version,
                                                    enabled,
                                                    description,
                                                    imagepath
                                                )         
                                                VALUES( 
                                                    :appname,
                                                    :version,
                                                    1,
                                                    :description,
                                                    :imagepath
                                                )");
                
                    $stmt->bindParam(':appname',$appname,PDO::PARAM_STR,50);
                    $stmt->bindParam(':version',$appversion,PDO::PARAM_STR,50);
                    $stmt->bindParam(':description',$appdescription,PDO::PARAM_STR,50);
                    $stmt->bindParam(':imagepath',$appicon,PDO::PARAM_STR,255);
                    $stmt->execute();
                    $app_list = array("status" => "201") + array("results" => "Created");  
                
                
            } catch(PDOException $e){
                
                
                //echo "Something went wrong...";
                $app_list = $stmt->errorInfo();
                $app_list = array("status" => "400") + array("results" => $e->getMessage());    
            }
        
            return $app_list;

    }  



    
    /*******************************************************************************
    * Methods for updating the application's status ENABLED/DISABLED
    ********************************************************************************/
    function UpdateAppStatus($appid,$stat) {
            include "mysqlconnect.php";

            
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $stmt = $conn->prepare("UPDATE application SET
                                                   enabled = :stat
                                                   WHERE
                                                   appid = :appid");
                
                    $stmt->bindParam(':stat',$stat,PDO::PARAM_BOOL);
                    $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
                    $stmt->execute();
                    $app_list = array("status" => "200") + array("results" => "OK");    
                
                
            } catch(PDOException $e){
                
                
                //echo "Something went wrong...";
                $app_list = $stmt->errorInfo();
                $app_list = array("status" => "400") + array("results" => $e->getMessage());    
            }
        
            return $app_list;

    }


    

    /*
        Delete app
    */
    function DeleteApp($appid) {
        include "mysqlconnect.php";

        $commit = true;    
        $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
        try {

            $conn->beginTransaction();

            $stmt = $conn->prepare("DELETE FROM formdictionary WHERE
                                               appid = :appid");

            $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
            $stmt->execute();


            $stmt = $conn->prepare("DELETE FROM application WHERE
                                               appid = :appid");

            $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
            $stmt->execute();
            $app_list = array("status" => "200") + array("results" => "OK");    


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



    
    /*******************************************************************************
    * Methods for updating application into the database
    ********************************************************************************/
    function UpdateApp($appid,$appname,$appversion,$appdescription,$appicon)
    {
            include "mysqlconnect.php";

            $image_link = "https://".$_SERVER['HTTP_HOST']."/".substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],"application.php"))."/".$appicon;
            
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $stmt = $conn->prepare("UPDATE application SET 
                                                    appname = :appname,
                                                    version = :version,
                                                    description = :description,
                                                    imagepath = :imagepath
                                                WHERE appid = :appid");
                
                    $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
                    $stmt->bindParam(':appname',$appname,PDO::PARAM_STR,50);
                    $stmt->bindParam(':version',$appversion,PDO::PARAM_STR,50);
                    $stmt->bindParam(':description',$appdescription,PDO::PARAM_STR,50);
                    $stmt->bindParam(':imagepath',$appicon,PDO::PARAM_STR,255);
                    $stmt->execute();
                    $app_list = array("status" => "200") + array("results" => "OK");  
                
                
            } catch(PDOException $e){
                
                
                //echo "Something went wrong...";
                $app_list = $stmt->errorInfo();
                $app_list = array("status" => "400") + array("results" => $e->getMessage());    
            }
        
            return $app_list;

    }  




    /*************************************************************************************
    * This function returns all devices per appid.
        {"status":"200","results":[{"idno":"1","fieldid":"1","referencevalue":"New"},
        {"idno":"2","fieldid":"1","referencevalue":"Random"}]}
    
    **************************************************************************************/
    function GetDevices($appid)
    {
      include "mysqlconnect.php";
    
        $stmt = $conn->prepare("SELECT deviceid,description,appid,apikey,enabled FROM devices WHERE appid = :appid");
        
        $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
        
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




    
    /*************************************************************************************
    * This function returns all devices per appid and apikey
        {"status":"200","results":[{"idno":"1","fieldid":"1","referencevalue":"New"},
        {"idno":"2","fieldid":"1","referencevalue":"Random"}]}
    
    **************************************************************************************/
    function GetDevicesByAPIKey($appid, $apikey)
    {
      include "mysqlconnect.php";
    
        $stmt = $conn->prepare("SELECT deviceid,description,appid,apikey,enabled FROM devices WHERE appid = :appid AND apikey = :apikey");
        
        $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);
        $stmt->bindParam(':apikey',$apikey,PDO::PARAM_STR,255);
        
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






    /*******************************************************************************
    * Methods for inserting new DEVICE
    ********************************************************************************/
    function InsertNewDevice($appid,$description,$apikey) {
            include "mysqlconnect.php";
            
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $stmt = $conn->prepare("INSERT INTO devices (
                                                    description,
                                                    apikey,
                                                    enabled,
                                                    appid
                                                )         
                                                VALUES( 
                                                    :description,
                                                    :apikey,
                                                    1,
                                                    :appid
                                                )");
                
                    $stmt->bindParam(':description',$description,PDO::PARAM_STR,50);
                    $stmt->bindParam(':apikey',$apikey,PDO::PARAM_STR,255);
                    $stmt->bindParam(':appid',$appid,PDO::PARAM_INT);                        
                    $stmt->execute();
                    
                $app_list = array("status" => "201") + array("results" => "Created");  
                    
                
            } catch(PDOException $e){
                
                //echo "Something went wrong...";
                $app_list = $stmt->errorInfo();
                $app_list = array("status" => "400") + array("results" => $e->getMessage());    
            }
        
            return $app_list;

    }


    
    /*******************************************************************************
    * Methods for DELETING new DEVICE
    ********************************************************************************/
    function DeleteDevice($deviceid) {
            include "mysqlconnect.php";

            
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $stmt = $conn->prepare("DELETE FROM devices WHERE
                                                   deviceid = :deviceid");
                
                    $stmt->bindParam(':deviceid',$deviceid,PDO::PARAM_INT);
                    $stmt->execute();
                    $app_list = array("status" => "200") + array("results" => "OK");    
                
                
            } catch(PDOException $e){
                
                
                //echo "Something went wrong...";
                $app_list = $stmt->errorInfo();
                $app_list = array("status" => "400") + array("results" => $e->getMessage());    
            }
        
            return $app_list;

    }




    
    /*******************************************************************************
    * Methods for updating the device's status ENABLED/DISABLED
    ********************************************************************************/
    function UpdateDeviceStatus($deviceid,$stat) {
            include "mysqlconnect.php";

            
            $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
            try {
                
                $stmt = $conn->prepare("UPDATE devices SET
                                                   enabled = :stat
                                                   WHERE
                                                   deviceid = :deviceid");
                
                    $stmt->bindParam(':stat',$stat,PDO::PARAM_BOOL);
                    $stmt->bindParam(':deviceid',$deviceid,PDO::PARAM_INT);
                    $stmt->execute();
                    $app_list = array("status" => "200") + array("results" => "OK");    
                
                
            } catch(PDOException $e){
                
                
                //echo "Something went wrong...";
                $app_list = $stmt->errorInfo();
                $app_list = array("status" => "400") + array("results" => $e->getMessage());    
            }
        
            return $app_list;

    }




/*************************************************************************************
* This function returns all user's shared items
    [{"status":"200","results":[{"idno":"1","uname":"1","pw":"New","sharedurl":"","description":"",
            "id_shared_with":"","email":"","firstname":"","lastname":""},
    {"status":"200","results":[{"idno":"1","uname":"1","pw":"New","sharedurl":"","description":"",
            "id_shared_with":"","email":"","firstname":"","lastname":""}]}]
**************************************************************************************/
function GetMySharedItems($userid) {
    include "mysqlconnect.php";
    
        $isBothOK = true;
    
        $stmt = $conn->prepare("select si.idno, si.title, si.uname, si.pw, si.sharedurl, si.description
                                    from shared_item si
                                where si.id_userid = :userid order by idno desc ");
        
        $stmt->bindParam(':userid',$userid,PDO::PARAM_INT);
        
        if($stmt->execute()) {
            $shared_list1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            //echo "Something went wrong...";
            $shared_list1 = $stmt->errorInfo();
            $isBothOK = false;
        }
    
    
        $stmt2 = $conn->prepare("select si.idno, si.title, si.uname, si.pw, si.sharedurl, si.description 
                                  from shared_item si 
                                  inner join shared_with sw on si.idno = sw.id_shared_item
                                where sw.id_shared_with = :userid order by idno desc ");
        
        $stmt2->bindParam(':userid',$userid,PDO::PARAM_INT);
        
        if($stmt2->execute()) {
            $shared_list2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        } else {
            //echo "Something went wrong...";
            $shared_list2 = $stmt2->errorInfo();
            $isBothOK = false;
        }
    
    
        if($isBothOK) {
            $shared_list = array_merge($shared_list1,$shared_list2);
            $shared_list = array("status" => "200") + array("results" => $shared_list);    
        } else {
            $shared_list = $stmt->errorInfo();
            $shared_list = array("status" => "400") + array("results" => $shared_list1." ".$shared_list2);
        }
        
      
      return $shared_list;
}



function GetMySharedItemUsers($userid) {
        include "mysqlconnect.php";
    
        /*$stmt = $conn->prepare("select si.idno,
                                    si.id_userid,-
                                    sw.id_shared_with,
                                    ui.firstname,-
                                    ui.lastname,-
                                    ui.email-
                                from shared_item si
                                inner join shared_with sw on si.idno = sw.id_shared_item
                                inner join userinfo ui on ui.userid = id_shared_with
                                where si.id_userid = :userid ");*/
    
        $stmt = $conn->prepare("select uc.userid,
                                    uc.useridconnection,
                                    sw.id_shared_with,
                                    ui.firstname,
                                    ui.lastname,
                                    ui.email,
                                    id_shared_item,
                                    u.imgurl
                                 from
                                userconnection uc 
                                inner join userinfo ui on uc.useridconnection = ui.userid
                                inner join user u on u.userid = ui.userid
                                inner join shared_with sw on uc.useridconnection = sw.id_shared_with
                                where uc.userid = :userid ");
    
        $stmt->bindParam(':userid',$userid,PDO::PARAM_INT);
        
          if($stmt->execute()) {
            $shared_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $shared_list = array("status" => "200") + array("results" => $shared_list);    
          } else {
        	  //echo "Something went wrong...";
        	  $shared_list = $stmt->errorInfo();
              $shared_list = array("status" => "400") + array("results" => $shared_list);
          }
    
      return $shared_list;
}


function GetMySharedItemUsersPerItem($userid,$sharedId) {
        include "mysqlconnect.php";
    
        $stmt = $conn->prepare("select uc.userid,
                                    uc.useridconnection,
                                    ui.firstname,
                                    ui.lastname,
                                    ui.email,
                                    id_shared_item
                                 from
                                userconnection uc 
                                inner join userinfo ui on uc.useridconnection = ui.userid
                                inner join shared_with sw on uc.useridconnection = sw.id_shared_with
                                where uc.userid = :userid and sw.id_shared_item = :sharedid ");
        
        $stmt->bindParam(':userid',$userid,PDO::PARAM_INT);
        $stmt->bindParam(':sharedid',$sharedId,PDO::PARAM_INT);
        
          if($stmt->execute()) {
            $shared_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $shared_list = array("status" => "200") + array("results" => $shared_list);    
          } else {
        	  //echo "Something went wrong...";
        	  $shared_list = $stmt->errorInfo();
              $shared_list = array("status" => "400") + array("results" => $shared_list);
          }
    
      
      return $shared_list;

}


function GetFriendsNotSharedWith($useid, $shareditemid) {
        include "mysqlconnect.php";
    
        $stmt = $conn->prepare("select 
                uc.useridconnection,
                ui.firstname, 
                ui.lastname, 
                ui.email 
              from userconnection uc inner join userinfo ui on uc.useridconnection = ui.userid
              where uc.userid = :userid and ui.userid not in 
              (select sw.id_shared_with from shared_with sw where sw.id_shared_item = :shareditemid) ");
        
        $stmt->bindParam(':userid',$useid,PDO::PARAM_INT);
        $stmt->bindParam(':shareditemid',$shareditemid,PDO::PARAM_INT);
        
          if($stmt->execute()) {
            $shared_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $shared_list = array("status" => "200") + array("results" => $shared_list);    
          } else {
            //echo "Something went wrong...";
            $shared_list = $stmt->errorInfo();
              $shared_list = array("status" => "400") + array("results" => $shared_list);
          }
    
      
      return $shared_list;
}




function GetAllFriends($userid) {
        include "mysqlconnect.php";
    
        $stmt = $conn->prepare("select ui.userid, ui.firstname, ui.lastname, ui.email, u.imgurl
                                    from user u
                                    inner join userinfo ui on u.userid = ui.userid
                                    inner join userconnection uc on ui.userid = uc.useridconnection
                                    where uc.userid = :userid ");
        
        $stmt->bindParam(':userid',$userid,PDO::PARAM_INT);
        
        
          if($stmt->execute()) {
            $shared_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $shared_list = array("status" => "200") + array("results" => $shared_list);    
          } else {
            //echo "Something went wrong...";
            $shared_list = $stmt->errorInfo();
              $shared_list = array("status" => "400") + array("results" => $shared_list);
          }
    
      
      return $shared_list;
}


function GetFriendsAndSharedStatus($useid, $shareditemid) {
        include "mysqlconnect.php";
    
        $stmt = $conn->prepare("select ui.userid, ui.firstname, ui.lastname, ui.email,
                                    (select count(*) from shared_with sw where sw.id_shared_with = ui.userid and id_shared_item = :shareditemid) as sharestatus
                                    from userinfo ui
                                    inner join userconnection uc on ui.userid = uc.useridconnection
                                    where uc.userid = :userid ");
        
        $stmt->bindParam(':userid',$useid,PDO::PARAM_INT);
        $stmt->bindParam(':shareditemid',$shareditemid,PDO::PARAM_INT);

        
          if($stmt->execute()) {
            $shared_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $shared_list = array("status" => "200") + array("results" => $shared_list);    
          } else {
            //echo "Something went wrong...";
            $shared_list = $stmt->errorInfo();
              $shared_list = array("status" => "400") + array("results" => $shared_list);
          }
    
      
      return $shared_list;
}



function DeleteUserFromShare($useid,$shareditemid) {
        include "mysqlconnect.php";
    
        $stmt = $conn->prepare("delete from shared_with where id_shared_with = :userid and id_shared_item = :shareditemid ");
        
        $stmt->bindParam(':userid',$useid,PDO::PARAM_INT);
        $stmt->bindParam(':shareditemid',$shareditemid,PDO::PARAM_INT);

        
          if($stmt->execute()) {
            $shared_list = array("status" => "200") + array("results" => "Data deleted...");    
          } else {
            //echo "Something went wrong...";
            $shared_list = $stmt->errorInfo();
              $shared_list = array("status" => "400") + array("results" => $shared_list);
          }
    
      
      return $shared_list;
}



function InsertUserToShare($useid, $shareditemid) {
        include "mysqlconnect.php";
    
        $stmt = $conn->prepare("insert into shared_with (id_shared_item, id_shared_with)  values (:shareditemid, :userid) ");
        
        $stmt->bindParam(':userid',$useid,PDO::PARAM_INT);
        $stmt->bindParam(':shareditemid',$shareditemid,PDO::PARAM_INT);
        
          if($stmt->execute()) {
            $shared_list = array("status" => "200") + array("results" => "Data inserted...");    
          } else {
            //echo "Something went wrong...";
            $shared_list = $stmt->errorInfo();
              $shared_list = array("status" => "400") + array("results" => $shared_list);
          }
    
      
      return $shared_list;
}



function AddNewSharedItems($useid, $title, $uname, $pw, $sharedurl, $description) {
    include "mysqlconnect.php";
    
        $stmt = $conn->prepare("insert into shared_item (title, uname, pw, sharedurl, description,id_userid)  values (
                    :title,
                    :uname,
                    :pw,
                    :sharedurl,
                    :description,
                    :userid) ");
        
        $stmt->bindParam(':title',$title,PDO::PARAM_STR,100);
        $stmt->bindParam(':uname',$uname,PDO::PARAM_STR,150);
        $stmt->bindParam(':pw',$pw,PDO::PARAM_STR,45);
        $stmt->bindParam(':sharedurl',$sharedurl,PDO::PARAM_STR,255);
        $stmt->bindParam(':description',$description,PDO::PARAM_STR,1000);
        $stmt->bindParam(':userid',$useid,PDO::PARAM_INT);
        
        
          if($stmt->execute()) {
            $shared_list = array("status" => "200") + array("results" => "Data inserted...");    
          } else {
            //echo "Something went wrong...";
            $shared_list = $stmt->errorInfo();
              $shared_list = array("status" => "400") + array("results" => $shared_list);
          }
    
      
      return $shared_list;
}




function DeleteSharedItems($idno) {
    include "mysqlconnect.php";    
    
        $commit = true;    
        $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
        try {

            $conn->beginTransaction();

            $stmt = $conn->prepare("delete from shared_with where id_shared_item = :idno ");
            $stmt->bindParam(':idno',$idno,PDO::PARAM_INT);
            $stmt->execute();
            
            $stmt2 = $conn->prepare("delete from shared_item where idno = :idno ");
            $stmt2->bindParam(':idno',$idno,PDO::PARAM_INT);
            $stmt2->execute();
            
            
            $app_list = array("status" => "200") + array("results" => "Share item deleted.");    


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



function UpdateSharedItems($idno,$title,$uname,$pw,$sharedurl,$description) {
    include "mysqlconnect.php";    
    
        $commit = true;    
        $app_list = array("status" => "200") + array("results" => "Nothing Happened");    
        
        try {

            $conn->beginTransaction();

            $stmt = $conn->prepare("update shared_item 
                                        set title = :title,
                                            uname = :uname,
                                            pw = :pw,
                                            sharedurl = :sharedurl,
                                            description = :description
                                        where idno = :idno ");
            
            $stmt->bindParam(':idno',$idno,PDO::PARAM_INT);
            $stmt->bindParam(':title',$title,PDO::PARAM_STR);
            $stmt->bindParam(':uname',$uname,PDO::PARAM_STR);
            $stmt->bindParam(':pw',$pw,PDO::PARAM_STR);
            $stmt->bindParam(':sharedurl',$sharedurl,PDO::PARAM_STR);
            $stmt->bindParam(':description',$description,PDO::PARAM_STR);
            $stmt->execute();
            
            
            $app_list = array("status" => "200") + array("results" => "Share item updated.");    


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







    /* These are functions available in this api set. */
    $possible_url = array("app_by_id","app_list","app_form","form_controls_and_values","form_controls","all_form_values","all_table_records",
                          "filtered_table_records","field_choices","put_field_values","all_tables","put_new_table","delete_table","delete_field",
                          "put_new_app","update_app_status","delete_app","put_updated_app","put_new_field","devices","put_devices","delete_device",
                          "device_status","devices_by_key","my_shared_items","my_shared_items_users","friends_not_shared_with",
                          "my_shared_items_users_byid","friends_and_shared_status","delete_friend_share","share_item_to_friend","new_shared_items",
                          "delete_shared_items","all_friends","update_shared_items");

	$value = array("status" => "400") + array("results" => "An error has occurred");    

    /***********************************************************************
    * MAIN BODY. Program entry point.
    ***********************************************************************/
    if (isset($_POST["action"]) && in_array($_POST["action"], $possible_url))
    {
      switch ($_POST["action"])
        {
          case "app_list":
            //application.php?action=app_list
            if(authenticateByAPI($_POST['action'], $_POST['smsg'],$_POST['deviceid']) == true)
                $value = get_app_list();
            else 
                $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            break;
          case "all_tables":
            //application.php?action=all_tables&appid=1
          if (isset($_POST["appid"])) {
            if(authenticateByAPI($_POST['action'].$_POST["appid"], $_POST['smsg'],$_POST['deviceid']) == true)
                $value = get_all_tables($_POST["appid"]);
            else 
                $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
          } else 
                $value = array("status" => "400") + array("results" => "Missing appid");
            break;
          case "app_by_id":
            //application.php?action=app_by_id&id=1
            if (isset($_POST["id"])) {
                if(authenticateByAPI($_POST['action'].$_POST["id"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = get_app_by_id($_POST["id"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");    
                
            } else
              $value = array("status" => "400") + array("results" => "Missing argument");
            break;
          case "app_form":
            //application.php?action=app_form&appid=1
            if (isset($_POST["appid"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = get_app_form($_POST["appid"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else
              $value = array("status" => "400") + array("results" => "Missing appid");
            break;
          case "form_controls_and_values":
            //application.php?action=form_controls_and_values&appid=1&recid=2
            if (isset($_POST["appid"]) && isset($_POST["recid"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["recid"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = get_form_controls_and_values($_POST["appid"],$_POST["recid"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else
                $value = array("status" => "400") + array("results" => "Missing appid or recid");
            break;
          case "form_controls":
            //application.php?action=form_controls&appid=1&tablename=form252
            if (isset($_POST["appid"]) && isset($_POST["tablename"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["tablename"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = get_form_controls($_POST["appid"],$_POST["tablename"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
                
                if($_POST["tablename"] == "")
                    $value = array("status" => "200") + array("results" => "");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid or tablename");
            break;
          case "all_form_values":
            //application.php?action=all_form_values&appid=1&tablename=form252
            if (isset($_POST["appid"]) && isset($_POST["tablename"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["tablename"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = get_all_form_values($_POST["appid"],$_POST["tablename"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid or recid");
            break;
          case "all_table_records":
            //application.php?action=all_table_records&appid=1&tablename=form252
            if (isset($_POST["appid"]) && isset($_POST["tablename"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["tablename"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = get_all_table_records($_POST["appid"],$_POST["tablename"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid or recid");
            break;
          case "filtered_table_records":
            //application.php?action=filtered_table_records&appid=1&tablename=form252&filter=inspectiontype=''New''
            if (isset($_POST["appid"]) && isset($_POST["tablename"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["tablename"].$_POST["filter"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = get_filtered_table_records($_POST["appid"],$_POST["tablename"],$_POST["filter"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else
                $value = array("status" => "400") + array("results" => "Missing appid or recid");
            break;
          case "field_choices":
            //application.php?action=field_choices&appid=1&tablename=form252
            if (isset($_POST["appid"]) && isset($_POST["tablename"])) {
                //if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["tablename"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = get_field_choices($_POST["appid"],$_POST["tablename"]);
                //else 
                //    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else
                $value = array("status" => "400") + array("results" => "Missing appid or recid");
            break;
          case "put_field_values":
            //application.php?action=put_field_values&appid=1&inputvalues={"records":[]}
            if (isset($_POST["appid"]) && isset($_POST["inputvalues"])) {
                //$value = authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["inputvalues"], $_POST['smsg'],$_POST['deviceid']);
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["inputvalues"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = InsertRecord($_POST["appid"],$_POST["inputvalues"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else
                $value = array("status" => "400") + array("results" => "Missing appid or recid");
            break;
          case "put_new_table":
            //application.php?action=put_new_table&appid=1&tablename=form252&fields={}
            if (isset($_POST["appid"]) && isset($_POST["tablename"]) && isset($_POST["fields"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["tablename"].$_POST["physicaltable"].$_POST["fields"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = InsertNewTable($_POST["appid"],$_POST["tablename"],$_POST["physicaltable"],$_POST["fields"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid, tablename or fields");
            break;
          case "put_new_field":
            //application.php?action=put_new_table&appid=1&tablename=form252&fields={}
            if (isset($_POST["appid"]) && isset($_POST["tablename"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["tablename"].$_POST["fieldname"].$_POST["fielddisplayname"].$_POST["fielddatatype"].$_POST["controltype"].$_POST["fieldlength"], $_POST['smsg'],$_POST['deviceid']) == true)
                    $value = InsertNewField($_POST["appid"],$_POST["tablename"],$_POST["fieldname"],$_POST["fielddisplayname"],$_POST["fielddatatype"],$_POST["controltype"],$_POST["fieldlength"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid, tablename or fields");
            break;
          case "put_new_app":
            //application.php?action=put_new_app&appname=form252&appversion=1.0.0&appdescription=&appicon=
            if (isset($_POST["appname"]) && isset($_POST["appversion"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appname"].$_POST["appversion"].$_POST["appdescription"].$_POST["appicon"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = InsertNewApp($_POST["appname"],$_POST["appversion"],$_POST["appdescription"],$_POST["appicon"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else
                $value = array("status" => "400") + array("results" => "Missing appname");
            break;
          case "delete_table":
            //application.php?action=delete_table&appid=1&tablename=form252
            if (isset($_POST["appid"]) && isset($_POST["tablename"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["tablename"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = DeleteTable($_POST["appid"],$_POST["tablename"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else
                $value = array("status" => "400") + array("results" => "Missing appid or tablename");
            break;
          case "delete_field":
            //application.php?action=delete_field&fieldid=1
            if (isset($_POST["fieldid"])) {
                if(authenticateByAPI($_POST['action'].$_POST["fieldid"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = DeleteField($_POST["fieldid"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing fieldid");
            break;
          case "delete_app":
            //application.php?action=delete_field&fieldid=1
            if (isset($_POST["appid"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = DeleteApp($_POST["appid"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid");
            break;
          case "update_app_status":
            //application.php?action=delete_field&fieldid=1
            if (isset($_POST["appid"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["stat"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = UpdateAppStatus($_POST["appid"],$_POST["stat"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid");
            break;
          case "put_updated_app":
            if (isset($_POST["appid"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["appname"].$_POST["appversion"].$_POST["appdescription"].$_POST["appicon"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = UpdateApp($_POST["appid"],$_POST["appname"],$_POST["appversion"],$_POST["appdescription"],$_POST["appicon"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid");
            break;
          case "devices":
            if (isset($_POST["appid"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = GetDevices($_POST["appid"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid");
            break;
          case "devices_by_key":
            if (isset($_POST["appid"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["apikey"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = GetDevicesByAPIKey($_POST["appid"],$_POST["apikey"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid");
            break;
          case "put_devices":
            if (isset($_POST["appid"])) {
                if(authenticateByAPI($_POST['action'].$_POST["appid"].$_POST["description"].$_POST["apikey"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = InsertNewDevice($_POST["appid"],$_POST["description"],$_POST["apikey"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid");
            break;
          case "delete_device":
            if (isset($_POST["did"])) {
                if(authenticateByAPI($_POST['action'].$_POST["did"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = DeleteDevice($_POST["did"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing appid");
            break;
          case "device_status":
            if (isset($_POST["did"])) {
                if(authenticateByAPI($_POST['action'].$_POST["did"].$_POST["stat"],$_POST['smsg'],$_POST['deviceid']) == true)
                    $value = UpdateDeviceStatus($_POST["did"],$_POST["stat"]);
                else 
                    $value = array("status" => "400") + array("results" => "Invalid API Key. You are not authorized to use this resource");
            } else 
                $value = array("status" => "400") + array("results" => "Missing deviceid");
            break;
          case "my_shared_items":
            if (isset($_POST["userid"])) {
                $value = GetMySharedItems($_POST["userid"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;
          case "new_shared_items":
            if (isset($_POST["userid"])) {
                $value = AddNewSharedItems($_POST["userid"], $_POST["title"], $_POST["uname"], $_POST["pw"], $_POST["sharedurl"], $_POST["description"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;
          case "delete_shared_items":
            if (isset($_POST["idno"])) {
                $value = DeleteSharedItems($_POST["idno"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;
          case "update_shared_items":
            if (isset($_POST["idno"])) {
                $value = UpdateSharedItems($_POST["idno"],$_POST["title"],$_POST["uname"],$_POST["pw"],$_POST["sharedurl"],$_POST["description"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing ID");
            break;
          case "my_shared_items_users":
            if (isset($_POST["userid"])) {
                $value = GetMySharedItemUsers($_POST["userid"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;
          case "my_shared_items_users_byid":
            if (isset($_POST["userid"])) {
                $value = GetMySharedItemUsersPerItem($_POST["userid"],$_POST['sharedid']);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;
          case "friends_not_shared_with":
            if (isset($_POST["userid"])) {
                $value = GetFriendsNotSharedWith($_POST["userid"],$_POST["shareditemid"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;
          case "all_friends":
            if (isset($_POST["userid"])) {
                $value = GetAllFriends($_POST["userid"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;      
         case "friends_and_shared_status":
            if (isset($_POST["userid"])) {
                $value = GetFriendsAndSharedStatus($_POST["userid"],$_POST["shareditemid"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;
          case "delete_friend_share":
            if (isset($_POST["userid"])) {
                $value = DeleteUserFromShare($_POST["userid"],$_POST["shareditemid"]);
            } else 
                $value = array("status" => "400") + array("results" => "Missing userid");
            break;
          case "share_item_to_friend":
            if (isset($_POST["userid"])) {
                $value = InsertUserToShare($_POST["userid"],$_POST["shareditemid"]);
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



