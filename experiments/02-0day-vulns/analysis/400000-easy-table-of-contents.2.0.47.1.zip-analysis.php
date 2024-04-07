<?php
/***
*
*Found actions: 5
*Found functions:4
*Extracted functions:3
*Total parameter names extracted: 3
*Overview: {'ezTOC_Option': {'eztoc_reset_options_to_default'}, 'eztoc_send_query_message': {'eztoc_send_query_message'}, 'eztoc_send_feedback': {'eztoc_send_feedback'}, 'eztoc_subscribe_for_newsletter': {'eztoc_subscribe_newsletter', 'nopriv_eztoc_subscribe_newsletter'}}
*
***/

/** Function ezTOC_Option() called by wp_ajax hooks: {'eztoc_reset_options_to_default'} **/
/** No function found :-/ **/


/** Function eztoc_send_query_message() called by wp_ajax hooks: {'eztoc_send_query_message'} **/
/** Parameters found in function eztoc_send_query_message(): {"post": ["eztoc_security_nonce", "message", "email"]} **/
function eztoc_send_query_message(){   
		    
		        if ( ! isset( $_POST['eztoc_security_nonce'] ) ){
		           return; 
		        }
		        if ( !wp_verify_nonce( $_POST['eztoc_security_nonce'], 'eztoc_ajax_check_nonce' ) ){
		           return;  
		        }   
		        $message        = $this->eztoc_sanitize_textarea_field($_POST['message']); 
		        $email          = sanitize_email($_POST['email']);
		                                
		        if(function_exists('wp_get_current_user')){

		            $user           = wp_get_current_user();

		         
		            $message = '<p>'.$message.'</p><br><br>'.'Query from Easy Table of Content plugin support tab';
		            
		            $user_data  = $user->data;        
		            $user_email = $user_data->user_email;     
		            
		            if($email){
		                $user_email = $email;
		            }            
		            //php mailer variables        
		            $sendto    = 'team@magazine3.in';
		            $subject   = "Easy Table of Content Query";
		            
		            $headers[] = 'Content-Type: text/html; charset=UTF-8';
		            $headers[] = 'From: '. esc_attr($user_email);            
		            $headers[] = 'Reply-To: ' . esc_attr($user_email);
		            // Load WP components, no themes.   

		            $sent = wp_mail($sendto, $subject, $message, $headers); 

		            if($sent){

		                 echo json_encode(array('status'=>'t'));  

		            }else{

		                echo json_encode(array('status'=>'f'));            

		            }
		            
		        }
		                        
		        wp_die();           
		}


/** Function eztoc_send_feedback() called by wp_ajax hooks: {'eztoc_send_feedback'} **/
/** Parameters found in function eztoc_send_feedback(): {"post": ["data"]} **/
function eztoc_send_feedback() {

    if( isset( $_POST['data'] ) ) {
        parse_str( $_POST['data'], $form );
    }
    
    if( !isset( $form['eztoc_security_nonce'] ) || isset( $form['eztoc_security_nonce'] ) && !wp_verify_nonce( sanitize_text_field( $form['eztoc_security_nonce'] ), 'eztoc_ajax_check_nonce' ) ) {
        echo 'security_nonce_not_verified';
        die();
    }
    
    $text = '';
    if( isset( $form['eztoc_disable_text'] ) ) {
        $text = implode( "\n\r", $form['eztoc_disable_text'] );
    }

    $headers = array();

    $from = isset( $form['eztoc_disable_from'] ) ? $form['eztoc_disable_from'] : '';
    if( $from ) {
        $headers[] = "From: $from";
        $headers[] = "Reply-To: $from";
    }

    $subject = isset( $form['eztoc_disable_reason'] ) ? $form['eztoc_disable_reason'] : '(no reason given)';

    if($subject == 'technical issue'){

          $text = trim($text);

          if(!empty($text)){

            $text = 'technical issue description: '.$text;

          }else{

            $text = 'no description: '.$text;
          }
      
    }

    $success = wp_mail( 'team@magazine3.in', $subject, $text, $headers );
    
    echo 'sent';
    die();
}


/** Function eztoc_subscribe_for_newsletter() called by wp_ajax hooks: {'eztoc_subscribe_newsletter', 'nopriv_eztoc_subscribe_newsletter'} **/
/** Parameters found in function eztoc_subscribe_for_newsletter(): {"post": ["eztoc_security_nonce", "name", "email", "website"]} **/
function eztoc_subscribe_for_newsletter(){
    if( !wp_verify_nonce( sanitize_text_field( $_POST['eztoc_security_nonce'] ), 'eztoc_ajax_check_nonce' ) ) {
        echo 'security_nonce_not_verified';
        die();
    }
    $api_url = 'http://magazine3.company/wp-json/api/central/email/subscribe';
    $api_params = array(
        'name' => sanitize_text_field($_POST['name']),
        'email'=> sanitize_text_field($_POST['email']),
        'website'=> sanitize_text_field($_POST['website']),
        'type'=> 'etoc'
    );
    $response = wp_remote_post( $api_url, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );
    $response = wp_remote_retrieve_body( $response );
    echo $response;
    die;
}


