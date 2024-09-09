<?php
session_start();

?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mes posts</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
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

        </header>
    
            <?php
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             * La première étape est donc de trouver quel est l'id de l'utilisateur
             * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
             * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
             * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
             */
            $userId =intval($_SESSION['connected_id']);
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            ?>

        
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */                
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";  //selectionne toute les colonnes de la tables users ou(avec accent) = userid
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
               
                ?>
               
   
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages de l'utilisatrice
                 */
                $laQuestionEnSql = "
                    SELECT posts.content, posts.created, users.alias as author_name, 
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$userId' 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 */
                while ($post = $lesInformations->fetch_assoc())
                {

                   
                    ?>                
                    <article>
                        <h3>
                            <time> <?php echo $post['created'] ?></time>
                        </h3>
                        <address>par <?php echo $post['author_name'] ?></address>
                        <div>
                        <p><?php echo $post['content'] ?></p>
                        </div>                                            
                        <footer>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>
