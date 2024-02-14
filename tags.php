<?php
require 'connexion.php'
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Les messages par mot-clé</title> 
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
        <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social"/>
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href=<?php if ($connectedId != 0) {echo "wall.php?user_id=" . $connectedId;} else {echo "login.php" ;} ?>>Mur</a>
                <a href="feed.php?user_id=<?php echo $connectedId?>">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href=<?php if ($connectedId !=0 ) { echo "#";} else {echo "login.php";} ?>>Profil</a>
                <ul>
                    <li><a href="settings.php?user_id=<?php echo $connectedId?>">Paramètres</a></li>
                    <li><a href="followers.php?user_id=<?php echo $connectedId?>">Mes suiveurs</a></li>
                    <li><a href="subscriptions.php?user_id=<?php echo $connectedId?>">Mes abonnements</a></li>
                </ul>

            </nav>
        </header>
        <div id="wrapper">
            <?php
            
            $tagId = intval($_GET['tag_id']);
            ?>
            

            <aside>
                <p>
                <?php
                
                $laQuestionEnSql = "SELECT * FROM tags WHERE id='$tagId'";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $tag = $lesInformations->fetch_assoc();
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
                
                $laQuestionEnSql = "
                    SELECT posts.content,
                    posts.created,
                    posts.id,
                    posts.user_id,
                    users.alias as author_name,  
                    count(likes.id) as like_number,  
                    GROUP_CONCAT(DISTINCT tags.label) AS taglist,
                    GROUP_CONCAT(DISTINCT tags.id ORDER BY tags.label) AS tagid
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
                            <p><?php
                            $splittedString = explode("\n", $post['content']);
                            //echo $splittedString;
                            foreach($splittedString as $ligne){
                                echo $ligne;
                                echo '<br>';
                            }?></p>
                            
                        </div>                                            
                        <footer>
                            <small>♥ <?php echo $post['like_number']?></small>
                            <form action="tags.php?tag_id=1" method ="post">
                                <input type = 'hidden' name='postId' value = "<?php echo $post['id'] ?>">
                                <button type='submit' name='like'>Aimer</button>
                            </form>
                            <?php
                                $splittedTag = explode(",", $post['taglist']);
                                $splittedId = explode(",", $post['tagid']);
                        //    echo $post['taglist'];
                        //    echo $post['tagid'];
                        //    print_r($splittedTag);
                        //    print_r($splittedId);
                                for ($i = 0 ; $i<count($splittedId); $i ++ ):?>
                                    <a href ="tags.php?tag_id=<?php
                                    echo $splittedId[$i];
                                ?>">#<?php echo $splittedTag[$i] ?></a>;
                                <?php endfor;
                            ?>
                        </footer>
                    </article>
                <?php } ?>


            </main>
        </div>
    </body>
</html>