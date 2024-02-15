<?php
require 'connexion.php';
if($connectedId !=0):
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mes abonnés </title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
        <a href='admin.php'><img src="logo.png" alt="Logo de notre réseau social"/> </a> 
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href="wall.php?user_id=<?php echo $connectedId?>">Mur</a>
                <a href="feed.php?user_id=<?php echo $connectedId?>">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href="#">Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=<?php echo $connectedId?>">Paramètres</a></li>
                    <li><a href="followers.php?user_id=<?php echo $connectedId?>">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php?user_id=<?php echo $connectedId?>">Mes abonnements</a></li>
                </ul>

            </nav>
        </header>
        <div id="wrapper">          
            <aside>
                <img src = "user<?php echo $userId ?>.jpg" alt = "Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez la liste des personnes qui
                        suivent les messages de l'utilisatrice
                        n° <?php echo $userId ?></p>

                </section>
            </aside>
            <main class='contacts'>
                
                <?php
                $laQuestionEnSql = "
                    SELECT users.*
                    FROM followers
                    LEFT JOIN users ON users.id=followers.following_user_id
                    WHERE followers.followed_user_id='$userId'
                    GROUP BY users.id
                    ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                
                while ($user = $lesInformations->fetch_assoc()){
                    ?>
                    <article>
                        <img src="user<?php echo $user['id'] ?>.jpg" alt="blason"/>
                        <h3><?php echo $user['alias']?></h3>
                        
                        <a href="wall.php?user_id=<?php echo $user['id'] ?>">Voir son mur</a>                    
                    </article>
                    <?php } ?>
            </main>
        </div>
    </body>
</html>
<?php else : ?>
    <p>Vous n'êtes pas connecté, impossible de charger la page</p>
    <a href="login.php">Se connecter </a> 
    <?php endif; ?>