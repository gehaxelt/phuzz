<?php
/***
*
*Found actions: 13
*Found functions:13
*Extracted functions:13
*Total parameter names extracted: 7
*Overview: {'ajax_current_load': {'blc_current_load'}, 'ajax_dismiss': {'blc_dismiss'}, 'ajax_full_status': {'blc_full_status'}, 'ajax_edit': {'blc_edit'}, 'ajax_unlink': {'blc_unlink'}, 'ajax_link_details': {'blc_link_details'}, 'ajax_recheck': {'blc_recheck'}, 'dismiss_multisite_notification': {'wpmudev_blc_multisite_notification_dismiss'}, 'ajax_discard': {'blc_discard'}, 'ajax_undismiss': {'blc_undismiss'}, 'ajax_deredirect': {'blc_deredirect'}, 'ajax_work': {'blc_work'}, 'ajax_dashboard_status': {'blc_dashboard_status'}}
*
***/

/** Function ajax_current_load() called by wp_ajax hooks: {'blc_current_load'} **/
/** No params detected :-/ **/


/** Function ajax_dismiss() called by wp_ajax hooks: {'blc_dismiss'} **/
/** No params detected :-/ **/


/** Function ajax_full_status() called by wp_ajax hooks: {'blc_full_status'} **/
/** No params detected :-/ **/


/** Function ajax_edit() called by wp_ajax hooks: {'blc_edit'} **/
/** Parameters found in function ajax_edit(): {"post": ["link_id", "new_url", "new_text"]} **/
function ajax_edit() {
			if ( ! current_user_can( 'edit_others_posts' ) || ! check_ajax_referer( 'blc_edit', false, false ) ) {
				die(
				json_encode(
					array(
						'error' => __( "You're not allowed to do that!", 'broken-link-checker' ),
					)
				)
				);
			}

			if ( empty( $_POST['link_id'] ) || empty( $_POST['new_url'] ) || ! is_numeric( $_POST['link_id'] ) ) {
				die(
				json_encode(
					array(
						'error' => __( 'Error : link_id or new_url not specified', 'broken-link-checker' ),
					)
				)
				);
			}

			//Load the link
			$link = new blcLink( intval( $_POST['link_id'] ) );

			if ( ! $link->valid() ) {
				die(
				json_encode(
					array(
						'error' => sprintf( __( "Oops, I can't find the link %d", 'broken-link-checker' ), intval( $_POST['link_id'] ) ),
					)
				)
				);
			}

			//Validate the new URL.
			$new_url = stripslashes( $_POST['new_url'] );
			$parsed  = @parse_url( $new_url );
			if ( ! $parsed ) {
				die(
				json_encode(
					array(
						'error' => __( 'Oops, the new URL is invalid!', 'broken-link-checker' ),
					)
				)
				);
			}

			if ( ! current_user_can( 'unfiltered_html' ) ) {
				//Disallow potentially dangerous URLs like "javascript:...".
				$protocols         = wp_allowed_protocols();
				$good_protocol_url = wp_kses_bad_protocol( $new_url, $protocols );
				if ( $new_url != $good_protocol_url ) {
					die(
					json_encode(
						array(
							'error' => __( 'Oops, the new URL is invalid!', 'broken-link-checker' ),
						)
					)
					);
				}
			}

			$new_text = ( isset( $_POST['new_text'] ) && is_string( $_POST['new_text'] ) ) ? stripslashes( $_POST['new_text'] ) : null;
			if ( '' === $new_text ) {
				$new_text = null;
			}
			if ( ! empty( $new_text ) && ! current_user_can( 'unfiltered_html' ) ) {
				$new_text = stripslashes( wp_filter_post_kses( addslashes( $new_text ) ) ); //wp_filter_post_kses expects slashed data.
			}

			$rez = $link->edit( $new_url, $new_text );
			if ( false === $rez ) {
				die(
				json_encode(
					array(
						'error' => __( 'An unexpected error occurred!', 'broken-link-checker' ),
					)
				)
				);
			} else {
				$new_link = $rez['new_link'];
				/** @var blcLink $new_link */
				$new_status   = $new_link->analyse_status();
				$ui_link_text = null;
				if ( isset( $new_text ) ) {
					$instances = $new_link->get_instances();
					if ( ! empty( $instances ) ) {
						$first_instance = reset( $instances );
						$ui_link_text   = $first_instance->ui_get_link_text();
					}
				}

				$response = array(
					'new_link_id' => $rez['new_link_id'],
					'cnt_okay'    => $rez['cnt_okay'],
					'cnt_error'   => $rez['cnt_error'],

					'status_text'    => $new_status['text'],
					'status_code'    => $new_status['code'],
					'http_code'      => empty( $new_link->http_code ) ? '' : $new_link->http_code,
					'redirect_count' => $new_link->redirect_count,

					'url'          => $new_link->url,
					'escaped_url'  => esc_url_raw( $new_link->url ),
					'final_url'    => $new_link->final_url,
					'link_text'    => isset( $new_text ) ? $new_text : null,
					'ui_link_text' => isset( $new_text ) ? $ui_link_text : null,

					'errors' => array(),
				);
				//url, status text, status code, link text, editable link text

				foreach ( $rez['errors'] as $error ) {
					/** @var $error WP_Error */
					array_push( $response['errors'], implode( ', ', $error->get_error_messages() ) );
				}
				die( json_encode( $response ) );
			}
		}


