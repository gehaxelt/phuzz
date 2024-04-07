<?php
class DB
{
   public $conn;
   
   function __construct($server, $user_name, $pass, $db)
   {
      $this->conn = mysqli_connect($server, $user_name, $pass);
      mysqli_select_db($this->conn, $db);
      mysqli_set_charset($this->conn,'utf8');
   }

   function __destruct()
   {
      mysqli_close($this->conn);
   }
}
?>