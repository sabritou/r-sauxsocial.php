<?php
session_start();
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Paramètres</title> 
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
                <a href="#">▾ Profil</a>
                <ul>
                    <li><a href="settings.php">Paramètres</a></li>
                    <li><a href="followers.php">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php">Mes abonnements</a></li>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="registration.php">Inscription</a></li>
                </ul>
            </nav>
        </header>
        <div id="wrapper" class='profile'>


            <aside>
                <img src="user.png" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les informations de l'utilisatrice
                        n° <?php echo intval($_SESSION['connected_id']) ?></p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 1: Les paramètres concernent une utilisatrice en particulier
                 * La première étape est donc de trouver quel est l'id de l'utilisatrice
                 * Celui ci est indiqué en parametre GET de la page sous la forme user_id=...
                 * Documentation : https://www.php.net/manual/fr/reserved.variables.get.php
                 * ... mais en résumé c'est une manière de passer des informations à la page en ajoutant des choses dans l'url
                 */
                $userId = intval($_SESSION['connected_id']);

                /**
                 * Etape 2: se connecter à la base de donnée
                 */
                $mysqli = new mysqli("localhost", "root", "root", "socialnetwork");

                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */
                $laQuestionEnSql = "
                    SELECT users.*, 
                    count(DISTINCT posts.id) as totalpost, 
                    count(DISTINCT given.post_id) as totalgiven, 
                    count(DISTINCT recieved.user_id) as totalrecieved 
                    FROM users 
                    LEFT JOIN posts ON posts.user_id=users.id 
                    LEFT JOIN likes as given ON given.user_id=users.id 
                    LEFT JOIN likes as recieved ON recieved.post_id=posts.id 
                    WHERE users.id = '$userId' 
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                if ( ! $lesInformations)
                {
                    echo("Échec de la requete : " . $mysqli->error);
                }
                $user = $lesInformations->fetch_assoc();

                /**
                 * Etape 4: à vous de jouer
                 */
                //@todo: afficher le résultat de la ligne ci dessous, remplacer les valeurs ci-après puis effacer la ligne ci-dessous
            
                ?>                
                <article class='parameters'>
                    <h3>Mes paramètres</h3>
                    <dl>
                    <dt>Pseudo</dt>
                        <dd><?php echo $user['alias'];?></dd>
                        <dt>Email</dt>
                        <dd><?php echo $user['email'];?></dd>
                        <dt>Nombre de message</dt>
                        <dd><?php echo $user['totalpost'];?></dd>
                        <dt>Nombre de "J'aime" donnés </dt>
                        <dd><?php echo $user['totalgiven'];?></dd>
                        <dt>Nombre de "J'aime" reçus</dt>
                        <dd><?php echo $user['totalrecieved'];?></dd>
                    </dl>

                </article>
            </main>
        </div>
    </body>
</html>
