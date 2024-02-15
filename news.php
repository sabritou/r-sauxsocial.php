<?php
session_start();
if (!$_SESSION['status']=="Active")
{
    header("Location: login.php");
}
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Actualités</title>
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet"  href="style.css"/>
    </head>
    <body>
        <header>
            <a href='admin.php'><img src="admin.png" alt="Logo de notre réseau social"/></a>
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
                    <p>Sur cette page vous trouverez les derniers messages de
                        tous les utilisatrices du site.</p>
                </section>
            </aside>
            <main>
                <?php
                // Etape 1: Ouvrir une connexion avec la base de donnée.
                $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
                //verification
                if ($mysqli->connect_errno)
                {
                    echo "<article>";
                    echo("Échec de la connexion : " . $mysqli->connect_error);
                    echo("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                    echo "</article>";
                    exit();
                }
                // Etape 2: Poser une question à la base de donnée et récupérer ses informations
                // cette requete vous est donnée, elle est complexe mais correcte,
                // si vous ne la comprenez pas c'est normal, passez, on y reviendra
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
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                // Vérification
                if ( ! $lesInformations)
                {
                    echo "<article>";
                    echo("Échec de la requete : " . $mysqli->error);
                    echo("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                    exit();
                }
                // Etape 3: Parcourir ces données et les ranger bien comme il faut dans du html
                // NB: à chaque tour du while, la variable post ci dessous reçois les informations du post suivant.
                while ($post = $lesInformations->fetch_assoc()) {
                    ?>
                    <article>
                        <h3>
                            <time><?php echo $post['created'] ?></time>
                        </h3>
                        <address>par <?php echo $post['author_name'] ?></address>
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
                                <small>
                                    <button class="show_replies_button" onclick="toggleReplies(<?php echo $post['id']; ?>)">Montrer les réponses</button>
                                </small>
                            <?php else : ?>
                                <small>
                                    <button class="reply_button" onclick="toggleReplyForm(<?php echo $post['id']; ?>)">Commenter</button>
                                </small>
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