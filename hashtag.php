<?php                   
                        
                        $hashtagSql = "SELECT * FROM tags";
                        $taglist = $mysqli->query($hashtagSql);
                        $tousLesTags = $taglist->fetch_all();

                        $uneQuestion = "SELECT id FROM posts ORDER BY posts.id DESC LIMIT 1";
                        $dernierPost = $mysqli->query($uneQuestion);
                        $ledernierPost = $dernierPost->fetch_assoc();
                        $leVraiId = $ledernierPost['id'];
                        
                        if (str_contains($postContent,'#')){ 
                             
                        $splittedPost = explode(" ",$postContent);
                            foreach($splittedPost as $word){
                                if (str_contains($word,'#')){
                                    $result = explode('#', $word);
                                    $motHashtag = $result[1];
                                    $verifInsertion = false;
                                    foreach ($tousLesTags as [$id, $label]){
                                        //var_dump($motHashtag== $label); die;
                                        if($motHashtag == $label) {
                                            $verifInsertion = true;
                                            $newHashtag = "INSERT INTO posts_tags(post_id,tag_id)
                                                            VALUES($leVraiId,$id)";
                                            $ok = $mysqli->query($newHashtag);
                                            if (! $ok){
                                                echo "Impossible d'ajouter un tag" . $mysqli->error;
                                            } else {
                                                echo "Tag enregistré";
                                            }
                                        }
                                    }
                                    if ($verifInsertion == false) {
                                            $hashtagInsert = "INSERT INTO tags (label)
                                                            VALUES ('$motHashtag')";
                                            $ok = $mysqli->query($hashtagInsert);
                                                if (! $ok){
                                                    echo "Impossible d'ajouter un nouvel hashtag" . $mysqli->error;
                                                }else{
                                                    echo "Nouveau tag enregistré";
                                                        }
                                            $question2 = "SELECT id FROM tags ORDER BY tags.id DESC LIMIT 1";
                                            $reponse = $mysqli->query($question2);
                                            $lederniertag = $reponse->fetch_assoc();
                                            $levraitag = $lederniertag['id'];
                                            
                                            $posttagsinsert = "INSERT INTO posts_tags (post_id, tag_id)
                                                VALUES ($leVraiId, $levraitag)";
                                            $ok2 = $mysqli->query($posttagsinsert);
                                            if (! $ok2){
                                                echo "Impossible d'ajouter à posts_tags" . $mysqli->error;
                                            }else{
                                                echo "Nouveau post_tag enregistré";
                                            }
                                    }
                                }                               
                            }
                        }     
                ?>