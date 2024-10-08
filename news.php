<?php
session_start();     // crée une session ou restaure celle trouvée sur le serveur.
if (!$_SESSION['status'] == "Active") {
    header("Location: login.php"); // Redirection vers la page login.php
}
?>



<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Actualités</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <header>
        
        <nav id="menu">
            <a href="news.php" >Home</a>
            <a href="wall.php">Mes posts</a>
            <a href="feed.php">Abonnements</a>
            <a href="tags.php?tag_id=1">Mots-clés</a>
            <a href="userpedpost.php">Post</a>
      

        </nav>


    </header>
    <div id="wrapper">
        <aside>
            <section>
                <h3>Resauc</h3>
                <p>Le résaux social pour tous !</p>
            </section>

            <nav id="user">
            <h>Profil :</h2>
            <ul>
                <li><a href="settings.php">Paramètres</a></li>
                <li><a href="followers.php">Mes suiveurs</a></li>
                <li><a href="subscriptions.php">Mes abonnements</a></li>
            </ul>
        </nav>

        </aside>

      
        <main>




            <?php
            // Etape 1: Ouvrir une connexion avec la base de donnée. / Nom de la bdd, mot de passe ..
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            //verification
            if ($mysqli->connect_errno) {
                echo "<article>";
                echo ("Échec de la connexion : " . $mysqli->connect_error);
                echo ("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                echo "</article>";
                exit();
            }
            // Etape 2: Poser une question à la base de donnée et récupérer ses informations
            // Ici on représente une requette sql le SELECT spécifie les colonnes que vous souhaitez récupérer de la base de données.

            $laQuestionEnSql = "
                    SELECT     
                    posts.id,  
                    posts.content,
                    posts.created,
                    posts.parent_id,
                    users.alias as author_name,
                    count(likes.id) as like_number,
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id
                    LEFT JOIN likes      ON likes.post_id  = posts.id
                    WHERE posts.parent_id IS NULL
                    GROUP BY posts.id
                    ORDER BY posts.created DESC
                    " ;
                    
                    // Au début nous somme dans la table post on sélectionne les collones post.id etccc..

                    // Le AS permet de modifier le nom // Nous faisont une simple jointure de la tables users ou userid = postuserid
                    // LEFT voir image google // LEFT permet de joindre les éléments de la table b à a // RIGHT inverse

                    // Where post .. IS NULL cela permet de trier les éléments et de renvoyer ce qui n'ont pas de post.parent.id (ce qui corresponde pas)
                    // Group by post permet de regrouper tout dans un groupe qui s'appelle post.id
                    // order by permet de classé dans la colone posts.created dans l'ordre décroissant.




            $lesInformations = $mysqli->query($laQuestionEnSql);






            // Vérification
            if (!$lesInformations) {
                echo "<article>";
                echo ("Échec de la requete : " . $mysqli->error);
                echo ("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                exit();
            }
            // Etape 3: Parcourir ces données et les ranger bien comme il faut dans du html
            // NB: à chaque tour du while, la variable post ci dessous reçois les informations du post suivant.

            // Utilisation de boucle while 
            // L'instruction while permet de créer une boucle qui s'exécute tant qu'une condition de test est vérifiée. La condition est évaluée avant d'exécuter l'instruction contenue dans la boucle.
            while ($post = $lesInformations->fetch_assoc()) {
                ?>
                <article id="post-<?php echo $post['id']; ?>">
                    <h3>
                        <time><?php echo $post['created'] ?></time>
                    </h3>
                    <address>
    <form action="subscriptions.php" method="post">
        <input type="hidden" name="user_id" value="<?php echo $post['author_id']; ?>">
        <button type="submit" name="subscribe_button" class="connexion"><?php echo $post['author_name']; ?></button>
    </form>
</address>
                    
                    <div>
                        <p><?php echo $post['content'] ?></p>
                    </div>
                    <footer>
                        <small>
                            <form action="like_comment.php" method="post">
                                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                <button type="submit" name="like" class='connexion'>♥</button> <?php echo $post['like_number']; ?>                                </form>
                        </small>
                        <?php
                        // Récupération du nombre de réponses à ce post
                        $repliesSQL =
                        "SELECT posts.id,posts.content,posts.created,users.alias as author_name  FROM posts JOIN users ON  users.id=posts.user_id WHERE posts.parent_id = " . $post['id'];
                        $repliesResult = $mysqli->query($repliesSQL);
                        $repliesRow = $repliesResult->fetch_all(MYSQLI_ASSOC);
                        if (count ($repliesRow) > 0) :
                        ?>
                            
                     
                        
                        <?php endif; ?>
                        <div class="reply-form-container" id="replyForm_<?php echo $post['id']; ?>">
                    <form method="post" action="reply_comment.php">
                        <input type="hidden" name="parent_id" value="<?php echo $post['id']; ?>">
                        <input type="hidden" name="user_id" value="<?php echo $_SESSION['connected_id']; ?>">
                        <textarea name="reply_content" placeholder="Répondre à ce message..." required></textarea>
                        <br>
                        <button type="submit" name="reply_button">Envoyer</button>
                    </form>
                </div>
                <?php
// Afficher les commentaires
if (!empty($repliesRow)) {
foreach ($repliesRow as $comment) {
    echo "<div class='comment'>";
    echo "<p><strong>" . $comment['author_name'] . "</strong> - " . $comment['created'] . "</p>";
    echo "<p>" . $comment['content'] . "</p>";
    echo "</div>";
}
} else {
echo "<p>Aucun commentaire pour le moment.</p>";
}
// Afficher le formulaire de commentaire
echo "<div class='comment-form'>";
echo "<form method='post' action='process_comment.php'>";
echo "<input type='hidden' name='post_id' value='" . $post['id'] . "'>";
// echo "<input type='text' name='author' placeholder='Votre nom' required><br>";
// echo "<textarea name='content' placeholder='Votre commentaire' required></textarea><br>";
// echo "<button type='submit' name='submit_comment'>Poster</button>";
echo "</form>";
echo "</div>";
?>
                    </footer>
                </article>
            <?php
            } // Fermeture de la boucle while
            ?>