<?php

namespace App\Controllers;

use Core\Controller;
use Core\Database;

class AuthController extends Controller
{

  public function index(){
    $this->view('auth/login');
  }

  public function register(){

    if($_SERVER['REQUEST_METHOD'] === "POST"){
      $name = $_POST['name'];
      $username = $_POST['username'];
      $email = $_POST['email'];
      $password = $_POST['password'];

      $db = Database::connect();

      $stm = $db->prepare("INSERT INTO users (name, username, email, password) VALUES (:name, :username, :email, :password)");
      
      $hash_password = password_hash($password, PASSWORD_DEFAULT);

      $stm->bindParam(":name", $name);
      $stm->bindParam(":username", $username);
      $stm->bindParam(":email", $email);
      
      $stm->bindParam(":password", $hash_password);
      
        if($stm->execute()) {
          $this->redirect('/login');
        }

    }
    $this->view('/auth/register');
  }

  public function login()
  {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $username = $_POST['username'];
      $password = $_POST['password'];

      $db = Database::connect();
      $stm = $db->prepare("SELECT * FROM users WHERE username = :username");

      $stm->bindParam(":username", $username);
      $stm->execute();

      $user = $stm->fetch();
      session_start();

      if($user && password_verify($password, $user['password'])){

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['username'] = $user['username'];

        $this->redirect('/dash');

      }
    }
    $this->view('auth/login');
  }

  public function logout(){
    session_start();
    session_destroy();
    $this->redirect('/auth');
    exit;
  }
}