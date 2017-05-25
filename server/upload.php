<?php



function getGUID(){
    if (function_exists('com_create_guid')){
        return com_create_guid();
    }else{
        mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        /*$uuid = chr(123)// "{"
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12)
            .chr(125);// "}"*/
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
        return $uuid;
    }
}

$GUID = getGUID();



$target_dir = "uploads";

if(!file_exists($target_dir))
{
	mkdir($target_dir, 0777, true);
}

$target_dir = $target_dir . "/" . $GUID . "_" . basename($_FILES["file"]["name"]);
//echo $target_dir;
if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_dir)) 
{
    echo json_encode(array("status" => "200") + array("results" => $GUID . "_" . basename($_FILES["file"]["name"])));    
} else {
	echo json_encode(array("status" => "400") + array("results" => "Sorry, there was an error uploading your file."));    
}
?>