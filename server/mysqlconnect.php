<?php

//Set Error Handler

ini_set('display_errors',1); 
error_reporting(E_ALL);

//SQL CONNECT USING MySQL
/*
$servername = "xolooshcom.ipagemysql.com";
$username = "sharemetoo";
$password = "sharemetoo";
$db = "sharemetoodb";
*/


$servername = "localhost";
$username = "root";
$password = "pa55w0rd";
$db = "sharemetoo";



try 
{
    $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //echo "Connected successfully"; 
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage();
}


?>
