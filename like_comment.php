<?php
session_start();


if (!isset($_SESSION['connected_id'])) {
    echo json_encode(['error' => 'Vous devez être connecté pour aimer un commentaire']);
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['post_id'])) {
        echo json_encode(['error' => 'ID de poste non fourni']);
        exit();
    }

    $user_id = $_SESSION['connected_id'];
    $post_id = $_POST['post_id'];

    $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
    if ($mysqli->connect_errno) {
        echo json_encode(['error' => 'Erreur de connexion à la base de données']);
        exit();
    }


    $sql = "INSERT INTO likes (post_id , user_id) VALUES ($post_id , $user_id) ";

    $ok = $mysqli->query($sql);
    if ( ! $ok)
    {

        echo "Impossible d'ajouter le message: " . $mysqli->error;
    } else
    {
        header("Location: news.php");
    }


}
