<?php
session_start();

if (!isset($_SESSION['Sadmin']) or $_SESSION["Sadmin"] != true) {
    if(isset($_SESSION['Suser'])){
        header("location: accueil.php");
        exit();
    }
    header("location: FormConnexion.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            text-align: center;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenue sur le Dashboard</h1>
        <p>Vous êtes connecté en tant qu'administrateur.</p>
    </div>
</body>
</html>
<?php

?>