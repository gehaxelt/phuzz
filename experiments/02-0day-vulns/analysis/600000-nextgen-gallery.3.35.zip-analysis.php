<?php
/***
*
*Found actions: 4
*Found functions:4
*Extracted functions:4
*Total parameter names extracted: 4
*Overview: {'createNewThumb': {'createNewThumb'}, 'ajax_set_post_thumbnail': {'ngg_set_post_thumbnail'}, 'ngg_ajax_operation': {'ngg_ajax_operation'}, 'ngg_rotateImage': {'rotateImage'}}
*
***/

/** Function createNewThumb() called by wp_ajax hooks: {'createNewThumb'} **/
/** Parameters found in function createNewThumb(): {"post": ["nonce", "id", "x", "rr", "y", "w", "h"]} **/
function createNewThumb() {

	// check for correct capability
	if ( !is_user_logged_in() )
		die('-1');

	// check for correct NextGEN capability
	if ( !current_user_can('NextGEN Manage gallery') )
		die('-1');

    if (!wp_verify_nonce($_POST['nonce'], 'ngg_update_thumbnail'))
        die('-1');

	$id = (int) $_POST['id'];

	$x = round( $_POST['x'] * $_POST['rr'], 0);
	$y = round( $_POST['y'] * $_POST['rr'], 0);
	$w = round( $_POST['w'] * $_POST['rr'], 0);
	$h = round( $_POST['h'] * $_POST['rr'], 0);
	$crop_frame = array('x' => $x, 'y' => $y, 'width' => $w, 'height' => $h);

	$storage = C_Gallery_Storage::get_instance();

	// XXX NextGEN Legacy wasn't handling watermarks or reflections at this stage, so we're forcefully disabling them to maintain compatibility
	$params = array('watermark' => false, 'reflection' => false, 'crop' => true, 'crop_frame' => $crop_frame);
	$result = $storage->generate_thumbnail($id, $params);

	if ($result) {
		echo "OK";
	} else {
		header('HTTP/1.1 500 Internal Server Error');
		echo "KO";
	}

	C_NextGEN_Bootstrap::shutdown();
}


/** Function ajax_set_post_thumbnail() called by wp_ajax hooks: {'ngg_set_post_thumbnail'} **/
/** Parameters found in function ajax_set_post_thumbnail(): {"post": ["nonce", "post_id", "thumbnail_id"]} **/
function ajax_set_post_thumbnail() 
	{
		// This function does the following:
		// 1) Check if the user is logged in and has permission to edit the post
		// 2) Get the thumbnail id from the POST request. The thumbnail id is actually the NGG image id
		// 3)]

		global $post_ID;

		// check for correct capability
		if ( !is_user_logged_in() )
			die( '-1' );

        if (!wp_verify_nonce($_POST['nonce'], 'ngg_set_post_thumbnails'))
            die('-1');

		// get the post id as global variable, otherwise the ajax_nonce failed later
		$post_ID = intval( $_POST['post_id'] );

		if ( !current_user_can( 'edit_post', $post_ID ) )
			die( '-1' );

		$thumbnail_id = intval( $_POST['thumbnail_id'] );

		// delete the image
		if ( $thumbnail_id == '-1' ) {
			delete_post_meta( $post_ID, '_thumbnail_id' );
			die('1');
		}

		if (($attachment_id = C_Gallery_Storage::get_instance()->set_post_thumbnail($post_ID, $thumbnail_id, TRUE))) {
			die(strval($attachment_id));
		}
		die(strval(0));
	}


