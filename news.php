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
            <a href='admin.php'><img src="logo.png" alt="Logo de notre réseau social"/></a>
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
                <p>
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
                    $enCoursDeTraitement4 = isset($_POST['dislike']);
                if($enCoursDeTraitement4) {
                    $liker = $connectedId;
                    $likedPost = $_POST['postId'];

                    $deleteSql = " DELETE FROM likes WHERE user_id = $liker AND post_id = $likedPost";
                    $ok = $mysqli->query($deleteSql);
                    if (! $ok)
                    {
                        echo "Impossible de dé-liker ce post." . $mysqli->error;                 
                    } else 
                    {
                        echo "Post dé-liké";
                    } 

                }
                $ecoutePostReponse = isset($_POST['answer']);
                if ($ecoutePostReponse)  {
                    $parentId = $_POST['postId'];
                    $postContent = $_POST['reponse'];
                    $ordreSql = "INSERT INTO posts (user_id, content, created, parent_id)
                    VALUES ($connectedId, '$postContent', NOW(),$parentId)";

                    $ok = $mysqli->query($ordreSql);
                    if (!$ok)
                    {
                        echo "Impossible de répondre" . $mysqli->error;
                    } else 
                    {
                        echo "réponse envoyée!";
                    }
                    require 'hashtag.php';
                }
                ?>
                </p>

                <img src="news.png" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez les derniers messages de
                        toutes les utilisatrices du site.</p>
                </section>
            </aside>
            <main>
                
                
<?php

                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    posts.id,
                    users.alias as author_name, 
                    posts.user_id, 
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist,
                    GROUP_CONCAT(DISTINCT tags.id ORDER BY tags.label) AS tagid                    
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
                if ( ! $lesInformations)
                {
                    echo "<article>";
                    echo("Échec de la requete : " . $mysqli->error);
                    echo("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                    exit();
                }

                
                while ($post = $lesInformations->fetch_assoc())
                {
                    
                    
                   
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
                            <form action="news.php?user_id=<?php echo $connectedId ?>" method ="post">
                                <input type = 'hidden' name='postId' value = "<?php echo $post['id'] ?>">
                                <button type='submit' name='dislike'>Ne plus aimer</button>
                            </form>
                            <br>
                            <?php
                                $splittedTag = explode(",", $post['taglist']);
                                $splittedId = explode(",", $post['tagid']);
                        
                                for ($i = 0 ; $i<count($splittedId); $i ++ ):?>
                                    <a href ="tags.php?tag_id=<?php
                                    echo $splittedId[$i];
                                ?>">#<?php echo $splittedTag[$i] ?></a> 
                                <?php endfor;
                            ?>
                            <br>
                            <form action="news.php?user_id=<?php echo $connectedId ?>" method ="post">
                                <input type = 'hidden' name='postId' value = "<?php echo $post['id'] ?>">  
                                <textarea name='reponse'></textarea>
                                <button type='submit' name='answer'>Répondre</button>
                            </form>
                        </footer>
                    </article>
                    <?php
                }
                ?>

            </main>
        </div>
    </body>
</html>
