<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['connected_id'])) {
    header("Location: login.php");
    echo json_encode(['404 erreur' => 'Vous devez etre connecter pour aimer un commentaire']);
    exit();
}

// Vérifier si la méthode de requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Vérifier si l'identifiant du poste est fourni
    if (!isset($_POST['post_id'])) {
        echo json_encode(['error' => 'ID de poste non fourni']);
        exit();
    }

    $user_id = $_SESSION['connected_id'];
    $post_id = $_POST['post_id'];

    // Connexion à la base de données
    $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
    if ($mysqli->connect_errno) {
        echo json_encode(['error' => 'Erreur de connexion à la base de données']);
        exit();
    }

    // Vérifier si l'utilisateur a déjà aimé ce commentaire
    $check_like_sql = "SELECT * FROM likes WHERE post_id = $post_id AND user_id = $user_id";
    $check_like_result = $mysqli->query($check_like_sql);

    // Si l'utilisateur n'a pas déjà aimé ce commentaire, ajouter le like
    if ($check_like_result->num_rows == 0) {
        $sql = "INSERT INTO likes (post_id , user_id) VALUES ($post_id , $user_id)";
        $ok = $mysqli->query($sql);
        if (!$ok) {
            echo json_encode(['error' => 'Erreur lors de l\'ajout du like']);
            exit();
        }
    } 

    // Rediriger l'utilisateur vers la page d'actualités après avoir aimé le commentaire
    header("Location: news.php");
    exit();

} else {
    // Affichage du formulaire de commentaire si la méthode n'est pas POST
    // Vous pouvez ajouter ici le formulaire de commentaire HTML
    // Assurez-vous de modifier le contenu du formulaire en fonction de vos besoins
    // Par exemple :
    ?>
    <form action="comment.php" method="post">
        <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
        <textarea name="comment_content"></textarea>
        <input type="submit" value="Commenter">
    </form>
    <?php
}
?>
