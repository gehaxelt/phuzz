<?php
/***
*
*Found actions: 19
*Found functions:19
*Extracted functions:19
*Total parameter names extracted: 13
*Overview: {'ajax_delete_widget': {'mm_delete_widget'}, 'ajax_save_grid_data': {'mm_save_grid_data'}, 'ajax_get_empty_grid_row': {'mm_get_empty_grid_row'}, 'output_spacer_block_html': {'mm_get_toggle_block_spacer'}, 'ajax_save_menu_item': {'mm_save_menu_item'}, 'ajax_update_widget_columns': {'mm_update_widget_columns'}, 'ajax_show_widget_form': {'mm_edit_widget'}, 'ajax_save_menu_item_settings': {'mm_save_menu_item_settings'}, 'save': {'mm_save_settings'}, 'ajax_add_widget': {'mm_add_widget'}, 'ajax_save_theme': {'megamenu_save_theme'}, 'output_menu_toggle_block_animated_html': {'mm_get_toggle_block_menu_toggle_animated'}, 'ajax_update_menu_item_columns': {'mm_update_menu_item_columns'}, 'ajax_get_lightbox_html': {'mm_get_lightbox_html'}, 'ajax_show_menu_item_form': {'mm_edit_menu_item'}, 'ajax_save_widget': {'mm_save_widget'}, 'output_menu_toggle_block_html': {'mm_get_toggle_block_menu_toggle'}, 'ajax_reorder_items': {'mm_reorder_items'}, 'ajax_get_empty_grid_column': {'mm_get_empty_grid_column'}}
*
***/

