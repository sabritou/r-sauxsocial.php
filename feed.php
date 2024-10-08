<?php
session_start();
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC Abonnements</title>         
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet"  href="style.css"/>
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
            /**
             * Cette page est TRES similaire à wall.php. 
             * Vous avez sensiblement à y faire la meme chose.
             * Il y a un seul point qui change c'est la requete sql.
             */
            /**
             * Etape 1: Le mur concerne un utilisateur en particulier
             */
            $userId = intval($_SESSION['connected_id']);
            ?>
            <?php
            /**
             * Etape 2: se connecter à la base de donnée
             */
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
               
                ?>
              
                <?php
                /**
                 * Etape 3: récupérer tous les messages des abonnements
                 */
                $laQuestionEnSql = "
                SELECT p.content,
                p.created,
                u.alias as author_name,  
                count(l.id) as like_number,  
                GROUP_CONCAT(DISTINCT t.label) AS taglist 
                FROM followers f
                JOIN users u ON u.id=f.followed_user_id
                JOIN posts p ON p.user_id=u.id
                LEFT JOIN posts_tags pt ON p.id = pt.post_id  
                LEFT JOIN tags t       ON pt.tag_id  = t.id 
                LEFT JOIN likes l      ON l.post_id  = p.id 
                WHERE f.following_user_id='$userId' 
                GROUP BY p.id
                ORDER BY p.created DESC   
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }

                /**
                 * Etape 4: @todo Parcourir les messsages et remplir correctement le HTML avec les bonnes valeurs php
                 * A vous de retrouver comment faire la boucle while de parcours...
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
                           
                            #<?php
                            $tags = explode(',', $post['taglist']);
                            foreach ($tags as $tag) {
                            echo "<a href=''>$tag</a>, ";
                            }
                            ?>
                        </footer>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>
