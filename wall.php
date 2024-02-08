<?php
require 'connexion.php'
?>

<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>ReSoC - Mur</title> 
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
                <a href=<?php if ($connectedId !=0 ) { echo "#";} else {echo "login.php";} ?>>Profil</a>
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
                /**
                 * Etape 3: récupérer le nom de l'utilisateur
                 */                
                $laQuestionEnSql = "SELECT * FROM users WHERE id= '$userId' ";
                $lesInformations = $mysqli->query($laQuestionEnSql);
                $user = $lesInformations->fetch_assoc();
                //@todo: afficher le résultat de la ligne ci dessous, remplacer XXX par l'alias et effacer la ligne ci-dessous
                //echo "<pre>" . print_r($user, 1) . "</pre>";
                $enCoursDeTraitement = isset($_POST['message']);
                    if ($enCoursDeTraitement)
                    {
                        // on ne fait ce qui suit que si un formulaire a été soumis.
                        // Etape 2: récupérer ce qu'il y a dans le formulaire @todo: c'est là que votre travaille se situe
                        // observez le résultat de cette ligne de débug (vous l'effacerez ensuite)
                        // echo "<pre>" . print_r($_POST, 1) . "</pre>";
                        // et complétez le code ci dessous en remplaçant les ???
                        $authorId = $connectedId;
                        $postContent = $_POST['message'];
                        $namePost = $user['alias'];

                        $postContent = $mysqli->real_escape_string($postContent);

                         
                        //Etape 3 : Petite sécurité
                        // pour éviter les injection sql : https://www.w3schools.com/sql/sql_injection.asp
                        //$authorId = intval($mysqli->real_escape_string($authorId));
                        //$postContent = $mysqli->real_escape_string($postContent);
                        //Etape 4 : construction de la requete
                        $lInstructionSql = "INSERT INTO posts (id, user_id, content, created)
                                VALUES (NULL, $authorId, '$postContent' , NOW())";
                         //echo $lInstructionSql;
                        // Etape 5 : execution
                        $ok = $mysqli->query($lInstructionSql);
                        if ( ! $ok)
                        {
                            echo "Impossible d'ajouter le message: " . $mysqli->error;
                        } else
                        {
                            echo "Message posté en tant que " , $namePost;
                        }
                    }
                
                $enCoursDeTraitement2 = isset($_POST['button']);
                if($enCoursDeTraitement2) {
                    $followed = $userId;
                    $following = $connectedId;
                    $nameFollowed = $user['alias'];
                    $lInstructionSql2 = "INSERT INTO followers (id, followed_user_id, following_user_id)
                        VALUES (NULL, $followed, $following)";
                
                
                    $ok = $mysqli->query($lInstructionSql2);
                    if (! $ok)
                    {
                        echo "Impossible de suivre cette personne." . $mysqli->error;                 
                    } else 
                    {
                        echo "Vous suivez bien " ,$nameFollowed;
                    } 
                }

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
                    <p>Sur cette page vous trouverez tous les message de l'utilisatrice : <?php echo $user['alias']?>
                        (n° <?php echo $userId ?>)
                    </p>
                    <p><?php if ($connectedId == $userId) :?>
                        <form action="wall.php?user_id=<?php echo  $connectedId?>" method="post">
                        <input type='hidden' name='???' value='achanger'>
                        <dl>
                        <dt><label for='message'>Post</label></dt>
                        <dd><textarea name='message'></textarea></dd>
                        </dl>
                        <input type='submit'>
                    </form>
                    <?php else:?>
                        <form action="wall.php?user_id=<?php echo  $userId?>" method="post">
                        
                        <button type='submit' name='button'>Suivre</button>
                    <?php endif; ?> </p>  
                    
                </section>
            </aside>
            <main>
                <?php
                /** 
                 * Etape 3: récupérer tous les messages de l'utilisatrice
                 */
                $laQuestionEnSql = "
                    SELECT posts.content, posts.created, posts.id, users.alias as author_name, 
                    COUNT(likes.id) as like_number, GROUP_CONCAT(DISTINCT tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    LEFT JOIN likes      ON likes.post_id  = posts.id 
                    WHERE posts.user_id='$userId' 
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
                    
                    <p> <?php if ($connectedId == $userId): ?>
                        bonjour
                    <?php endif; ?> </p>

                    <article>
                        <h3>
                            <time datetime='2020-02-01 11:12:13' ><?php echo $post['created']?></time>
                        </h3>
                        <address><?php echo $post['author_name']?></address>
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
                            <form action="wall.php?user_id=<?php echo $connectedId ?>" method ="post">
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
