<?php
require 'connexion.php';
if ($connectedId !=0 ): 
    


?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Flux</title>         
        <meta name="author" content="Julien Falconnet">
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <header>
        <a href='admin.php'><img src="resoc.jpg" alt="Logo de notre réseau social"/> </a>
            <nav id="menu">
                <a href="news.php">Actualités</a>
                <a href="wall.php?user_id=<?php echo $connectedId?>">Mur</a>
                <a href="feed.php?user_id=<?php echo $connectedId?>">Flux</a>
                <a href="tags.php?tag_id=1">Mots-clés</a>
            </nav>
            <nav id="user">
                <a href=<?php if ($connectedId !=0 ) { echo "#";} else {echo "login.php";} ?> >Profil</a>
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
                
                $laQuestionEnSql = "SELECT * FROM `users` WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
               
                ?> </p>
                <img src="user.jpg" alt="Portrait de l'utilisatrice"/>
                <section>
                    <h3>Présentation</h3>
                    <p>Sur cette page vous trouverez tous les message des utilisatrices
                        auxquel est abonnée l'utilisatrice <?php echo $user['alias']?>
                        (n° <?php echo $userId ?>)
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
                    FROM followers 
                    JOIN users ON users.id=followers.followed_user_id
                    JOIN posts ON posts.user_id=users.id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE followers.following_user_id='$userId' 
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
                            foreach($splittedString as $ligne){
                                echo $ligne;
                                echo '<br>';
                            }?></p>
                            
                        </div>                                            
                        <footer>
                            <small>♥ <?php echo $post['like_number']?></small>
                            <form action="feed.php?user_id=<?php echo $connectedId ?>" method ="post">
                                <input type = 'hidden' name='postId' value = "<?php echo $post['id'] ?>">
                                <button type='submit' name='like'>Aimer</button>
                            </form>
                            <?php
                                $splittedTag = explode(",", $post['taglist']);
                                $splittedId = explode(",", $post['tagid']);
                        
                                for ($i = 0 ; $i<count($splittedId); $i ++ ):?>
                                    <a href ="tags.php?tag_id=<?php
                                    echo $splittedId[$i];
                                ?>">#<?php echo $splittedTag[$i] ?></a>;
                                <?php endfor;
                            ?>
                    </footer>
                </article>
                <?php
                }
                ?>


            </main>
        </div>
    </body>
</html>
<?php else : ?>
    <p>Vous n'êtes pas connecté, impossible de charger la page</p>
    <a href="login.php">Se connecter </a> 
    <?php endif; ?>
