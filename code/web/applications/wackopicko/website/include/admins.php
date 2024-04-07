<?php

require_once("ourdb.php");

class Admins
{
   static public $HOME_URL = "/admin/index.php?page=home";
   static public $LOGIN_URL = "/admin/index.php?page=login";
   static public $CREATE_URL = "/admin/index.php?page=create";
   static public $cur_admin = False;
   static public $cur_user = False;

   static function get_admin_id($adminid)
   {
      $query = sprintf("SELECT * from admin where id = '%d'",
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

   static function get_admin_session($sessid)
   {
      $query = sprintf("SELECT admin.id, admin.login, admin.password from admin, admin_session where admin_session.id = '%s' and admin_session.admin_id = admin.id limit 1;",
		       mysqli_real_escape_string(OURDB->conn, $sessid));
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

   static function create_admin($login, $pass)
   {
      $query = sprintf("INSERT into `admin` (`id`, `login`, `password`) VALUES (NULL, '%s', SHA1('%s'));",
		       mysqli_real_escape_string(OURDB->conn, $login),
		       mysqli_real_escape_string(OURDB->conn, $pass));
      if ($res = mysqli_query(OURDB->conn, $query))
      {
	 return mysqli_insert_id(OURDB->conn);
      }
      else
      {
	 return False;
      }
   }

   static function login_admin($adminid)
   {
      // Don't trust the php session, we're using our own
      $query = sprintf("INSERT into `admin_session` (`id`, `admin_id`, `created_on`) VALUES (NULL, '%s', NOW());",
		       mysqli_real_escape_string(OURDB->conn, $adminid));
      if ($res = mysqli_query(OURDB->conn, $query))
      {
	 // add the cookie
	 $id = mysqli_insert_id(OURDB->conn);
	 setcookie("session", $id);
	 return mysqli_insert_id(OURDB->conn);
      }
      else
      {
	 return False;
      }
   }
   static function clean_admin_session()
   {
      mysqli_query(OURDB->conn, "DELETE from admin_session WHERE created_on < DATE_SUB( NOW(), INTERVAL 1 HOUR );");
   }

   static function check_login($admin, $pass)
   {
      $query = sprintf("SELECT * from `admin` where `login` like '%s' and `password` = SHA1( '%s' ) limit 1;",
		       mysqli_real_escape_string(OURDB->conn, $admin),
		       mysqli_real_escape_string(OURDB->conn, $pass));
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

   static function current_admin()
   {
      if (isset($_COOKIE['session']))
      {
	 if (!self::$cur_admin)
	 {
	    Admins::clean_admin_session();
	    self::$cur_user = Admins::get_admin_session($_COOKIE['session']);
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
      if (Admins::current_admin())
      {
	 return true;
      }
      else
      {
	 return False;
      }
   }
   


}

?>