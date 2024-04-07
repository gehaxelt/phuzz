<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:3
*Total parameter names extracted: 3
*Overview: {'dismiss_warnings': {'mainwp-child_dismiss_warnings'}, 'download_archive': {'mainwp_backupbuddy_download_archive'}, 'download_htaccess': {'mainwp_wordfence_download_htaccess'}}
*
***/

/** Function dismiss_warnings() called by wp_ajax hooks: {'mainwp-child_dismiss_warnings'} **/
/** Parameters found in function dismiss_warnings(): {"post": ["what", "warnings"]} **/
function dismiss_warnings() {
		if ( isset( $_POST['what'] ) ) {
			$dismissWarnings = get_option( 'mainwp_child_dismiss_warnings' );
			if ( ! is_array( $dismissWarnings ) ) {
				$dismissWarnings = array();
			}
			if ( 'warning' == $_POST['what'] ) {
				if ( isset( $_POST['warnings'] ) ) {
					$warnings = intval( $_POST['warnings'] );
				} else {
					$warnings = self::get_warnings();
				}
				$dismissWarnings['warnings'] = $warnings;
			}
			MainWP_Helper::update_option( 'mainwp_child_dismiss_warnings', $dismissWarnings );
		}
	}


/** Function download_archive() called by wp_ajax hooks: {'mainwp_backupbuddy_download_archive'} **/
/** Parameters found in function download_archive(): {"get": ["_wpnonce"]} **/
function download_archive() {

		if ( ! isset( $_GET['_wpnonce'] ) || empty( $_GET['_wpnonce'] ) ) {
			die( '-1' );
		}

		if ( ! MainWP_Utility::verify_nonce_without_session( $_GET['_wpnonce'], 'mainwp_download_backup' ) ) {
			die( '-2' );
		}

		\backupbuddy_core::verifyAjaxAccess();

		if ( is_multisite() && ! current_user_can( 'manage_network' ) ) { // If a Network and NOT the superadmin must make sure they can only download the specific subsite backups for security purposes.

			if ( ! strstr( \pb_backupbuddy::_GET( 'backupbuddy_backup' ), \backupbuddy_core::backup_prefix() ) ) {
				die( 'Access Denied. You may only download backups specific to your Multisite Subsite. Only Network Admins may download backups for another subsite in the network.' );
			}
		}

		if ( ! file_exists( \backupbuddy_core::getBackupDirectory() . \pb_backupbuddy::_GET( 'backupbuddy_backup' ) ) ) { // Does not exist.
			die( 'Error #548957857584784332. The requested backup file does not exist. It may have already been deleted.' );
		}

		$abspath    = str_replace( '\\', '/', ABSPATH );
		$backup_dir = str_replace( '\\', '/', \backupbuddy_core::getBackupDirectory() );

		if ( false === stristr( $backup_dir, $abspath ) ) {
			die( 'Error #5432532. You cannot download backups stored outside of the WordPress web root. Please use FTP or other means.' );
		}

		$sitepath     = str_replace( $abspath, '', $backup_dir );
		$download_url = rtrim( site_url(), '/\\' ) . '/' . trim( $sitepath, '/\\' ) . '/' . \pb_backupbuddy::_GET( 'backupbuddy_backup' );

		if ( '1' == \pb_backupbuddy::$options['lock_archives_directory'] ) {

			if ( file_exists( \backupbuddy_core::getBackupDirectory() . '.htaccess' ) ) {
				$unlink_status = unlink( \backupbuddy_core::getBackupDirectory() . '.htaccess' );
				if ( false === $unlink_status ) {
					die( 'Error #844594. Unable to temporarily remove .htaccess security protection on archives directory to allow downloading. Please verify permissions of the BackupBuddy archives directory or manually download via FTP.' );
				}
			}

			header( 'Location: ' . $download_url );
			ob_clean();
			flush();
			sleep( 8 );

			$htaccess_creation_status = file_put_contents( \backupbuddy_core::getBackupDirectory() . '.htaccess', 'deny from all' );
			if ( false === $htaccess_creation_status ) {
				die( 'Error #344894545. Security Warning! Unable to create security file (.htaccess) in backups archive directory. This file prevents unauthorized downloading of backups should someone be able to guess the backup location and filenames. This is unlikely but for best security should be in place. Please verify permissions on the backups directory.' );
			}
		} else {
			header( 'Location: ' . $download_url );
		}
		die();
	}


/** Function download_htaccess() called by wp_ajax hooks: {'mainwp_wordfence_download_htaccess'} **/
/** Parameters found in function download_htaccess(): {"get": ["_wpnonce"]} **/
function download_htaccess() {
		if ( ! isset( $_GET['_wpnonce'] ) || empty( $_GET['_wpnonce'] ) ) {
			die( '-1' );
		}

		if ( ! MainWP_Utility::verify_nonce_without_session( $_GET['_wpnonce'], 'mainwp_download_htaccess' ) ) {
			die( '-2' );
		}

		$url = site_url();
		$url = preg_replace( '/^https?:\/\//i', '', $url );
		$url = preg_replace( '/[^a-zA-Z0-9\.]+/', '_', $url );
		$url = preg_replace( '/^_+/', '', $url );
		$url = preg_replace( '/_+$/', '', $url );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename="htaccess_Backup_for_' . $url . '.txt"' );
		$file = \wfCache::getHtaccessPath();
		readfile( $file );
		die();
	}


