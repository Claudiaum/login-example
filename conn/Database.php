<?php
class Database {

  private $host = 'localhost';
  private $user = 'root';
  private $password = 'vertrigo';
  private $db_name = 'exemplo_login';
  private $conn;

  public function connect(){
    $this->conn = null;

    try{
      // Data Source Name
      $dsn = 'mysql:host='.$this->host.';dbname='.$this->db_name;

      $this->conn = new PDO($dsn,$this->user,$this->password);
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e){
      echo 'Connection Error: '.$e->getMessage();
    }

    return $this->conn;
  }

}

?>