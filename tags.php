<?php
session_start();
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Les message par mot-clé</title> 
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

            <?php
           
            $tagId = intval($_GET['tag_id']);



            ?>
            <?php
           
            $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");
            ?>

            <aside>
                <?php
                /**
                 * Etape 3: récupérer le nom du mot-clé
                 */
                $laQuestionEnSql = "SELECT * FROM tags WHERE id= '$tagId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par le label et effacer la ligne ci-dessous
               
                ?>
             
                <?php
                /**
                 * Etape 3: récupérer tous les messages avec un mot clé donné
                 */
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts_tags as filter 
                    JOIN posts ON posts.id=filter.post_id
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE filter.tag_id = '$tagId' 
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