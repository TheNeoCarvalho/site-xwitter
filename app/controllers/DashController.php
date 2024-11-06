<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;
use Core\helpers;

session_start();

class DashController extends Controller
{

    public function dash(){
        $user_id = $_SESSION['user_id'];

        $sql = "
            SELECT 
                users.name,
                users.username,
                tweets.id,
                tweets.content,
                tweets.created_at,
                COUNT(likes.id) AS like_count
            FROM 
                tweets
            INNER JOIN 
                users ON tweets.user_id = users.id
            LEFT JOIN 
                likes ON tweets.id = likes.tweet_id
            GROUP BY 
                tweets.id
            ORDER BY 
                tweets.created_at DESC";
        
        $toFollow = "
            SELECT u.id, u.name, u.username
            FROM users u
            WHERE NOT EXISTS (
                SELECT 1
                FROM followers f
                WHERE f.user_follow = :user_id AND f.user_following = u.id
            ) AND u.id != :user_id
            ";

        $db = Database::connect();

        $stmFollows = $db->prepare($toFollow);
        $stm = $db->prepare($sql);
        
        $stmFollows->bindParam(':user_id', $user_id);

        $stm->execute();
        $stmFollows->execute();

        $tweets = $stm->fetchAll();
        $followrs = $stmFollows->fetchAll();

        $this->view("dash/index", ['tweets' => $tweets, 'followrs' => $followrs]);
    }

    public function tweet() {
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            session_start();

            $tweet = $_POST["tweet"];
            $user_id = $_SESSION['user_id'];

            
            $db = Database::connect();

            $stm = $db->prepare("INSERT INTO tweets (content, user_id) VALUES (:tweet, :user_id)");
            
            $stm->bindParam(":tweet", $tweet);
            $stm->bindParam(":user_id", $user_id);

            $stm->execute();
            
            $this->redirect("/dash");
        }

    }

    public function like() {
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            session_start();

            $tweet_id = intval($_POST["tweet_id"]);
            $user_id = $_SESSION['user_id'];

            $db = Database::connect();

            $stm = $db->prepare("INSERT INTO likes (tweet_id, user_id) VALUES (:tweet_id, :user_id)");
            
            $stm->bindParam(":tweet_id", $tweet_id); 
            $stm->bindParam(":user_id", $user_id);

            $stm->execute();
            
            $this->redirect("/dash");
        }

    }

    public function follow()
{

    if($_SERVER['REQUEST_METHOD'] === "POST"){
        $user_id = $_SESSION['user_id'];  // ID do usuário logado
        $follow_user_id = $_POST['follow_user_id'];  // ID do usuário a ser seguido

        // Verificar se o usuário já segue o outro
        $db = Database::connect();

        // Inserir o relacionamento de seguimento
        $sql = "INSERT INTO followers (user_follow, user_following, created_at) VALUES (:user_id, :follow_user_id, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':follow_user_id', $follow_user_id);
        
        if($stmt->execute()) {
            $this->redirect('/dash');
        }

        
    }
}
    

}