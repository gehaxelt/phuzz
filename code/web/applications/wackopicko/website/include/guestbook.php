<?php

require_once("ourdb.php");

class Guestbook
{
   static public $GUESTBOOK_URL = "/guestbook.php";

   static function get_all_guestbooks()
   {
      $query = sprintf("SELECT `id`, `name`, `comment`, `created_on` from `guestbook` ORDER BY created_on DESC;");
      $res = mysqli_query(OURDB->conn, $query);
      $to_return = array();
      if ($res)
      {
	 while ($row = mysqli_fetch_assoc($res))
	 {
	    $to_return[] = $row;
	 }
	 return $to_return;
      }
      else
      {
	 return False;
      }
   }

   static function add_guestbook($name, $comment, $vuln = False)
   {
      if ($vuln)
      {
	 $query = sprintf("INSERT INTO `guestbook` (`id`, `name`, `comment`, `created_on`) VALUES (NULL, '%s', '%s', NOW());",
			  $name,
			  mysqli_real_escape_string(OURDB->conn, $comment));
      }
      else
      {
	 $query = sprintf("INSERT INTO `guestbook` (`id`, `name`, `comment`, `created_on`) VALUES (NULL, '%s', '%s', NOW());",
			  mysqli_real_escape_string(OURDB->conn, $name),
			  mysqli_real_escape_string(OURDB->conn, $comment));
      }
      return mysqli_query(OURDB->conn, $query);
   }
}
?>