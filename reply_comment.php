<?php
session_start();
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['connected_id'])) {
    // Redirige vers la page de connexion
    header("Location: login.php");
    exit();
}
// Vérifie si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //var_dump($_POST);die;
    // Récupère les données du formulaire
    $post_id = $_POST['parent_id'];
    $comment_content = $_POST['reply_content'];
    $user_id = $_POST['user_id'];
    // Insertion du commentaire dans la base de données
    $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
    if ($mysqli->connect_errno) {
        echo "Erreur de connexion à la base de données : " . $mysqli->connect_error;
        exit();
    }
    // Préparez et exécutez la requête SQL pour insérer le commentaire
    $insert_query = "INSERT INTO posts (user_id, content, created, parent_id) VALUES ($user_id, '$comment_content', NOW(), $post_id)";
    if ($mysqli->query($insert_query) === TRUE) {
        // Redirige vers la page précédente après l'ajout du commentaire
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit();
    } else {
        echo "Erreur lors de l'ajout du commentaire : " . $mysqli->error;
    }
}
?>