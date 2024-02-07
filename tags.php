<?php
require 'connexion.php'
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
            <img src="resoc.jpg" alt="Logo de notre réseau social"/>
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
            <?php
            /**
             * Cette page est similaire à wall.php ou feed.php 
             * mais elle porte sur les mots-clés (tags)
             */
            /**
             * Etape 1: Le mur concerne un mot-clé en particulier
             */
            $tagId = intval($_GET['tag_id']);
            ?>
            

            <aside>
                <p>
                <?php
                /**
                 * Etape 3: récupérer le nom du mot-clé
                 */
                $laQuestionEnSql = "SELECT * FROM tags WHERE id='$tagId'";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par le label et effacer la ligne ci-dessous
                //echo "<pre>" . print_r($tag, 1) . "</pre>";
                
                
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
                ?> </p>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages comportant
                        le mot-clé <?php echo $tag['label']?>
                        (n° <?php echo $tag['id'] ?>)
                    </p>

                </section>
            </aside>
            <main>
                <?php
                /**
                 * Etape 3: récupérer tous les messages avec un mot clé donné
                 */
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    posts.id,
                    posts.user_id,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts_tags as filter 
                    JOIN posts ON posts.id=filter.post_id
                    JOIN users ON users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE filter.tag_id='$tagId'
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

                    //echo "<pre>" . print_r($post, 1) . "</pre>";
                    ?>                
                    <article>
                        <h3>
                        <time datetime='2020-02-01 11:12:13' ><?php echo $post['created']?></time>
                        </h3>
                        <address><?php echo $post['author_name']?></address>
                        <a href="wall.php?user_id=<?php echo $post['user_id'] ?>">Voir son mur</a>                    

                        <div>
                            <p><?php echo $post['content']?></p>
                            
                        </div>                                            
                        <footer>
                            <small>♥ <?php echo $post['like_number']?></small>
                            <form action="tags.php?tag_id=1" method ="post">
                                <input type = 'hidden' name='postId' value = "<?php echo $post['id'] ?>">
                                <button type='submit' name='like'>Aimer</button>
                            </form>
                            <a href="">#<?php echo $post['taglist']?></a>
                        </footer>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>