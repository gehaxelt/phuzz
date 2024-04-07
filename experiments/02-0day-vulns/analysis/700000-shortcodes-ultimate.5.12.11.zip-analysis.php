<?php
/***
*
*Found actions: 10
*Found functions:10
*Extracted functions:9
*Total parameter names extracted: 7
*Overview: {'preview': {'su_generator_preview'}, 'ajax_get_taxonomies': {'su_generator_get_taxonomies'}, 'ajax_get_preset': {'su_generator_get_preset'}, 'Freemius': {'fs_toggle_debug_mode'}, 'ajax_get_icons': {'su_generator_get_icons'}, 'settings': {'su_generator_settings'}, 'ajax_remove_preset': {'su_generator_remove_preset'}, 'dismiss_notice_ajax_callback': {'fs_dismiss_notice_action_{$ajax_action_suffix}'}, 'ajax_get_terms': {'su_generator_get_terms'}, 'ajax_add_preset': {'su_generator_add_preset'}}
*
***/

/** Function preview() called by wp_ajax hooks: {'su_generator_preview'} **/
/** Parameters found in function preview(): {"post": ["shortcode"]} **/
function preview() {
		// Check authentication
		self::access();
		// Output results
		do_action( 'su/generator/preview/before' );
		echo '<h5>' . __( 'Preview', 'shortcodes-ultimate' ) . '</h5>';
		echo do_shortcode( wp_kses_post( wp_unslash( $_POST['shortcode'] ) ) );
		echo '<div style="clear:both"></div>';
		do_action( 'su/generator/preview/after' );
		die();
	}


/** Function ajax_get_taxonomies() called by wp_ajax hooks: {'su_generator_get_taxonomies'} **/
/** No params detected :-/ **/


/** Function ajax_get_preset() called by wp_ajax hooks: {'su_generator_get_preset'} **/
/** Parameters found in function ajax_get_preset(): {"get": ["id", "shortcode", "nonce"]} **/
function ajax_get_preset() {
		self::access();
		// Check incoming data
		if ( empty( $_GET['id'] ) ) return;
		if ( empty( $_GET['shortcode'] ) ) return;
		// Check Nonce
		if (
			empty( $_GET['nonce'] ) ||
			! is_string( $_GET['nonce'] ) ||
			! wp_verify_nonce( $_GET['nonce'], 'su_generator_preset' )
		) {
			return;
		}
		// Clean-up incoming data
		$id = sanitize_key( $_GET['id'] );
		$shortcode = sanitize_key( $_GET['shortcode'] );
		// Default data
		$data = array();
		// Get the existing presets
		$presets = get_option( 'su_presets_' . $shortcode );
		// Check that preset is exists
		if ( is_array( $presets ) && isset( $presets[$id]['settings'] ) ) $data = $presets[$id]['settings'];
		// Print results
		die( json_encode( $data ) );
	}


/** Function Freemius() called by wp_ajax hooks: {'fs_toggle_debug_mode'} **/
/** No function found :-/ **/


/** Function ajax_get_icons() called by wp_ajax hooks: {'su_generator_get_icons'} **/
/** No params detected :-/ **/


