<?php
/***
*
*Found actions: 9
*Found functions:9
*Extracted functions:9
*Total parameter names extracted: 6
*Overview: {'ajax_getBackupFolderSize': {'shortpixel_get_backup_size'}, 'ajaxBrowseContent': {'shortpixel_browse_content'}, 'ajax_proposeQuotaUpgrade': {'shortpixel_propose_upgrade'}, 'ajaxRequest': {'shortpixel_ajaxRequest'}, 'ajax_getItemView': {'shortpixel_get_item_view'}, 'deactivatePluginCallback': {'shortpixel_deactivate_plugin'}, 'ajax_processQueue': {'shortpixel_image_processing'}, 'ajax_checkquota': {'shortpixel_check_quota'}, 'ajax_getComparerData': {'shortpixel_get_comparer_data'}}
*
***/

/** Function ajax_getBackupFolderSize() called by wp_ajax hooks: {'shortpixel_get_backup_size'} **/
/** No params detected :-/ **/


/** Function ajaxBrowseContent() called by wp_ajax hooks: {'shortpixel_browse_content'} **/
/** Parameters found in function ajaxBrowseContent(): {"post": ["dir"]} **/
function ajaxBrowseContent()
    {
      if ( ! $this->userIsAllowed )  {
          wp_die(esc_html(__('You do not have sufficient permissions to access this page.','shortpixel-image-optimiser')));
      }
      $fs = \wpSPIO()->filesystem();
      $rootDirObj = $fs->getWPFileBase();
      $path = $rootDirObj->getPath();

			// @todo Add Nonce here
      $postDir = isset($_POST['dir']) ? trim(sanitize_text_field(wp_unslash($_POST['dir']))) : null;
      if (! is_null($postDir))
      {
         $postDir = rawurldecode($postDir);
         $children = explode('/', $postDir );

         foreach($children as $child)
         {
            if ($child == '.' || $child == '..')
              continue;

             $path .= '/' . $child;
         }
      }

      $dirObj = $fs->getDirectory($path);

      if ($dirObj->getPath() !== $rootDirObj->getPath() && ! $dirObj->isSubFolderOf($rootDirObj))
      {
        exit(esc_html(__('This directory seems not part of WordPress', 'shortpixel-image-optimiser')));
      }

      if( $dirObj->exists() ) {

          //$dir = $fs->getDirectory($postDir);
    //      $files = $dirObj->getFiles();
          $subdirs = $fs->sortFiles($dirObj->getSubDirectories()); // runs through FS sort.


          foreach($subdirs as $index => $dir) // weed out the media library subdirectories.
          {
            $dirname = $dir->getName();
						// @todo This should probably be checked via getBackupDirectory or so, not hardcoded ShortipxelBackups
            if($dirname == 'ShortpixelBackups' || $this->checkifMediaLibrary($dir) )
            {
               unset($subdirs[$index]);
            }
          }

          if( count($subdirs) > 0 ) {
              echo "<ul class='jqueryFileTree'>";
              foreach($subdirs as $dir ) {
                  $returnDir = substr($dir->getPath(), strlen($rootDirObj->getPath())); // relative to root.
                  $dirpath = $dir->getPath();
                  $dirname = $dir->getName();
                  // @todo Should in time be moved to othermedia_controller / check if media library

                  $htmlRel	= str_replace("'", "&apos;", $returnDir );
                  $htmlName	= htmlentities($dirname);
                  //$ext	= preg_replace('/^.*\./', '', $file);

                  if( $dir->exists() ) {
                      //KEEP the spaces in front of the rel values - it's a trick to make WP Hide not replace the wp-content path
                          echo "<li class='directory collapsed'><a rel=' " .esc_attr($htmlRel) . "'>" . esc_html($htmlName) . "</a></li>";
                  }

              }

              echo "</ul>";
          }
          elseif ($_POST['dir'] == '/')
          {
            echo "<ul class='jqueryFileTree'>";
            esc_html_e('No Directories found that can be added to Custom Folders', 'shortpixel-image-optimiser');
            echo "</ul>";
          }
      }

      die();
    }


/** Function ajax_proposeQuotaUpgrade() called by wp_ajax hooks: {'shortpixel_propose_upgrade'} **/
/** No params detected :-/ **/


