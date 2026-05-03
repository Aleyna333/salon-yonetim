<?php 
$server = "localhost";
$user = "root";
$password ="";
$database = "mini_adres";
$baglanti = mysqli_connect($server, $user, $password, $database);

if(!$baglanti){
    die("Hata:" . mysqli_connect_error());
}
?>