/** Function settings() called by wp_ajax hooks: {'su_generator_settings'} **/
/** Parameters found in function settings(): {"request": ["shortcode"]} **/
function settings() {
		self::access();
		// Param check
		if ( empty( $_REQUEST['shortcode'] ) ) wp_die( __( 'Shortcode not specified', 'shortcodes-ultimate' ) );
		// Request queried shortcode
		$shortcode = su_get_shortcode( sanitize_key( $_REQUEST['shortcode'] ) );
		// Call custom callback
		if (
			isset( $shortcode['generator_callback'] ) &&
			is_callable( $shortcode['generator_callback'] )
		) {
			call_user_func( $shortcode['generator_callback'], $shortcode );
			exit;
		}
		// Prepare skip-if-default option
		$skip = ( get_option( 'su_option_skip' ) === 'on' ) ? ' su-generator-skip' : '';
		// Prepare actions
		$actions = apply_filters( 'su/generator/actions', array(
				'insert' => '<a href="javascript:void(0);" class="button button-primary button-large su-generator-insert"><i class="sui sui-check"></i> ' . __( 'Insert shortcode', 'shortcodes-ultimate' ) . '</a>',
				'preview' => '<a href="javascript:void(0);" class="button button-large su-generator-toggle-preview"><i class="sui sui-eye"></i> ' . __( 'Live preview', 'shortcodes-ultimate' ) . '</a>'
			) );
		// Shortcode header
		$return = '<div id="su-generator-breadcrumbs">';
		$return .= apply_filters( 'su/generator/breadcrumbs', '<a href="javascript:void(0);" class="su-generator-home" title="' . __( 'Click to return to the shortcodes list', 'shortcodes-ultimate' ) . '">' . __( 'All shortcodes', 'shortcodes-ultimate' ) . '</a> &rarr; <span>' . $shortcode['name'] . '</span> <small class="alignright">' . $shortcode['desc'] . '</small><div class="su-generator-clear"></div>' );
		$return .= '</div>';
		// Shortcode note
		if ( isset( $shortcode['note'] ) ) {
			$return .= '<div class="su-generator-note"><i class="sui sui-info-circle"></i><div class="su-generator-note-content">' . wpautop( $shortcode['note'] ) . '</div></div>';
		}
		// Shortcode CTA
		if ( isset( $shortcode['generator_cta'] ) ) {
			$return .= '<div class="su-generator-cta"><div class="su-generator-cta-content">' . $shortcode['generator_cta'] . '</div></div>';
		}
		// Shortcode has atts
		if ( isset( $shortcode['atts'] ) && count( $shortcode['atts'] ) ) {
			// Loop through shortcode parameters
			foreach ( $shortcode['atts'] as $attr_name => $attr_info ) {
				// Prepare default value
				$default = (string) ( isset( $attr_info['default'] ) ) ? $attr_info['default'] : '';
				$attr_info['name'] = ( isset( $attr_info['name'] ) ) ? $attr_info['name'] : $attr_name;
				$return .= '<div class="su-generator-attr-container' . $skip . '" data-default="' . esc_attr( $default ) . '">';
				$return .= '<h5>' . $attr_info['name'] . '</h5>';
				// Create field types
				if ( !isset( $attr_info['type'] ) && isset( $attr_info['values'] ) && is_array( $attr_info['values'] ) && count( $attr_info['values'] ) ) $attr_info['type'] = 'select';
				elseif ( !isset( $attr_info['type'] ) ) $attr_info['type'] = 'text';
				if ( is_callable( array( 'Su_Generator_Views', $attr_info['type'] ) ) ) $return .= call_user_func( array( 'Su_Generator_Views', $attr_info['type'] ), $attr_name, $attr_info );
				elseif ( isset( $attr_info['callback'] ) && is_callable( $attr_info['callback'] ) ) $return .= call_user_func( $attr_info['callback'], $attr_name, $attr_info );
				if ( isset( $attr_info['desc'] ) ) $return .= '<div class="su-generator-attr-desc">' . str_replace( array( '<b%value>', '<b_>' ), '<b class="su-generator-set-value" title="' . __( 'Click to set this value', 'shortcodes-ultimate' ) . '">', $attr_info['desc'] ) . '</div>';
				$return .= '</div>';
			}
		}
		// Single shortcode (not closed)
		if ( $shortcode['type'] == 'single' ) $return .= '<input type="hidden" name="su-generator-content" id="su-generator-content" value="false" />';
		// Wrapping shortcode
		else {

			if ( !isset( $shortcode['content'] ) ) {
				$shortcode['content'] = '';
			}

			if ( is_array( $shortcode['content'] ) ) {
				$shortcode['content'] = self::get_shortcode_code( $shortcode['content'] );
			}

			// Prepare shortcode content
			$return .= '<div class="su-generator-attr-container"><h5>' . __( 'Content', 'shortcodes-ultimate' ) . '</h5><textarea name="su-generator-content" id="su-generator-content" rows="5">' . esc_attr( str_replace( array( '%prefix_', '__' ), su_get_shortcode_prefix(), $shortcode['content'] ) ) . '</textarea></div>';
		}
		$return .= '<div id="su-generator-preview"></div>';
		$return .= '<div class="su-generator-actions su-generator-clearfix">' . implode( ' ', array_values( $actions ) ) . '</div>';
		set_transient( 'su/generator/settings/' . sanitize_text_field( $_REQUEST['shortcode'] ), $return, 2 * DAY_IN_SECONDS );
		echo $return;
		exit;
	}


