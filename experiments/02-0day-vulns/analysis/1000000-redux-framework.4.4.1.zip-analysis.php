<?php
/***
*
*Found actions: 7
*Found functions:5
*Extracted functions:5
*Total parameter names extracted: 3
*Overview: {'timer': {'redux_custom_font_timer'}, 'redux_delete_widget_area_area': {'redux_delete_widget_area'}, 'ajax': {'redux_custom_fonts', 'redux_hide_admin_notice', 'redux_submit_support_data'}, 'admin_ajax': {'redux_activation'}, 'google_fonts_update': {'redux_update_google_fonts'}}
*
***/

/** Function timer() called by wp_ajax hooks: {'redux_custom_font_timer'} **/
/** No params detected :-/ **/


/** Function redux_delete_widget_area_area() called by wp_ajax hooks: {'redux_delete_widget_area'} **/
/** Parameters found in function redux_delete_widget_area_area(): {"post": ["_wpnonce", "name"]} **/
function redux_delete_widget_area_area() {
			if ( isset( $_POST ) && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'delete-redux-widget_area-nonce' ) ) {
				if ( isset( $_POST['name'] ) && ! empty( sanitize_text_field( wp_unslash( $_POST['name'] ) ) ) ) {
					$name               = sanitize_text_field( wp_unslash( $_POST['name'] ) );
					$this->widget_areas = $this->get_widget_areas();
					$key                = array_search( $name, $this->widget_areas, true );

					if ( $key >= 0 ) {
						unset( $this->widget_areas[ $key ] );
						$this->save_widget_areas();
					}

					echo 'widget_area-deleted';
				}
			}

			die();
		}


/** Function ajax() called by wp_ajax hooks: {'redux_custom_fonts', 'redux_hide_admin_notice', 'redux_submit_support_data'} **/
/** Parameters found in function ajax(): {"post": ["id", "nonce"]} **/
function ajax() {
			global $current_user;

			if ( isset( $_POST['id'] ) ) {
				// Get the notice id.
				$id = explode( '&', sanitize_text_field( wp_unslash( $_POST['id'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
				$id = $id[0];

				// Get the user id.
				$userid = $current_user->ID;

				if ( ! isset( $_POST['nonce'] ) || ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), $id . $userid . 'nonce' ) ) ) {
					die( 0 );
				} else {
					// Add the dismiss request to the user meta.
					update_user_meta( $userid, 'ignore_' . $id, true );
				}
			}
		}


/** Function admin_ajax() called by wp_ajax hooks: {'redux_activation'} **/
/** Parameters found in function admin_ajax(): {"request": ["nonce", "activate"]} **/
function admin_ajax() {

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';

			if ( empty( $nonce ) || ! wp_verify_nonce( $nonce, $this->nonce ) ) {
				die( __( 'Security check failed.', 'redux-framework' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}

			if ( 'false' === $_REQUEST['activate'] ) {
				echo wp_json_encode(
					array(
						'type' => 'close',
						'msg'  => '',
					)
				);

				update_option( 'redux-framework_extendify_plugin_notice', 'hide' );

				die();
			}

			$res = $this->install_extendify();

			if ( true === $res ) {
				update_option( 'redux-framework_extendify_plugin_notice', 'hide' );
			}

			die();
		}


/** Function google_fonts_update() called by wp_ajax hooks: {'redux_update_google_fonts'} **/
/** No params detected :-/ **/