/** Function ajax_unlink() called by wp_ajax hooks: {'blc_unlink'} **/
/** Parameters found in function ajax_unlink(): {"post": ["link_id"]} **/
function ajax_unlink() {
			if ( ! current_user_can( 'edit_others_posts' ) || ! check_ajax_referer( 'blc_unlink', false, false ) ) {
				die(
				json_encode(
					array(
						'error' => __( "You're not allowed to do that!", 'broken-link-checker' ),
					)
				)
				);
			}

			if ( isset( $_POST['link_id'] ) ) {
				//Load the link
				$link = new blcLink( intval( $_POST['link_id'] ) );

				if ( ! $link->valid() ) {
					die(
					json_encode(
						array(
							'error' => sprintf( __( "Oops, I can't find the link %d", 'broken-link-checker' ), intval( $_POST['link_id'] ) ),
						)
					)
					);
				}

				//Try and unlink it
				$rez = $link->unlink();

				if ( false === $rez ) {
					die(
					json_encode(
						array(
							'error' => __( 'An unexpected error occured!', 'broken-link-checker' ),
						)
					)
					);
				} else {
					$response = array(
						'cnt_okay'  => $rez['cnt_okay'],
						'cnt_error' => $rez['cnt_error'],
						'errors'    => array(),
					);
					foreach ( $rez['errors'] as $error ) {
						/** @var WP_Error $error */
						array_push( $response['errors'], implode( ', ', $error->get_error_messages() ) );
					}

					die( json_encode( $response ) );
				}
			} else {
				die(
				json_encode(
					array(
						'error' => __( 'Error : link_id not specified', 'broken-link-checker' ),
					)
				)
				);
			}
		}


/** Function ajax_link_details() called by wp_ajax hooks: {'blc_link_details'} **/
/** Parameters found in function ajax_link_details(): {"get": ["link_id"], "post": ["link_id"]} **/
function ajax_link_details() {
			global $wpdb;
			/* @var wpdb $wpdb */

			if ( ! current_user_can( 'edit_others_posts' ) ) {
				die( __( "You don't have sufficient privileges to access this information!", 'broken-link-checker' ) );
			}

			//FB::log("Loading link details via AJAX");

			if ( isset( $_GET['link_id'] ) ) {
				//FB::info("Link ID found in GET");
				$link_id = intval( $_GET['link_id'] );
			} elseif ( isset( $_POST['link_id'] ) ) {
				//FB::info("Link ID found in POST");
				$link_id = intval( $_POST['link_id'] );
			} else {
				//FB::error('Link ID not specified, you hacking bastard.');
				die( __( 'Error : link ID not specified', 'broken-link-checker' ) );
			}

			//Load the link.
			$link = new blcLink( $link_id );

			if ( ! $link->is_new ) {
				//FB::info($link, 'Link loaded');
				if ( ! class_exists( 'blcTablePrinter' ) ) {
					require dirname( $this->loader ) . '/includes/admin/table-printer.php';
				}
				blcTablePrinter::details_row_contents( $link );
				die();
			} else {
				printf( __( 'Failed to load link details (%s)', 'broken-link-checker' ), $wpdb->last_error );
				die();
			}
		}


/** Function ajax_recheck() called by wp_ajax hooks: {'blc_recheck'} **/
/** Parameters found in function ajax_recheck(): {"post": ["link_id"]} **/
function ajax_recheck() {
			if ( ! current_user_can( 'edit_others_posts' ) || ! check_ajax_referer( 'blc_recheck', false, false ) ) {
				die(
				json_encode(
					array(
						'error' => __( "You're not allowed to do that!", 'broken-link-checker' ),
					)
				)
				);
			}

			if ( ! isset( $_POST['link_id'] ) || ! is_numeric( $_POST['link_id'] ) ) {
				die(
				json_encode(
					array(
						'error' => __( 'Error : link_id not specified', 'broken-link-checker' ),
					)
				)
				);
			}

			$id   = intval( $_POST['link_id'] );
			$link = new blcLink( $id );

			if ( ! $link->valid() ) {
				die(
				json_encode(
					array(
						'error' => sprintf( __( "Oops, I can't find the link %d", 'broken-link-checker' ), $id ),
					)
				)
				);
			}

			$transactionManager = TransactionManager::getInstance();
			$transactionManager->start();

			//In case the immediate check fails, this will ensure the link is checked during the next work() run.
			$link->last_check_attempt  = 0;
			$link->isOptionLinkChanged = true;
			$link->save();

			//Check the link and save the results.
			$link->check( true );

			$transactionManager->commit();

			$status   = $link->analyse_status();
			$response = array(
				'status_text'    => $status['text'],
				'status_code'    => $status['code'],
				'http_code'      => empty( $link->http_code ) ? '' : $link->http_code,
				'redirect_count' => $link->redirect_count,
				'final_url'      => $link->final_url,
			);

			die( json_encode( $response ) );
		}


