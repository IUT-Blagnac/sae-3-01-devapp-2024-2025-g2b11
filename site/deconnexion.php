<?php
session_start();

if (isset($_SESSION['Suser'])) {
    session_destroy();
    header("Location: accueil.php");
}
?>