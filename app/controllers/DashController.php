<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;

class DashController extends Controller
{

    public function dash(){

        $sql = "SELECT users.name, tweets.content FROM users INNER JOIN tweets ON users.id = tweets.id_user ORDER BY tweets.created_at DESC";

        $db = Database::connect();

        $stm = $db->prepare($sql);
        $tweets = $stm->execute();
        
        $this->view("dash/index", ['tweets' => $tweets]);
    }

    public function tweet() {
        if($_SERVER['REQUEST_METHOD'] === "POST"){
            $tweet = $_POST["tweet"];

            $db = Database::connect();

            $stm = $db->prepare("INSERT INTO tweets (content, id_user) VALUES (:tweet, :id_user)");
            
            $stm->bindParam(":tweet", $tweet);
            $stm->bindParam(":id_user", $_SESSION['user_id']);
            
            $stm->execute();
            
            $this->redirect("/dash");
        }

    }

    public function index() {

    }
    

}