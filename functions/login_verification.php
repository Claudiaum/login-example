<?php
if(!isset($_SESSION))session_start();

$return = array("confirmation"=>false);
try{
if(
  isset($_POST) AND
  isset($_POST['email']) AND
  isset($_POST['password']) AND
  isset($_POST['remember-me'])
){
  // Database process
  include_once("../conn/Database.php");
  $db = new Database();
  $conn = $db->connect();
  
  if(gettype($conn)==="object"){
    // Login process
    include_once("../models/Login.php");
    $login = new Login($conn);
    $result_login = $login->verify_user(
      $_POST['email'],
      $_POST['password']
    );
    if($result_login['confirmation']){
      // Authorization process
      include_once("../models/Auth.php");
      $auth = new Auth($conn,$login->getUserId());
      $result_auth = $auth->new($_SERVER['HTTP_USER_AGENT']);
      if($result_auth['confirmation']){
        $auth_json = json_encode(array(
          "user_id" => $login->getUserId(),
          "token" => $result_auth['new_token'],
          "expiration" => $result_auth['expiration']
        ));
        if($_POST['remember-me']==="true"){
          setcookie("exemplo_login",$auth_json,$auth_json['expiration'],"/");
        }
        $_SESSION['exemplo_login'] = $auth_json;
        $return['confirmation'] = true;
      }else{
        $return['error_message'] = "Authorization process not working.";
        $return['error_type'] = "auth_not_working";
      }
    }else{
      $return['error_message'] = "Client not subscribed or password is wrong.";
      $return['error_type'] = "not_subscribed";
    }
  }else{
    $return['error_message'] = "Database not working";
    $return['error_type'] = "db_not_working.";
  }
}else{
  $return['error_message'] = "Invalid post variables.";
  $return['error_type'] = "invalid_post.";
}
}catch(Exception $e){
  $return['error_message'] = $e;
  $return['error_type'] = "something_else";
}
die(json_encode($return));
?>