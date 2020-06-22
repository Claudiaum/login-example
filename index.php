<?php
include_once("functions/init_login.php")
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="icons/favicon.ico">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" 
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" 
    crossorigin="anonymous">

    <title>Login Example</title>

    <link rel="stylesheet" href="css/style.css?<?=time()?>">

    <link rel="apple-touch-icon" sizes="180x180" href="/icons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16x16.png">
    <link rel="manifest" href="site.webmanifest">
    <link rel="mask-icon" href="/icons/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
  </head>

  <body class="text-center">

  <div class="container">
    <div class="row">
      <div class="d-flex justify-content-center w-100 pt-5 mt-5">

        <?php
        // If user is not authorized
        if($user_authorization!==true){
        ?>

        <form class="form-signin">
          <img class="mb-4" src="img/lock.png" alt="" width="72" height="72">
          <h1 class="h3 mb-3 font-weight-normal">Please log in</h1>

          <?php 
          if(isset($auth_message)){
          ?>
            <div class="alert alert-warning alert-dismissible" role="alert" id="login-message">
              Message: <?=$auth_message?> <br>
              Reason: <?=$auth_reason?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
          <?php
          }
          ?>

          <div class="alert alert-warning alert-dismissible collapse" role="alert" id="login-message">
            The user or the password is not correct! Try again.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          
          <label for="inputEmail" class="sr-only">E-mail</label>
          <input 
            type="email" 
            id="inputEmail" 
            class="form-control" 
            placeholder="E-mail" 
            name="email"
            required autofocus>

          <label for="inputPassword" class="sr-only">Password</label>
          <input 
          type="password" 
          id="inputPassword" 
          class="form-control my-1" 
          minlength="6" 
          placeholder="Password" 
          name="password"
          required>

          <div class="checkbox mb-3">
            <label>
              <input 
                type="checkbox"
                name="remember-me"
                value="remember-me"> Remember me
            </label>
          </div>
          <button class="btn btn-lg btn-primary btn-block" type="submit">Log in</button>
          <p class="mt-5 mb-3 text-muted">&copy; 2020</p>
          <div class="flags d-flex justify-content-center" >
            <p class="m-0 align-self-center">Translate: </p>
            <img src="img/brazil.png" alt="Bandeira do Brasil" class="m-1 flag-icon">
            <img src="img/usa.png" alt="EUA flag" class="m-1 flag-icon">
          </div>
        </form> 
        
        <?php
        }else{
        ?>

          <div class="card card-block">
            <card class="body">
              <h3>User id: <?=$user_id?></h3>
              <p>Session: <?=json_encode($session)?></p>
              <p>Session date: <?=date("d/m/Y H:i:s",$session['expiration'])?></p>
              <p>Cookie: <?=json_encode($cookie)?></p>
              <p>Cookie date: <?=date("d/m/Y H:i:s",$cookie['expiration'])?></p>
              <p>Return_auth_verify: <?=json_encode($return_auth_verify)?></p>
            </card>
          </div>
        
        <?php
        }
        ?>

      </div>
    </div>
  </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" 
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" 
    crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" 
    integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" 
    crossorigin="anonymous"></script>
    <script src="js/main.js?<?=time()?>"></script>
  </body>
</html>