/** Function ajax_delete_widget() called by wp_ajax hooks: {'mm_delete_widget'} **/
/** Parameters found in function ajax_delete_widget(): {"post": ["widget_id"]} **/
function ajax_delete_widget() {

			check_ajax_referer( 'megamenu_edit' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$widget_id = sanitize_text_field( $_POST['widget_id'] );

			$deleted = $this->delete_widget( $widget_id );

			if ( $deleted ) {
				$this->send_json_success( sprintf( __( 'Deleted %s', 'megamenu' ), $widget_id ) );
			} else {
				$this->send_json_error( sprintf( __( 'Failed to delete %s', 'megamenu' ), $widget_id ) );
			}

		}


/** Function ajax_save_grid_data() called by wp_ajax hooks: {'mm_save_grid_data'} **/
/** Parameters found in function ajax_save_grid_data(): {"post": ["grid", "parent_menu_item"]} **/
function ajax_save_grid_data() {

			check_ajax_referer( 'megamenu_edit' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$grid                = isset( $_POST['grid'] ) ? $_POST['grid'] : false;
			$parent_menu_item_id = absint( $_POST['parent_menu_item'] );

			$saved = true;

			$existing_settings = get_post_meta( $parent_menu_item_id, '_megamenu', true );

			if ( is_array( $grid ) ) {

				$submitted_settings = array_merge( $existing_settings, array( 'grid' => $grid ) );

			}

			update_post_meta( $parent_menu_item_id, '_megamenu', $submitted_settings );

			if ( $saved ) {
				$this->send_json_success( sprintf( __( 'Saved (%s)', 'megamenu' ), json_encode( $grid ) ) );
			} else {
				$this->send_json_error( sprintf( __( "Didn't save", 'megamenu' ), json_encode( $grid ) ) );
			}

		}


/** Function ajax_get_empty_grid_row() called by wp_ajax hooks: {'mm_get_empty_grid_row'} **/
/** No params detected :-/ **/


/** Function output_spacer_block_html() called by wp_ajax hooks: {'mm_get_toggle_block_spacer'} **/
/** No params detected :-/ **/


/** Function ajax_save_menu_item() called by wp_ajax hooks: {'mm_save_menu_item'} **/
/** Parameters found in function ajax_save_menu_item(): {"post": ["menu_item_id", "settings"]} **/
function ajax_save_menu_item() {

			$menu_item_id = absint( sanitize_text_field( $_POST['menu_item_id'] ) );

			check_ajax_referer( 'megamenu_save_menu_item_' . $menu_item_id );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$submitted_settings = isset( $_POST['settings'] ) ? $_POST['settings'] : array();

			if ( $menu_item_id > 0 && is_array( $submitted_settings ) ) {

				$existing_settings = get_post_meta( $menu_item_id, '_megamenu', true );

				if ( is_array( $existing_settings ) ) {
					$submitted_settings = array_merge( $existing_settings, $submitted_settings );
				}

				update_post_meta( $menu_item_id, '_megamenu', $submitted_settings );
			}

			$this->send_json_success( sprintf( __( 'Saved %s', 'megamenu' ), $id_base ) );

		}


/** Function ajax_update_widget_columns() called by wp_ajax hooks: {'mm_update_widget_columns'} **/
/** Parameters found in function ajax_update_widget_columns(): {"post": ["id", "columns"]} **/
function ajax_update_widget_columns() {

			check_ajax_referer( 'megamenu_edit' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$widget_id = sanitize_text_field( $_POST['id'] );
			$columns   = absint( $_POST['columns'] );

			$updated = $this->update_widget_columns( $widget_id, $columns );

			if ( $updated ) {
				$this->send_json_success( sprintf( __( 'Updated %1$s (new columns: %2$d)', 'megamenu' ), $widget_id, $columns ) );
			} else {
				$this->send_json_error( sprintf( __( 'Failed to update %s', 'megamenu' ), $widget_id ) );
			}

		}


/** Function ajax_show_widget_form() called by wp_ajax hooks: {'mm_edit_widget'} **/
/** Parameters found in function ajax_show_widget_form(): {"post": ["widget_id"]} **/
function ajax_show_widget_form() {

			check_ajax_referer( 'megamenu_edit' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$widget_id = sanitize_text_field( $_POST['widget_id'] );

			if ( ob_get_contents() ) {
				ob_clean(); // remove any warnings or output from other plugins which may corrupt the response
			}

			wp_die( trim( $this->show_widget_form( $widget_id ) ) );

		}


/** Function ajax_save_menu_item_settings() called by wp_ajax hooks: {'mm_save_menu_item_settings'} **/
/** Parameters found in function ajax_save_menu_item_settings(): {"post": ["settings", "menu_item_id", "tab", "clear_cache"]} **/
function ajax_save_menu_item_settings() {

			check_ajax_referer( 'megamenu_edit' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$submitted_settings = isset( $_POST['settings'] ) ? $_POST['settings'] : array();

			$menu_item_id = absint( $_POST['menu_item_id'] );

			if ( $menu_item_id > 0 && is_array( $submitted_settings ) ) {

				// only check the checkbox values if the general settings form was submitted
				if ( isset( $_POST['tab'] ) && $_POST['tab'] == 'general_settings' ) {

					$checkboxes = array( 'hide_text', 'disable_link', 'hide_arrow', 'hide_on_mobile', 'hide_on_desktop', 'close_after_click', 'hide_sub_menu_on_mobile', 'collapse_children' );

					foreach ( $checkboxes as $checkbox ) {
						if ( ! isset( $submitted_settings[ $checkbox ] ) ) {
							$submitted_settings[ $checkbox ] = 'false';
						}
					}
				}

				$submitted_settings = apply_filters( 'megamenu_menu_item_submitted_settings', $submitted_settings, $menu_item_id );

				$existing_settings = get_post_meta( $menu_item_id, '_megamenu', true );

				if ( is_array( $existing_settings ) ) {

					$submitted_settings = array_merge( $existing_settings, $submitted_settings );

				}

				update_post_meta( $menu_item_id, '_megamenu', $submitted_settings );

				do_action( 'megamenu_save_menu_item_settings', $menu_item_id );

			}

			if ( isset( $_POST['clear_cache'] ) ) {

				do_action( 'megamenu_delete_cache' );

			}

			if ( ob_get_contents() ) {
				ob_clean(); // remove any warnings or output from other plugins which may corrupt the response
			}

			wp_send_json_success();

		}


/** Function save() called by wp_ajax hooks: {'mm_save_settings'} **/
/** Parameters found in function save(): {"post": ["menu", "megamenu_meta"]} **/
function save() {
			check_ajax_referer( 'megamenu_edit', 'nonce' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			if ( isset( $_POST['menu'] ) && $_POST['menu'] > 0 && is_nav_menu( $_POST['menu'] ) && isset( $_POST['megamenu_meta'] ) ) {
				$raw_submitted_settings    = $_POST['megamenu_meta'];
				$parsed_submitted_settings = json_decode( stripslashes( $raw_submitted_settings ), true );
				$submitted_settings        = array();

				foreach ( $parsed_submitted_settings as $index => $value ) {
					$name = $value['name'];

					preg_match_all( '/\[(.*?)\]/', $name, $matches ); // find values between square brackets.

					if ( isset( $matches[1][0] ) && isset( $matches[1][1] ) ) {
						$location                                    = $matches[1][0];
						$setting                                     = $matches[1][1];
						$submitted_settings[ $location ][ $setting ] = $value['value'];
					}
				}

				$submitted_settings = apply_filters( 'megamenu_submitted_settings_meta', $submitted_settings );

				if ( ! get_option( 'megamenu_settings' ) ) {
					update_option( 'megamenu_settings', $submitted_settings );
				} else {
					$existing_settings = get_option( 'megamenu_settings' );

					foreach ( $submitted_settings as $location => $settings ) {
						if ( isset( $existing_settings[ $location ] ) ) {
							$existing_settings[ $location ] = array_merge( $existing_settings[ $location ], $settings );

							if ( ! isset( $settings['enabled'] ) ) {
								unset( $existing_settings[ $location ]['enabled'] );
							}
						} else {
							$existing_settings[ $location ] = $settings;
						}
					}

					update_option( 'megamenu_settings', $existing_settings );
				}

				do_action( 'megamenu_after_save_settings' );
				do_action( 'megamenu_delete_cache' );
			}

			wp_die();
		}


/** Function ajax_add_widget() called by wp_ajax hooks: {'mm_add_widget'} **/
/** Parameters found in function ajax_add_widget(): {"post": ["id_base", "menu_item_id", "title", "is_grid_widget"]} **/
function ajax_add_widget() {

			check_ajax_referer( 'megamenu_edit' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$id_base        = sanitize_text_field( $_POST['id_base'] );
			$menu_item_id   = absint( $_POST['menu_item_id'] );
			$title          = sanitize_text_field( $_POST['title'] );
			$is_grid_widget = isset( $_POST['is_grid_widget'] ) && $_POST['is_grid_widget'] == 'true';

			$added = $this->add_widget( $id_base, $menu_item_id, $title, $is_grid_widget );

			if ( $added ) {
				$this->send_json_success( $added );
			} else {
				$this->send_json_error( sprintf( __( 'Failed to add %1$s to %2$d', 'megamenu' ), $id_base, $menu_item_id ) );
			}

		}


/** Function ajax_save_theme() called by wp_ajax hooks: {'megamenu_save_theme'} **/
/** No params detected :-/ **/


/** Function output_menu_toggle_block_animated_html() called by wp_ajax hooks: {'mm_get_toggle_block_menu_toggle_animated'} **/
/** No params detected :-/ **/


/** Function ajax_update_menu_item_columns() called by wp_ajax hooks: {'mm_update_menu_item_columns'} **/
/** Parameters found in function ajax_update_menu_item_columns(): {"post": ["id", "columns"]} **/
function ajax_update_menu_item_columns() {

			check_ajax_referer( 'megamenu_edit' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$id      = absint( $_POST['id'] );
			$columns = absint( $_POST['columns'] );

			$updated = $this->update_menu_item_columns( $id, $columns );

			if ( $updated ) {
				$this->send_json_success( sprintf( __( 'Updated %1$s (new columns: %2$d)', 'megamenu' ), $id, $columns ) );
			} else {
				$this->send_json_error( sprintf( __( 'Failed to update %s', 'megamenu' ), $id ) );
			}

		}


/** Function ajax_get_lightbox_html() called by wp_ajax hooks: {'mm_get_lightbox_html'} **/
/** No params detected :-/ **/


/** Function ajax_show_menu_item_form() called by wp_ajax hooks: {'mm_edit_menu_item'} **/
/** Parameters found in function ajax_show_menu_item_form(): {"post": ["widget_id"]} **/
function ajax_show_menu_item_form() {

			check_ajax_referer( 'megamenu_edit' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$menu_item_id = sanitize_text_field( $_POST['widget_id'] );

			$nonce = wp_create_nonce( 'megamenu_save_menu_item_' . $menu_item_id );

			$saved_settings = array_filter( (array) get_post_meta( $menu_item_id, '_megamenu', true ) );
			$menu_item_meta = array_merge( Mega_Menu_Nav_Menus::get_menu_item_defaults(), $saved_settings );

			if ( ob_get_contents() ) {
				ob_clean(); // remove any warnings or output from other plugins which may corrupt the response
			}
			?>

		<form method='post'>
			<input type='hidden' name='action' value='mm_save_menu_item' />
			<input type='hidden' name='menu_item_id' value='<?php echo esc_attr( $menu_item_id ); ?>' />
			<input type='hidden' name='_wpnonce'  value='<?php echo esc_attr( $nonce ); ?>' />
			<div class='widget-content'>
				<?php

				$css_version = get_transient( 'megamenu_css_version' );

				if ( $css_version && version_compare( $css_version, '2.6.1', '<' ) ) {
					$link    = "<a href='" . esc_attr( admin_url( 'admin.php?page=maxmegamenu_tools' ) ) . "'>" . __( 'Mega Menu' ) . ' > ' . __( 'Tools' ) . '</a>';
					$notice  = "<div class='notice notice-success'><p>";
					$notice .= sprintf( __( 'Your menu CSS needs to be updated before you can use the following setting. Please go to %s and Clear the CSS Cache (you will only need to do this once).', 'megamenu' ), $link );
					$notice .= '</p></div>';
					$notice .= '</div>';

					echo $notice;
				}

				?>

				<p>
					<label><?php _e( 'Sub menu columns', 'megamenu' ); ?></label>

					<select name="settings[submenu_columns]">
						<option value='1' <?php selected( $menu_item_meta['submenu_columns'], 1, true ); ?> >1 <?php __( 'column', 'megamenu' ); ?></option>
						<option value='2' <?php selected( $menu_item_meta['submenu_columns'], 2, true ); ?> >2 <?php __( 'columns', 'megamenu' ); ?></option>
						<option value='3' <?php selected( $menu_item_meta['submenu_columns'], 3, true ); ?> >3 <?php __( 'columns', 'megamenu' ); ?></option>
						<option value='4' <?php selected( $menu_item_meta['submenu_columns'], 4, true ); ?> >4 <?php __( 'columns', 'megamenu' ); ?></option>
						<option value='5' <?php selected( $menu_item_meta['submenu_columns'], 5, true ); ?> >5 <?php __( 'columns', 'megamenu' ); ?></option>
						<option value='6' <?php selected( $menu_item_meta['submenu_columns'], 6, true ); ?> >6 <?php __( 'columns', 'megamenu' ); ?></option>
					</select>
				</p>
				<p>
					<div class='widget-controls'>
						<a class='close' href='#close'><?php _e( 'Close', 'megamenu' ); ?></a>
					</div>

					<?php
						submit_button( __( 'Save' ), 'button-primary alignright', 'savewidget', false );
					?>
				</p>
			</div>
		</form>

			<?php

		}


/** Function ajax_save_widget() called by wp_ajax hooks: {'mm_save_widget'} **/
/** Parameters found in function ajax_save_widget(): {"post": ["widget_id", "id_base"]} **/
function ajax_save_widget() {

			$widget_id = sanitize_text_field( $_POST['widget_id'] );
			$id_base   = sanitize_text_field( $_POST['id_base'] );

			check_ajax_referer( 'megamenu_save_widget_' . $widget_id );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$saved = $this->save_widget( $id_base );

			if ( $saved ) {
				$this->send_json_success( sprintf( __( 'Saved %s', 'megamenu' ), $id_base ) );
			} else {
				$this->send_json_error( sprintf( __( 'Failed to save %s', 'megamenu' ), $id_base ) );
			}

		}


/** Function output_menu_toggle_block_html() called by wp_ajax hooks: {'mm_get_toggle_block_menu_toggle'} **/
/** Parameters found in function output_menu_toggle_block_html(): {"get": ["theme"]} **/
function output_menu_toggle_block_html( $block_id, $settings = array() ) {

			if ( empty( $settings ) ) {
				$block_id = '0';
			}

			$theme_id = 'default';

			if ( isset( $_GET['theme'] ) ) {
				$theme_id = esc_attr( $_GET['theme'] );

			}

			$defaults = $this->get_default_menu_toggle_block( $theme_id );

			$settings = array_merge( $defaults, $settings );

			?>

		<div class='block'>
			<div class='block-title'><?php _e( 'TOGGLE', 'megamenu' ); ?> <span title='<?php _e( 'Menu Toggle', 'megamenu' ); ?>' class="dashicons dashicons-menu"></span></div>
			<div class='block-settings'>
				<h3><?php _e( 'Menu Toggle Settings', 'megamenu' ); ?></h3>
				<input type='hidden' class='type' name='toggle_blocks[<?php echo $block_id; ?>][type]' value='menu_toggle' />
				<input type='hidden' class='align' name='toggle_blocks[<?php echo $block_id; ?>][align]' value='<?php echo $settings['align']; ?>'>
				<label>
					<?php _e( 'Closed Text', 'megamenu' ); ?><input type='text' class='closed_text' name='toggle_blocks[<?php echo $block_id; ?>][closed_text]' value='<?php echo stripslashes( esc_attr( $settings['closed_text'] ) ); ?>' />
				</label>
				<label>
					<?php _e( 'Open Text', 'megamenu' ); ?><input type='text' class='open_text' name='toggle_blocks[<?php echo $block_id; ?>][open_text]' value='<?php echo stripslashes( esc_attr( $settings['open_text'] ) ); ?>' />
				</label>
				<label>
					<?php _e( 'Closed Icon', 'megamenu' ); ?>
					<?php $this->print_icon_option( 'closed_icon', $block_id, $settings['closed_icon'], $this->toggle_icons() ); ?>
				</label>
				<label>
					<?php _e( 'Open Icon', 'megamenu' ); ?>
					<?php $this->print_icon_option( 'open_icon', $block_id, $settings['open_icon'], $this->toggle_icons() ); ?>
				</label>
				<label>
					<?php _e( 'Text Color', 'megamenu' ); ?>
					<?php $this->print_toggle_color_option( 'text_color', $block_id, $settings['text_color'] ); ?>
				</label>
				<label>
					<?php _e( 'Text Size', 'megamenu' ); ?><input type='text' class='text_size' name='toggle_blocks[<?php echo $block_id; ?>][text_size]' value='<?php echo stripslashes( esc_attr( $settings['text_size'] ) ); ?>' />
				</label>
				<label>
					<?php _e( 'Icon Color', 'megamenu' ); ?>
					<?php $this->print_toggle_color_option( 'icon_color', $block_id, $settings['icon_color'] ); ?>
				</label>
				<label>
					<?php _e( 'Icon Size', 'megamenu' ); ?><input type='text' class='icon_size' name='toggle_blocks[<?php echo $block_id; ?>][icon_size]' value='<?php echo stripslashes( esc_attr( $settings['icon_size'] ) ); ?>' />
				</label>
				<label>
					<?php _e( 'Icon Position', 'megamenu' ); ?><select name='toggle_blocks[<?php echo $block_id; ?>][icon_position]'>
						<option value='before' <?php selected( $settings['icon_position'], 'before' ); ?> ><?php _e( 'Before', 'megamenu' ); ?></option>
						<option value='after' <?php selected( $settings['icon_position'], 'after' ); ?> ><?php _e( 'After', 'megamenu' ); ?></option>
					</select>
				</label>
				<a class='mega-delete'><?php _e( 'Delete', 'megamenu' ); ?></a>
			</div>
		</div>

			<?php
		}


/** Function ajax_reorder_items() called by wp_ajax hooks: {'mm_reorder_items'} **/
/** Parameters found in function ajax_reorder_items(): {"post": ["items"]} **/
function ajax_reorder_items() {

			check_ajax_referer( 'megamenu_edit' );

			$capability = apply_filters( 'megamenu_options_capability', 'edit_theme_options' );

			if ( ! current_user_can( $capability ) ) {
				return;
			}

			$items = isset( $_POST['items'] ) ? $_POST['items'] : false;

			$saved = false;

			if ( $items ) {
				$moved = $this->reorder_items( $items );
			}

			if ( $moved ) {
				$this->send_json_success( sprintf( __( 'Moved (%s)', 'megamenu' ), json_encode( $items ) ) );
			} else {
				$this->send_json_error( sprintf( __( "Didn't move items", 'megamenu' ), json_encode( $items ) ) );
			}

		}


/** Function ajax_get_empty_grid_column() called by wp_ajax hooks: {'mm_get_empty_grid_column'} **/
/** No params detected :-/ **/


