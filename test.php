<?php
date_default_timezone_set("America/Sao_Paulo");
// write database/login/auth tests

function write_on_table($class, $func, $in, $out, $confirmation ){
  $today = date("Y/m/d");
  $hour = date("H:i:s",); 
  $in_formated = "";
  if(gettype($in)==="object" OR gettype($in)==="array"){
    $in_formated .="[<br>";
    foreach ($in as $key => $value) {
      $in_formated .="$key => $value , <br>";
    }
    $in_formated .="]<br>";
  }else{
    $in_formated = $in;
  }
  $out_formated = "";
  if(gettype($out)==="object" OR gettype($out)==="array"){
    $out_formated .="[<br>";
    foreach ($out as $key => $value) {
      $out_formated .="$key => ".json_encode($value)." , <br>";
    }
    $out_formated .="]<br>";
  }else{
    $out_formated = $out;
  }
  $confirmation_formated = $confirmation ? "✔️" : "❌" ;
  $html = <<<HTML
  <tr>
    <td scope="row" class="small-font">$today $hour</td>
    <td>$class</td>
    <td>$func</td>
    <td class="small-font"> $in_formated </td>
    <td class="small-font"> $out_formated </td>
    <td> $confirmation_formated </td>
  </tr>
  HTML;
  echo $html;
}

?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="icons/favicon.ico">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.css">
    <link rel="stylesheet" href="css/test.css">

    <title>Login Example - Tests</title>

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
      <div class="d-flex flex-column justify-content-center w-100 py-5 my-5">
        <h1 class="display-4 mb-5">Testes Unitários</h1>
        <div class="card">
          <table data-toggle="table" class="table table-striped">
            <thead class="thead-inverse thead-dark">
              <tr>
                <th data-sortable="true">Hora</th>
                <th >Classe</th>
                <th >Função</th>
                <th >Entrada</th>
                <th >Saída</th>
                <th data-sortable="true">Confirmação</th>
              </tr>
            </thead>
            <tbody>
        <?php
          
          // ---- Tests ----
          // write_on_table($class, $func, $in, $out, $confimation )

          // ---- Database
          include_once("conn/Database.php");
          $db_test = new Database();
          $conn_test = $db_test->connect();
          write_on_table(
            "conn/Database.php",
            "connect",
            "[]",
            gettype($conn_test)==="object" ? "new PDO instance object" : $conn_test,
            gettype($conn_test)==="object" ? true : false,
          );

          // ---- Login

          include_once("models/Login.php");
          $login_test = new Login($conn_test);
          $email_test = "test@test.com.br";
          $pass_test = "test_password";

          // create test user
          $return_login = $login_test->new_user($email_test,$pass_test);
          write_on_table(
            "models/Login.php",
            "new_user(\$email,\$password)",
            "$email_test, $pass_test",
            $return_login,
            $return_login["confirmation"]
          );
          
          // verify correct
          $return_login = $login_test->verify_user($email_test,$pass_test);
          write_on_table(
            "models/Login.php",
            "verify_user(\$email,\$password)",
            "$email_test, $pass_test",
            $return_login,
            $return_login["confirmation"]
          );
          
          // verify wrong email
          $return_login = $login_test->verify_user("wrong@email.com",$pass_test);
          write_on_table(
            "models/Login.php",
            "verify_user(\$email,\$password)",
            "wrong@email.com, $pass_test",
            $return_login,
            !$return_login["confirmation"]
          );

          // verify wrong password
          $return_login = $login_test->verify_user($email_test,"wrong_password");
          write_on_table(
            "models/Login.php",
            "verify_user(\$email,\$password)",
            "$email_test, wrong_password",
            $return_login,
            !$return_login["confirmation"]
          );

          // ---- Auth
          include_once("models/Auth.php");
          $return_login = $login_test->verify_user($email_test,$pass_test);
          $auth_test = new Auth($conn_test,$login_test->getUserId());

          // create new test token
          // new($console_name,$console_type,$new_token = 0)
          $return_login_new = $auth_test->new(
            $_SERVER['HTTP_USER_AGENT'],
            "123123123"
          );
          $aux_in = $_SERVER['HTTP_USER_AGENT'].",123123123";
          write_on_table(
            "models/Auth.php",
            "new(\$id,\$console_name,\$console_type,\$new_token = 0)",
            $aux_in,
            $return_login_new,
            $return_login_new['confirmation'],
          );

          // verify created token
          // verify($cookie) -> ["expiration"=>12312312,"token"=>"adsdasd2312"]
          $new_cookie = array(
            "expiration" => $return_login_new["expiration"],
            "token" => "123123123",
          );
          $return_login_verify = $auth_test->verify_cookie($new_cookie);
          write_on_table(
            "models/Auth.php",
            "verify_cookie(\$new_cookie)",
            $new_cookie,
            $return_login_verify,
            $return_login_verify['confirmation'],
          );

          // verify wrong cookie token
          $new_cookie = array(
            "expiration" => $return_login_verify["expiration"],
            "token" => "123123123",
          );
          $return_login_verify_wrong_token = $auth_test->verify_cookie($new_cookie);
          write_on_table(
            "models/Auth.php",
            "verify_cookie(\$new_cookie)",
            $new_cookie,
            $return_login_verify_wrong_token,
            !$return_login_verify_wrong_token['confirmation'],
          );

          // verify wrong cookie date
          $new_cookie = array(
            "expiration" => 0,
            "token" => $return_login_verify["new_token"],
          );
          $return_verify_session_wrong = $auth_test->verify_cookie($new_cookie);
          write_on_table(
            "models/Auth.php",
            "verify_cookie(\$new_cookie)",
            $new_cookie,
            $return_verify_session_wrong,
            !$return_verify_session_wrong['confirmation'],
          );

          // verify session
          $session = array(
            "expiration" => $return_login_verify["expiration"],
            "token" => $return_login_verify["new_token"],
          );
          $return_verify_session = $auth_test->verify_session($session);
          write_on_table(
            "models/Auth.php",
            "verify_session(\$session)",
            $session,
            $return_verify_session,
            $return_verify_session['confirmation'],
          );

          
          // verify expired session
          $session = array(
            "expiration" => $return_login_verify["expiration"] - 86401,
            "token" => $return_login_verify["new_token"],
          );
          $return_verify_session = $auth_test->verify_session($session);
          write_on_table(
            "models/Auth.php",
            "verify_session(\$session)",
            $session,
            $return_verify_session,
            !$return_verify_session['confirmation'],
          );


          // delete test user
          $return_login_delete = $login_test->delete_user($email_test,$pass_test);
          write_on_table(
            "models/Login.php",
            "delete_user(\$email,\$password)",
            "$email_test, $pass_test",
            $return_login_delete,
            $return_login_delete["confirmation"]
          );

          // delete user sessions
          $return_sessions_delete = $auth_test->clear_sessions();
          write_on_table(
            "models/Login.php",
            "clear_sessions()",
            "[]",
            $return_sessions_delete,
            $return_sessions_delete["confirmation"]
          );

        ?>
          </tbody>
        </table>
        </div>
      </div>
    </div>
  </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js" 
    integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" 
    crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" 
    integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" 
    crossorigin="anonymous"></script>
    <script src="https://unpkg.com/bootstrap-table@1.16.0/dist/bootstrap-table.min.js"></script>
    <script src="js/test.js"></script>
  </body>
</html>
