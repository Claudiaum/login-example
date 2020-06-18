<?php

date_default_timezone_set("America/Sao_Paulo");
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

  public function verify_cookie($cookie){
    $return = array("confirmation"=> false);
    $token = isset($cookie["token"]) ? $cookie["token"] : "";

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
      session_token = :token AND
      session_expiration = :expiration
    LIMIT 0,1
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute([
      "user_id" => $this->user_id,
      "token" => $token,
      "expiration" => date('Y-m-d H:i:s',$cookie['expiration'])
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
    if(strtotime($fetch->session_expiration) < time()){
      $return["message"] = "Token expired.";
      $return["reason"] = "cookie_expired";
      $delete_query = "DELETE FROM $this->table 
        WHERE
          session_token = :token
      ";
      $delete_stmt = $this->conn->prepare($delete_query);
      $delete_stmt->execute(["token"=>$token]);

      if(!$delete_stmt){
        $return["error"] = "DB not working: ".$delete_stmt->errorInfo(); 
      }

      return $return;
    }else{
      // If cookie  is fine
      $return["confirmation"] = true;
      $return["message"] = "Cookie will be renewed.";
      $return["reason"] = "cookie_renewed";
      $new_token = bin2hex(random_bytes(32));
      // $expiration = time()+(60*60*24*7); // 1 week to renew
      // $expiration = date('Y-m-d H:i:s',time()+(0)); // 0 seconds to renew / test
      // $expiration = date('Y-m-d H:i:s',time()+(60)); // 60 seconds to renew
      $expiration = date('Y-m-d H:i:s',time()+(86400)); // 1 day renew
      $return['new_token'] = $new_token;
      $return['expiration'] = strtotime($expiration);
      // return $return;
      
      $insert_query = "UPDATE $this->table
        SET
          session_token = :new_token,
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
        $return["confirmation"] = false;
        $return["error"] = "DB not working: ".$insert_stmt->errorInfo(); 
      }

      return $return;
    }
  }

  public function verify_session($session){
    $return = array("confirmation"=> false);
    $token = isset($session["token"]) ? $session["token"] : "";

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
      session_token = :token AND
      session_expiration = :expiration
    LIMIT 0,1
    ";

    $stmt = $this->conn->prepare($query);
    $stmt->execute([
      "user_id" => $this->user_id,
      "token" => $token,
      "expiration" =>  date('Y-m-d H:i:s',$session['expiration'])
    ]);
    
    // DB error
    if(!$stmt){
      $return["error"] = "DB not working: ".$stmt->errorInfo(); 
      return $return;
    }

    // If token doesn't exists
    if($stmt->rowCount()===0){
      $return["message"] = "Token doesn't exists, but it should.";
      $return["reason"] = "session_copied";
      return $return;
    }

    $fetch = $stmt->fetch();
    // If session expired destroy token
    if(strtotime($fetch->session_expiration) < time()){
      $return["message"] = "Token expired.";
      $return["reason"] = "session_expired";
      $delete_query = "DELETE FROM $this->table 
        WHERE
          session_token = :token
      ";
      $delete_stmt = $this->conn->prepare($delete_query);
      $delete_stmt->execute(["token"=>$token]);

      if(!$delete_stmt){
        $return["error"] = "DB not working: ".$delete_stmt->errorInfo(); 
      }
    }else{
      // If session  is fine
      $return["confirmation"] = true;
      $return["message"] = "Session is fine.";
      $return["reason"] = "session_fine";
    }
    return $return;
  }

  public function new($console_name,$new_token = 0){
    $return = array("confirmation" => false);
    $return["message"] = "Cookie created.";
    $return["reason"] = "cookie_created";
    $console_type = !strpos($console_name, "Mobi") ? "PC" : "Mobile";
    if($new_token === 0){
      $new_token = bin2hex(random_bytes(32));
    }
    // $expiration = time()+(60*60*24*7); // 1 week to renew
    // $expiration = date('Y-m-d H:i:s',time()+(60)); // 0 seconds to renew
    $expiration = date('Y-m-d H:i:s',time()+(86400)); // 1 day renew
    $return['new_token'] = $new_token;
    $return['expiration'] = strtotime($expiration);
    
    $update_query = "INSERT INTO $this->table
      (`session_token`, `session_expiration`, `session_user_id`, `session_console_name`, `session_console_type`) 
      VALUES 
      (:new_token,:expiration,:user_id,:console_name,:console_type)
    ";
    $update_stmt = $this->conn->prepare($update_query);
    $update_stmt->execute([
      "new_token"=>$new_token,
      "expiration"=>$expiration,
      "user_id"=>$this->user_id,
      "console_name"=>$console_name,
      "console_type"=>$console_type,
    ]);
    
    if(!$update_stmt){
      $return["error"] = "DB not working: ".$update_stmt->errorInfo(); 
    }

    $return["confirmation"] = true;
    return $return;
  }

  public function clear_sessions(){
    $return = array("confirmation" => false);
    // $this->user_email = $email;
    $query = "DELETE FROM $this->table
      WHERE
        session_user_id = :user_id
    ";

    // Prepare statement
    $stmt = $this->conn->prepare($query);

    // Execute query with boolean return
    $stmt->execute([
      "user_id"=>$this->user_id
    ]);
    
    // DB error
    if(!$stmt){
      $return["error"] = "DB not working: ".$stmt->errorInfo(); 
      return $return;
    }

    $return["confirmation"] = true;
    $return["user_id"] = $this->user_id;
    return $return;
  }

}

?>