/** Function ajax_remove_preset() called by wp_ajax hooks: {'su_generator_remove_preset'} **/
/** Parameters found in function ajax_remove_preset(): {"post": ["id", "shortcode", "nonce"]} **/
function ajax_remove_preset() {
		self::access();
		// Check incoming data
		if ( empty( $_POST['id'] ) ) return;
		if ( empty( $_POST['shortcode'] ) ) return;
		// Check Nonce
		if (
			empty( $_POST['nonce'] ) ||
			! is_string( $_POST['nonce'] ) ||
			! wp_verify_nonce( $_POST['nonce'], 'su_generator_preset' )
		) {
			return;
		}
		// Clean-up incoming data
		$id = sanitize_key( $_POST['id'] );
		$shortcode = sanitize_key( $_POST['shortcode'] );
		// Prepare option name
		$option = 'su_presets_' . $shortcode;
		// Get the existing presets
		$current = get_option( $option );
		// Check that preset is exists
		if ( !is_array( $current ) || empty( $current[$id] ) ) return;
		// Remove preset
		unset( $current[$id] );
		// Save updated option
		update_option( $option, $current );
		// Clear cache
		delete_transient( 'su/generator/settings/' . $shortcode );
	}


/** Function dismiss_notice_ajax_callback() called by wp_ajax hooks: {'fs_dismiss_notice_action_{$ajax_action_suffix}'} **/
/** Parameters found in function dismiss_notice_ajax_callback(): {"post": ["message_id"]} **/
function dismiss_notice_ajax_callback() {
            check_admin_referer( 'fs_dismiss_notice_action' );

            if ( ! is_numeric( $_POST['message_id'] ) ) {
                $this->_sticky_storage->remove( $_POST['message_id'] );
            }

            wp_die();
        }


/** Function ajax_get_terms() called by wp_ajax hooks: {'su_generator_get_terms'} **/
/** Parameters found in function ajax_get_terms(): {"request": ["tax", "class", "multiple", "size", "noselect"]} **/
function ajax_get_terms() {
		self::access();
		$args = array();
		if ( isset( $_REQUEST['tax'] ) ) $args['options'] = (array) self::get_terms( sanitize_key( $_REQUEST['tax'] ) );
		if ( isset( $_REQUEST['class'] ) ) $args['class'] = (string) sanitize_key( $_REQUEST['class'] );
		if ( isset( $_REQUEST['multiple'] ) ) $args['multiple'] = (bool) sanitize_key( $_REQUEST['multiple'] );
		if ( isset( $_REQUEST['size'] ) ) $args['size'] = (int) sanitize_key( $_REQUEST['size'] );
		if ( isset( $_REQUEST['noselect'] ) ) $args['noselect'] = (bool) sanitize_key( $_REQUEST['noselect'] );
		die( su_html_dropdown( $args ) );
	}


/** Function ajax_add_preset() called by wp_ajax hooks: {'su_generator_add_preset'} **/
/** Parameters found in function ajax_add_preset(): {"post": ["id", "name", "settings", "shortcode", "nonce"]} **/
function ajax_add_preset() {
		self::access();
		// Check incoming data
		if ( empty( $_POST['id'] ) ) return;
		if ( empty( $_POST['name'] ) ) return;
		if ( empty( $_POST['settings'] ) ) return;
		if ( empty( $_POST['shortcode'] ) ) return;
		// Check Nonce
		if (
			empty( $_POST['nonce'] ) ||
			! is_string( $_POST['nonce'] ) ||
			! wp_verify_nonce( $_POST['nonce'], 'su_generator_preset' )
		) {
			return;
		}
		// Clean-up incoming data
		$id = sanitize_key( $_POST['id'] );
		$name = sanitize_text_field( $_POST['name'] );
		$shortcode = sanitize_key( $_POST['shortcode'] );
		// Validate and sanitize settings
		$settings = is_array( $_POST['settings'] ) ? stripslashes_deep( $_POST['settings'] ) : array();
		$settings = array_map( 'wp_kses_post', $settings );
		// Prepare option name
		$option = 'su_presets_' . $shortcode;
		// Get the existing presets
		$current = get_option( $option );
		// Create array with new preset
		$new = array(
			'id'       => $id,
			'name'     => $name,
			'settings' => $settings
		);
		// Add new array to the option value
		if ( !is_array( $current ) ) $current = array();
		$current[$id] = $new;
		// Save updated option
		update_option( $option, $current );
		// Clear cache
		delete_transient( 'su/generator/settings/' . $shortcode );
	}


