<?php
/***
*
*Found actions: 1
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'ajax_process_image': {'regeneratethumbnail'}}
*
***/

/** Function ajax_process_image() called by wp_ajax hooks: {'regeneratethumbnail'} **/
/** Parameters found in function ajax_process_image(): {"request": ["id", "frt_wpnonce"]} **/
function ajax_process_image() {
		if ( empty( $_REQUEST['id'] ) ) {
			$this->ob_clean();
			wp_die( wp_json_encode( array( 'error' => esc_html__( 'No attachment ID submitted.', 'force-regenerate-thumbnails' ) ) ) );
		}

		// No timeout limit.
		if ( $this->stl_check() ) {
			set_time_limit( 0 );
		}

		// Don't break the JSON result.
		error_reporting( 0 );
		$id = (int) $_REQUEST['id'];

		try {
			header( 'Content-type: application/json' );
			if ( ! current_user_can( $this->capability ) ) {
				throw new Exception( esc_html__( 'Your user account does not have permission to regenerate thumbnails.', 'force-regenerate-thumbnails' ) );
			}
			if ( empty( $_REQUEST['frt_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['frt_wpnonce'] ), 'force-regenerate-attachment' ) ) {
				throw new Exception( esc_html__( 'Access token has expired, please reload the page.', 'force-regenerate-thumbnails' ) );
			}

			if ( apply_filters( 'regenerate_thumbs_skip_image', false, $id ) ) {
				die(
					wp_json_encode(
						array(
							/* translators: %d: attachment ID number */
							'success' => '<div id="message" class="notice notice-info"><p>' . sprintf( esc_html__( 'Skipped: %d', 'force-regenerate-thumbnails' ), (int) $id ) . '</p></div>',
						)
					)
				);
			}

			$image = get_post( $id );

			if ( is_null( $image ) ) {
				/* translators: %d: attachment ID number */
				throw new Exception( sprintf( esc_html__( 'Failed: %d is an invalid media ID.', 'force-regenerate-thumbnails' ), (int) $id ) );
			}

			if ( 'attachment' !== $image->post_type || ( 'image/' !== substr( $image->post_mime_type, 0, 6 ) && 'application/pdf' !== $image->post_mime_type ) ) {
				/* translators: %d: attachment ID number */
				throw new Exception( sprintf( esc_html__( 'Failed: %d is an invalid media ID.', 'force-regenerate-thumbnails' ), (int) $id ) );
			}

			if ( 'application/pdf' === $image->post_mime_type && ! extension_loaded( 'imagick' ) ) {
				throw new Exception( esc_html__( 'Failed: The imagick extension is required for PDF files.', 'force-regenerate-thumbnails' ) );
			}

			if ( 'image/svg+xml' === $image->post_mime_type ) {
				die(
					wp_json_encode(
						array(
							/* translators: %d: attachment ID number */
							'success' => '<div id="message" class="notice notice-info"><p>' . sprintf( esc_html__( 'Skipped: %d is a SVG', 'force-regenerate-thumbnails' ), (int) $id ) . '</p></div>',
						)
					)
				);
			}

			$upload_dir = wp_get_upload_dir();
			$meta       = wp_get_attachment_metadata( $image->ID );

			// Get full-size image.
			$image_fullpath = $this->get_attachment_path( $image->ID, $meta );

			$debug_1 = $image_fullpath;
			$debug_2 = '';
			$debug_3 = '';
			$debug_4 = '';

			// Didn't get a valid image path.
			if ( empty( $image_fullpath ) || false === realpath( $image_fullpath ) ) {
				throw new Exception( esc_html__( 'The originally uploaded image file cannot be found.', 'force-regenerate-thumbnails' ) );
			}

			$thumb_deleted    = array();
			$thumb_error      = array();
			$thumb_regenerate = array();

			/**
			 * Try delete all thumbnails
			 */
			if ( ! empty( $meta['sizes'] ) && is_iterable( $meta['sizes'] ) ) {
				foreach ( $meta['sizes'] as $size_data ) {
					if ( empty( $size_data['file'] ) ) {
						continue;
					}
					$thumb_fullpath = trailingslashit( $file_info['dirname'] ) . wp_basename( $size_data['file'] );
					if ( apply_filters( 'regenerate_thumbs_weak', false, $thumb_fullpath ) ) {
						continue;
					}
					if ( $thumb_fullpath !== $image_fullpath && is_file( $thumb_fullpath ) ) {
						do_action( 'regenerate_thumbs_pre_delete', $thumb_fullpath );
						unlink( $thumb_fullpath );
						if ( is_file( $thumb_fullpath . '.webp' ) ) {
							unlink( $thumb_fullpath . '.webp' );
						}
						clearstatcache();
						do_action( 'regenerate_thumbs_post_delete', $thumb_fullpath );
						if ( ! is_file( $thumb_fullpath ) ) {
							$thumb_deleted[] = sprintf( '%dx%d', $size_data['width'], $size_data['height'] );
						} else {
							$thumb_error[] = sprintf( '%dx%d', $size_data['width'], $size_data['height'] );
						}
					}
				}
			}

			// Hack to find thumbnail.
			$file_info = pathinfo( $image_fullpath );
			$file_stem = $this->remove_from_end( $file_info['filename'], '-scaled' ) . '-';

			$files = array();
			$path  = opendir( $file_info['dirname'] );

			if ( false !== $path ) {
				$thumb = readdir( $path );
				while ( false !== $thumb ) {
					if ( 0 === strpos( $thumb, $file_stem ) && str_ends_with( $thumb, $file_info['extension'] ) ) {
						$files[] = $thumb;
					}
					$thumb = readdir( $path );
				}
				closedir( $path );
				sort( $files );
			}

			foreach ( $files as $thumb ) {
				$thumb_fullpath = trailingslashit( $file_info['dirname'] ) . $thumb;
				if ( apply_filters( 'regenerate_thumbs_weak', false, $thumb_fullpath ) ) {
					continue;
				}

				$thumb_info  = pathinfo( $thumb_fullpath );
				$valid_thumb = explode( $file_stem, $thumb_info['filename'] );
				if ( '' === $valid_thumb[0] && ! empty( $valid_thumb[1] ) ) {
					if ( 0 === strpos( $valid_thumb[1], 'scaled-' ) ) {
						$valid_thumb[1] = str_replace( 'scaled-', '', $valid_thumb[1] );
					}
					$dimension_thumb = explode( 'x', $valid_thumb[1] );
					if ( 2 === count( $dimension_thumb ) ) {
						if ( is_numeric( $dimension_thumb[0] ) && is_numeric( $dimension_thumb[1] ) ) {
							do_action( 'regenerate_thumbs_pre_delete', $thumb_fullpath );
							unlink( $thumb_fullpath );
							if ( is_file( $thumb_fullpath . '.webp' ) ) {
								unlink( $thumb_fullpath . '.webp' );
							}
							clearstatcache();
							do_action( 'regenerate_thumbs_post_delete', $thumb_fullpath );
							if ( ! is_file( $thumb_fullpath ) ) {
								$thumb_deleted[] = sprintf( '%dx%d', $dimension_thumb[0], $dimension_thumb[1] );
							} else {
								$thumb_error[] = sprintf( '%dx%d', $dimension_thumb[0], $dimension_thumb[1] );
							}
						}
					}
				}
			}

			/**
			 * Regenerate all thumbnails
			 */
			if ( function_exists( 'wp_get_original_image_path' ) ) {
				$original_path = apply_filters( 'regenerate_thumbs_original_image', wp_get_original_image_path( $image->ID, true ) );
			}
			if ( empty( $original_path ) || ! is_file( $original_path ) ) {
				$original_path = $image_fullpath;
			}

			$metadata = wp_generate_attachment_metadata( $image->ID, $original_path );
			if ( is_wp_error( $metadata ) ) {
				throw new Exception( esc_html( $metadata->get_error_message() ) );
			}
			if ( empty( $metadata ) ) {
				throw new Exception( esc_html__( 'Unknown failure.', 'force-regenerate-thumbnails' ) );
			}
			wp_update_attachment_metadata( $image->ID, $metadata );
			do_action( 'regenerate_thumbs_post_update', $image->ID, $original_path );

			/**
			 * Verify results (deleted, errors, success)
			 */
			$files = array();
			$path  = opendir( $file_info['dirname'] );
			if ( false !== $path ) {
				$thumb = readdir( $path );
				while ( false !== $thumb ) {
					/* if ( ! ( strrpos( $thumb, $file_info['filename'] ) === false ) ) { */
					if ( 0 === strpos( $thumb, $file_stem ) && str_ends_with( $thumb, $file_info['extension'] ) ) {
						$files[] = $thumb;
					}
					$thumb = readdir( $path );
				}
				closedir( $path );
				sort( $files );
			}
			if ( ! empty( $metadata['sizes'] ) && is_iterable( $metadata['sizes'] ) ) {
				foreach ( $metadata['sizes'] as $size_data ) {
					if ( empty( $size_data['file'] ) ) {
						continue;
					}
					if ( in_array( $size_data['file'], $files, true ) ) {
						continue;
					}
					$thumb_regenerate[] = sprintf( '%dx%d', $size_data['width'], $size_data['height'] );
				}
			}

			foreach ( $files as $thumb ) {
				$thumb_fullpath = trailingslashit( $file_info['dirname'] ) . $thumb;
				$thumb_info     = pathinfo( $thumb_fullpath );
				$valid_thumb    = explode( $file_stem, $thumb_info['filename'] );
				if ( '' === $valid_thumb[0] ) {
					$dimension_thumb = explode( 'x', $valid_thumb[1] );
					if ( 2 === count( $dimension_thumb ) ) {
						if ( is_numeric( $dimension_thumb[0] ) && is_numeric( $dimension_thumb[1] ) ) {
							$thumb_regenerate[] = sprintf( '%dx%d', $dimension_thumb[0], $dimension_thumb[1] );
						}
					}
				}
			}

			// Remove success if has in error list.
			foreach ( $thumb_regenerate as $key => $regenerate ) {
				if ( in_array( $regenerate, $thumb_error, true ) ) {
					unset( $thumb_regenerate[ $key ] );
				}
			}

			// Remove deleted if has in success list, so that we only show those that were no longer registered.
			foreach ( $thumb_deleted as $key => $deleted ) {
				if ( in_array( $deleted, $thumb_regenerate, true ) ) {
					unset( $thumb_deleted[ $key ] );
				}
			}

			/**
			 * Display results
			 */
			ob_start(); // To suppress any error output.
			$message = '<strong>' .
				sprintf(
					/* translators: 1: attachment title 2: attachment ID number */
					esc_html__( '%1$s (ID %2$d)', 'force-regenerate-thumbnails' ),
					esc_html( get_the_title( $id ) ),
					(int) $image->ID
				) .
				'</strong>';
			$message .= '<br><br>';
			/* translators: %s: the path to the uploads directory */
			$message .= '<code>' . sprintf( esc_html__( 'Upload Directory: %s', 'force-regenerate-thumbnails' ), esc_html( $upload_dir['basedir'] ) ) . '</code><br>';
			/* translators: %s: the base URL of the uploads directory */
			$message .= '<code>' . sprintf( esc_html__( 'Upload URL: %s', 'force-regenerate-thumbnails' ), esc_html( $upload_dir['baseurl'] ) ) . '</code><br>';
			/* translators: %s: the full path of the image */
			$message .= '<code>' . sprintf( esc_html__( 'Image: %s', 'force-regenerate-thumbnails' ), esc_html( $debug_1 ) ) . '</code><br>';
			if ( $debug_2 ) {
				/* translators: %s: debug info, if populated */
				$message .= '<code>' . sprintf( esc_html__( 'Image Debug 2: %s', 'force-regenerate-thumbnails' ), esc_html( $debug_2 ) ) . '</code><br>';
			}
			if ( $debug_3 ) {
				/* translators: %s: debug info, if populated */
				$message .= '<code>' . sprintf( esc_html__( 'Image Debug 3: %s', 'force-regenerate-thumbnails' ), esc_html( $debug_3 ) ) . '</code><br>';
			}
			if ( $debug_4 ) {
				/* translators: %s: debug info, if populated */
				$message .= '<code>' . sprintf( esc_html__( 'Image Debug 4: %s', 'force-regenerate-thumbnails' ), esc_html( $debug_4 ) ) . '</code><br>';
			}

			$message .= '<br>';

			if ( count( $thumb_deleted ) > 0 ) {
				/* translators: %s: list of deleted thumbs */
				$message .= sprintf( esc_html__( 'Deleted: %s', 'force-regenerate-thumbnails' ), esc_html( implode( ', ', $thumb_deleted ) ) ) . '<br>';
			}
			if ( count( $thumb_error ) > 0 ) {
				/* translators: %s: an error message (translated elsewhere) */
				$message .= '<strong><span style="color: #dd3d36;">' . sprintf( esc_html__( 'Deleted error: %s', 'force-regenerate-thumbnails' ), esc_html( implode( ', ', $thumb_error ) ) ) . '</span></strong><br>';
				/* translators: %s: the path to the uploads directory */
				$message .= '<span style="color: #dd3d36;">' . sprintf( esc_html__( 'Please ensure the folder has write permissions (chmod 755): %s', 'force-regenerate-thumbnails' ), esc_html( $upload_dir['basedir'] ) ) . '</span><br>';
			}
			if ( count( $thumb_regenerate ) > 0 ) {
				/* translators: %s: list of regenerated thumbs/sizes */
				$message .= sprintf( esc_html__( 'Regenerate: %s', 'force-regenerate-thumbnails' ), esc_html( implode( ', ', $thumb_regenerate ) ) ) . '<br>';
				if ( count( $thumb_error ) <= 0 ) {
					/* translators: %s: time elapsed */
					$message .= sprintf( esc_html__( 'Successfully regenerated in %s seconds', 'force-regenerate-thumbnails' ), esc_html( timer_stop() ) ) . '<br>';
				}
			}

			$message = $this->remove_from_end( $message, '<br>' );

			$this->ob_clean();
			if ( count( $thumb_error ) > 0 ) {
				die( wp_json_encode( array( 'error' => '<div id="message" class="notice notice-error"><p>' . $message . '</p></div>' ) ) );
			} else {
				die( wp_json_encode( array( 'success' => '<div id="message" class="notice notice-success"><p>' . $message . '</p></div>' ) ) );
			}
		} catch ( Exception $e ) {
			$this->die_json_failure_msg( $id, '<b><span style="color: #DD3D36;">' . $e->getMessage() . '</span></b>' );
		}
		exit;
	}

	/**
	 * Retrieves the path of an attachment via the ID number.
	 *
	 * @param int   $id The attachment ID number.
	 * @param array $meta The attachment metadata.
	 * @return string The full path to the image.
	 */
	function get_attachment_path( $id, $meta ) {

		// Retrieve the location of the WordPress upload folder.
		$upload_dir  = wp_upload_dir( null, false, true );
		$upload_path = trailingslashit( $upload_dir['basedir'] );

		// Get the file path from postmeta.
		$file               = get_post_meta( $id, '_wp_attached_file', true );
		$file_path          = ( 0 !== strpos( $file, '/' ) && ! preg_match( '|^.:\\\|', $file ) ? $upload_path . $file : $file );
		$filtered_file_path = apply_filters( 'get_attached_file', $file_path, $id );
		if (
			(
				! $this->stream_wrapped( $filtered_file_path ) ||
				$this->stream_wrapper_exists()
			)
			&& is_file( $filtered_file_path )
		) {
			return $filtered_file_path;
		}
		if (
			(
				! $this->stream_wrapped( $file_path ) ||
				$this->stream_wrapper_exists()
			)
			&& is_file( $file_path )
		) {
			return $file_path;
		}
		if ( is_array( $meta ) && ! empty( $meta['file'] ) ) {
			$file_path = $meta['file'];
			if ( $this->stream_wrapped( $file_path ) && ! $this->stream_wrapper_exists() ) {
				return '';
			}
			if ( is_file( $file_path ) ) {
				return $file_path;
			}
			$file_path = trailingslashit( $upload_path ) . $file_path;
			if ( is_file( $file_path ) ) {
				return $file_path;
			}
			$upload_path = trailingslashit( WP_CONTENT_DIR ) . 'uploads/';
			$file_path   = $upload_path . $meta['file'];
			if ( is_file( $file_path ) ) {
				return $file_path;
			}
		}
		return '';
	}

	/**
	 * Checks the existence of a cloud storage stream wrapper.
	 *
	 * @return bool True if a supported stream wrapper is found, false otherwise.
	 */
	function stream_wrapper_exists() {
		$wrappers = stream_get_wrappers();
		if ( ! is_iterable( $wrappers ) ) {
			return false;
		}
		foreach ( $wrappers as $wrapper ) {
			if ( strpos( $wrapper, 's3' ) === 0 ) {
				return true;
			}
			if ( strpos( $wrapper, 'gs' ) === 0 ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Checks the filename for an S3 or GCS stream wrapper.
	 *
	 * @param string $filename The filename to be searched.
	 * @return bool True if a stream wrapper is found, false otherwise.
	 */
	function stream_wrapped( $filename ) {
		if ( false !== strpos( $filename, '://' ) ) {
			if ( strpos( $filename, 's3' ) === 0 ) {
				return true;
			}
			if ( strpos( $filename, 'gs' ) === 0 ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Trims the given 'needle' from the end of the 'haystack'.
	 *
	 * @param string $haystack The string to be modified if it contains needle.
	 * @param string $needle The string to remove if it is at the end of the haystack.
	 * @return string The haystack with needle removed from the end.
	 */
	function remove_from_end( $haystack, $needle ) {
		$needle_length = strlen( $needle );
		if ( substr( $haystack, -$needle_length ) === $needle ) {
			return substr( $haystack, 0, -$needle_length );
		}
		return $haystack;
	}

	/**
	 * Checks if a function is disabled or does not exist.
	 *
	 * @param string $function The name of a function to test.
	 * @param bool   $debug Whether to output debugging.
	 * @return bool True if the function is available, False if not.
	 */
	function function_exists( $function, $debug = false ) {
		if ( extension_loaded( 'suhosin' ) && function_exists( 'ini_get' ) ) {
			// phpcs:ignore WordPress.PHP.NoSilencedErrors
			$suhosin_disabled = @ini_get( 'suhosin.executor.func.blacklist' );
			if ( ! empty( $suhosin_disabled ) ) {
				$suhosin_disabled = explode( ',', $suhosin_disabled );
				$suhosin_disabled = array_map( 'trim', $suhosin_disabled );
				$suhosin_disabled = array_map( 'strtolower', $suhosin_disabled );
				if ( function_exists( $function ) && ! in_array( $function, $suhosin_disabled, true ) ) {
					return true;
				}
				return false;
			}
		}
		return \function_exists( $function );
	}

	/**
	 * Find out if set_time_limit() is allowed.
	 */
	function stl_check() {
		if ( defined( 'FTR_DISABLE_STL' ) && FTR_DISABLE_STL ) {
			// set_time_limit() disabled by user.
			return false;
		}
		if ( function_exists( 'wp_is_ini_value_changeable' ) && ! wp_is_ini_value_changeable( 'max_execution_time' ) ) {
			// max_execution_time is not configurable.
			return false;
		}
		return $this->function_exists( 'set_time_limit' );
	}

	/**
	 * Clear output buffers without throwing a fit.
	 */
	function ob_clean() {
		if ( ob_get_length() ) {
			ob_end_clean();
		}
	}

	/**
	 * Helper to make a JSON failure message
	 *
	 * @param integer $id An attachment ID.
	 * @param string  $message An error message.
	 * @access public
	 * @since 1.8
	 */
	function die_json_failure_msg( $id, $message ) {
		$this->ob_clean();
		die(
			wp_json_encode(
				array(
					/* translators: %d: the attachment ID number */
					'error' => sprintf( esc_html__( '(ID %d)', 'force-regenerate-thumbnails' ), $id ) . '<br />' . $message,
				)
			)
		);
	}
}


