<?php
session_start();
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mes abonnements</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <img src="user.png" alt="Logo de notre réseau social"/>
            <nav id="menu">
                <a href="news.php">Home</a>
                <a href="wall.php">Mur</a>
                <a href="feed.php">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
                <a href="userpedpost.php">Post</a>
            </nav>
            <nav id="user">
                <a href="#">Profil</a>
                <ul>
                    <li><a href="settings.php">Paramètres</a></li>
                    <li><a href="followers.php">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php">Mes abonnements</a></li>
                </ul>
            </nav>
        </header>
        <div id="wrapper">
            <aside>
                <img src="user.png" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez la liste des personnes dont
                        l'utilisatrice
                        n° <?php echo intval($_SESSION['connected_id']) ?>
                        suit les messages
                    </p>

                </section>
            </aside>
            <main class='contacts'>
                <?php
                // Etape 1: récupérer l'id de l'utilisateur
                $userId = intval($_SESSION['connected_id']);
                // Etape 2: se connecter à la base de donnée
                $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
                // Etape 3: récupérer le nom de l'utilisateur
                $laQuestionEnSql = "
                    SELECT users.* 
                    FROM followers 
                    LEFT JOIN users ON users.id=followers.followed_user_id 
                    WHERE followers.following_user_id='$userId'
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);

// Afficher la requête SQL pour le débogage
echo "Requête SQL : $insertQuery";

// Insérer l'abonnement dans la base de données
if ($mysqli->query($insertQuery) === TRUE) {
    echo "Vous vous êtes abonné à cet utilisateur avec succès !";
} else {
    echo "Erreur lors de l'abonnement à cet utilisateur : " . $mysqli->error;
}



// Vérifier si l'utilisateur est déjà abonné à l'utilisateur à suivre
$query = "SELECT * FROM followers WHERE followed_user_id = $userToFollowId AND following_user_id = $userId";
$result = $mysqli->query($query);


if ($result->num_rows > 0) {
    echo "Vous êtes déjà abonné à cet utilisateur.";
    exit;
}

// Insérer l'abonnement dans la base de données
$insertQuery = "INSERT INTO followers (followed_user_id, following_user_id) VALUES ($userToFollowId, '$userId')";



if ($mysqli->query($insertQuery) === TRUE) {
    echo "Vous vous êtes abonné à cet utilisateur avec succès !";
} else {
    echo "Erreur lors de l'abonnement à cet utilisateur : " . $mysqli->error;
}





                // Etape 4: à vous de jouer
                //@todo: faire la boucle while de parcours des abonnés et mettre les bonnes valeurs ci dessous 
                while ($followers = $lesInformations->fetch_assoc())
                {

                    ?>   


                
            
                <article>
                    <img src="user.png" alt="blason"/>
                    <h3> <?php echo $followers['alias']; ?></h3>
                </article>

                <?php } ?>

            </main>
        </div>
    </body>
</html>
