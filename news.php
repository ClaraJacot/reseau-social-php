<?php
require 'connexion.php'
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Actualités</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
            <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social"/></a>
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href=<?php if ($connectedId != 0) {echo "wall.php?user_id=" . $connectedId;} else {echo "login.php" ;} ?>>Mur</a>
                <a href="feed.php?user_id=<?php echo $connectedId?>">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href=<?php if ($connectedId !=0 ) { echo "#";} else {echo "login.php";} ?>> Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=<?php echo $connectedId?>">Paramètres</a></li>
                    <li><a href="followers.php?user_id=<?php echo $connectedId?>">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php?user_id=<?php echo $connectedId?>">Mes abonnements</a></li>
                </ul>
            </nav>
        </header>
        <div id="wrapper">
            <aside>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages de
                        tous les utilisatrices du site.</p>
                </section>
            </aside>
            <main>
                <!-- L'article qui suit est un exemple pour la présentation et 
                  @todo: doit etre retiré -->
                              

                
                //verification

                // Etape 2: Poser une question à la base de donnée et récupérer ses informations
                // cette requete vous est donnée, elle est complexe mais correcte, 
                // si vous ne la comprenez pas c'est normal, passez, on y reviendra
                <?php 
                    $enCoursDeTraitement3 = isset($_POST['like']);
                    if($enCoursDeTraitement3) {
                        $liker1 = $connectedId;
                        $likedPost1 = $_POST['postId'];
                        
                        $lInstructionSql3 = "INSERT INTO likes (id, user_id, post_id)
                            VALUES (NULL, $liker1, $likedPost1)";
                            
                    
                    
                        $ok = $mysqli->query($lInstructionSql3);
                        if (! $ok)
                        {
                            echo "Impossible de liker ce post." . $mysqli->error;                 
                        } else 
                        {
                            echo "Post liké";
                        } 
                    }
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    posts.id,
                    users.alias as author_name, 
                    posts.user_id, 
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    LIMIT 6
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
                while ($post = $lesInformations->fetch_assoc())
                {
                    //la ligne ci-dessous doit etre supprimée mais regardez ce 
                    //qu'elle affiche avant pour comprendre comment sont organisées les information dans votre 
                    //echo "<pre>" . print_r($post, 1) . "</pre>";

                    // @todo : Votre mission c'est de remplacer les AREMPLACER par les bonnes valeurs
                    // ci-dessous par les bonnes valeurs cachées dans la variable $post 
                    // on vous met le pied à l'étrier avec created
                    // 
                    
                    // avec le ? > ci-dessous on sort du mode php et on écrit du html comme on veut... mais en restant dans la boucle
                
                    ?>
                    <article>
                        <h3>
                            <time><?php echo $post['created'] ?></time>
                        </h3>
                        <address><?php echo $post['author_name']?></address>
                        <a href="wall.php?user_id=<?php echo $post['user_id'] ?>">Voir son mur</a>                    

                        <div>
                            <p><?php
                            $splittedString = explode("\n", $post['content']);
                            //echo $splittedString;
                            foreach($splittedString as $ligne){
                                echo $ligne;
                                echo '<br>';
                            }?></p>
                        </div>
                        <footer>
                            <small>♥<?php echo $post['like_number']?></small>
                            <form action="news.php?user_id=<?php echo $connectedId ?>" method ="post">
                                <input type = 'hidden' name='postId' value = "<?php echo $post['id'] ?>">
                                <button type='submit' name='like'>Aimer</button>
                            </form>
                            <a href="">#<?php echo $post['taglist']?></a>,
                        </footer>
                    </article>
                    <?php
                    // avec le <?php ci-dessus on retourne en mode php 
                }// cette accolade ferme et termine la boucle while ouverte avant.
                ?>

            </main>
        </div>
    </body>
</html>
