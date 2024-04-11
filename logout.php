<?php
{
    session_start();  
    session_destroy();
    $_SESSION = array();
    header("location:login.php");
}
?>

<!-- 
    
session_start();: Cette ligne démarre ou reprend une session existante. Une session est une manière pour un site web de garder des données utilisateur entre différentes pages ou visites.

session_destroy();: Cela détruit la session actuelle, effaçant toutes les données qui y sont stockées.

$_SESSION = array();: Cette ligne réinitialise le tableau associé à la session. Cela assure qu'aucune donnée utilisateur n'est laissée derrière.

header("location:login.php");: Cette ligne redirige l'utilisateur vers la page de connexion (login.php) après avoir détruit sa session. -->