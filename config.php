<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "oearms";
$con =mysqli_connect($servername,$username,$password,$dbname);
if($con){
    // echo "Connected";
}
else{
    echo "Not connected";
}
?>