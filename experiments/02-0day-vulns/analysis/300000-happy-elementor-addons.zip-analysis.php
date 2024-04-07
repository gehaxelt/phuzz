<?php
/***
*
*Found actions: 13
*Found functions:10
*Extracted functions:10
*Total parameter names extracted: 10
*Overview: {'clear_cache': {'ha_clear_cache'}, 'process_request': {'ha_process_dynamic_select'}, 'process_autocomplete': {'ha_condition_autocomplete'}, 'mailchimp_prepare_ajax': {'nopriv_ha_mailchimp_ajax', 'ha_mailchimp_ajax'}, 'process_condition_update': {'ha_condition_update'}, 'ha_get_template_type': {'ha_cond_template_type'}, 'ha_get_current_condition': {'ha_cond_get_current'}, 'twitter_feed_ajax': {'ha_twitter_feed_action', 'nopriv_ha_twitter_feed_action'}, 'post_tab': {'nopriv_ha_post_tab_action', 'ha_post_tab_action'}, 'process_ignore_request': {'ignore_attention_seeker'}}
*
***/

/** Function clear_cache() called by wp_ajax hooks: {'ha_clear_cache'} **/
/** Parameters found in function clear_cache(): {"post": ["type", "post_id"]} **/
function clear_cache() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! check_ajax_referer( 'ha_clear_cache', 'nonce' ) ) {
			wp_send_json_error();
		}

		$type = isset( $_POST['type'] ) ? $_POST['type'] : '';
		$post_id = isset( $_POST['post_id'] ) ? $_POST['post_id'] : 0;
		$assets_cache = new Assets_Cache( $post_id );
		if ( $type === 'page' ) {
			$assets_cache->delete();
		} elseif ( $type === 'all' ) {
			$assets_cache->delete_all();
		}
		wp_send_json_success();
	}


/** Function process_request() called by wp_ajax hooks: {'ha_process_dynamic_select'} **/
/** Parameters found in function process_request(): {"request": ["object_type"]} **/
function process_request() {
		try {
			self::validate_reqeust();

			$object_type = ! empty( $_REQUEST['object_type'] ) ? trim( $_REQUEST['object_type'] ) : '';

			if ( ! in_array( $object_type, [ 'post', 'term', 'user', 'mailchimp_list' ], true ) ) {
				throw new Exception( 'Invalid object type' );
			}

			$response = [];

			if ( $object_type === 'post' ) {
				$response = self::process_post();
			}

			if ( $object_type === 'term' ) {
				$response = self::process_term();
			}

			if ( $object_type === 'mailchimp_list' ) {
				$response = self::process_mailchimp_list();
			}

			wp_send_json_success( $response );
		} catch( Exception $e ) {
			wp_send_json_error( $e->getMessage() );
		}
	}


