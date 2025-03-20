<?php
session_start();
/*$currentPage= $_SERVER['SCRIPT_NAME'];
echo $currentPage;*/
if ($_SESSION['authentification'] == false) {
    header('Location: landingpage.php');
    exit();
}
