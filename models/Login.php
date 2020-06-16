<?php
class Login {

  //Database params
  private $conn;
  private $table = "login_example_users";

  //Login process information
  private $user_id;
  private $user_email;
  private $user_subscription_date;
  private $user_last_login;
  private $user_clearance;
  private $user_status;

  public function __construct($db) {
    $this->conn = $db;
  }

  public function verify_user($email, $password){
    $return = array("confirmation" => false);
    // $this->user_email = $email;
    $query = "SELECT
        user_id,
        user_email,
        user_subscription_date,
        user_last_login,
        user_clearance,
        user_status
      FROM 
        $this->table
      WHERE
        user_email = :email AND
        user_password = :password
      LIMIT 0,1
    ";

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Execute query with boolean return
    $stmt->execute([
      "email"=>$email,
      "password"=>$password
    ]);
    
    // DB error
    if(!$stmt){
      $return["error"] = "DB not working: ".$stmt->errorInfo(); 
      return $return;
    }

    // If user doesn't exists
    if($stmt->rowCount()===0){
      $return["message"] = "Client not subscribed.";
      $return["reason"] = "not_subscribed";
      return $return;
    }

    // If user exists
    $fetch = $stmt->fetch();
    $this->user_id = $fetch->user_id;
    $this->user_email = $fetch->user_email;
    $this->user_subscription_date = $fetch->user_subscription_date;
    $this->user_last_login = $fetch->user_last_login;
    $this->user_clearance = $fetch->user_clearance;
    $this->user_status = $fetch->user_status;

    $return["confirmation"] = true;
    $return["message"] = "Client subscribed.";
    $return["email"] = $email;
    return $return;
  }

  //create new user
  public function new_user($email, $password){
    $return = array("confirmation" => false);
    // $this->user_email = $email;
    $query = "INSERT INTO $this->table
      ( user_email,
        user_password)
      VALUES 
      ( :email,
        :password )
    ";

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Execute query with boolean return
    $stmt->execute([
      "email"=>$email,
      "password"=>$password,
    ]);
    
    // DB error
    if(!$stmt){
      $return["error"] = "DB not working: ".$stmt->errorInfo(); 
      return $return;
    }

    $return["confirmation"] = true;
    $return["message"] = "New client created.";
    return $return;
  }

  //delete user
  public function delete_user($email, $password){
    $return = array("confirmation" => false);
    // $this->user_email = $email;
    $query = "DELETE FROM $this->table
      WHERE
        user_email = :email AND
        user_password = :password
    ";

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Execute query with boolean return
    $stmt->execute([
      "email"=>$email,
      "password"=>$password,
    ]);
    
    // DB error
    if(!$stmt){
      $return["error"] = "DB not working: ".$stmt->errorInfo(); 
      return $return;
    }

    $return["confirmation"] = true;
    $return["message"] = "Client deleted. Clear sessions now.";
    return $return;
  }
  

  //gets
  public function getUserId(){
    return $this->user_id;
  }

}