/** Function dismiss_multisite_notification() called by wp_ajax hooks: {'wpmudev_blc_multisite_notification_dismiss'} **/
/** No params detected :-/ **/


/** Function ajax_discard() called by wp_ajax hooks: {'blc_discard'} **/
/** Parameters found in function ajax_discard(): {"post": ["link_id"]} **/
function ajax_discard() {
			if ( ! current_user_can( 'edit_others_posts' ) || ! check_ajax_referer( 'blc_discard', false, false ) ) {
				die( __( "You're not allowed to do that!", 'broken-link-checker' ) );
			}

			if ( isset( $_POST['link_id'] ) ) {
				//Load the link
				$link = new blcLink( intval( $_POST['link_id'] ) );

				if ( ! $link->valid() ) {
					printf( __( "Oops, I can't find the link %d", 'broken-link-checker' ), intval( $_POST['link_id'] ) );
					die();
				}
				//Make it appear "not broken"
				$link->broken             = false;
				$link->warning            = false;
				$link->false_positive     = true;
				$link->last_check_attempt = time();
				$link->log                = __( 'This link was manually marked as working by the user.', 'broken-link-checker' );

				$link->isOptionLinkChanged = true;

				$transactionManager = TransactionManager::getInstance();
				$transactionManager->start();

				//Save the changes
				if ( $link->save() ) {
					$transactionManager->commit();
					die( 'OK' );
				} else {
					die( __( "Oops, couldn't modify the link!", 'broken-link-checker' ) );
				}
			} else {
				die( __( 'Error : link_id not specified', 'broken-link-checker' ) );
			}
		}


/** Function ajax_undismiss() called by wp_ajax hooks: {'blc_undismiss'} **/
/** No params detected :-/ **/


/** Function ajax_deredirect() called by wp_ajax hooks: {'blc_deredirect'} **/
/** Parameters found in function ajax_deredirect(): {"post": ["link_id"]} **/
function ajax_deredirect() {
			if ( ! current_user_can( 'edit_others_posts' ) || ! check_ajax_referer( 'blc_deredirect', false, false ) ) {
				die(
				json_encode(
					array(
						'error' => __( "You're not allowed to do that!", 'broken-link-checker' ),
					)
				)
				);
			}

			if ( ! isset( $_POST['link_id'] ) || ! is_numeric( $_POST['link_id'] ) ) {
				die(
				json_encode(
					array(
						'error' => __( 'Error : link_id not specified', 'broken-link-checker' ),
					)
				)
				);
			}

			$id   = intval( $_POST['link_id'] );
			$link = new blcLink( $id );

			if ( ! $link->valid() ) {
				die(
				json_encode(
					array(
						'error' => sprintf( __( "Oops, I can't find the link %d", 'broken-link-checker' ), $id ),
					)
				)
				);
			}

			//The actual task is simple; it's error handling that's complicated.
			$result = $link->deredirect();
			if ( is_wp_error( $result ) ) {
				die(
				json_encode(
					array(
						'error' => sprintf( '%s [%s]', $result->get_error_message(), $result->get_error_code() ),
					)
				)
				);
			}

			$link = $result['new_link'];
			/** @var blcLink $link */

			$status   = $link->analyse_status();
			$response = array(
				'url'         => $link->url,
				'escaped_url' => esc_url_raw( $link->url ),
				'new_link_id' => $result['new_link_id'],

				'status_text'    => $status['text'],
				'status_code'    => $status['code'],
				'http_code'      => empty( $link->http_code ) ? '' : $link->http_code,
				'redirect_count' => $link->redirect_count,
				'final_url'      => $link->final_url,

				'cnt_okay'  => $result['cnt_okay'],
				'cnt_error' => $result['cnt_error'],
				'errors'    => array(),
			);

			//Convert WP_Error's to simple strings.
			if ( ! empty( $result['errors'] ) ) {
				foreach ( $result['errors'] as $error ) {
					/** @var WP_Error $error */
					$response['errors'][] = $error->get_error_message();
				}
			}

			die( json_encode( $response ) );
		}


/** Function ajax_work() called by wp_ajax hooks: {'blc_work'} **/
/** No params detected :-/ **/


/** Function ajax_dashboard_status() called by wp_ajax hooks: {'blc_dashboard_status'} **/
/** No params detected :-/ **/


