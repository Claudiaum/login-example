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

  public function verify_authorization($cookie){
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
    if($stmt->rowCount()>0){
      $return["message"] = "Token doesn't exists, but it should.";
      $return["reason"] = "cookie_copied";
      return $return;
    }

    // If cookie expired

    // If cookie  is fine
  }

}

?>