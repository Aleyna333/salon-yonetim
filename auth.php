<?php
session_start();
if (!isset($_SESSION["giris"])) {
    header("Location: giris.php");
    exit;
}