/** Function ngg_ajax_operation() called by wp_ajax hooks: {'ngg_ajax_operation'} **/
/** Parameters found in function ngg_ajax_operation(): {"post": ["_wpnonce", "image", "operation"]} **/
function ngg_ajax_operation() {

	// if nonce is not correct it returns -1
	check_ajax_referer( "ngg-ajax" );

	// check for correct capability
	if ( !is_user_logged_in() )
		die('-1');

    if (!wp_verify_nonce($_POST['_wpnonce'], 'ngg-ajax'))
        die('-1');

	// check for correct NextGEN capability
	if ( !current_user_can('NextGEN Upload images') && !current_user_can('NextGEN Manage gallery') )
		die('-1');

	// include the ngg function
	include_once (dirname (__FILE__) . '/functions.php');

	// Get the image id
	if ( isset($_POST['image'])) {
		$id = (int) $_POST['image'];
		// let's get the image data
		$picture = nggdb::find_image( $id );
		// what do you want to do ?
		switch ( $_POST['operation'] ) {
			case 'create_thumbnail' :
				$result = nggAdmin::create_thumbnail($picture);
			break;
			case 'resize_image' :
				$result = nggAdmin::resize_image($picture);
			break;
			case 'rotate_cw' :
				$result = nggAdmin::rotate_image($picture, 'CW');
				nggAdmin::create_thumbnail($picture);
			break;
			case 'rotate_ccw' :
				$result = nggAdmin::rotate_image($picture, 'CCW');
				nggAdmin::create_thumbnail($picture);
			break;
			case 'set_watermark' :
				$result = nggAdmin::set_watermark($picture);
			break;
			case 'recover_image' :
				$result = nggAdmin::recover_image($id) ? '1': '0';
			break;
			case 'import_metadata' :
				$result = C_Image_Mapper::get_instance()->reimport_metadata($id) ? '1' : '0';
			break;
			case 'get_image_ids' :
				$result = nggAdmin::get_image_ids( $id );
                break;

            // This will read the EXIF and then write it with the Orientation tag reset
            case 'strip_orientation_tag':
                $storage = C_Gallery_Storage::get_instance();
                $image_path = $storage->get_image_abspath($id);
                $backup_path = $image_path . '_backup';
                $exif_abspath = @file_exists($backup_path) ? $backup_path : $image_path;
                $exif_iptc = @C_Exif_Writer::read_metadata($exif_abspath);
                foreach ($storage->get_image_sizes($id) as $size) {
                    if ($size === 'backup')
                        continue;
                    @C_Exif_Writer::write_metadata($storage->get_image_abspath($id, $size), $exif_iptc);
                }
                $result = '1';
                break;
			default :
				do_action( 'ngg_ajax_' . $_POST['operation'] );
				die('-1');
			break;
		}
		// A success should return a '1'
		die ($result);
	}

	// The script should never stop here
	die('0');
}


/** Function ngg_rotateImage() called by wp_ajax hooks: {'rotateImage'} **/
/** Parameters found in function ngg_rotateImage(): {"post": ["nonce", "id", "ra"]} **/
function ngg_rotateImage() {

	// check for correct capability
	if ( !is_user_logged_in() )
		die('-1');

    if (!wp_verify_nonce($_POST['nonce'], 'ngg-rotate-image'))
        die('-1');

	// check for correct NextGEN capability
	if ( !current_user_can('NextGEN Manage gallery') )
		die('-1');

	require_once( dirname( dirname(__FILE__) ) . '/ngg-config.php');

	// include the ngg function
	include_once (dirname (__FILE__). '/functions.php');

	$id = (int) $_POST['id'];
	$result = '-1';

	switch ( $_POST['ra'] ) {
		case 'cw' :
			$result = nggAdmin::rotate_image($id, 'CW');
		break;
		case 'ccw' :
			$result = nggAdmin::rotate_image($id, 'CCW');
		break;
		case 'fv' :
			// Note: H/V have been inverted here to make it more intuitive
			$result = nggAdmin::rotate_image($id, 0, 'H');
		break;
		case 'fh' :
			// Note: H/V have been inverted here to make it more intuitive
			$result = nggAdmin::rotate_image($id, 0, 'V');
		break;
	}

    // recreate the thumbnail
    nggAdmin::create_thumbnail($id);

	if ( $result == 1 )
		die('1');

	header('HTTP/1.1 500 Internal Server Error');
	die( $result );
}


