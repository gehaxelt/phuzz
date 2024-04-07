<?php
/***
*
*Found actions: 61
*Found functions:55
*Extracted functions:50
*Total parameter names extracted: 26
*Overview: {'ajax_tracks': {'jetpack_tracks'}, 'regenerate_post_by_email_address': {'jetpack_post_by_email_regenerate'}, 'options_page_tumblr': {'publicize_tumblr_options_page'}, 'jetpack_debugger_sync_progress_ajax': {'jetpack_sync_progress_check'}, 'wp_ajax_videopress_get_upload_jwt': {'videopress-get-upload-jwt'}, 'wp_ajax_upsell_nudge_jitm': {'upsell_nudge_jitm'}, 'ajax_new_service': {'sharing_new_service'}, 'grunion_ajax_shortcode': {'grunion_shortcode'}, 'options_page_facebook': {'publicize_facebook_options_page'}, 'options_save_twitter': {'publicize_twitter_options_save'}, 'ajax_update_widget_token_id': {'wpcom_instagram_widget_update_widget_token_id'}, 'grunion_ajax_shortcode_to_json': {'grunion_shortcode_to_json'}, 'grunion_recheck_queue': {'grunion_recheck_queue'}, 'ajax_dismiss_handler': {'jetpack-protect-dismiss-multisite-banner'}, 'wp_ajax_update_transcoding_status': {'videopress-update-transcoding-status'}, 'test_publicize_conns': {'test_publicize_conns'}, 'ajax_delete_service': {'sharing_delete_service'}, 'handle_optout_request': {'nopriv_privacy_optout', 'privacy_optout'}, '\\accept_tos': {'jetpack_accept_tos'}, 'ajax_get_payment_buttons': {'customize-jetpack-simple-payments-buttons-get'}, 'upload': {'jetpack_comic_upload'}, 'jetpack_debugger_full_sync_start': {'jetpack_debugger_full_sync_start'}, 'options_save_linkedin': {'publicize_linkedin_options_save'}, 'grunion_display_form_view': {'grunion_form_builder'}, 'delete_post_by_email_address': {'jetpack_post_by_email_disable'}, 'ajax_check_api_key': {'customize-contact-info-api-key'}, 'remote_request_handlers': {'nopriv_{$action}'}, 'download_feedback_as_csv': {'feedback_export'}, 'wp_ajax_jitm_dismiss': {'jitm_dismiss'}, 'jetpack_connection_banner_callback': {'jetpack_connection_banner'}, 'grunion_ajax_spam': {'grunion_ajax_spam'}, 'ajax_delete_payment_button': {'customize-jetpack-simple-payments-button-delete'}, 'export_to_gdrive': {'grunion_export_to_gdrive'}, 'options_page_linkedin': {'publicize_linkedin_options_page'}, 'ajax_save_services': {'sharing_save_services'}, 'options_save_tumblr': {'publicize_tumblr_options_save'}, 'options_save_facebook': {'publicize_facebook_options_save'}, 'plugin_edit_ajax': {'edit-theme-plugin-file'}, 'Jetpack_Recommendations_Banner': {'jetpack_recommendations_banner'}, 'theme_edit_ajax': {'edit-theme-plugin-file'}, 'ajax_save_payment_button': {'customize-jetpack-simple-payments-button-save'}, 'options_page_twitter': {'publicize_twitter_options_page'}, 'post_attachment_comment': {'nopriv_post_attachment_comment', 'post_attachment_comment'}, 'test_gdrive_connection': {'grunion_gdrive_connection'}, 'grunion_delete_spam_feedbacks': {'jetpack_delete_spam_feedbacks'}, 'wp_ajax_videopress_get_upload_token': {'videopress-get-upload-token'}, 'create_post_by_email_address': {'jetpack_post_by_email_enable'}, 'jetpack_debugger_ajax_local_testing_suite': {'health-check-jetpack-local_testing_suite'}, 'ajax_sidebar_state': {'sidebar_state'}, 'ajax_request': {'grunion-contact-form', 'nopriv_grunion-contact-form'}, 'ajax_save_options': {'sharing_save_options'}, 'handle_optout_markup': {'nopriv_privacy_optout_markup', 'privacy_optout_markup'}, 'ajax_recheck_ssl': {'jetpack-recheck-ssl'}, 'get_attachment_comments': {'get_attachment_comments', 'nopriv_get_attachment_comments'}, 'wp_ajax_videopress_get_playback_jwt': {'nopriv_videopress-get-playback-jwt', 'videopress-get-playback-jwt'}}
*
***/