/** Function ajaxRequest() called by wp_ajax hooks: {'shortpixel_ajaxRequest'} **/
/** Parameters found in function ajaxRequest(): {"post": ["screen_action", "type", "id", "loadFile"]} **/
function ajaxRequest()
    {
        $this->checkNonce('ajax_request');

			  // phpcs:ignore -- Nonce is checked
        $action = isset($_POST['screen_action']) ? sanitize_text_field($_POST['screen_action']) : false;
				// phpcs:ignore -- Nonce is checked
        $typeArray = isset($_POST['type'])  ? array(sanitize_text_field($_POST['type'])) : array('media', 'custom');
				// phpcs:ignore -- Nonce is checked
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;

        $json = new \stdClass;
        foreach($typeArray as $type)
        {
          $json->$type = new \stdClass;
          $json->$type->id = $id;
          $json->$type->results = null;
          $json->$type->is_error = false;
          $json->status = false;
        }

        $data = array('id' => $id, 'typeArray' => $typeArray, 'action' => $action);

        if (count($typeArray) == 1) // Actions which need specific type like optimize / restore.
        {
          $data['type'] = $typeArray[0];
          unset($data['typeArray']);
        }

				Log::addInfo('AjaxController: Action detected :' . $action);
        switch($action)
        {
           case 'restoreItem':
              $json = $this->restoreItem($json, $data);
           break;
           case 'reOptimizeItem':
             $json = $this->reOptimizeItem($json, $data);
           break;
           case 'optimizeItem':
             $json = $this->optimizeItem($json, $data);
           break;
           case 'createBulk':
             $json = $this->createBulk($json, $data);
           break;
           case 'applyBulkSelection':
             $json = $this->applyBulkSelection($json, $data);
           break;
           case 'startBulk':
             $json = $this->startBulk($json, $data);
           break;
           case 'finishBulk':
             $json = $this->finishBulk($json, $data);
           break;
           case 'startRestoreAll':
              $json = $this->startRestoreAll($json,$data);
           break;
           case 'startMigrateAll':
              $json = $this->startMigrateAll($json, $data);
           break;
					 case 'startRemoveLegacy':
					 		$json = $this->startRemoveLegacy($json, $data);
					 break;
					 case "toolsRemoveAll":
					 		 $json = $this->removeAllData($json, $data);
					 break;
					 case "toolsRemoveBackup":
					 		 $json = $this->removeBackup($json, $data);
					 break;
					 case 'request_new_api_key': // @todo Dunnoo why empty, should go if not here.

					 break;
					 case "loadLogFile":
					  	$data['logFile'] = isset($_POST['loadFile']) ? sanitize_text_field($_POST['loadFile']) : null;
					 		$json = $this->loadLogFile($json, $data);
					 break;
					 case "redoLegacy":
					 	  $this->redoLegacy($json, $data);
					 break;

           default:
              $json->$type->message = __('Ajaxrequest - no action found', 'shorpixel-image-optimiser');
              $json->error = self::NO_ACTION;
           break;

        }
        $this->send($json);

    }


/** Function ajax_getItemView() called by wp_ajax hooks: {'shortpixel_get_item_view'} **/
/** Parameters found in function ajax_getItemView(): {"post": ["type", "id"]} **/
function ajax_getItemView()
    {
        $this->checkNonce('item_view');
				// phpcs:ignore -- Nonce is checked
          $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'media';
				// phpcs:ignore -- Nonce is checked
          $id = isset($_POST['id']) ? intval($_POST['id']) : false;
					$result = '';

          if ($id > 0)
          {
             if ($type == 'media')
             {
               ob_start();
               $control = new ListMediaViewController();
               $control->doColumn('wp-shortPixel', $id);
               $result = ob_get_contents();
               ob_end_clean();
             }
             if ($type == 'custom')
             {
                ob_start();
                $control = new OtherMediaViewController();
                $item = \wpSPIO()->filesystem()->getImage($id, 'custom');
                  $control->doActionColumn($item);
                $result = ob_get_contents();
                ob_end_clean();
             }
          }

          $json = new \stdClass;
          $json->$type = new \stdClass;
          $json->$type->itemView = $result;
          $json->$type->id = $id;
          $json->$type->results = null;
          $json->$type->is_error = false;
          $json->status = true;

          $this->send($json);
    }


