<?php
require_once("ourdb.php");

class Cart
{

   static public $ACTION_URL = "/cart/action.php";
   static public $CONFIRM_URL = "/cart/confirm.php";
   static public $REVIEW_URL = "/cart/review.php";
   static public $ADD_COUPON_URL = "/cart/add_coupon.php";

   static function add_coupon($cartid, $couponcode)
   {
      $query = sprintf("SELECT * from `coupons` where code = '%s' LIMIT 1;", mysqli_real_escape_string(OURDB->conn, $couponcode));
      if (!$res = mysqli_query(OURDB->conn, $query))
      {
	 return False;
      }
      if (!$arr = mysqli_fetch_assoc($res))
      {
	 return False;
      }
      $couponid = $arr['id'];
      $query = sprintf("INSERT into `cart_coupons` (`cart_id`, `coupon_id`) VALUES ('%d', '%d');",
                        mysqli_real_escape_string(OURDB->conn, $cartid),
                        mysqli_real_escape_string(OURDB->conn, $couponid));
      return mysqli_query(OURDB->conn, $query);
   }

   static function get_cart($userid)
   {
      $query = sprintf("SELECT * from cart where user_id = '%d' limit 1;",
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

   static function create_cart($userid)
   {
      $query = sprintf("INSERT into `cart` (`id`, `user_id`, `created_on`) VALUES (NULL, '%d', NOW());",
		       mysqli_real_escape_string(OURDB->conn, $userid));
      if (mysqli_query(OURDB->conn, $query))
      {
	 return mysqli_insert_id(OURDB->conn);
      }
      else
      {
	 return False;
      }      
   }

   static function add_to_cart($cartid, $picid)
   {
      $query = sprintf("INSERT into `cart_items` (`id`, `cart_id`, `picture_id`) VALUES (NULL, '%d', '%d');",
		       mysqli_real_escape_string(OURDB->conn, $cartid),
		       mysqli_real_escape_string(OURDB->conn, $picid));
      if (mysqli_query(OURDB->conn, $query))
      {
	 return mysqli_insert_id(OURDB->conn);
      }
      else
      {
	 return False;
      }      
   }


   static function delete_from_cart($cartid, $picid)
   {
      $query = sprintf("DELETE from `cart_items` where cart_id = '%d' and picture_id = '%d' limit 1;",
		       mysqli_real_escape_string(OURDB->conn, $cartid),
		       mysqli_real_escape_string(OURDB->conn, $picid));
      if (mysqli_query(OURDB->conn, $query))
      {
	 return True;
      }
      else
      {
	 return False;
      }      
   }

   static function cart_items($cartid)
   {
      $query = sprintf("SELECT * from cart_items, pictures where cart_items.cart_id = '%d' and cart_items.picture_id = pictures.id;",
		       mysqli_real_escape_string(OURDB->conn, $cartid));
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
	 return False;
      }      
   }

   static function cart_coupons($cartid)
   {
      $query = sprintf("SELECT coupons.code, coupons.discount from cart_coupons, coupons where cart_coupons.cart_id = '%d' and cart_coupons.coupon_id = coupons.id;",
		       mysqli_real_escape_string(OURDB->conn, $cartid));
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
	 return False;
      }      
   }

   static function cart_total($cartid)
   {
      $coupons = Cart::cart_coupons($cartid);
      $query = sprintf("SELECT SUM(pictures.price) from cart_items, pictures where cart_items.cart_id = '%d' and cart_items.picture_id = pictures.id;",
		       mysqli_real_escape_string(OURDB->conn, $cartid));
      $res = mysqli_query(OURDB->conn, $query);
      if ($res)
      {
	 $row = mysqli_fetch_row($res);
	 $sum = $row[0];	 
	 if ($coupons)
	 {
	    foreach ($coupons as $coupon)
	    {
	       $sum *= $coupon['discount'] / 100.0;
	    }
	 }
	 return $sum;
      }
      else
      {
	 return False;
      }      		       
   }

   static function purchase($cart)
   {
      mysqli_query(OURDB->conn, "BEGIN;");
      $items = Cart::cart_items($cart['id']);
      $coupons = Cart::cart_coupons($cartid);
      $sum = 0;
      foreach ($items as $item)
      {
	 $sum += $item['price'];
	 $query = sprintf("INSERT into `own` (`id`, `user_id`, `picture_id`) VALUES (NULL, '%d', '%d');",
			  mysqli_real_escape_string(OURDB->conn, $cart['user_id']),
			  mysqli_real_escape_string(OURDB->conn, $item['picture_id']));	 
	 if (!mysqli_query(OURDB->conn, $query))
	 {
	    mysqli_query(OURDB->conn, "ROLLBACK;");
	    return False;
	 }
	 $query = sprintf("UPDATE `users` set tradebux = tradebux + %d where id = '%d' limit 1;",
			  mysqli_real_escape_string(OURDB->conn, $item['price']),
			  mysqli_real_escape_string(OURDB->conn, $item['user_id']));
	 if (!mysqli_query(OURDB->conn, $query))
	 {
	    mysqli_query(OURDB->conn, "ROLLBACK;");
	    return False;
	 }
      }
      if ($coupons)
      {
	 foreach ($coupons as $coupon)
	 {
	    $sum *= $coupon['discount'] / 100.0;
	 }
      }
      
      $query = sprintf("UPDATE `users` set tradebux = tradebux - %d where id = '%d' limit 1;",
		       mysqli_real_escape_string(OURDB->conn, $sum),
		       mysqli_real_escape_string(OURDB->conn, $cart['user_id']));
      if (!mysqli_query(OURDB->conn, $query))
      {
	 mysqli_query(OURDB->conn, "ROLLBACK;");
	 return False;
      }
      
      if (!Cart::delete_cart($cart['id'], True))
      {
	 mysqli_query(OURDB->conn, "ROLLBACK;");
	 return False;
      }
      
      mysqli_query(OURDB->conn, "COMMIT;");
      return True;
   }

   static function delete_cart($cartid, $in_transaction)
   {
      if (!$in_transaction)
      {
	 mysqli_query(OURDB->conn, "BEGIN;");
      }
      $query = sprintf("DELETE from cart_items where cart_items.cart_id = '%d'",
		       mysqli_real_escape_string(OURDB->conn, $cartid));
      if (!mysqli_query(OURDB->conn, $query))
      {
	 if (!$in_transaction)
	 {
	    mysqli_query(OURDB->conn, "ROLLBACK;");
	 }
	 return False;
      }
      $query = sprintf("DELETE from cart where id = '%d' limit 1;",
		       mysqli_real_escape_string(OURDB->conn, $cartid));
      if (!mysqli_query(OURDB->conn, $query))
      {
	 if (!$in_transaction)
	 {
	    mysqli_query(OURDB->conn, "ROLLBACK;");
	 }
	 return False;
      }
      
      if (!$in_transaction)
      {
	 mysqli_query(OURDB->conn, "COMMIT;");
      }
      return True;
   }
}
?>