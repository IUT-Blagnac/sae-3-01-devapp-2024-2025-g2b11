<?php

$user='R2024MYSAE3001';
$pass='LiY926Jeq49gdV';
try{
    $conn = new PDO('mysql:host=localhost:3306;dbname=R2024MYSAE3001;charset=UTF8',$user,$pass);

    }
    catch(PDOException $e){
        echo 'Erreur: '.$e->getMessage()."<br>";
        die();
    }

?>