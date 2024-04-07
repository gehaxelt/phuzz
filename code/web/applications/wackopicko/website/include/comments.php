<?php

require_once("ourdb.php");

class Comments
{
   static public $ADD_COMMENT_URL = "/comments/add_comment.php";
   static public $PREVIEW_COMMENT_URL = "/comments/preview_comment.php";
   static public $DELETE_PREVIEW_COMMENT_URL = "/comments/delete_preview_comment.php";

   static function add_preview($text, $userid, $pictureid)
   {
      $query = sprintf("INSERT INTO `comments_preview` (`id`, `text`, `user_id`, `picture_id`, `created_on`) VALUES (NULL, '%s', '%d', '%d', NOW());",
		       mysqli_real_escape_string(OURDB->conn, $text),
		       mysqli_real_escape_string(OURDB->conn, $userid),
		       mysqli_real_escape_string(OURDB->conn, $pictureid));
      mysqli_query(OURDB->conn, $query);
      return mysqli_insert_id(OURDB->conn);
   }

   static function get_all_comments_picture($picid)
   {
      $query = sprintf("SELECT `comments`.`user_id` , `comments`.`text` , `comments`.`created_on` , `users`.`login` FROM `comments` , `users` WHERE `picture_id` = '%d' AND `users`.`id` = `comments`.`user_id` ORDER BY created_on DESC;",
		       mysqli_real_escape_string(OURDB->conn, $picid));
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

   static function delete_preview($previewid, $userid)
   {
      $query = sprintf("DELETE from `comments_preview` where id = '%d' and user_id = '%d'",
                        mysqli_real_escape_string(OURDB->conn, $previewid),
                        mysqli_real_escape_string(OURDB->conn, $userid));
      return mysqli_query(OURDB->conn, $query);
   }

   static function add_comment($previewid, $userid)
   {     
      mysqli_query(OURDB->conn, "BEGIN;");
      $query = sprintf("SELECT `user_id`, `text`, `picture_id`, `created_on` from `comments_preview` where `id` = '%d' and `user_id` = '%d' LIMIT 1;",
                        mysqli_real_escape_string(OURDB->conn, $previewid),
                        mysqli_real_escape_string(OURDB->conn, $userid));
      $res = mysqli_query(OURDB->conn, $query);
      if (!$res)
      {
	 mysqli_query(OURDB->conn, "ROLLBACK;");
	 return False;
      }
      $preview = mysqli_fetch_assoc($res);

      $query = sprintf("INSERT INTO `comments` (`id`, `text`, `user_id`, `picture_id`, `created_on`) VALUES (NULL, '%s', '%d', '%d', '%s');",
		       mysqli_real_escape_string(OURDB->conn, $preview['text']),
		       mysqli_real_escape_string(OURDB->conn, $preview['user_id']),
		       mysqli_real_escape_string(OURDB->conn, $preview['picture_id']),
                       mysqli_real_escape_string(OURDB->conn, $preview['created_on']));
                       
      if (!mysqli_query(OURDB->conn, $query))
      {
	 mysqli_query(OURDB->conn, "ROLLBACK;");
	 return False;
      }

      $query = sprintf("DELETE from `comments_preview` where id = '%d'",
                        mysqli_real_escape_string(OURDB->conn, $preview['id']));
      if (!mysqli_query(OURDB->conn, $query))
      {
	 mysqli_query(OURDB->conn, "ROLLBACK;");
	 return False;
      }
      return mysqli_query(OURDB->conn, "COMMIT;");
      
   }

   

}

?>