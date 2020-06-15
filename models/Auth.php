<?php
class Auth {

  // Database params
  private $conn;
  private $table = "login_example_sessions";

  // Params
  private $user_id;

  public function __construct($db,$user_id) {
    $this->conn = $db;
    $this->user_id = $user_id;
  }

  public function verify($cookie){
    $return = array("confirmation"=> false);
    $token = $cookie["token"] || "";

    $query = "SELECT
      session_id,
      session_token,
      session_expiration,
      session_console_name,
      session_console_type
    FROM
      $this->table
    WHERE
      session_user_id = :user_id AND
      session_token = :token
    LIMIT 0,1
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute([
      "user_id" => $this->user_id,
      "token" => $token
    ]);
    
    // DB error
    if(!$stmt){
      $return["error"] = "DB not working: ".$stmt->errorInfo(); 
      return $return;
    }

    // If token doesn't exists
    if($stmt->rowCount()===0){
      $return["message"] = "Token doesn't exists, but it should.";
      $return["reason"] = "cookie_copied";
      return $return;
    }

    $fetch = $stmt->fetch();
    // If cookie expired destroy token
    if($fetch->session_expiration < $cookie['expiration']){
      $return["message"] = "Token expired.";
      $return["reason"] = "cookie_expired";
      $delete_query = "DELETE FROM $table 
        WHERE
          session_token = :token
        LIMIT 1
      ";
      $delete_stmt = $this->conn->prepare($delete_query);
      $delete_stmt->execute(["token"=>$token]);

      if(!$delete_stmt){
        $return["error"] = "DB not working: ".$delete_stmt->errorInfo(); 
      }

      return $return;
    }else{
      // If cookie  is fine
      $return["authorization"] = true;
      $return["message"] = "Cookie will be renewed.";
      $return["reason"] = "cookie_renewed";
      $new_token = bin2hex(random_bytes(32));
      // $expiration = time()+(60*60*24*7); // 1 week to renew
      $expiration = time()+(60); // 60 seconds to renew
      $return['new_token'] = $new_token;
      $return['expiration'] = $expiration;
      
      $insert_query = "UPDATE $table
        SET
          session_token = :new_token
          session_expiration = :expiration
        WHERE
          session_token = :token
        LIMIT 1
      ";
      $insert_stmt = $this->conn->prepare($insert_query);
      $insert_stmt->execute([
        "new_token"=>$new_token,
        "expiration"=>$expiration,
        "token"=>$token,
      ]);
      
      if(!$insert_stmt){
        $return["authorization"] = false;
        $return["error"] = "DB not working: ".$insert_stmt->errorInfo(); 
      }

      return $return;
    }
  }

  public function new($id,$console_name,$console_type){
    $return = array("confirmation" => false);
    $return["message"] = "Cookie created.";
    $return["reason"] = "cookie_created";
    $new_token = bin2hex(random_bytes(32));
    // $expiration = time()+(60*60*24*7); // 1 week to renew
    $expiration = time()+(60); // 60 seconds to renew
    $return['new_token'] = $new_token;
    $return['expiration'] = $expiration;
    
    $update_query = "INSERT INTO $table
      (`session_token`, `session_expiration`, `session_user_id`, `session_console_name`, `session_console_type`) 
      VALUES 
      (:new_token,:expiration,:user_id,:console_name,:console_type)
    ";
    $update_stmt = $this->conn->prepare($update_query);
    $update_stmt->execute([
      "new_token"=>$new_token,
      "expiration"=>$expiration,
      "user_id"=>$id,
      "console_name"=>$console_name,
      "console_type"=>$console_type,
    ]);
    
    if(!$update_stmt){
      $return["error"] = "DB not working: ".$update_stmt->errorInfo(); 
    }

    return $return;
  }

}

?>