/** Function deactivatePluginCallback() called by wp_ajax hooks: {'shortpixel_deactivate_plugin'} **/
/** Parameters found in function deactivatePluginCallback(): {"post": ["reason", "details", "anonymous"]} **/
function deactivatePluginCallback() {

        check_ajax_referer( 'shortpixel_deactivate_plugin', 'security' );


				Log::addDebug('Deactive Plugin Callback POST', $_POST);

        if ( isset($_POST['reason']) && isset($_POST['details']) && isset($_POST['anonymous']) ) {
            require_once(\WPSPIO()->plugin_path() . 'class/view/shortpixel-plugin-request.php');
            $anonymous = (intval($_POST['anonymous']) == 1) ? true : false;
            $args = array(
                'key' =>  $this->key,
                'reason' => sanitize_text_field(wp_unslash($_POST['reason'])),
                'details' => sanitize_text_field(wp_unslash($_POST['details'])),
                'anonymous' => $anonymous
            );
            $request = new ShortPixelPluginRequest( $this->plugin_file, 'http://' . SHORTPIXEL_API . '/v2/feedback.php', $args );
            if ( $request->request_successful ) {
                echo json_encode( array(
                    'status' => 'ok',
                ) );
            }else{
                echo json_encode( array(
                    'status' => 'nok',
                ) );
            }
        }else{
            echo json_encode( array(
                'status' => 'OK',
            ) );
        }

        die();

    }


/** Function ajax_processQueue() called by wp_ajax hooks: {'shortpixel_image_processing'} **/
/** Parameters found in function ajax_processQueue(): {"post": ["isBulk", "queues"]} **/
function ajax_processQueue()
    {
        $this->checkNonce('processing');
        $this->checkProcessorKey();

        // Notice that POST variables are always string, so 'true', not true.
				// phpcs:ignore -- Nonce is checked
        $isBulk = (isset($_POST['isBulk']) && $_POST['isBulk'] === 'true') ? true : false;
				// phpcs:ignore -- Nonce is checked
        $queue = (isset($_POST['queues'])) ? sanitize_text_field($_POST['queues']) : 'media,custom';

        $queues = array_filter(explode(',', $queue), 'trim');

        $control = new OptimizeController();
        $control->setBulk($isBulk);
        $result = $control->processQueue($queues);

        $this->send($result);
    }


/** Function ajax_checkquota() called by wp_ajax hooks: {'shortpixel_check_quota'} **/
/** No params detected :-/ **/


/** Function ajax_getComparerData() called by wp_ajax hooks: {'shortpixel_get_comparer_data'} **/
/** Parameters found in function ajax_getComparerData(): {"post": ["type", "id"]} **/
function ajax_getComparerData() {

        $this->checkNonce('ajax_request');

        $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : 'media';
        $id = isset($_POST['id']) ? intval($_POST['id']) : false;

        if ( $id === false || !current_user_can( 'upload_files' ) && !current_user_can( 'edit_posts' ) )  {

            $json->status = false;
            $json->id = $id;
            $json->message = __('Error - item to compare could not be found or no access', 'shortpixel-image-optimiser');
            $this->send($json);
        }

        $ret = array();
        $fs = \wpSPIO()->filesystem();
        $imageObj = $fs->getImage($id, $type);

        // With PDF, the thumbnail called 'full' is the image, the main is the PDF file
        if ($imageObj->getExtension() == 'pdf')
        {
           $thumbImg = $imageObj->getThumbnail('full');
           if ($thumbImg !== false)
           {
              $imageObj = $thumbImg;
           }
        }



        $backupFile = $imageObj->getBackupFile();
        if (is_object($backupFile))
          $backup_url = $fs->pathToUrl($backupFile);
        else
          $backup_url = '';

        $ret['origUrl'] = $backup_url; // $backupUrl . $urlBkPath . $meta->getName();

          $ret['optUrl'] = $imageObj->getURL(); // $uploadsUrl . $meta->getWebPath();
          $ret['width'] = $imageObj->getMeta('originalWidth'); // $meta->getActualWidth();
          $ret['height'] = $imageObj->getMeta('originalHeight');

          if (is_null($ret['width']) || $ret['width'] == false)
          {

              if (! $imageObj->is_virtual())
              {
                $ret['width'] = $imageObj->get('width'); // $imageSizes[0];
                $ret['height']= $imageObj->get('height'); //imageSizes[1];
              }
              else
              {
                  $size = getimagesize($backupFile->getFullPath());
                  if (is_array($size))
                  {
                     $ret['width'] = $size[0];
                     $ret['height'] = $size[1];
                  }

              }

          }

        $this->send( (object) $ret);
    }


