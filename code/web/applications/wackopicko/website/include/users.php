<?php

require_once("ourdb.php");


class Users
{
   static public $HOME_URL = "/users/home.php";
   static public $VIEW_URL = "/users/view.php";
   static public $LOGIN_URL = "/users/login.php";
   static public $LOGOUT_URL = "/users/logout.php";
   static public $PURCHASED_URL = "/pictures/purchased.php";
   static public $cur_user = False;

   static function get_user($userid)
   {
      $query = sprintf("SELECT * from users where id = '%d'",
		       mysqli_real_escape_string(OURDB->conn, $userid));
      $res = mysqli_query(OURDB->conn, $query);
      if ($res)
      {
	 return mysqli_fetch_assoc($res);
      }
      else
      {
	 return False;
      }
   }

   static function create_user($username, $pass, $firstname, $lastname, $vuln = False)
   {
      $salt = mt_rand(0, 900);
      $salt = base64_encode($salt);
      $initial_bux = 100;
      if ($vuln)
      {
	 $pass = mysqli_real_escape_string(OURDB->conn, $pass);
	 $firstname = mysqli_real_escape_string(OURDB->conn, $firstname);
	 $pass = $pass . $salt;
	 $query = "INSERT INTO `users` (`id`, `login`, `password`, `firstname`, `lastname`, `salt`, `tradebux`, `created_on`, `last_login_on`) VALUES (NULL, '{$username}', SHA1('{$pass}'), '{$firstname}', '{$lastname}','{$salt}', '{$initial_bux}', NOW(), NOW());";
      }
      else
      {
	 $query = sprintf("INSERT INTO `users` (`id`, `login`, `password`, `firstname`, `lastname`, `salt`, `tradebux`, `created_on`, `last_login_on`) VALUES (NULL, '%s', SHA1('%s'), '%s', '%s', '%s','%d', NOW(), NOW());",
			  mysqli_real_escape_string(OURDB->conn, $username),
			  mysqli_real_escape_string(OURDB->conn, $pass . $salt),
			  mysqli_real_escape_string(OURDB->conn, $firstname),
	                  mysqli_real_escape_string(OURDB->conn, $lastname),
			  mysqli_real_escape_string(OURDB->conn, $salt),
			  mysqli_real_escape_string(OURDB->conn, $initial_bux));
			  
      }
      if ($res = mysqli_query(OURDB->conn, $query))
      {
	 return mysqli_insert_id(OURDB->conn);
      }
      else
      {
	 if ($vuln)
	 {
	    die(mysqli_error(OURDB));
	 }
	 else
	 {
	    return False;
	 }
      }
	 
   }

   static function login_user($userid)
   {
      session_start();
      $_SESSION['userid'] = $userid;
      $query = sprintf("UPDATE `users` SET `last_login_on` = NOW( ) WHERE `users`.`id` = '%d' LIMIT 1;",
		       mysqli_real_escape_string(OURDB->conn, $userid));
      return mysqli_query(OURDB->conn, $query);
   }

   static function logout()
   {
      session_unset();
   }

   static function check_login($username, $pass, $vuln = False)
   {
      if ($vuln)
      {
	 $query = sprintf("SELECT * from `users` where `login` like '%s' and `password` = SHA1( CONCAT('%s', `salt`)) limit 1;",
	                   $username,
	                   mysqli_real_escape_string(OURDB->conn, $pass));	 
      }
      else
      {
	 $query = sprintf("SELECT * from `users` where `login` like '%s' and `password` = SHA1( CONCAT('%s', `salt`)) limit 1;",
	                   mysqli_real_escape_string(OURDB->conn, $username),
	                   mysqli_real_escape_string(OURDB->conn, $pass));
      }
      $res = mysqli_query(OURDB->conn, $query);
      if ($res)
      {
	 return mysqli_fetch_assoc($res);
      }
      else
      {
	 if ($vuln)
	 {
	    die(mysqli_error(OURDB));
	 }
	 else
	 {
	    return False;
	 }
      }
   }

   static function similar_login($login, $vuln = False)
   {
      if ($vuln)
      {
	 $query = "SELECT * from `users` where `firstname` like '%{$login}%' and firstname != '{$login}'";
      }
      else
      {
	 $query = sprintf("SELECT * from `users` where `firstname` like '%%%s%%' and firstname != '%s'",
			  mysqli_real_escape_string(OURDB->conn, $login),
			  mysqli_real_escape_string(OURDB->conn, $login));
      }
      $res = mysqli_query(OURDB->conn, $query);
      $to_ret = array();
      if ($res)
      {
	 while ($row = mysqli_fetch_assoc($res))
	 {
	    $to_ret[] = $row;
	 }
	 return $to_ret;
	 
      }
      else
      {
	 if ($vuln)
	 {
	    die(mysqli_error(OURDB));
	 }
	 return False;
      }
      
      
   }

   static function current_user()
   {
      if (isset($_SESSION['userid']))
      {
	 if (!self::$cur_user)
	 {
	    self::$cur_user = Users::get_user($_SESSION['userid']);
	 }
	 return self::$cur_user;
      }
      else
      {
	 return False;
      }
   }
   
   static function is_logged_in()
   {
      if (Users::current_user())
      {
	 return True;
      }
      else
      {
	 return False;
      }
   }
}

?>