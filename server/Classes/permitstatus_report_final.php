<?php

// output headers so that the file is downloaded rather than displayed
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename=permit_status.csv');
header('Content-Type: text/csv; charset=utf-8');


// create a file pointer connected to the output stream
$output = fopen('php://output', 'w');

// output the column headings
fputcsv($output, array('NAME','PERMITNUMBER','AGENCY','DIVISION', 'TYPE','FEE','STATUS','EXPIRATIONDATE', 'OWNER','PROJECTNUMBER','PROJECTNAME','CONSTRUCTIONSTART','PERMITSUBMITTEDESTIMATE'));

// fetch the data
include "mysqlconnect.php";

        
        $agency              = $_POST["agency"];
        $type                = $_POST["type"];
        $status              = $_POST["status"];
        $expiration          = $_POST["expiration"];
        $expirationto        = $_POST["expirationto"];
        $constructionstart   = $_POST["constructionstart"];
        $constructionstartto = $_POST["constructionstartto"];
        $division            = $_POST["division"];
        $permitowner         = $_POST["permitowner"];

        $clause = "";
        
        if(strlen($agency)>0)
            $clause = $clause . " permit_agency = :agency and ";
            
        if(strlen($type)>0)
            $clause = $clause . " permit_type = :type and ";
            
        if(strlen($status)>0)
            $clause = $clause . " PermitStatus = :status and ";
            
        if((strlen($expiration)>0) && (strlen($expirationto)>0))
            $clause = $clause . " Permit_Expiration_Date between :expiration and :expirationto and ";
            
        if((strlen($constructionstart)>0) && (strlen($constructionstartto)>0))
            $clause = $clause . " PlannedConstructionStart between :constart and :constartto and ";
        
        if(strlen($division)>0)
            $clause = $clause . " DivisionName = :divname and ";
        
        if(strlen($permitowner)>0)
            $clause = $clause . " permit_owner = :permowner and ";
        
        
        $clause = substr($clause,0,(strlen($clause)-4));
        
        if(strlen($clause) > 0)
            $clause = " where " . $clause;
        
        //$sql = "select permitname,PermitNumber,permit_agency,DivisionName,permit_type,permit_fee,PermitStatus,Permit_Expiration_Date,permit_owner,ProjectNumbers,projectname,PlannedConstructionStart,PermitSubmittedEstimate
        //                        from v_permit_centric_view " . $clause;
        //echo $sql;
        
        $stmt = $conn->prepare("select 
                                    permitname,
                                    PermitNumber,
                                    permit_agency,
                                    DivisionName,
                                    permit_type,
                                    permit_fee,
                                    PermitStatus,
                                    Permit_Expiration_Date,
                                    permit_owner,
                                    ProjectNumbers,
                                    projectname,
                                    PlannedConstructionStart,
                                    PermitSubmittedEstimate
                                from v_permit_centric_view " . $clause);
        
        if(strlen($agency)>0) {
            $stmt->bindParam(':agency',$agency,PDO::PARAM_STR);
        }
            
        if(strlen($type)>0) {
            $stmt->bindParam(':type',$type,PDO::PARAM_STR);
        }
            
        if(strlen($status)>0) {
            $stmt->bindParam(':status',$status,PDO::PARAM_STR);
        }
            
        if((strlen($expiration)>0) && (strlen($expirationto)>0)) {
            $stmt->bindParam(':expiration',$expiration,PDO::PARAM_STR);
            $stmt->bindParam(':expirationto',$expirationto,PDO::PARAM_STR);
        }
            
        if((strlen($constructionstart)>0) && (strlen($constructionstartto)>0)) {
            $stmt->bindParam(':constart',$constructionstart,PDO::PARAM_STR);
            $stmt->bindParam(':constartto',$constructionstartto,PDO::PARAM_STR);
        }
        
        if(strlen($division)>0) {
            $stmt->bindParam(':divname',$division,PDO::PARAM_STR);
        }
        
        if(strlen($permitowner)>0) {
            $stmt->bindParam(':permowner',$permitowner,PDO::PARAM_STR);
        }
        
    if($stmt->execute()) {
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // loop over the rows, outputting them
        foreach($rows as $row) {
            fputcsv($output, $row);
        }  
    } 


?>