<?php
if(!isset($_SESSION)) session_start();
date_default_timezone_set("America/Sao_Paulo");

// Fake hoisting
$session;
$cookie;
$user_id; // is set if session or cookie exists
$db; // new Database class isntance
$conn; // PDO connection from Database
$auth; // new Auth class isntance
$return_auth_verify;
$auth_json; // to save all 3 values in only one cookie/session variable
$user_authorization; // key to show log/sign or to show if user is logged
$auth_message; // in case authorization is denied
$auth_reason; // in case authorization is denied
// unset($_SESSION['exemplo_login']); // to see the cookie working, uncomment this

// Verify if session exists
// if it exists, then the token is not renewed
if(isset($_SESSION['exemplo_login'])){
  // echo(json_encode($_SESSION));
  $session = json_decode($_SESSION['exemplo_login'],true);
  if($session['expiration'] < time()){
    unset($session);
  }else{
    $user_id = $session["user_id"];
  }
}

// If session don't exists or has expired
if(!isset($session)){
  // Verify if cookie exists
  // echo(json_encode($_COOKIE));
  if(isset($_COOKIE['exemplo_login'])){
    $cookie = json_decode($_COOKIE['exemplo_login'],true);
    $user_id = $cookie['user_id'];
  }
}

// Database
include_once("conn/Database.php");
$db = new Database();
$conn = $db->connect();
// If connection is fine
if(gettype($conn)==="object" AND isset($user_id)){
  // If user_id exists then the user has logged somehow
  // so we don't need to log him again, we just need to 
  // verify his authorization, but if his auth ain't valid
  // we clear his Session/Cookie and show him the log page
  include_once("models/Auth.php");
  $auth = new Auth($conn,$user_id);
  // If user_id is set, then $session or $cookie is set as well
  if(isset($session)){
    $return_auth_verify = $auth->verify_session($session);
  }else{
    if(isset($cookie)){
      $return_auth_verify = $auth->verify_cookie($cookie);
    }
  }
   
  if(isset($return_auth_verify)){
    if($return_auth_verify['confirmation'] === true){
      // It will return 'new_token' only if 'remember-me' was cheked
      // And it only affects the cookie
      if(isset($return_auth_verify['new_token'])){
        $auth_json = json_encode(array(
          "user_id" => $user_id,
          "token" => $return_auth_verify['new_token'],
          "expiration" => $return_auth_verify['expiration']
        ));
        setcookie("exemplo_login",$auth_json,$return_auth_verify['expiration'],"/");
        $_SESSION['exemplo_login'] = $auth_json;
      }
      // Show welcome/dashboard 
      $user_authorization = true;
    }else{
      // For some reason user was rejected
      // The posssible return have an array with
      // "confirmation"
      // "message" => String with a message explaining why
      // "reason" => Short string without spaces to use with a switch
      $auth_message = $return_auth_verify['message'];
      $auth_reason = $return_auth_verify['reason'];
      setcookie('exemplo_login', null, -1, '/'); 
      unset($_SESSION['exemplo_login']);
      $user_authorization = false;
    }
  }else{
    // User is not logged
    // Show log/sign in form
    $user_authorization = false;
  }
}else{
  // connection is broken
  // Show log/sign in form
  $user_authorization = false;
}

?>