/** Function ajax_tracks() called by wp_ajax hooks: {'jetpack_tracks'} **/
/** Parameters found in function ajax_tracks(): {"request": ["tracksNonce", "tracksEventName", "tracksEventType", "tracksEventProp"]} **/
function ajax_tracks() {
		// Check for nonce.
		if (
			empty( $_REQUEST['tracksNonce'] )
			|| ! wp_verify_nonce( $_REQUEST['tracksNonce'], 'jp-tracks-ajax-nonce' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- WP core doesn't pre-sanitize nonces either.
		) {
			wp_send_json_error(
				__( 'You aren’t authorized to do that.', 'jetpack-connection' ),
				403
			);
		}

		if ( ! isset( $_REQUEST['tracksEventName'] ) || ! isset( $_REQUEST['tracksEventType'] ) ) {
			wp_send_json_error(
				__( 'No valid event name or type.', 'jetpack-connection' ),
				403
			);
		}

		$tracks_data = array();
		if ( 'click' === $_REQUEST['tracksEventType'] && isset( $_REQUEST['tracksEventProp'] ) ) {
			if ( is_array( $_REQUEST['tracksEventProp'] ) ) {
				$tracks_data = array_map( 'filter_var', wp_unslash( $_REQUEST['tracksEventProp'] ) );
			} else {
				$tracks_data = array( 'clicked' => filter_var( wp_unslash( $_REQUEST['tracksEventProp'] ) ) );
			}
		}

		$this->record_user_event( filter_var( wp_unslash( $_REQUEST['tracksEventName'] ) ), $tracks_data, null, false );

		wp_send_json_success();
	}


/** Function regenerate_post_by_email_address() called by wp_ajax hooks: {'jetpack_post_by_email_regenerate'} **/
/** No function found :-/ **/


/** Function options_page_tumblr() called by wp_ajax hooks: {'publicize_tumblr_options_page'} **/
/** Parameters found in function options_page_tumblr(): {"request": ["connection"]} **/
function options_page_tumblr() {
		$connection_name = isset( $_REQUEST['connection'] ) ? filter_var( wp_unslash( $_REQUEST['connection'] ) ) : null;

		// Nonce check.
		check_admin_referer( 'options_page_tumblr_' . $connection_name );

		$connected_services = $this->get_all_connections();
		$connection         = $connected_services['tumblr'][ $connection_name ];
		$options_to_show    = $connection['connection_data']['meta']['options_responses'];
		$request            = $options_to_show[0];

		$blogs = $request['response']['user']['blogs'];

		$blog_selected = false;

		if ( ! empty( $connection['connection_data']['meta']['tumblr_base_hostname'] ) ) {
			foreach ( $blogs as $blog ) {
				if ( $connection['connection_data']['meta']['tumblr_base_hostname'] === $this->get_basehostname( $blog['url'] ) ) {
					$blog_selected = $connection['connection_data']['meta']['tumblr_base_hostname'];
					break;
				}
			}
		}

		// Use their Primary blog if they haven't selected one yet.
		if ( ! $blog_selected ) {
			foreach ( $blogs as $blog ) {
				if ( $blog['primary'] ) {
					$blog_selected = $this->get_basehostname( $blog['url'] );
				}
			}
		}
		?>

		<div id="thickbox-content">

			<?php
			ob_start();
			Publicize_UI::connected_notice( 'Tumblr' );
			$update_notice = ob_get_clean();

			if ( ! empty( $update_notice ) ) {
				echo wp_kses_post( $update_notice );
			}
			?>

			<p><?php echo wp_kses( __( 'Share to my <strong>Tumblr blog</strong>:', 'jetpack-publicize-pkg' ), array( 'strong' ) ); ?></p>

			<ul id="option-tumblr-blog">

				<?php
				foreach ( $blogs as $blog ) {
					$url = $this->get_basehostname( $blog['url'] );
					?>
					<li>
						<input type="radio" name="option" data-type="blog" id="<?php echo esc_attr( $url ); ?>"
							value="<?php echo esc_attr( $url ); ?>" <?php checked( $blog_selected === $url, true ); ?> />
						<label for="<?php echo esc_attr( $url ); ?>"><span
								class="name"><?php echo esc_html( $blog['title'] ); ?></span></label>
					</li>
				<?php } ?>

			</ul>

			<?php Publicize_UI::global_checkbox( 'tumblr', $connection_name ); ?>

			<p style="text-align: center;">
				<input type="submit" value="<?php esc_attr_e( 'OK', 'jetpack-publicize-pkg' ); ?>"
					class="button tumblr-options save-options" name="save"
					data-connection="<?php echo esc_attr( $connection_name ); ?>"
					rel="<?php echo esc_attr( wp_create_nonce( 'save_tumblr_blog_' . $connection_name ) ); ?>"/>
			</p> <br/>
		</div>

		<?php
	}


/** Function jetpack_debugger_sync_progress_ajax() called by wp_ajax hooks: {'jetpack_sync_progress_check'} **/
/** No params detected :-/ **/


/** Function wp_ajax_videopress_get_upload_jwt() called by wp_ajax hooks: {'videopress-get-upload-jwt'} **/
/** No params detected :-/ **/


/** Function wp_ajax_upsell_nudge_jitm() called by wp_ajax hooks: {'upsell_nudge_jitm'} **/
/** No params detected :-/ **/


/** Function ajax_new_service() called by wp_ajax hooks: {'sharing_new_service'} **/
/** Parameters found in function ajax_new_service(): {"post": ["_wpnonce", "sharing_name", "sharing_url", "sharing_icon"]} **/
function ajax_new_service() {
		if (
			isset( $_POST['_wpnonce'] )
			&& isset( $_POST['sharing_name'] )
			&& isset( $_POST['sharing_url'] )
			&& isset( $_POST['sharing_icon'] )
			&& wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'sharing-new_service' )
		) {
			$sharer  = new Sharing_Service();
			$service = $sharer->new_service(
				sanitize_text_field( wp_unslash( $_POST['sharing_name'] ) ),
				esc_url_raw( wp_unslash( $_POST['sharing_url'] ) ),
				esc_url_raw( wp_unslash( $_POST['sharing_icon'] ) )
			);

			if ( $service ) {
				$this->output_service( $service->get_id(), $service );
				echo '<!--->';
				$service->button_style = 'icon-text';
				$this->output_preview( $service );

				die();
			}
		}

		// Fail
		die( '1' );
	}


