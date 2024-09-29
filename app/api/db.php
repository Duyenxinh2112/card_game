<?php

$host = "localhost";
$usernam = "root";
$password = "";
$dbname = "card_game";

$conn = mysqli_connect($host, $usernam,$password,$dbname);

if(!$conn){
    die("Connect Failed: " . mysqli_connect_error());
}
?>