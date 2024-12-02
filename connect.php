<?php
$servname = "localhost";
$username = "root";
$password = "";
$dbname = "fantasy sports league management system";
// database connection
$conn = mysqli_connect($servname, $username, $password, $dbname);
if(!$conn){
    echo "Connection failed";
}
?>