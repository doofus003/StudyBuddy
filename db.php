<?php
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "demo";

$conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

if(!$conn) {
    echo "error de connection !!!";
}

?>