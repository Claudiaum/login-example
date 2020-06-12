<?php
class Login {

  //Database params
  private $conn;
  private $table = "login_example_users";

  //Login process information
  private $user_id;
  private $user_email;
  private $user_photo;
  private $user_subscription_date;
  private $user_last_login;
  private $user_clearance;
  private $user_status;

  public function __construct($db) {
    $this->conn = $db;
  }

  public function verify_login($email, $password){
    $return = array("confirmation" => false);
    $this->user_email = $email;
    $query = "SELECT
        user_id as id,
        user_email as email,
        user_photo as photo,
        user_subscription_date as subscription_date,
        user_last_login as last_login,
        user_clearance as clearance,
        user_status as status
      FROM 
        $this->table
      WHERE
        user_email = :email,
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

    // If user don't exists
    if($stmt->rowCount()>0){
      $return["message"] = "Client not subscribed.";
      $return["reason"] = "not_subscribed";
      return $return;
    }

    // If user exists
    $fetch = $stmt->fetch();
    $this->user_id = $fetch->user_id;
    $this->user_photo = $fetch->user_photo;
    $this->user_subscription_date = $fetch->user_subscription_date;
    $this->user_last_login = $fetch->user_last_login;
    $this->user_clearance = $fetch->user_clearance;
    $this->user_status = $fetch->user_status;

    $return["confirmation"] = true;
    $return["message"] = "Client subscribed.";
    return $return;
  }

}