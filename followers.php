
<?php
session_start();
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mes abonnés </title> 
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
            <aside>
                <section>
                    <h3>Followers</h3>


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
                    LEFT JOIN users ON users.id=followers.following_user_id
                    WHERE followers.followed_user_id='$userId'
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                // Etape 4: à vous de jouer
                //@todo: faire la boucle while de parcours des abonnés et mettre les bonnes valeurs ci dessous 
                while ($followers = $lesInformations->fetch_assoc())
                {

                    ?>   
                
            
                <article>
                    <img src="user.png" alt="blason"/>
                    <h3><?php echo $followers['alias'];?></h3>
                                     
                </article>

                <?php } ?>


            </main>
        </div>
    </body>
</html>