/** Function process_autocomplete() called by wp_ajax hooks: {'ha_condition_autocomplete'} **/
/** Parameters found in function process_autocomplete(): {"request": ["object_type"]} **/
function process_autocomplete() {
        try {
            $this->validate_reqeust();

            $object_type = !empty($_REQUEST['object_type']) ? trim($_REQUEST['object_type']) : '';

            if (!in_array($object_type, ['post', 'tax', 'author', 'archive', 'singular'], true)) {
                throw new Exception('Invalid object type');
            }

            $response = [];

            if ($object_type === 'post') {
                $response = $this->process_post();
            }

            if ($object_type === 'tax') {
                $response = $this->process_term();
            }

            if ($object_type === 'singular') {
                $response = $this->singular_conditions();
            }

            if ($object_type === 'archive') {
                $response = $this->archive_conditions();
            }

            wp_send_json_success($response);
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }


/** Function mailchimp_prepare_ajax() called by wp_ajax hooks: {'nopriv_ha_mailchimp_ajax', 'ha_mailchimp_ajax'} **/
/** Parameters found in function mailchimp_prepare_ajax(): {"post": ["subscriber_info"]} **/
function mailchimp_prepare_ajax() {

		$security = check_ajax_referer( 'happy_addons_nonce', 'security' );

		if ( ! $security ) {
			return;
		}

		parse_str( isset( $_POST['subscriber_info'] ) ? $_POST['subscriber_info'] : '', $subsciber );

		if ( ! class_exists( 'Happy_Addons\Elementor\Widget\Mailchimp\Mailchimp_Api' ) ) {
			include_once HAPPY_ADDONS_DIR_PATH . 'widgets/mailchimp/mailchimp-api.php';
		}

		$response = Widget\Mailchimp\Mailchimp_Api::insert_subscriber_to_mailchimp( $subsciber );

		echo wp_send_json( $response );

		wp_die();
	}


/** Function process_condition_update() called by wp_ajax hooks: {'ha_condition_update'} **/
/** Parameters found in function process_condition_update(): {"request": ["template_id", "conds"]} **/
function process_condition_update() {
        try {
            $this->validate_reqeust();
            $templateID = isset($_REQUEST['template_id']) ? $_REQUEST['template_id'] : null;
            $requestConditions = isset($_REQUEST['conds']) ? $_REQUEST['conds'] : [];

            $exitsConditions = get_post_meta($templateID, '_ha_display_cond', true);

            $mergedConditions = !empty( $exitsConditions ) ? array_diff($requestConditions, $exitsConditions) : $requestConditions;

            if ($templateID) {

                $allExtitsCondition = $this->ha_get_all_conditions();
                $templateType = get_post_meta($templateID, '_ha_library_type', true);

                $duplicate = $this->ha_check_template_conditions($templateType, $requestConditions, $mergedConditions, $allExtitsCondition);

                if (!$duplicate) {
                    $cond = update_post_meta($templateID, '_ha_display_cond', array_unique($requestConditions));
                    $updates = get_post_meta($templateID, '_ha_display_cond');

                    if($cond != null) {
                        $this->cache->regenerate();
                        wp_send_json_success($updates);
                    }else {
                        wp_send_json_error();
                    }
                } else {
                    wp_send_json_error(['msg' => esc_html__('Unable to save, conflicting include exclude condition detected. Please change the conditions accordingly.', 'happy-elementor-addons')]);
                }

            } else {

                wp_send_json_error();
            }

            //_ha_display_cond;
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }


/** Function ha_get_template_type() called by wp_ajax hooks: {'ha_cond_template_type'} **/
/** Parameters found in function ha_get_template_type(): {"request": ["post_id"]} **/
function ha_get_template_type() {
        try {
            //$this->validate_reqeust();

            $id = isset($_REQUEST['post_id']) ? $_REQUEST['post_id'] : null;
            if ($id) {
                $tpl_type = get_post_meta($id, '_ha_library_type', true);
                wp_send_json_success($tpl_type);
            } else {
                wp_send_json_error();
            }
            //_ha_display_cond;
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }


/** Function ha_get_current_condition() called by wp_ajax hooks: {'ha_cond_get_current'} **/
/** Parameters found in function ha_get_current_condition(): {"request": ["template_id"]} **/
function ha_get_current_condition() {
        try {
            // $this->validate_reqeust();
            $templateID = isset($_REQUEST['template_id']) ? $_REQUEST['template_id'] : null;
            // wp_send_json_success($templateID);
            if ($templateID) {
                $cond = get_post_meta($templateID, '_ha_display_cond', true);
                if ($cond) {
                    ob_start();
                    $this->cond_to_html($cond);
                    $html = ob_get_contents();
                    ob_end_clean();
                    wp_send_json_success($html);
                } else {
                    wp_send_json_error();
                }
            } else {
                wp_send_json_error();
            }
        } catch (Exception $e) {
            wp_send_json_error($e->getMessage());
        }
    }


/** Function twitter_feed_ajax() called by wp_ajax hooks: {'ha_twitter_feed_action', 'nopriv_ha_twitter_feed_action'} **/
/** Parameters found in function twitter_feed_ajax(): {"post": ["query_settings", "loaded_item"]} **/
function twitter_feed_ajax() {

		$security = check_ajax_referer( 'happy_addons_nonce', 'security' );

		if ( true == $security && isset( $_POST['query_settings'] ) ) :
			$settings    = $_POST['query_settings'];
			$loaded_item = $_POST['loaded_item'];

			$user_name      = trim( $settings['user_name'] );
			$ha_tweets_cash = '_' . $settings['id'] . '_tweet_cash';

			$transient_key = $user_name . $ha_tweets_cash;
			$twitter_data  = get_transient( $transient_key );
			$credentials   = $settings['credentials'];

			$auth_response = wp_remote_post(
				'https://api.twitter.com/oauth2/token',
				[
					'method'      => 'POST',
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => [
						'Authorization' => 'Basic ' . $credentials,
						'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
					],
					'body'        => ['grant_type' => 'client_credentials'],
				]
			);

			$body = json_decode( wp_remote_retrieve_body( $auth_response ) );

			if ( ! empty( $body ) ) {
				$token           = $body->access_token;
				$tweets_response = wp_remote_get(
					'https://api.twitter.com/1.1/statuses/user_timeline.json?screen_name=' . $settings['user_name'] . '&count=999&tweet_mode=extended',
					[
						'httpversion' => '1.1',
						'blocking'    => true,
						'headers'     => ['Authorization' => "Bearer $token"],
					]
				);

				if ( ! is_wp_error( $tweets_response ) ) {
					$twitter_data = json_decode( wp_remote_retrieve_body( $tweets_response ), true );
					set_transient( $transient_key, $twitter_data, 0 );
				}
			}
			if ( 'yes' == $settings['remove_cache'] ) {
				delete_transient( $transient_key );
			}

			switch ( $settings['sort_by'] ) {
				case 'old-posts':
					usort(
						$twitter_data,
						function ( $a, $b ) {
							if ( $a['created_at'] == $b['created_at'] ) {
								return 0;
							}

							return ( $a['created_at'] < $b['created_at'] ) ? -1 : 1;
						}
					);
					break;
				case 'favorite_count':
					usort(
						$twitter_data,
						function ( $a, $b ) {
							if ( $a['favorite_count'] == $b['favorite_count'] ) {
								return 0;
							}

							return ( $a['favorite_count'] > $b['favorite_count'] ) ? -1 : 1;
						}
					);
					break;
				case 'retweet_count':
					usort(
						$twitter_data,
						function ( $a, $b ) {
							if ( $a['retweet_count'] == $b['retweet_count'] ) {
								return 0;
							}

							return ( $a['retweet_count'] > $b['retweet_count'] ) ? -1 : 1;
						}
					);
					break;
				default:
					$twitter_data;
			}

			$items = array_splice( $twitter_data, $loaded_item, $settings['tweets_limit'] );

			foreach ( $items as $item ) :
				if ( ! empty( $item['entities']['urls'] ) ) {
					$content = str_replace( $item['entities']['urls'][0]['url'], '', $item['full_text'] );
				} else {
					$content = $item['full_text'];
				}

				$description = explode( ' ', $content );
				if ( ! empty( $settings['content_word_count'] ) && count( $description ) > $settings['content_word_count'] ) {
					$description_shorten = array_slice( $description, 0, $settings['content_word_count'] );
					$description         = implode( ' ', $description_shorten ) . '...';
				} else {
					$description = $content;
				}
				?>
				<div class="ha-tweet-item">

					<?php if ( 'yes' == $settings['show_twitter_logo'] ) : ?>
						<div class="ha-tweeter-feed-icon">
							<i class="fa fa-twitter"></i>
						</div>
					<?php endif; ?>

					<div class="ha-tweet-inner-wrapper">

						<div class="ha-tweet-author">
							<?php if ( 'yes' == $settings['show_user_image'] ) : ?>
								<a href="<?php echo esc_url( 'https://twitter.com/' . $user_name ); ?>">
									<img src="<?php echo esc_url( $item['user']['profile_image_url_https'] ); ?>" alt="<?php echo esc_attr( $item['user']['name'] ); ?>" class="ha-tweet-avatar">
								</a>
							<?php endif; ?>

							<div class="ha-tweet-user">
								<?php if ( 'yes' == $settings['show_name'] ) : ?>
									<a href="<?php echo esc_url( 'https://twitter.com/' . $user_name ); ?>" class="ha-tweet-author-name">
										<?php echo esc_html( $item['user']['name'] ); ?>
									</a>
								<?php endif; ?>

								<?php if ( 'yes' == $settings['show_user_name'] ) : ?>
									<a href="<?php echo esc_url( 'https://twitter.com/' . $user_name ); ?>" class="ha-tweet-username">
										<?php echo esc_html( $settings['user_name'] ); ?>
									</a>
								<?php endif; ?>
							</div>
						</div>

						<div class="ha-tweet-content">
							<p>
								<?php echo esc_html( $description ); ?>

								<?php if ( 'yes' == $settings['read_more'] ) : ?>
									<a href="<?php echo esc_url( '//twitter.com/' . $item['user']['screen_name'] . '/status/' . $item['id'] ); ?>" target="_blank">
										<?php echo esc_html( $settings['read_more_text'] ); ?>
									</a>
								<?php endif; ?>
							</p>

							<?php if ( 'yes' == $settings['show_date'] ) : ?>
								<div class="ha-tweet-date">
									<?php echo esc_html( date( 'M d Y', strtotime( $item['created_at'] ) ) ); ?>
								</div>
							<?php endif; ?>
						</div>

					</div>

					<?php if ( 'yes' == $settings['show_favorite'] || 'yes' == $settings['show_retweet'] ) : ?>
						<div class="ha-tweet-footer-wrapper">
							<div class="ha-tweet-footer">

								<?php if ( 'yes' == $settings['show_favorite'] ) : ?>
									<div class="ha-tweet-favorite">
										<?php echo esc_html( $item['favorite_count'] ); ?>
										<i class="fa fa-heart-o"></i>
									</div>
								<?php endif; ?>

								<?php if ( 'yes' == $settings['show_retweet'] ) : ?>
									<div class="ha-tweet-retweet">
										<?php echo esc_html( $item['retweet_count'] ); ?>
										<i class="fa fa-retweet"></i>
									</div>
								<?php endif; ?>

							</div>
						</div>
					<?php endif; ?>

				</div>
				<?php
			endforeach;
		endif;
		wp_die();
	}


/** Function post_tab() called by wp_ajax hooks: {'nopriv_ha_post_tab_action', 'ha_post_tab_action'} **/
/** Parameters found in function post_tab(): {"post": ["post_tab_query", "term_id"]} **/
function post_tab() {

		$security = check_ajax_referer( 'happy_addons_nonce', 'security' );

		if ( true == $security ) :
			$settings   = $_POST['post_tab_query'];
			$post_type  = $settings['post_type'];
			$taxonomy   = $settings['taxonomy'];
			$item_limit = $settings['item_limit'];
			$excerpt    = $settings['excerpt'];
			$title_tag  = $settings['title_tag'];
			$term_id    = $_POST['term_id'];
			$orderby    = $settings['orderby'];
			$order      = $settings['order'];

			$args = [
				'post_status'      => 'publish',
				'post_type'        => $post_type,
				'posts_per_page'   => $item_limit,
				'orderby'          => $orderby,
				'order'            => $order,
				'suppress_filters' => false,
				'tax_query'        => [
					[
						'taxonomy' => $taxonomy,
						'field'    => 'term_id',
						'terms'    => $term_id,
					],
				],
			];

			$posts = get_posts( $args );

			if ( count( $posts ) !== 0 ) :
				?>
				<div class="ha-post-tab-item-wrapper active" data-term="<?php echo esc_attr( $term_id ); ?>">
					<?php foreach ( $posts as $post ) : ?>
						<div class="ha-post-tab-item">
							<div class="ha-post-tab-item-inner">
								<?php if ( has_post_thumbnail( $post->ID ) ) : ?>
									<a href="<?php echo esc_url( get_the_permalink( $post->ID ) ); ?>" class="ha-post-tab-thumb">
										<?php echo get_the_post_thumbnail( $post->ID, 'full' ); ?>
									</a>
								<?php endif; ?>
								<?php
									printf(
										'<%1$s class="ha-post-tab-title"><a href="%2$s">%3$s</a></%1$s>',
										ha_escape_tags( $title_tag, 'h2' ),
										esc_url( get_the_permalink( $post->ID ) ),
										esc_html( $post->post_title )
									);
								?>
								<?php if ( ( 'yes' == $settings['show_user_meta'] ) || ( 'yes' == $settings['show_date_meta'] ) ) : ?>
									<div class="ha-post-tab-meta">
										<?php if ( 'yes' == $settings['show_user_meta'] ) : ?>
											<span class="ha-post-tab-meta-author">
												<i class="fa fa-user-o"></i>
												<a href="<?php echo esc_url( get_author_posts_url( $post->post_author ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $post->post_author ) ); ?></a>
											</span>
										<?php endif; ?>
										<?php if ( 'yes' == $settings['show_date_meta'] ) : ?>
											<?php
											$archive_year  = get_the_time( 'Y', $post->ID );
											$archive_month = get_the_time( 'm', $post->ID );
											$archive_day   = get_the_time( 'd', $post->ID );
											?>
											<span class="ha-post-tab-meta-date">
												<i class="fa fa-calendar-o"></i>
												<a href="<?php echo esc_url( get_day_link( $archive_year, $archive_month, $archive_day ) ); ?>"><?php echo get_the_date( get_option( 'date_format' ), $post->ID ); ?></a>
											</span>
										<?php endif; ?>
									</div>
								<?php endif; ?>
								<?php if ( 'yes' === $excerpt && ! empty( $post->post_excerpt ) ) : ?>
									<div class="ha-post-tab-excerpt">
										<p><?php echo esc_html( $post->post_excerpt ); ?></p>
									</div>
								<?php endif; ?>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				<?php

			endif;
		endif;
		wp_die();
	}


/** Function process_ignore_request() called by wp_ajax hooks: {'ignore_attention_seeker'} **/
/** Parameters found in function process_ignore_request(): {"post": ["nonce", "id"]} **/
function process_ignore_request() {
        $nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
        $id = isset( $_POST['id'] ) ? $_POST['id'] : '';

        if ( wp_verify_nonce( $nonce, 'ignore_attention_seeker' ) && $id ) {
            $seeker = wp_list_filter( self::get_attentions(), ['_id' => $id] );
            $expire_date = $seeker[0]['end_date'] - time();
            set_transient( self::generate_db_key( $id ), 'ignore', $expire_date );
            wp_send_json_success();
        }

        exit;
    }