/** Function grunion_ajax_shortcode() called by wp_ajax hooks: {'grunion_shortcode'} **/
/** Parameters found in function grunion_ajax_shortcode(): {"post": ["fields"]} **/
function grunion_ajax_shortcode() {
		check_ajax_referer( 'grunion_shortcode' );

		if ( ! current_user_can( 'edit_posts' ) ) {
			die( '-1' );
		}

		$attributes = array();

		foreach ( array( 'subject', 'to' ) as $attribute ) {
			if ( isset( $_POST[ $attribute ] ) && is_scalar( $_POST[ $attribute ] ) && (string) $_POST[ $attribute ] !== '' ) {
				$attributes[ $attribute ] = sanitize_text_field( wp_unslash( $_POST[ $attribute ] ) );
			}
		}

		if ( isset( $_POST['fields'] ) && is_array( $_POST['fields'] ) ) {
			$fields = sanitize_text_field( stripslashes_deep( $_POST['fields'] ) );
			usort( $fields, 'grunion_sort_objects' );

			$field_shortcodes = array();

			foreach ( $fields as $field ) {
				$field_attributes = array();

				if ( isset( $field['required'] ) && 'true' === $field['required'] ) {
					$field_attributes['required'] = 'true';
				}

				foreach ( array( 'options', 'label', 'type' ) as $attribute ) {
					if ( isset( $field[ $attribute ] ) ) {
						$field_attributes[ $attribute ] = $field[ $attribute ];
					}
				}

				$field_shortcodes[] = new Contact_Form_Field( $field_attributes );
			}
		}

		$grunion = new Contact_Form( $attributes, $field_shortcodes );

		die( "\n$grunion\n" ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}


/** Function options_page_facebook() called by wp_ajax hooks: {'publicize_facebook_options_page'} **/
/** Parameters found in function options_page_facebook(): {"request": ["connection"]} **/
function options_page_facebook() {
		$connection_name = isset( $_REQUEST['connection'] ) ? filter_var( wp_unslash( $_REQUEST['connection'] ) ) : null;

		// Nonce check.
		check_admin_referer( 'options_page_facebook_' . $connection_name );

		$connected_services = $this->get_all_connections();
		$connection         = $connected_services['facebook'][ $connection_name ];
		$options_to_show    = ( ! empty( $connection['connection_data']['meta']['options_responses'] ) ? $connection['connection_data']['meta']['options_responses'] : false );

		$pages = ( ! empty( $options_to_show[1]['data'] ) ? $options_to_show[1]['data'] : false );

		$page_selected = false;
		if ( ! empty( $connection['connection_data']['meta']['facebook_page'] ) ) {
			$found = false;
			if ( $pages && isset( $pages->data ) && is_array( $pages->data ) ) {
				foreach ( $pages->data as $page ) {
					if ( $page->id === (int) $connection['connection_data']['meta']['facebook_page'] ) {
						$found = true;
						break;
					}
				}
			}

			if ( $found ) {
				$page_selected = $connection['connection_data']['meta']['facebook_page'];
			}
		}

		?>

		<div id="thickbox-content">
			<?php
			ob_start();
			Publicize_UI::connected_notice( 'Facebook' );
			$update_notice = ob_get_clean();

			if ( ! empty( $update_notice ) ) {
				echo wp_kses_post( $update_notice );
			}
			$page_info_message = sprintf(
				wp_kses(
					/* translators: %s is the link to the support page about using Facebook with Jetpack Social */
					__( 'Facebook supports Jetpack Social connections to Facebook Pages, but not to Facebook Profiles. <a href="%s">Learn More about Jetpack Social for Facebook</a>', 'jetpack-publicize-pkg' ),
					array( 'a' => array( 'href' ) )
				),
				esc_url( Redirect::get_url( 'jetpack-support-publicize-facebook' ) )
			);

			if ( $pages ) :
				?>
				<p>
					<?php
						echo wp_kses(
							__( 'Share to my <strong>Facebook Page</strong>:', 'jetpack-publicize-pkg' ),
							array( 'strong' )
						);
					?>
				</p>
				<table id="option-fb-fanpage">
					<tbody>

					<?php foreach ( $pages as $i => $page ) : ?>
						<?php if ( ! ( $i % 2 ) ) : ?>
							<tr>
						<?php endif; ?>
						<td class="radio">
							<input
								type="radio"
								name="option"
								data-type="page"
								id="<?php echo esc_attr( $page['id'] ); ?>"
								value="<?php echo esc_attr( $page['id'] ); ?>"
								<?php checked( $page_selected && (int) $page_selected === (int) $page['id'], true ); ?> />
						</td>
						<td class="thumbnail"><label for="<?php echo esc_attr( $page['id'] ); ?>"><img
									src="<?php echo esc_url( str_replace( '_s', '_q', $page['picture']['data']['url'] ) ); ?>"
									width="50" height="50"/></label></td>
						<td class="details">
							<label for="<?php echo esc_attr( $page['id'] ); ?>">
								<span class="name"><?php echo esc_html( $page['name'] ); ?></span><br/>
								<span class="category"><?php echo esc_html( $page['category'] ); ?></span>
							</label>
						</td>
						<?php if ( ( $i % 2 ) || ( count( $pages ) - 1 === $i ) ) : ?>
							</tr>
						<?php endif; ?>
					<?php endforeach; ?>

					</tbody>
				</table>

				<?php Publicize_UI::global_checkbox( 'facebook', $connection_name ); ?>
				<p style="text-align: center;">
					<input type="submit" value="<?php esc_attr_e( 'OK', 'jetpack-publicize-pkg' ); ?>"
						class="button fb-options save-options" name="save"
						data-connection="<?php echo esc_attr( $connection_name ); ?>"
						rel="<?php echo esc_attr( wp_create_nonce( 'save_fb_token_' . $connection_name ) ); ?>"/>
				</p><br/>
				<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<p><?php echo $page_info_message; ?></p>
			<?php else : ?>
				<div>
					<?php // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<p><?php echo $page_info_message; ?></p>
					<p>
						<?php
							echo wp_kses(
								sprintf(
									/* translators: %1$s is the link to Facebook documentation to create a page, %2$s is the target of the link */
									__( '<a class="button" href="%1$s" target="%2$s">Create a Facebook page</a> to get started.', 'jetpack-publicize-pkg' ),
									'https://www.facebook.com/pages/creation/',
									'_blank noopener noreferrer'
								),
								array( 'a' => array( 'class', 'href', 'target' ) )
							);
						?>
					</p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}


/** Function options_save_twitter() called by wp_ajax hooks: {'publicize_twitter_options_save'} **/
/** No params detected :-/ **/


/** Function ajax_update_widget_token_id() called by wp_ajax hooks: {'wpcom_instagram_widget_update_widget_token_id'} **/
/** Parameters found in function ajax_update_widget_token_id(): {"post": ["keyring_id", "instagram_widget_id"]} **/
function ajax_update_widget_token_id() {
		if ( ! check_ajax_referer( 'instagram-widget-save-token', 'savetoken', false ) ) {
			wp_send_json_error( array( 'message' => 'bad_nonce' ), 403 );
		}

		if ( ! current_user_can( 'customize' ) ) {
			wp_send_json_error( array( 'message' => 'not_authorized' ), 403 );
		}

		$token_id  = ! empty( $_POST['keyring_id'] ) ? (int) $_POST['keyring_id'] : null;
		$widget_id = ! empty( $_POST['instagram_widget_id'] ) ? (int) $_POST['instagram_widget_id'] : null;

		// For Simple sites check if the token is valid.
		// (For Atomic sites, this check is done via the api: wpcom/v2/instagram/<token_id>).
		if ( defined( 'IS_WPCOM' ) && IS_WPCOM ) {
			$token = Keyring::init()->get_token_store()->get_token(
				array(
					'type' => 'access',
					'id'   => $token_id,
				)
			);
			if ( get_current_user_id() !== (int) $token->meta['user_id'] ) {
				return wp_send_json_error( array( 'message' => 'not_authorized' ), 403 );
			}
		}

		$this->update_widget_token_id( $token_id, $widget_id );
		$this->update_widget_token_legacy_status( false );

		return wp_send_json_success( null, 200 );
	}


/** Function grunion_ajax_shortcode_to_json() called by wp_ajax hooks: {'grunion_shortcode_to_json'} **/
/** Parameters found in function grunion_ajax_shortcode_to_json(): {"post": ["post_id", "content"]} **/
function grunion_ajax_shortcode_to_json() {
		global $post;

		check_ajax_referer( 'grunion_shortcode_to_json' );

		if ( ! empty( $_POST['post_id'] ) && ! current_user_can( 'edit_post', (int) $_POST['post_id'] ) ) {
			die( '-1' );
		} elseif ( ! current_user_can( 'edit_posts' ) ) {
			die( '-1' );
		}

		if ( ! isset( $_POST['content'] ) || ! is_numeric( $_POST['post_id'] ) ) {
			die( '-1' );
		}

		$content = sanitize_text_field( wp_unslash( $_POST['content'] ) );

		// doesn't look like a post with a [contact-form] already.
		if ( false === has_shortcode( $content, 'contact-form' ) ) {
			die( '' );
		}

		$post = get_post( (int) $_POST['post_id'] ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		do_shortcode( $content );

		$grunion = Contact_Form::$last;

		$out = array(
			'to'      => '',
			'subject' => '',
			'fields'  => array(),
		);

		foreach ( $grunion->fields as $field ) {
			$out['fields'][ $field->get_attribute( 'id' ) ] = $field->attributes;
		}

		foreach ( array( 'to', 'subject' ) as $attribute ) {
			$value = $grunion->get_attribute( $attribute );
			if ( isset( $grunion->defaults[ $attribute ] ) && $value === $grunion->defaults[ $attribute ] ) {
				$value = '';
			}
			$out[ $attribute ] = $value;
		}

		die( wp_json_encode( $out ) );
	}


/** Function grunion_recheck_queue() called by wp_ajax hooks: {'grunion_recheck_queue'} **/
/** Parameters found in function grunion_recheck_queue(): {"post": ["limit", "offset"]} **/
function grunion_recheck_queue() {
		$blog_id = get_current_blog_id();

		if (
			empty( $_POST[ 'jetpack_check_feedback_spam_' . (string) $blog_id ] )
			|| ! wp_verify_nonce( sanitize_key( $_POST[ 'jetpack_check_feedback_spam_' . (string) $blog_id ] ), 'grunion_recheck_queue' )
		) {
			wp_send_json_error(
				__( 'You aren’t authorized to do that.', 'jetpack-forms' ),
				403
			);

			return;
		}

		if ( ! current_user_can( 'delete_others_posts' ) ) {
			wp_send_json_error(
				__( 'You don’t have permission to do that.', 'jetpack-forms' ),
				403
			);

			return;
		}

		$query = 'post_type=feedback&post_status=publish';

		if ( isset( $_POST['limit'], $_POST['offset'] ) ) {
			$query .= '&posts_per_page=' . (int) $_POST['limit'] . '&offset=' . (int) $_POST['offset'];
		}

		$approved_feedbacks = get_posts( $query );

		foreach ( $approved_feedbacks as $feedback ) {
			$meta = get_post_meta( $feedback->ID, '_feedback_akismet_values', true );

			if ( ! $meta ) {
				// _feedback_akismet_values is eventually deleted when it's no longer
				// within a reasonable time period to check the feedback for spam, so
				// if it's gone, don't attempt a spam recheck.
				continue;
			}

			$meta['recheck_reason'] = 'recheck_queue';

			/**
			 * Filter whether the submitted feedback is considered as spam.
			 *
			 * @module contact-form
			 *
			 * @since 3.4.0
			 *
			 * @param bool false Is the submitted feedback spam? Default to false.
			 * @param array $meta Feedack values returned by the Akismet plugin.
			 */
			$is_spam = apply_filters( 'jetpack_contact_form_is_spam', false, $meta );

			if ( $is_spam ) {
				wp_update_post(
					array(
						'ID'          => $feedback->ID,
						'post_status' => 'spam',
					)
				);
				/** This action is already documented in modules/contact-form/admin.php */
				do_action( 'contact_form_akismet', 'spam', $meta );
			}
		}

		wp_send_json(
			array(
				'processed' => count( $approved_feedbacks ),
			)
		);
	}


/** Function ajax_dismiss_handler() called by wp_ajax hooks: {'jetpack-protect-dismiss-multisite-banner'} **/
/** No params detected :-/ **/


/** Function wp_ajax_update_transcoding_status() called by wp_ajax hooks: {'videopress-update-transcoding-status'} **/
/** Parameters found in function wp_ajax_update_transcoding_status(): {"post": ["post_id"]} **/
function wp_ajax_update_transcoding_status() {
		if ( ! isset( $_POST['post_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Informational AJAX response.
			wp_send_json_error( array( 'message' => __( 'A valid post_id is required.', 'jetpack-videopress-pkg' ) ) );
			return;
		}

		$post_id = (int) $_POST['post_id']; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( ! videopress_update_meta_data( $post_id ) ) {
			wp_send_json_error( array( 'message' => __( 'That post does not have a VideoPress video associated to it.', 'jetpack-videopress-pkg' ) ) );
			return;
		}

		wp_send_json_success(
			array(
				'message' => __( 'Status updated', 'jetpack-videopress-pkg' ),
				'status'  => videopress_get_transcoding_status( $post_id ),
			)
		);
	}


/** Function test_publicize_conns() called by wp_ajax hooks: {'test_publicize_conns'} **/
/** No params detected :-/ **/


/** Function ajax_delete_service() called by wp_ajax hooks: {'sharing_delete_service'} **/
/** Parameters found in function ajax_delete_service(): {"post": ["_wpnonce", "service"]} **/
function ajax_delete_service() {
		if (
			isset( $_POST['_wpnonce'] )
			&& isset( $_POST['service'] )
			&& wp_verify_nonce(
				sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ),
				'sharing-options_' . sanitize_text_field( wp_unslash( $_POST['service'] ) )
			)
		) {
			$sharer = new Sharing_Service();
			$sharer->delete_service( sanitize_text_field( wp_unslash( $_POST['service'] ) ) );
		}
	}


/** Function handle_optout_request() called by wp_ajax hooks: {'nopriv_privacy_optout', 'privacy_optout'} **/
/** Parameters found in function handle_optout_request(): {"post": ["optout"]} **/
function handle_optout_request() {
		check_ajax_referer( 'ccpa_optout', 'security' );

		$optout = isset( $_POST['optout'] ) && 'true' === $_POST['optout'];
		$optout ? self::set_optout_cookie() : self::set_optin_cookie();

		wp_send_json_success( $optout );
	}


/** Function \accept_tos() called by wp_ajax hooks: {'jetpack_accept_tos'} **/
/** No function found :-/ **/


/** Function ajax_get_payment_buttons() called by wp_ajax hooks: {'customize-jetpack-simple-payments-buttons-get'} **/
/** No params detected :-/ **/


/** Function upload() called by wp_ajax hooks: {'jetpack_comic_upload'} **/
/** No params detected :-/ **/


/** Function jetpack_debugger_full_sync_start() called by wp_ajax hooks: {'jetpack_debugger_full_sync_start'} **/
/** No params detected :-/ **/


/** Function options_save_linkedin() called by wp_ajax hooks: {'publicize_linkedin_options_save'} **/
/** No params detected :-/ **/


/** Function grunion_display_form_view() called by wp_ajax hooks: {'grunion_form_builder'} **/
/** No params detected :-/ **/


/** Function delete_post_by_email_address() called by wp_ajax hooks: {'jetpack_post_by_email_disable'} **/
/** No function found :-/ **/


/** Function ajax_check_api_key() called by wp_ajax hooks: {'customize-contact-info-api-key'} **/
/** Parameters found in function ajax_check_api_key(): {"post": ["apikey"]} **/
function ajax_check_api_key() {
			if ( isset( $_POST['apikey'] ) ) {
				if ( check_ajax_referer( 'customize_contact_info_api_key' ) && current_user_can( 'customize' ) ) {
					$apikey                     = wp_kses( wp_unslash( $_POST['apikey'] ), array() );
					$default_instance           = $this->defaults();
					$default_instance['apikey'] = $apikey;
					wp_send_json( array( 'result' => esc_html( $this->has_good_map( $default_instance ) ) ) );
				}
			} else {
				wp_die();
			}
		}


/** Function remote_request_handlers() called by wp_ajax hooks: {'nopriv_{$action}'} **/
/** No params detected :-/ **/


/** Function download_feedback_as_csv() called by wp_ajax hooks: {'feedback_export'} **/
/** No params detected :-/ **/


/** Function wp_ajax_jitm_dismiss() called by wp_ajax hooks: {'jitm_dismiss'} **/
/** Parameters found in function wp_ajax_jitm_dismiss(): {"request": ["id", "feature_class"]} **/
function wp_ajax_jitm_dismiss() {
		check_ajax_referer( 'jitm_dismiss' );
		$jitm = \Automattic\Jetpack\JITMS\JITM::get_instance();
		if ( isset( $_REQUEST['id'] ) && isset( $_REQUEST['feature_class'] ) ) {
			$jitm->dismiss( sanitize_text_field( wp_unslash( $_REQUEST['id'] ) ), sanitize_text_field( wp_unslash( $_REQUEST['feature_class'] ) ) );
		}
		wp_die();
	}


/** Function jetpack_connection_banner_callback() called by wp_ajax hooks: {'jetpack_connection_banner'} **/
/** Parameters found in function jetpack_connection_banner_callback(): {"request": ["dismissBanner"]} **/
function jetpack_connection_banner_callback() {
		check_ajax_referer( 'jp-connection-banner-nonce', 'nonce' );

		// Disable the banner dismiss functionality if the pre-connection prompt helpers filter is set.
		if (
			isset( $_REQUEST['dismissBanner'] ) &&
			! Jetpack_Connection_Banner::force_display()
		) {
			Jetpack_Options::update_option( 'dismissed_connection_banner', 1 );
			wp_send_json_success();
		}

		wp_die();
	}


/** Function grunion_ajax_spam() called by wp_ajax hooks: {'grunion_ajax_spam'} **/
/** Parameters found in function grunion_ajax_spam(): {"post": ["make_it", "post_id", "sub_menu"]} **/
function grunion_ajax_spam() {
		global $wpdb;

		if ( empty( $_POST['make_it'] ) ) {
			return;
		}

		$post_id = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
		check_ajax_referer( 'grunion-post-status' );
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			wp_die( esc_html__( 'You are not allowed to manage this item.', 'jetpack-forms' ) );
		}

		// init will construct/get the instance and make sure all the filters and actions
		// are in place for this process to go through
		Contact_Form_Plugin::init();

		$current_menu = '';
		if ( isset( $_POST['sub_menu'] ) && preg_match( '|post_type=feedback|', sanitize_text_field( wp_unslash( $_POST['sub_menu'] ) ) ) ) {
			if ( preg_match( '|post_status=spam|', sanitize_text_field( wp_unslash( $_POST['sub_menu'] ) ) ) ) {
				$current_menu = 'spam';
			} elseif ( preg_match( '|post_status=trash|', sanitize_text_field( wp_unslash( $_POST['sub_menu'] ) ) ) ) {
				$current_menu = 'trash';
			} else {
				$current_menu = 'messages';
			}
		}

		$post             = get_post( $post_id );
		$post_type_object = get_post_type_object( $post->post_type );
		$akismet_values   = get_post_meta( $post_id, '_feedback_akismet_values', true );
		if ( $_POST['make_it'] === 'spam' ) {
			$post->post_status = 'spam';
			$status            = wp_insert_post( $post );

			/** This action is already documented in modules/contact-form/admin.php */
			do_action( 'contact_form_akismet', 'spam', $akismet_values );
		} elseif ( $_POST['make_it'] === 'ham' ) {
			$post->post_status = 'publish';
			$status            = wp_insert_post( $post );

			/** This action is already documented in modules/contact-form/admin.php */
			do_action( 'contact_form_akismet', 'ham', $akismet_values );

			$comment_author_email = false;
			$reply_to_addr        = false;
			$message              = false;
			$to                   = false;
			$headers              = false;
			$blog_url             = wp_parse_url( site_url() );

			// resend the original email
			$email          = get_post_meta( $post_id, '_feedback_email', true );
			$content_fields = Contact_Form_Plugin::parse_fields_from_content( $post_id );

			if ( ! empty( $email ) && ! empty( $content_fields ) ) {
				if ( isset( $content_fields['_feedback_author_email'] ) ) {
					$comment_author_email = $content_fields['_feedback_author_email'];
				}

				if ( isset( $email['to'] ) ) {
					$to = $email['to'];
				}

				if ( isset( $email['message'] ) ) {
					$message = $email['message'];
				}

				if ( isset( $email['headers'] ) ) {
					$headers = $email['headers'];
				} else {
					$headers = 'From: "' . $content_fields['_feedback_author'] . '" <wordpress@' . $blog_url['host'] . ">\r\n";

					if ( ! empty( $comment_author_email ) ) {
						$reply_to_addr = $comment_author_email;
					} elseif ( is_array( $to ) ) {
						$reply_to_addr = $to[0];
					}

					if ( $reply_to_addr ) {
						$headers .= 'Reply-To: "' . $content_fields['_feedback_author'] . '" <' . $reply_to_addr . ">\r\n";
					}

					$headers .= 'Content-Type: text/plain; charset="' . get_option( 'blog_charset' ) . '"';
				}

				/**
				 * Filters the subject of the email sent after a contact form submission.
				 *
				 * @module contact-form
				 *
				 * @since 3.0.0
				 *
				 * @param string $content_fields['_feedback_subject'] Feedback's subject line.
				 * @param array $content_fields['_feedback_all_fields'] Feedback's data from old fields.
				 */
				$subject = apply_filters( 'contact_form_subject', $content_fields['_feedback_subject'], $content_fields['_feedback_all_fields'] );

				Contact_Form::wp_mail( $to, $subject, $message, $headers );
			}
		} elseif ( $_POST['make_it'] === 'publish' ) {
			if ( ! current_user_can( $post_type_object->cap->delete_post, $post_id ) ) {
				wp_die( esc_html__( 'You are not allowed to move this item out of the Trash.', 'jetpack-forms' ) );
			}

			if ( ! wp_untrash_post( $post_id ) ) {
				wp_die( esc_html__( 'Error in restoring from Trash.', 'jetpack-forms' ) );
			}
		} elseif ( $_POST['make_it'] === 'trash' ) {
			if ( ! current_user_can( $post_type_object->cap->delete_post, $post_id ) ) {
				wp_die( esc_html__( 'You are not allowed to move this item to the Trash.', 'jetpack-forms' ) );
			}

			if ( ! wp_trash_post( $post_id ) ) {
				wp_die( esc_html__( 'Error in moving to Trash.', 'jetpack-forms' ) );
			}
		}

		$sql          = "
			SELECT post_status,
				COUNT( * ) AS post_count
			FROM `{$wpdb->posts}`
			WHERE post_type =  'feedback'
			GROUP BY post_status
		";
		$status_count = (array) $wpdb->get_results( $sql, ARRAY_A ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

		$status      = array();
		$status_html = '';
		foreach ( $status_count as $row ) {
			$status[ $row['post_status'] ] = $row['post_count'];
		}

		if ( isset( $status['publish'] ) ) {
			$status_html .= '<li><a href="edit.php?post_type=feedback"';
			if ( $current_menu === 'messages' ) {
				$status_html .= ' class="current"';
			}

			$status_html .= '>' . __( 'Messages', 'jetpack-forms' ) . ' <span class="count">';
			$status_html .= '(' . number_format( $status['publish'] ) . ')';
			$status_html .= '</span></a> |</li>';
		}

		if ( isset( $status['trash'] ) ) {
			$status_html .= '<li><a href="edit.php?post_status=trash&amp;post_type=feedback"';
			if ( $current_menu === 'trash' ) {
				$status_html .= ' class="current"';
			}

			$status_html .= '>' . __( 'Trash', 'jetpack-forms' ) . ' <span class="count">';
			$status_html .= '(' . number_format( $status['trash'] ) . ')';
			$status_html .= '</span></a>';
			if ( isset( $status['spam'] ) ) {
				$status_html .= ' |';
			}
			$status_html .= '</li>';
		}

		if ( isset( $status['spam'] ) ) {
			$status_html .= '<li><a href="edit.php?post_status=spam&amp;post_type=feedback"';
			if ( $current_menu === 'spam' ) {
				$status_html .= ' class="current"';
			}

			$status_html .= '>' . __( 'Spam', 'jetpack-forms' ) . ' <span class="count">';
			$status_html .= '(' . number_format( $status['spam'] ) . ')';
			$status_html .= '</span></a></li>';
		}

		echo $status_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- we're building the html to echo.
		exit;
	}


/** Function ajax_delete_payment_button() called by wp_ajax hooks: {'customize-jetpack-simple-payments-button-delete'} **/
/** Parameters found in function ajax_delete_payment_button(): {"post": ["params"]} **/
function ajax_delete_payment_button() {
			if ( ! check_ajax_referer( 'customize-jetpack-simple-payments', 'customize-jetpack-simple-payments-nonce', false ) ) {
				wp_send_json_error( 'bad_nonce', 400 );
			}

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( 'customize_not_allowed', 403 );
			}

			if ( empty( $_POST['params'] ) || ! is_array( $_POST['params'] ) ) {
				wp_send_json_error( 'missing_params', 400 );
			}

			$params         = wp_unslash( $_POST['params'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Manually validated just below.
			$illegal_params = array_diff( array_keys( $params ), array( 'product_post_id' ) );
			if ( ! empty( $illegal_params ) ) {
				wp_send_json_error( 'illegal_params', 400 );
			}

			$product_id   = (int) $params['product_post_id'];
			$product_post = get_post( $product_id );

			$return = array( 'status' => $product_post->post_status );

			wp_delete_post( $product_id, true );
			$status = get_post_status( $product_id );
			if ( false === $status ) {
				$return['status'] = 'deleted';
			}

			$this->record_event( 'deleted', 'delete', array( 'id' => $product_id ) );

			wp_send_json_success( $return );
		}


/** Function export_to_gdrive() called by wp_ajax hooks: {'grunion_export_to_gdrive'} **/
/** No params detected :-/ **/


/** Function options_page_linkedin() called by wp_ajax hooks: {'publicize_linkedin_options_page'} **/
/** No params detected :-/ **/


/** Function ajax_save_services() called by wp_ajax hooks: {'sharing_save_services'} **/
/** Parameters found in function ajax_save_services(): {"post": ["_wpnonce", "hidden", "visible"]} **/
function ajax_save_services() {
		if (
			isset( $_POST['_wpnonce'] )
			&& wp_verify_nonce( sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ), 'sharing-options' )
			&& isset( $_POST['hidden'] )
			&& isset( $_POST['visible'] )
		) {
			$sharer = new Sharing_Service();

			$sharer->set_blog_services(
				explode( ',', sanitize_text_field( wp_unslash( $_POST['visible'] ) ) ),
				explode( ',', sanitize_text_field( wp_unslash( $_POST['hidden'] ) ) )
			);
			die();
		}
	}


/** Function options_save_tumblr() called by wp_ajax hooks: {'publicize_tumblr_options_save'} **/
/** Parameters found in function options_save_tumblr(): {"post": ["connection", "selected_id"]} **/
function options_save_tumblr() {
		$connection_name = isset( $_POST['connection'] ) ? filter_var( wp_unslash( $_POST['connection'] ) ) : null;

		// Nonce check.
		check_admin_referer( 'save_tumblr_blog_' . $connection_name );
		$options = array( 'tumblr_base_hostname' => isset( $_POST['selected_id'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_id'] ) ) : null );

		$this->set_remote_publicize_options( $connection_name, $options );
	}


/** Function options_save_facebook() called by wp_ajax hooks: {'publicize_facebook_options_save'} **/
/** Parameters found in function options_save_facebook(): {"request": ["connection"], "post": ["type", "selected_id"]} **/
function options_save_facebook() {
		$connection_name = isset( $_REQUEST['connection'] ) ? filter_var( wp_unslash( $_REQUEST['connection'] ) ) : null;

		// Nonce check.
		check_admin_referer( 'save_fb_token_' . $connection_name );

		if ( ! isset( $_POST['type'] ) || 'page' !== $_POST['type'] || ! isset( $_POST['selected_id'] ) ) {
			return;
		}

		// Check for a numeric page ID.
		$page_id = $_POST['selected_id']; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- Manually validated just below
		if ( ! ctype_digit( $page_id ) ) {
			die( 'Security check' );
		}

		// Publish to Page.
		$options = array(
			'facebook_page'    => $page_id,
			'facebook_profile' => null,
		);

		$this->set_remote_publicize_options( $connection_name, $options );
	}


/** Function plugin_edit_ajax() called by wp_ajax hooks: {'edit-theme-plugin-file'} **/
/** No params detected :-/ **/


/** Function Jetpack_Recommendations_Banner() called by wp_ajax hooks: {'jetpack_recommendations_banner'} **/
/** No function found :-/ **/


/** Function theme_edit_ajax() called by wp_ajax hooks: {'edit-theme-plugin-file'} **/
/** No params detected :-/ **/


/** Function ajax_save_payment_button() called by wp_ajax hooks: {'customize-jetpack-simple-payments-button-save'} **/
/** Parameters found in function ajax_save_payment_button(): {"post": ["params"]} **/
function ajax_save_payment_button() {
			if ( ! check_ajax_referer( 'customize-jetpack-simple-payments', 'customize-jetpack-simple-payments-nonce', false ) ) {
				wp_send_json_error( 'bad_nonce', 400 );
			}

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( 'customize_not_allowed', 403 );
			}

			$post_type_object = get_post_type_object( Jetpack_Simple_Payments::$post_type_product );
			if ( ! current_user_can( $post_type_object->cap->create_posts ) || ! current_user_can( $post_type_object->cap->publish_posts ) ) {
				wp_send_json_error( 'insufficient_post_permissions', 403 );
			}

			if ( empty( $_POST['params'] ) || ! is_array( $_POST['params'] ) ) {
				wp_send_json_error( 'missing_params', 400 );
			}

			$params = wp_unslash( $_POST['params'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Manually validated by validate_ajax_params().
			$errors = $this->validate_ajax_params( $params );
			if ( ! empty( $errors->errors ) ) {
				wp_send_json_error( $errors );
			}

			$product_post_id = isset( $params['product_post_id'] ) ? (int) $params['product_post_id'] : 0;

			$product_post = array(
				'ID'            => $product_post_id,
				'post_type'     => Jetpack_Simple_Payments::$post_type_product,
				'post_status'   => 'publish',
				'post_title'    => $params['post_title'],
				'post_content'  => $params['post_content'],
				'_thumbnail_id' => ! empty( $params['image_id'] ) ? $params['image_id'] : -1,
				'meta_input'    => array(
					'spay_currency' => $params['currency'],
					'spay_price'    => $params['price'],
					'spay_multiple' => isset( $params['multiple'] ) ? (int) $params['multiple'] : 0,
					'spay_email'    => is_email( $params['email'] ),
				),
			);

			if ( empty( $product_post_id ) ) {
				$product_post_id = wp_insert_post( $product_post );
			} else {
				$product_post_id = wp_update_post( $product_post );
			}

			if ( ! $product_post_id || is_wp_error( $product_post_id ) ) {
				wp_send_json_error( $product_post_id );
			}

			$tracks_properties = array(
				'id'       => $product_post_id,
				'currency' => $params['currency'],
				'price'    => $params['price'],
			);
			if ( 0 === $product_post['ID'] ) {
				$this->record_event( 'created', 'create', $tracks_properties );
			} else {
				$this->record_event( 'updated', 'update', $tracks_properties );
			}

			wp_send_json_success(
				array(
					'product_post_id'    => $product_post_id,
					'product_post_title' => $params['post_title'],
				)
			);
		}


/** Function options_page_twitter() called by wp_ajax hooks: {'publicize_twitter_options_page'} **/
/** No params detected :-/ **/


/** Function post_attachment_comment() called by wp_ajax hooks: {'nopriv_post_attachment_comment', 'post_attachment_comment'} **/
/** Parameters found in function post_attachment_comment(): {"post": ["nonce", "blog_id", "id", "comment", "author", "email", "url"]} **/
function post_attachment_comment() {
		if ( ! headers_sent() ) {
			header( 'Content-type: text/javascript' );
		}

		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'carousel_nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- WP Core doesn't unslash or sanitize nonces either
			die( wp_json_encode( array( 'error' => __( 'Nonce verification failed.', 'jetpack' ) ) ) );
		}

		$_blog_id = isset( $_POST['blog_id'] ) ? (int) $_POST['blog_id'] : 0;
		$_post_id = isset( $_POST['id'] ) ? (int) $_POST['id'] : 0;
		$comment  = isset( $_POST['comment'] ) ? filter_var( wp_unslash( $_POST['comment'] ) ) : null;

		if ( empty( $_blog_id ) ) {
			die( wp_json_encode( array( 'error' => __( 'Missing target blog ID.', 'jetpack' ) ) ) );
		}

		if ( empty( $_post_id ) ) {
			die( wp_json_encode( array( 'error' => __( 'Missing target post ID.', 'jetpack' ) ) ) );
		}

		if ( empty( $comment ) ) {
			die( wp_json_encode( array( 'error' => __( 'No comment text was submitted.', 'jetpack' ) ) ) );
		}

		// Used in context like NewDash.
		$switched = false;
		if ( is_multisite() && get_current_blog_id() !== $_blog_id ) {
			switch_to_blog( $_blog_id );
			$switched = true;
		}

		/** This action is documented in modules/carousel/jetpack-carousel.php */
		do_action( 'jp_carousel_check_blog_user_privileges' );

		if ( ! comments_open( $_post_id ) ) {
			if ( $switched ) {
				restore_current_blog();
			}
			die( wp_json_encode( array( 'error' => __( 'Comments on this post are closed.', 'jetpack' ) ) ) );
		}

		if ( is_user_logged_in() ) {
			$user         = wp_get_current_user();
			$user_id      = $user->ID;
			$display_name = $user->display_name;
			$email        = $user->user_email;
			$url          = $user->user_url;

			if ( empty( $user_id ) ) {
				if ( $switched ) {
					restore_current_blog();
				}
				die( wp_json_encode( array( 'error' => __( 'Sorry, but we could not authenticate your request.', 'jetpack' ) ) ) );
			}
		} else {
			$user_id      = 0;
			$display_name = isset( $_POST['author'] ) ? sanitize_text_field( wp_unslash( $_POST['author'] ) ) : null;
			$email        = isset( $_POST['email'] ) ? wp_unslash( $_POST['email'] ) : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Checked or sanitized below.
			$url          = isset( $_POST['url'] ) ? esc_url_raw( wp_unslash( $_POST['url'] ) ) : null;

			if ( get_option( 'require_name_email' ) ) {
				if ( empty( $display_name ) ) {
					if ( $switched ) {
						restore_current_blog();
					}
					die( wp_json_encode( array( 'error' => __( 'Please provide your name.', 'jetpack' ) ) ) );
				}

				if ( empty( $email ) ) {
					if ( $switched ) {
						restore_current_blog();
					}
					die( wp_json_encode( array( 'error' => __( 'Please provide an email address.', 'jetpack' ) ) ) );
				}

				if ( ! is_email( $email ) ) {
					if ( $switched ) {
						restore_current_blog();
					}
					die( wp_json_encode( array( 'error' => __( 'Please provide a valid email address.', 'jetpack' ) ) ) );
				}
			} else {
				$email = $email !== null ? sanitize_email( $email ) : null;
			}
		}

		$comment_data = array(
			'comment_content'      => $comment,
			'comment_post_ID'      => $_post_id,
			'comment_author'       => $display_name,
			'comment_author_email' => $email,
			'comment_author_url'   => $url,
			'comment_approved'     => 0,
			'comment_type'         => 'comment',
		);

		if ( ! empty( $user_id ) ) {
			$comment_data['user_id'] = $user_id;
		}

		// Note: wp_new_comment() sanitizes and validates the values (too).
		$comment_id = wp_new_comment( $comment_data );

		/**
		 * Fires before adding a new comment to the database via the get_attachment_comments ajax endpoint.
		 *
		 * @module carousel
		 *
		 * @since 1.6.0
		 */
		do_action( 'jp_carousel_post_attachment_comment' );
		$comment_status = wp_get_comment_status( $comment_id );

		if ( $switched ) {
			restore_current_blog();
		}

		die(
			wp_json_encode(
				array(
					'comment_id'     => $comment_id,
					'comment_status' => $comment_status,
				)
			)
		);
	}


/** Function test_gdrive_connection() called by wp_ajax hooks: {'grunion_gdrive_connection'} **/
/** No params detected :-/ **/


/** Function grunion_delete_spam_feedbacks() called by wp_ajax hooks: {'jetpack_delete_spam_feedbacks'} **/
/** Parameters found in function grunion_delete_spam_feedbacks(): {"post": ["nonce"]} **/
function grunion_delete_spam_feedbacks() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'jetpack_delete_spam_feedbacks' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- core doesn't sanitize nonce checks either.
			wp_send_json_error(
				__( 'You aren’t authorized to do that.', 'jetpack-forms' ),
				403
			);

			return;
		}

		if ( ! current_user_can( 'delete_others_posts' ) ) {
			wp_send_json_error(
				__( 'You don’t have permission to do that.', 'jetpack-forms' ),
				403
			);

			return;
		}

		$deleted_feedbacks = 0;

		$delete_limit = 25;
		/**
		 * Filter the amount of Spam feedback one can delete at once.
		 *
		 * @module contact-form
		 *
		 * @since 8.7.0
		 *
		 * @param int $delete_limit Number of spam to process at once. Default to 25.
		 */
		$delete_limit = apply_filters( 'jetpack_delete_spam_feedbacks_limit', $delete_limit );
		$delete_limit = (int) $delete_limit;
		$delete_limit = max( 1, min( 100, $delete_limit ) ); // Allow a range of 1-100 for the delete limit.

		$query_args = array(
			'post_type'      => 'feedback',
			'post_status'    => 'spam',
			'posts_per_page' => $delete_limit,
		);

		$query          = new \WP_Query( $query_args );
		$spam_feedbacks = $query->get_posts();

		foreach ( $spam_feedbacks as $feedback ) {
			wp_delete_post( $feedback->ID, true );

			++$deleted_feedbacks;
		}

		wp_send_json(
			array(
				'success' => true,
				'data'    => array(
					'counts' => array(
						'deleted' => $deleted_feedbacks,
						'limit'   => $delete_limit,
					),
				),
			)
		);
	}


/** Function wp_ajax_videopress_get_upload_token() called by wp_ajax hooks: {'videopress-get-upload-token'} **/
/** No params detected :-/ **/


/** Function create_post_by_email_address() called by wp_ajax hooks: {'jetpack_post_by_email_enable'} **/
/** No function found :-/ **/


/** Function jetpack_debugger_ajax_local_testing_suite() called by wp_ajax hooks: {'health-check-jetpack-local_testing_suite'} **/
/** No params detected :-/ **/


/** Function ajax_sidebar_state() called by wp_ajax hooks: {'sidebar_state'} **/
/** Parameters found in function ajax_sidebar_state(): {"request": ["expanded"]} **/
function ajax_sidebar_state() {
		$expanded = isset( $_REQUEST['expanded'] ) ? filter_var( wp_unslash( $_REQUEST['expanded'] ), FILTER_VALIDATE_BOOLEAN ) : false; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		Client::wpcom_json_api_request_as_user(
			'/me/preferences',
			'2',
			array(
				'method' => 'POST',
			),
			(object) array( 'calypso_preferences' => (object) array( 'sidebarCollapsed' => ! $expanded ) ),
			'wpcom'
		);

		wp_die();
	}


/** Function ajax_request() called by wp_ajax hooks: {'grunion-contact-form', 'nopriv_grunion-contact-form'} **/
/** No params detected :-/ **/


/** Function ajax_save_options() called by wp_ajax hooks: {'sharing_save_options'} **/
/** Parameters found in function ajax_save_options(): {"post": ["_wpnonce", "service"]} **/
function ajax_save_options() {
		if (
			isset( $_POST['_wpnonce'] )
			&& isset( $_POST['service'] )
			&& wp_verify_nonce(
				sanitize_key( wp_unslash( $_POST['_wpnonce'] ) ),
				'sharing-options_' . sanitize_text_field( wp_unslash( $_POST['service'] ) )
			)
		) {
			$sharer  = new Sharing_Service();
			$service = $sharer->get_service( sanitize_text_field( wp_unslash( $_POST['service'] ) ) );

			if ( $service && $service instanceof Sharing_Advanced_Source ) {
				$service->update_options( $_POST );

				$sharer->set_service( sanitize_text_field( wp_unslash( $_POST['service'] ) ), $service );
			}

			$this->output_service( $service->get_id(), $service, true );
			echo '<!--->';
			$service->button_style = 'icon-text';
			$this->output_preview( $service );
			die();
		}
	}


/** Function handle_optout_markup() called by wp_ajax hooks: {'nopriv_privacy_optout_markup', 'privacy_optout_markup'} **/
/** No params detected :-/ **/


/** Function ajax_recheck_ssl() called by wp_ajax hooks: {'jetpack-recheck-ssl'} **/
/** No params detected :-/ **/


/** Function get_attachment_comments() called by wp_ajax hooks: {'get_attachment_comments', 'nopriv_get_attachment_comments'} **/
/** Parameters found in function get_attachment_comments(): {"request": ["id", "offset"]} **/
function get_attachment_comments() {
		if ( ! headers_sent() ) {
			header( 'Content-type: text/javascript' );
		}

		/**
		 * Allows for the checking of privileges of the blog user before comments
		 * are packaged as JSON and sent back from the get_attachment_comments
		 * AJAX endpoint
		 *
		 * @module carousel
		 *
		 * @since 1.6.0
		 */
		do_action( 'jp_carousel_check_blog_user_privileges' );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- we do not need to verify the nonce for this public request for publicly accessible data (as checked below).
		$attachment_id = ( isset( $_REQUEST['id'] ) ) ? (int) $_REQUEST['id'] : 0;
		$offset        = ( isset( $_REQUEST['offset'] ) ) ? (int) $_REQUEST['offset'] : 0;
		// phpcs:enable

		if ( ! $attachment_id ) {
			wp_send_json_error(
				__( 'Missing attachment ID.', 'jetpack' ),
				403
			);
			return;
		}

		$attachment_post = get_post( $attachment_id );
		// If we have no info about that attachment, bail.
		if ( ! ( $attachment_post instanceof WP_Post ) ) {
			wp_send_json_error(
				__( 'Missing attachment info.', 'jetpack' ),
				403
			);
			return;
		}

		// This AJAX call should only be used to fetch comments of attachments.
		if ( 'attachment' !== $attachment_post->post_type ) {
			wp_send_json_error(
				__( 'You aren’t authorized to do that.', 'jetpack' ),
				403
			);
			return;
		}

		$parent_post = get_post_parent( $attachment_id );

		/*
		 * If we have no info about that parent post, no extra checks.
		 * The attachment doesn't have a parent post, so is public.
		 * If we have a parent post, let's ensure the user has access to it.
		 */
		if ( $parent_post instanceof WP_Post ) {
			/*
			 * Fetch info about user making the request.
			 * If we have no info, bail.
			 * Even logged out users should get a WP_User user with id 0.
			 */
			$current_user = wp_get_current_user();
			if ( ! ( $current_user instanceof WP_User ) ) {
				wp_send_json_error(
					__( 'Missing user info.', 'jetpack' ),
					403
				);
				return;
			}

			/*
			 * If a post is private / draft
			 * and the current user doesn't have access to it,
			 * bail.
			 */
			if (
				'publish' !== $parent_post->post_status
				&& ! current_user_can( 'read_post', $parent_post->ID )
			) {
				wp_send_json_error(
					__( 'You aren’t authorized to do that.', 'jetpack' ),
					403
				);
				return;
			}
		}

		if ( $offset < 1 ) {
			$offset = 0;
		}

		$comments = get_comments(
			array(
				'status'  => 'approve',
				'order'   => ( 'asc' === get_option( 'comment_order' ) ) ? 'ASC' : 'DESC',
				'number'  => 10,
				'offset'  => $offset,
				'post_id' => $attachment_id,
			)
		);

		$out = array();

		// Can't just send the results, they contain the commenter's email address.
		foreach ( $comments as $comment ) {
			$avatar = get_avatar( $comment->comment_author_email, 64 );
			if ( ! $avatar ) {
				$avatar = '';
			}
			$out[] = array(
				'id'              => $comment->comment_ID,
				'parent_id'       => $comment->comment_parent,
				'author_markup'   => get_comment_author_link( $comment->comment_ID ),
				'gravatar_markup' => $avatar,
				'date_gmt'        => $comment->comment_date_gmt,
				'content'         => wpautop( $comment->comment_content ),
			);
		}

		die( wp_json_encode( $out ) );
	}


/** Function wp_ajax_videopress_get_playback_jwt() called by wp_ajax hooks: {'nopriv_videopress-get-playback-jwt', 'videopress-get-playback-jwt'} **/
/** No params detected :-/ **/


