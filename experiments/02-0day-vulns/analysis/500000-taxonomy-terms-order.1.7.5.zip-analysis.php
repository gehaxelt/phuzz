<?php
/***
*
*Found actions: 1
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'TO_saveAjaxOrder': {'update-taxonomy-order'}}
*
***/

/** Function TO_saveAjaxOrder() called by wp_ajax hooks: {'update-taxonomy-order'} **/
/** Parameters found in function TO_saveAjaxOrder(): {"post": ["nonce", "order"]} **/
function TO_saveAjaxOrder()
        {
            global $wpdb;
            
            if  ( ! wp_verify_nonce( $_POST['nonce'], 'update-taxonomy-order' ) )
                die();
             
            $data               = stripslashes($_POST['order']);
            $unserialised_data  = json_decode($data, TRUE);
                    
            if (is_array($unserialised_data))
            foreach($unserialised_data as $key => $values ) 
                {
                    //$key_parent = str_replace("item_", "", $key);
                    $items = explode("&", $values);
                    unset($item);
                    foreach ($items as $item_key => $item_)
                        {
                            $items[$item_key] = trim(str_replace("item[]=", "",$item_));
                        }
                    
                    if (is_array($items) && count($items) > 0)
                        {
                            foreach( $items as $item_key => $term_id ) 
                                {
                                    $wpdb->update( $wpdb->terms, array('term_order' => ($item_key + 1)), array('term_id' => $term_id) );
                                }
                            clean_term_cache($items);
                        } 
                }
                
            do_action('tto/update-order');
                
            die();
        }


